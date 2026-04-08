<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Documento;
use App\Models\Foto;
use App\Models\Igreja;
use App\Models\Permission;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class PermissionsAndVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_church_index_requires_visualization_permission(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('igrejas.index'))
            ->assertForbidden();

        $user->permissions()->attach(
            Permission::query()->where('slug', 'igrejas.visualizar')->firstOrFail(),
        );

        $this->actingAs($user)
            ->get(route('igrejas.index'))
            ->assertOk();
    }

    public function test_public_api_returns_only_public_fields_and_public_assets(): void
    {
        $igreja = Igreja::factory()->create([
            'nome_fantasia' => 'Igreja do Centro',
            'razao_social' => 'Razao Privada Ltda',
            'publico_nome_fantasia' => true,
            'publico_razao_social' => false,
            'publico_cidade' => true,
            'cidade' => 'Cuiaba',
            'estado' => 'MT',
        ]);

        Foto::factory()->create([
            'igreja_id' => $igreja->id,
            'disk' => 'local',
            'is_public' => true,
        ]);

        Foto::factory()->create([
            'igreja_id' => $igreja->id,
            'disk' => 'local',
            'is_public' => false,
        ]);

        Documento::query()->create([
            'igreja_id' => $igreja->id,
            'titulo' => 'Guia publico',
            'path' => 'uploads/guia-publico.pdf',
            'disk' => 'local',
            'tipo' => 'pdf',
            'mime_type' => 'application/pdf',
            'tamanho' => 1024,
            'publico' => true,
        ]);

        Documento::query()->create([
            'igreja_id' => $igreja->id,
            'titulo' => 'Guia privado',
            'path' => 'uploads/guia-privado.pdf',
            'disk' => 'local',
            'tipo' => 'pdf',
            'mime_type' => 'application/pdf',
            'tamanho' => 1024,
            'publico' => false,
        ]);

        $this->getJson(route('api.igrejas.show', $igreja))
            ->assertOk()
            ->assertJsonPath('data.nome_fantasia', 'Igreja do Centro')
            ->assertJsonPath('data.cidade', 'Cuiaba')
            ->assertJsonMissingPath('data.razao_social')
            ->assertJsonCount(1, 'data.fotos')
            ->assertJsonCount(1, 'data.documentos');
    }

    public function test_private_document_download_is_blocked_for_guests(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('uploads/manual-interno.pdf', 'conteudo');

        $igreja = Igreja::factory()->create();
        $documento = Documento::query()->create([
            'igreja_id' => $igreja->id,
            'titulo' => 'manual-interno.pdf',
            'path' => 'uploads/manual-interno.pdf',
            'disk' => 'local',
            'tipo' => 'pdf',
            'mime_type' => 'application/pdf',
            'tamanho' => 10,
            'publico' => false,
        ]);

        $this->get(route('files.documentos.show', $documento))
            ->assertForbidden();
    }

    public function test_public_document_preview_is_available_for_guests_inline(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('uploads/manual-publico.pdf', 'conteudo');

        $igreja = Igreja::factory()->create();
        $documento = Documento::query()->create([
            'igreja_id' => $igreja->id,
            'titulo' => 'manual-publico',
            'path' => 'uploads/manual-publico.pdf',
            'disk' => 'local',
            'tipo' => 'pdf',
            'mime_type' => 'application/pdf',
            'tamanho' => 10,
            'publico' => true,
        ]);

        $response = $this->get(route('files.documentos.preview', $documento));

        $response->assertOk();
        self::assertStringStartsWith(
            'inline;',
            (string) $response->headers->get('content-disposition'),
        );
    }

    public function test_private_document_viewer_page_is_blocked_for_guests(): void
    {
        $igreja = Igreja::factory()->create();
        $documento = Documento::query()->create([
            'igreja_id' => $igreja->id,
            'titulo' => 'Manual interno',
            'path' => 'uploads/manual-interno.pdf',
            'disk' => 'local',
            'tipo' => 'pdf',
            'mime_type' => 'application/pdf',
            'tamanho' => 10,
            'publico' => false,
        ]);

        $this->get(route('portal.documentos.show', $documento))
            ->assertForbidden();
    }
}
