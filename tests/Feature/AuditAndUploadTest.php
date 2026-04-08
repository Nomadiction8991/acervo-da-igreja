<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\SyncFileToDriveJob;
use App\Jobs\SyncFotoToDriveJob;
use App\Models\AuditLog;
use App\Models\Documento;
use App\Models\DriveAccount;
use App\Models\Foto;
use App\Models\GrupoDocumento;
use App\Models\Igreja;
use App\Models\Permission;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class AuditAndUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_church_lifecycle_writes_audit_logs_with_old_and_new_values(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user);

        $igreja = Igreja::query()->create([
            'codigo_controle' => 'ABC1234',
            'nome_fantasia' => 'Igreja Inicial',
            'razao_social' => 'Razao Inicial',
            'publico_nome_fantasia' => true,
            'publico_cidade' => true,
            'publico_estado' => true,
        ]);

        $igreja->update(['nome_fantasia' => 'Igreja Atualizada']);
        $igreja->delete();

        $this->assertSame(3, AuditLog::query()->where('entidade', Igreja::class)->count());

        $this->assertDatabaseHas('audit_logs', [
            'acao' => 'create',
            'modulo' => 'igrejas',
            'entidade_id' => $igreja->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'acao' => 'update',
            'modulo' => 'igrejas',
            'entidade_id' => $igreja->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'acao' => 'delete',
            'modulo' => 'igrejas',
            'entidade_id' => $igreja->id,
        ]);
    }

    public function test_document_upload_without_drive_selection_stores_private_file_without_dispatching_sync_job(): void
    {
        Storage::fake('local');
        Queue::fake();
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $user->permissions()->attach(
            Permission::query()->where('slug', 'documentos.criar')->firstOrFail(),
        );

        $igreja = Igreja::factory()->create();
        $grupo = GrupoDocumento::query()->create([
            'nome' => 'Interno',
            'descricao' => 'Arquivos internos',
            'publico_padrao' => false,
        ]);

        $response = $this->actingAs($user)->post(route('documentos.store'), [
            'igreja_id' => $igreja->id,
            'grupo_documento_id' => $grupo->id,
            'titulo' => 'Relatorio Interno',
            'descricao' => 'Relatorio reservado',
            'tipo' => 'pdf',
            'arquivo' => UploadedFile::fake()->create('relatorio.pdf', 64, 'application/pdf'),
        ]);

        $documento = Documento::query()->firstOrFail();

        $response->assertRedirect(route('documentos.show', $documento));
        Storage::disk('local')->assertExists($documento->path);

        $this->assertSame('local', $documento->disk);
        $this->assertFalse($documento->publico);
        $this->assertNull($documento->sync_status);
        $this->assertNull($documento->drive_account_id);
        $this->assertNull($documento->drive_folder_id);

        Queue::assertNotPushed(SyncFileToDriveJob::class);
    }

    public function test_document_upload_with_selected_drive_account_and_folder_does_not_dispatch_sync_job_automatically(): void
    {
        Storage::fake('local');
        Queue::fake();
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $user->permissions()->attach(
            Permission::query()->where('slug', 'documentos.criar')->firstOrFail(),
        );

        $igreja = Igreja::factory()->create();
        $driveAccount = DriveAccount::query()->create([
            'nome' => 'Conta Acervo',
            'provider' => 'google_drive',
            'client_id' => 'client-id-123',
            'client_secret' => 'client-secret-123',
            'refresh_token' => 'refresh-token-123',
            'folder_id' => 'folder-padrao',
        ]);

        $response = $this->actingAs($user)->post(route('documentos.store'), [
            'igreja_id' => $igreja->id,
            'titulo' => 'Ata do conselho',
            'tipo' => 'pdf',
            'drive_account_id' => $driveAccount->id,
            'drive_folder_id' => 'folder-do-documento',
            'arquivo' => UploadedFile::fake()->create('ata.pdf', 64, 'application/pdf'),
        ]);

        $documento = Documento::query()->firstOrFail();

        $response->assertRedirect(route('documentos.show', $documento));
        $this->assertNull($documento->sync_status);
        $this->assertSame($driveAccount->id, $documento->drive_account_id);
        $this->assertSame('folder-do-documento', $documento->drive_folder_id);

        Queue::assertNotPushed(SyncFileToDriveJob::class);
    }

    public function test_photo_upload_does_not_dispatch_drive_sync_job_automatically_even_when_connected_account_exists(): void
    {
        Storage::fake('local');
        Queue::fake();
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $user->permissions()->attach(
            Permission::query()->where('slug', 'fotos.criar')->firstOrFail(),
        );

        $igreja = Igreja::factory()->create();
        DriveAccount::query()->create([
            'nome' => 'Conta Acervo',
            'provider' => 'google_drive',
            'is_active' => true,
            'client_id' => 'client-id-123',
            'client_secret' => 'client-secret-123',
            'refresh_token' => 'refresh-token-123',
        ]);

        $response = $this->actingAs($user)->post(route('fotos.store', $igreja), [
            'fotos' => [UploadedFile::fake()->create('foto-igreja.jpg', 256, 'image/jpeg')],
            'is_public' => true,
        ]);

        $response->assertRedirect(route('fotos.index', $igreja));

        /** @var \App\Models\Foto $foto */
        $foto = $igreja->fotos()->firstOrFail();

        $this->assertNull($foto->drive_account_id);
        $this->assertNull($foto->sync_status);
        $this->assertNull($foto->sync_error);

        Queue::assertNotPushed(SyncFotoToDriveJob::class);
    }

    public function test_photo_upload_does_not_dispatch_drive_sync_when_no_connected_account_exists(): void
    {
        Storage::fake('local');
        Queue::fake();
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $user->permissions()->attach(
            Permission::query()->where('slug', 'fotos.criar')->firstOrFail(),
        );

        $igreja = Igreja::factory()->create();

        $response = $this->actingAs($user)->post(route('fotos.store', $igreja), [
            'fotos' => [UploadedFile::fake()->create('foto-sem-drive.jpg', 256, 'image/jpeg')],
            'is_public' => true,
        ]);

        $response->assertRedirect(route('fotos.index', $igreja));

        /** @var \App\Models\Foto $foto */
        $foto = $igreja->fotos()->firstOrFail();

        $this->assertNull($foto->drive_account_id);
        $this->assertNull($foto->sync_status);

        Queue::assertNotPushed(SyncFotoToDriveJob::class);
    }

    public function test_manual_photo_sync_action_dispatches_drive_job_and_marks_photo_as_pending(): void
    {
        Queue::fake();
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $user->permissions()->attach(
            Permission::query()->where('slug', 'fotos.editar')->firstOrFail(),
        );

        $igreja = Igreja::factory()->create();
        $driveAccount = DriveAccount::query()->create([
            'nome' => 'Conta Acervo',
            'provider' => 'google_drive',
            'is_active' => true,
            'refresh_token' => 'refresh-token-123',
        ]);

        $foto = Foto::query()->create([
            'igreja_id' => $igreja->id,
            'drive_account_id' => null,
            'drive_folder_id' => null,
            'caminho' => 'uploads/fotos/manual.jpg',
            'disk' => 'local',
            'nome_original' => 'manual.jpg',
            'mime_type' => 'image/jpeg',
            'tamanho' => 2048,
            'is_public' => true,
            'is_principal' => true,
            'ordem' => 1,
            'sync_status' => 'error',
            'sync_error' => 'Falha anterior',
            'drive_file_id' => 'old-file-id',
            'drive_link' => 'https://drive.google.com/file/d/old-file-id/view',
            'synced_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->from(route('fotos.show', [$igreja, $foto]))
            ->post(route('fotos.sync-drive', [$igreja, $foto]));

        $response
            ->assertRedirect(route('fotos.show', [$igreja, $foto]))
            ->assertSessionHas('success');

        $foto->refresh();

        $this->assertSame($driveAccount->id, $foto->drive_account_id);
        $this->assertSame('pending', $foto->sync_status);
        $this->assertNull($foto->sync_error);
        $this->assertNull($foto->drive_file_id);
        $this->assertNull($foto->drive_link);
        $this->assertNull($foto->synced_at);

        Queue::assertPushed(SyncFotoToDriveJob::class, static function (SyncFotoToDriveJob $job) use ($foto): bool {
            return $job->fotoId === $foto->id;
        });
    }

    public function test_manual_photo_sync_action_rejects_when_no_connected_drive_account_exists(): void
    {
        Queue::fake();
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $user->permissions()->attach(
            Permission::query()->where('slug', 'fotos.editar')->firstOrFail(),
        );

        $igreja = Igreja::factory()->create();

        $foto = Foto::query()->create([
            'igreja_id' => $igreja->id,
            'drive_account_id' => null,
            'drive_folder_id' => null,
            'caminho' => 'uploads/fotos/sem-conta.jpg',
            'disk' => 'local',
            'nome_original' => 'sem-conta.jpg',
            'mime_type' => 'image/jpeg',
            'tamanho' => 2048,
            'is_public' => true,
            'is_principal' => true,
            'ordem' => 1,
            'sync_status' => null,
        ]);

        $response = $this->actingAs($user)
            ->from(route('fotos.show', [$igreja, $foto]))
            ->post(route('fotos.sync-drive', [$igreja, $foto]));

        $response
            ->assertRedirect(route('fotos.show', [$igreja, $foto]))
            ->assertSessionHasErrors('drive_sync');

        $foto->refresh();

        $this->assertNull($foto->drive_account_id);
        $this->assertNull($foto->sync_status);
        $this->assertNull($foto->drive_file_id);
        $this->assertNull($foto->drive_link);
        $this->assertNull($foto->synced_at);

        Queue::assertNotPushed(SyncFotoToDriveJob::class);
    }

    public function test_document_upload_rejects_drive_account_without_completed_connection(): void
    {
        Storage::fake('local');
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $user->permissions()->attach(
            Permission::query()->where('slug', 'documentos.criar')->firstOrFail(),
        );

        $igreja = Igreja::factory()->create();
        $driveAccount = DriveAccount::query()->create([
            'nome' => 'Conta pendente',
            'provider' => 'google_drive',
            'client_id' => 'client-id-123',
            'client_secret' => 'client-secret-123',
        ]);

        $response = $this->actingAs($user)->post(route('documentos.store'), [
            'igreja_id' => $igreja->id,
            'titulo' => 'Documento de teste',
            'tipo' => 'pdf',
            'drive_account_id' => $driveAccount->id,
            'arquivo' => UploadedFile::fake()->create('pendente.pdf', 64, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('drive_account_id');
        $this->assertDatabaseCount('documentos', 0);
    }

    public function test_document_upload_rejects_shared_drive_root_id_as_folder_override(): void
    {
        Storage::fake('local');
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $user->permissions()->attach(
            Permission::query()->where('slug', 'documentos.criar')->firstOrFail(),
        );

        $igreja = Igreja::factory()->create();
        $driveAccount = DriveAccount::query()->create([
            'nome' => 'Conta Acervo',
            'provider' => 'google_drive',
            'client_id' => 'client-id-123',
            'client_secret' => 'client-secret-123',
            'refresh_token' => 'refresh-token-123',
        ]);

        $response = $this->actingAs($user)->post(route('documentos.store'), [
            'igreja_id' => $igreja->id,
            'titulo' => 'Documento de teste',
            'tipo' => 'pdf',
            'drive_account_id' => $driveAccount->id,
            'drive_folder_id' => '0ACFCBLYL72ZGUk9PVA',
            'arquivo' => UploadedFile::fake()->create('teste.pdf', 64, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('drive_folder_id');
        $this->assertDatabaseCount('documentos', 0);
    }

    public function test_manual_sync_action_dispatches_drive_job_and_marks_document_as_pending(): void
    {
        Queue::fake();
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $user->permissions()->attach(
            Permission::query()->where('slug', 'documentos.editar')->firstOrFail(),
        );

        $driveAccount = DriveAccount::query()->create([
            'nome' => 'Conta Acervo',
            'email' => 'acervo@example.com',
            'provider' => 'google_drive',
            'client_id' => 'client-id-123',
            'client_secret' => 'client-secret-123',
            'refresh_token' => 'refresh-token-123',
        ]);

        $documento = Documento::query()->create([
            'igreja_id' => Igreja::factory()->create()->id,
            'user_id' => $user->id,
            'drive_account_id' => $driveAccount->id,
            'titulo' => 'Manual interno',
            'path' => 'uploads/manual.pdf',
            'disk' => 'local',
            'tipo' => 'pdf',
            'mime_type' => 'application/pdf',
            'tamanho' => 1024,
            'publico' => false,
            'sync_status' => 'error',
            'sync_error' => 'Falha anterior',
            'drive_file_id' => 'old-id',
            'drive_link' => 'https://drive.google.com/file/d/old-id/view',
            'synced_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->from(route('documentos.show', $documento))
            ->post(route('documentos.sync-drive', $documento));

        $response
            ->assertRedirect(route('documentos.show', $documento))
            ->assertSessionHas('success');

        $documento->refresh();

        $this->assertSame('pending', $documento->sync_status);
        $this->assertNull($documento->sync_error);
        $this->assertNull($documento->drive_file_id);
        $this->assertNull($documento->drive_link);
        $this->assertNull($documento->synced_at);

        Queue::assertPushed(SyncFileToDriveJob::class, static function (SyncFileToDriveJob $job) use ($documento): bool {
            return $job->documentoId === $documento->id;
        });
    }

    public function test_manual_sync_action_rejects_document_without_drive_account(): void
    {
        Queue::fake();
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $user->permissions()->attach(
            Permission::query()->where('slug', 'documentos.editar')->firstOrFail(),
        );

        $documento = Documento::query()->create([
            'igreja_id' => Igreja::factory()->create()->id,
            'user_id' => $user->id,
            'drive_account_id' => null,
            'titulo' => 'Manual interno',
            'path' => 'uploads/manual.pdf',
            'disk' => 'local',
            'tipo' => 'pdf',
            'mime_type' => 'application/pdf',
            'tamanho' => 1024,
            'publico' => false,
            'sync_status' => null,
        ]);

        $response = $this->actingAs($user)
            ->from(route('documentos.show', $documento))
            ->post(route('documentos.sync-drive', $documento));

        $response
            ->assertRedirect(route('documentos.show', $documento))
            ->assertSessionHasErrors('drive_sync');

        $documento->refresh();

        $this->assertSame('error', $documento->sync_status);
        $this->assertStringContainsString('Selecione uma conta Google Drive', (string) $documento->sync_error);

        Queue::assertNotPushed(SyncFileToDriveJob::class);
    }

    public function test_document_index_shows_drive_status_and_drive_account_email(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $user->permissions()->attach(
            Permission::query()->where('slug', 'documentos.visualizar')->firstOrFail(),
        );

        $driveAccount = DriveAccount::query()->create([
            'nome' => 'Conta Acervo',
            'email' => 'acervo@example.com',
            'provider' => 'google_drive',
            'refresh_token' => 'refresh-token-123',
        ]);

        Documento::query()->create([
            'igreja_id' => Igreja::factory()->create()->id,
            'drive_account_id' => $driveAccount->id,
            'titulo' => 'Documento publico',
            'path' => 'uploads/publico.pdf',
            'disk' => 'local',
            'tipo' => 'pdf',
            'mime_type' => 'application/pdf',
            'tamanho' => 1024,
            'publico' => true,
            'sync_status' => 'synced',
        ]);

        $this->actingAs($user)
            ->get(route('documentos.index'))
            ->assertOk()
            ->assertSee('Status Drive')
            ->assertSee('acervo@example.com')
            ->assertSee('Sincronizado');
    }

    public function test_sync_job_reports_helpful_message_for_shared_drive_id_with_drive_file_scope(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('uploads/ata.pdf', 'conteudo pdf');

        config([
            'services.google_drive.oauth_scope' => 'https://www.googleapis.com/auth/drive.file',
        ]);

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'drive-access-token',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
            ]),
            'https://www.googleapis.com/upload/drive/v3/files*' => Http::response([
                'error' => [
                    'code' => 404,
                    'message' => 'File not found: 0ACFCBLYL72ZGUk9PVA.',
                ],
            ], 404),
        ]);

        $user = User::factory()->admin()->create();
        $driveAccount = DriveAccount::query()->create([
            'nome' => 'Conta Acervo',
            'provider' => 'google_drive',
            'client_id' => 'client-id-123',
            'client_secret' => 'client-secret-123',
            'refresh_token' => 'refresh-token-123',
        ]);

        $documento = Documento::query()->create([
            'igreja_id' => Igreja::factory()->create()->id,
            'user_id' => $user->id,
            'drive_account_id' => $driveAccount->id,
            'drive_folder_id' => '0ACFCBLYL72ZGUk9PVA',
            'titulo' => 'Ata do conselho',
            'path' => 'uploads/ata.pdf',
            'disk' => 'local',
            'tipo' => 'pdf',
            'mime_type' => 'application/pdf',
            'tamanho' => 128,
            'publico' => false,
            'sync_status' => 'pending',
        ]);

        $job = new SyncFileToDriveJob($documento->id);
        $job->handle(app(\App\Services\GoogleDriveService::class), app(\App\Services\AuditLogService::class));

        $documento->refresh();

        $this->assertSame('error', $documento->sync_status);
        $this->assertStringContainsString('Shared Drive', (string) $documento->sync_error);
        $this->assertStringContainsString('drive.file', (string) $documento->sync_error);
    }
}
