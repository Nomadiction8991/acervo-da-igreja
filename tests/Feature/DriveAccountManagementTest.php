<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\DriveAccount;
use App\Models\Documento;
use App\Models\Igreja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class DriveAccountManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_multiple_drive_accounts_without_global_active_switch(): void
    {
        $user = User::factory()->admin()->create();

        $existing = DriveAccount::query()->create([
            'nome' => 'Conta Antiga',
            'email' => 'antiga@example.com',
            'provider' => 'google_drive',
            'folder_id' => 'folder-old',
            'client_id' => 'old-client',
            'client_secret' => 'old-secret',
            'refresh_token' => 'old-refresh',
        ]);

        $response = $this->actingAs($user)->post(route('drive-accounts.store'), [
            'nome' => 'Conta Principal',
            'email' => 'nova@example.com',
            'folder_id' => 'folder-123',
            'client_id' => 'client-id-123',
            'client_secret' => 'client-secret-123',
            'refresh_token' => 'refresh-token-123',
        ]);

        $newAccount = DriveAccount::query()->where('nome', 'Conta Principal')->firstOrFail();

        $response->assertRedirect(route('drive-accounts.show', $newAccount));
        $this->assertSame('client-id-123', $newAccount->client_id);
        $this->assertSame('refresh-token-123', $newAccount->refresh_token);
        $this->assertDatabaseHas('drive_accounts', ['id' => $existing->id]);
        $this->assertDatabaseHas('drive_accounts', ['id' => $newAccount->id]);
    }

    public function test_admin_cannot_save_shared_drive_root_id_as_default_folder(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->post(route('drive-accounts.store'), [
            'nome' => 'Conta Invalida',
            'folder_id' => '0ACFCBLYL72ZGUk9PVA',
            'client_id' => 'client-id-123',
            'client_secret' => 'client-secret-123',
            'refresh_token' => 'refresh-token-123',
        ]);

        $response->assertSessionHasErrors('folder_id');
        $this->assertDatabaseMissing('drive_accounts', [
            'nome' => 'Conta Invalida',
        ]);
    }

    public function test_connection_test_updates_email_and_metadata(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'token-123',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
            ]),
            'https://www.googleapis.com/drive/v3/about*' => Http::response([
                'user' => [
                    'displayName' => 'Conta Oficial',
                    'emailAddress' => 'drive@example.com',
                ],
            ]),
            'https://www.googleapis.com/drive/v3/files/root*' => Http::response([
                'id' => 'root-folder-id',
            ]),
            'https://www.googleapis.com/drive/v3/files/folder-123*' => Http::response([
                'id' => 'folder-123',
                'name' => 'Documentos Oficiais',
                'mimeType' => 'application/vnd.google-apps.folder',
                'webViewLink' => 'https://drive.google.com/drive/folders/folder-123',
            ]),
        ]);

        $user = User::factory()->admin()->create();
        $driveAccount = DriveAccount::query()->create([
            'nome' => 'Conta Teste',
            'provider' => 'google_drive',
            'folder_id' => 'folder-123',
            'client_id' => 'client-id-123',
            'client_secret' => 'client-secret-123',
            'refresh_token' => 'refresh-token-123',
        ]);

        $response = $this->actingAs($user)
            ->post(route('drive-accounts.test', $driveAccount));

        $response
            ->assertRedirect(route('drive-accounts.show', $driveAccount))
            ->assertSessionHas('success');

        $driveAccount->refresh();

        $this->assertSame('drive@example.com', $driveAccount->email);
        $this->assertSame('Conta Oficial', data_get($driveAccount->metadata, 'display_name'));
        $this->assertSame('root-folder-id', data_get($driveAccount->metadata, 'root_folder_id'));
        $this->assertSame('Documentos Oficiais', data_get($driveAccount->metadata, 'configured_folder.name'));
        $this->assertNotNull(data_get($driveAccount->metadata, 'last_tested_at'));

        Http::assertSent(static function (Request $request): bool {
            if (! str_starts_with($request->url(), 'https://www.googleapis.com/drive/v3/files/folder-123')) {
                return false;
            }

            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return ($query['supportsAllDrives'] ?? null) === 'true';
        });
    }

    public function test_admin_can_create_drive_account_without_manual_tokens_for_oauth_flow(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->post(route('drive-accounts.store'), [
            'nome' => 'Conta OAuth',
            'folder_id' => 'folder-oauth',
        ]);

        $driveAccount = DriveAccount::query()->where('nome', 'Conta OAuth')->firstOrFail();

        $response->assertRedirect(route('drive-accounts.show', $driveAccount));
        $this->assertNull($driveAccount->client_id);
        $this->assertNull($driveAccount->client_secret);
        $this->assertNull($driveAccount->refresh_token);
    }

    public function test_oauth_callback_saves_refresh_token_and_marks_connection_method(): void
    {
        config([
            'services.google_drive.client_id' => 'global-client-id',
            'services.google_drive.client_secret' => 'global-client-secret',
            'services.google_drive.redirect_uri' => 'http://localhost:8000/google/drive/callback',
            'services.google_drive.oauth_scope' => 'https://www.googleapis.com/auth/drive.file',
        ]);

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'oauth-access-token',
                'refresh_token' => 'oauth-refresh-token',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
            ]),
            'https://www.googleapis.com/drive/v3/about*' => Http::response([
                'user' => [
                    'displayName' => 'Conta OAuth',
                    'emailAddress' => 'oauth@example.com',
                ],
            ]),
            'https://www.googleapis.com/drive/v3/files/root*' => Http::response([
                'id' => 'oauth-root-folder',
            ]),
            'https://www.googleapis.com/drive/v3/files?fields=id&supportsAllDrives=true' => Http::response([
                'id' => 'acervo-root-id',
            ]),
        ]);

        $user = User::factory()->admin()->create();
        $driveAccount = DriveAccount::query()->create([
            'nome' => 'Conta OAuth',
            'provider' => 'google_drive',
        ]);

        $redirectResponse = $this->actingAs($user)
            ->get(route('drive-accounts.oauth.redirect', $driveAccount));

        $redirectResponse->assertRedirect();
        self::assertStringStartsWith(
            'https://accounts.google.com/o/oauth2/v2/auth?',
            (string) $redirectResponse->headers->get('Location'),
        );

        /** @var array<string, array{drive_account_id?: int}> $states */
        $states = session('google_drive_oauth_states', []);
        $state = array_key_first($states);

        self::assertIsString($state);

        $callbackResponse = $this->actingAs($user)->get(route('google.drive.callback', [
            'state' => $state,
            'code' => 'google-auth-code',
        ]));

        $callbackResponse
            ->assertRedirect(route('drive-accounts.show', $driveAccount))
            ->assertSessionHas('success');

        $driveAccount->refresh();

        $this->assertSame('oauth@example.com', $driveAccount->email);
        $this->assertSame('oauth-refresh-token', $driveAccount->refresh_token);
        $this->assertSame('acervo-root-id', $driveAccount->folder_id);
        $this->assertSame('oauth', data_get($driveAccount->metadata, 'connection_method'));
        $this->assertSame('Conta OAuth', data_get($driveAccount->metadata, 'display_name'));
        $this->assertNotNull(data_get($driveAccount->metadata, 'oauth_connected_at'));
    }

    public function test_oauth_callback_does_not_fail_when_root_folder_is_unavailable(): void
    {
        config([
            'services.google_drive.client_id' => 'global-client-id',
            'services.google_drive.client_secret' => 'global-client-secret',
            'services.google_drive.redirect_uri' => 'http://localhost:8000/google/drive/callback',
            'services.google_drive.oauth_scope' => 'https://www.googleapis.com/auth/drive.file',
        ]);

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'oauth-access-token',
                'refresh_token' => 'oauth-refresh-token',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
            ]),
            'https://www.googleapis.com/drive/v3/about*' => Http::response([
                'user' => [
                    'displayName' => 'Conta OAuth',
                    'emailAddress' => 'oauth@example.com',
                ],
            ]),
            'https://www.googleapis.com/drive/v3/files/root*' => Http::response([
                'error' => [
                    'code' => 404,
                    'message' => 'File not found: 0ACFCBLYL72ZGUk9PVA.',
                ],
            ], 404),
            'https://www.googleapis.com/drive/v3/files?fields=id&supportsAllDrives=true' => Http::response([
                'id' => 'acervo-root-id',
            ]),
        ]);

        $user = User::factory()->admin()->create();
        $driveAccount = DriveAccount::query()->create([
            'nome' => 'Conta OAuth',
            'provider' => 'google_drive',
        ]);

        $redirectResponse = $this->actingAs($user)
            ->get(route('drive-accounts.oauth.redirect', $driveAccount));

        $redirectResponse->assertRedirect();

        /** @var array<string, array{drive_account_id?: int}> $states */
        $states = session('google_drive_oauth_states', []);
        $state = array_key_first($states);

        self::assertIsString($state);

        $callbackResponse = $this->actingAs($user)->get(route('google.drive.callback', [
            'state' => $state,
            'code' => 'google-auth-code',
        ]));

        $callbackResponse
            ->assertRedirect(route('drive-accounts.show', $driveAccount))
            ->assertSessionHas('success');

        $driveAccount->refresh();

        $this->assertSame('oauth-refresh-token', $driveAccount->refresh_token);
        $this->assertSame('acervo-root-id', $driveAccount->folder_id);
        $this->assertNull(data_get($driveAccount->metadata, 'root_folder_id'));
    }

    public function test_connection_test_uses_global_client_credentials_for_oauth_connected_account(): void
    {
        config([
            'services.google_drive.client_id' => 'global-client-id',
            'services.google_drive.client_secret' => 'global-client-secret',
        ]);

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'token-123',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
            ]),
            'https://www.googleapis.com/drive/v3/about*' => Http::response([
                'user' => [
                    'displayName' => 'Conta OAuth',
                    'emailAddress' => 'oauth@example.com',
                ],
            ]),
            'https://www.googleapis.com/drive/v3/files/root*' => Http::response([
                'id' => 'oauth-root-folder',
            ]),
        ]);

        $user = User::factory()->admin()->create();
        $driveAccount = DriveAccount::query()->create([
            'nome' => 'Conta OAuth',
            'provider' => 'google_drive',
            'refresh_token' => 'oauth-refresh-token',
            'metadata' => ['connection_method' => 'oauth'],
        ]);

        $response = $this->actingAs($user)
            ->post(route('drive-accounts.test', $driveAccount));

        $response
            ->assertRedirect(route('drive-accounts.show', $driveAccount))
            ->assertSessionHas('success');

        $driveAccount->refresh();

        $this->assertSame('oauth@example.com', $driveAccount->email);
        $this->assertSame('oauth-root-folder', data_get($driveAccount->metadata, 'root_folder_id'));
    }

    public function test_sync_job_uses_document_selected_drive_account_and_folder_override(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('uploads/manual.pdf', 'conteudo pdf');

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'drive-access-token',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
            ]),
            'https://www.googleapis.com/upload/drive/v3/files*' => Http::response([
                'id' => 'drive-file-id',
                'webViewLink' => 'https://drive.google.com/file/d/drive-file-id/view',
            ]),
            'https://www.googleapis.com/drive/v3/files/drive-file-id*' => Http::response([
                'webViewLink' => 'https://drive.google.com/file/d/drive-file-id/view',
            ]),
        ]);

        $driveAccount = DriveAccount::query()->create([
            'nome' => 'Conta Conselho',
            'provider' => 'google_drive',
            'folder_id' => 'folder-padrao',
            'client_id' => 'client-id-123',
            'client_secret' => 'client-secret-123',
            'refresh_token' => 'refresh-token-123',
        ]);

        $documento = Documento::query()->create([
            'igreja_id' => Igreja::factory()->create()->id,
            'drive_account_id' => $driveAccount->id,
            'drive_folder_id' => 'folder-especifica',
            'titulo' => 'Manual de reuniao',
            'path' => 'uploads/manual.pdf',
            'disk' => 'local',
            'tipo' => 'pdf',
            'mime_type' => 'application/pdf',
            'tamanho' => 128,
            'publico' => false,
            'sync_status' => 'pending',
        ]);

        $job = new \App\Jobs\SyncFileToDriveJob($documento->id);
        $job->handle(app(\App\Services\GoogleDriveService::class), app(\App\Services\AuditLogService::class));

        $documento->refresh();

        $this->assertSame('synced', $documento->sync_status);
        $this->assertSame('drive-file-id', $documento->drive_file_id);
        $this->assertSame('https://drive.google.com/file/d/drive-file-id/view', $documento->drive_link);
        $this->assertDatabaseHas('file_sync_logs', [
            'documento_id' => $documento->id,
            'drive_account_id' => $driveAccount->id,
            'status' => 'synced',
        ]);

        Http::assertSent(static function (Request $request): bool {
            if (! str_starts_with($request->url(), 'https://www.googleapis.com/upload/drive/v3/files')) {
                return false;
            }

            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return ($query['supportsAllDrives'] ?? null) === 'true'
                && str_contains((string) $request->body(), 'folder-especifica');
        });
    }

    public function test_sync_job_recreates_default_folder_when_account_folder_returns_not_found(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('uploads/manual.pdf', 'conteudo pdf');

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'drive-access-token',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
            ]),
            'https://www.googleapis.com/upload/drive/v3/files*' => Http::response([
                'id' => 'drive-file-id',
                'webViewLink' => 'https://drive.google.com/file/d/drive-file-id/view',
            ], 200),
            'https://www.googleapis.com/drive/v3/files?fields=id&supportsAllDrives=true' => Http::sequence()
                ->push(['id' => 'nova-pasta-acervo'], 200)
                ->push(['id' => 'pasta-igreja'], 200)
                ->push(['id' => 'pasta-documentos'], 200)
                ->push(['id' => 'pasta-imagens'], 200),
            'https://www.googleapis.com/drive/v3/files/drive-file-id*' => Http::response([
                'webViewLink' => 'https://drive.google.com/file/d/drive-file-id/view',
            ]),
        ]);

        $driveAccount = DriveAccount::query()->create([
            'nome' => 'Conta Conselho',
            'provider' => 'google_drive',
            'folder_id' => '0ACFCBLYL72ZGUk9PVA',
            'client_id' => 'client-id-123',
            'client_secret' => 'client-secret-123',
            'refresh_token' => 'refresh-token-123',
        ]);

        $documento = Documento::query()->create([
            'igreja_id' => Igreja::factory()->create()->id,
            'drive_account_id' => $driveAccount->id,
            'drive_folder_id' => null,
            'titulo' => 'Manual de reuniao',
            'path' => 'uploads/manual.pdf',
            'disk' => 'local',
            'tipo' => 'pdf',
            'mime_type' => 'application/pdf',
            'tamanho' => 128,
            'publico' => false,
            'sync_status' => 'pending',
        ]);

        $job = new \App\Jobs\SyncFileToDriveJob($documento->id);
        $job->handle(app(\App\Services\GoogleDriveService::class), app(\App\Services\AuditLogService::class));

        $documento->refresh();
        $driveAccount->refresh();

        $this->assertSame('synced', $documento->sync_status);
        $this->assertSame('drive-file-id', $documento->drive_file_id);
        $this->assertSame('nova-pasta-acervo', $driveAccount->folder_id);
        $this->assertSame('0ACFCBLYL72ZGUk9PVA', data_get($driveAccount->metadata, 'replaced_folder_id'));
        $this->assertNotNull(data_get($driveAccount->metadata, 'folder_replaced_at'));
        $this->assertSame('pasta-documentos', data_get($driveAccount->metadata, 'church_drive_folders.'.$documento->igreja_id.'.documentos_folder_id'));
        $this->assertSame('pasta-imagens', data_get($driveAccount->metadata, 'church_drive_folders.'.$documento->igreja_id.'.imagens_folder_id'));

        Http::assertSent(static function (Request $request): bool {
            if (! str_starts_with($request->url(), 'https://www.googleapis.com/upload/drive/v3/files')) {
                return false;
            }

            return str_contains((string) $request->body(), 'pasta-documentos')
                && ! str_contains((string) $request->body(), '0ACFCBLYL72ZGUk9PVA');
        });

        Http::assertSent(static function (Request $request): bool {
            if ($request->url() !== 'https://www.googleapis.com/drive/v3/files?fields=id&supportsAllDrives=true') {
                return false;
            }

            return str_contains((string) $request->body(), 'Acervo da Igreja');
        });

        Http::assertSent(static function (Request $request): bool {
            if ($request->url() !== 'https://www.googleapis.com/drive/v3/files?fields=id&supportsAllDrives=true') {
                return false;
            }

            return str_contains((string) $request->body(), 'Documentos')
                && str_contains((string) $request->body(), 'pasta-igreja');
        });

        Http::assertSent(static function (Request $request): bool {
            if ($request->url() !== 'https://www.googleapis.com/drive/v3/files?fields=id&supportsAllDrives=true') {
                return false;
            }

            return str_contains((string) $request->body(), 'Imagens')
                && str_contains((string) $request->body(), 'pasta-igreja');
        });
    }

    public function test_sync_job_backfills_missing_images_folder_when_documents_folder_already_exists(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('uploads/manual.pdf', 'conteudo pdf');

        $igreja = Igreja::factory()->create();

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'drive-access-token',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
            ]),
            'https://www.googleapis.com/upload/drive/v3/files*' => Http::response([
                'id' => 'drive-file-id',
                'webViewLink' => 'https://drive.google.com/file/d/drive-file-id/view',
            ], 200),
            'https://www.googleapis.com/drive/v3/files?fields=id&supportsAllDrives=true' => Http::response([
                'id' => 'pasta-imagens',
            ], 200),
        ]);

        $driveAccount = DriveAccount::query()->create([
            'nome' => 'Conta Conselho',
            'provider' => 'google_drive',
            'folder_id' => 'acervo-root-id',
            'client_id' => 'client-id-123',
            'client_secret' => 'client-secret-123',
            'refresh_token' => 'refresh-token-123',
            'metadata' => [
                'church_drive_folders' => [
                    (string) $igreja->id => [
                        'igreja_id' => $igreja->id,
                        'igreja_nome' => $igreja->nome_fantasia,
                        'folder_id' => 'pasta-igreja',
                        'documentos_folder_id' => 'pasta-documentos',
                    ],
                ],
            ],
        ]);

        $documento = Documento::query()->create([
            'igreja_id' => $igreja->id,
            'drive_account_id' => $driveAccount->id,
            'drive_folder_id' => null,
            'titulo' => 'Manual de reuniao',
            'path' => 'uploads/manual.pdf',
            'disk' => 'local',
            'tipo' => 'pdf',
            'mime_type' => 'application/pdf',
            'tamanho' => 128,
            'publico' => false,
            'sync_status' => 'pending',
        ]);

        $job = new \App\Jobs\SyncFileToDriveJob($documento->id);
        $job->handle(app(\App\Services\GoogleDriveService::class), app(\App\Services\AuditLogService::class));

        $documento->refresh();
        $driveAccount->refresh();

        $this->assertSame('synced', $documento->sync_status);
        $this->assertSame('pasta-documentos', data_get($driveAccount->metadata, 'church_drive_folders.'.$igreja->id.'.documentos_folder_id'));
        $this->assertSame('pasta-imagens', data_get($driveAccount->metadata, 'church_drive_folders.'.$igreja->id.'.imagens_folder_id'));

        Http::assertSent(static function (Request $request): bool {
            if (! str_starts_with($request->url(), 'https://www.googleapis.com/upload/drive/v3/files')) {
                return false;
            }

            return str_contains((string) $request->body(), 'pasta-documentos');
        });

        Http::assertSent(static function (Request $request): bool {
            if ($request->url() !== 'https://www.googleapis.com/drive/v3/files?fields=id&supportsAllDrives=true') {
                return false;
            }

            return str_contains((string) $request->body(), 'Imagens')
                && str_contains((string) $request->body(), 'pasta-igreja');
        });
    }
}
