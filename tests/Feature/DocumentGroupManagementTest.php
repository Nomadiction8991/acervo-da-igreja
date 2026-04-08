<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Documento;
use App\Models\GrupoDocumento;
use App\Models\Igreja;
use App\Models\Permission;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DocumentGroupManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_index_requires_permission(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $grupoDocumento = GrupoDocumento::query()->create([
            'nome' => 'Atas',
            'descricao' => 'Documentos de reunioes internas.',
            'publico_padrao' => false,
        ]);

        $this->actingAs($user)
            ->get(route('grupo-documentos.index'))
            ->assertForbidden();

        $this->grantPermissions($user, ['grupos_documentos.visualizar']);

        $this->actingAs($user)
            ->get(route('grupo-documentos.index'))
            ->assertOk()
            ->assertSee('Grupos de documentos')
            ->assertSee($grupoDocumento->nome);
    }

    public function test_authorized_user_can_create_group_and_see_it_in_document_form(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $igreja = Igreja::factory()->create();

        $this->grantPermissions($user, [
            'grupos_documentos.visualizar',
            'grupos_documentos.criar',
            'documentos.criar',
        ]);

        $this->actingAs($user)
            ->post(route('grupo-documentos.store'), [
                'nome' => 'Contratos',
                'descricao' => 'Contratos e termos assinados.',
                'publico_padrao' => '1',
            ])
            ->assertRedirect();

        $grupoDocumento = GrupoDocumento::query()->where('nome', 'Contratos')->firstOrFail();

        $this->actingAs($user)
            ->get(route('documentos.create', ['igreja_id' => $igreja->id]))
            ->assertOk()
            ->assertSee('Gerenciar grupos')
            ->assertSee($grupoDocumento->nome);
    }

    public function test_group_with_linked_documents_cannot_be_deleted(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $igreja = Igreja::factory()->create();
        $grupoDocumento = GrupoDocumento::query()->create([
            'nome' => 'Juridico',
            'descricao' => 'Documentos juridicos.',
            'publico_padrao' => false,
        ]);

        Documento::query()->create([
            'igreja_id' => $igreja->id,
            'grupo_documento_id' => $grupoDocumento->id,
            'titulo' => 'Estatuto interno',
            'path' => 'uploads/estatuto.pdf',
            'disk' => 'local',
            'tipo' => 'pdf',
            'mime_type' => 'application/pdf',
            'tamanho' => 256,
            'publico' => false,
        ]);

        $this->grantPermissions($user, [
            'grupos_documentos.visualizar',
            'grupos_documentos.deletar',
        ]);

        $this->actingAs($user)
            ->from(route('grupo-documentos.show', $grupoDocumento))
            ->delete(route('grupo-documentos.destroy', $grupoDocumento))
            ->assertRedirect(route('grupo-documentos.show', $grupoDocumento))
            ->assertSessionHasErrors('grupo_documento');

        $this->assertDatabaseHas('grupo_documentos', [
            'id' => $grupoDocumento->id,
            'nome' => 'Juridico',
        ]);
    }

    /**
     * @param list<string> $slugs
     */
    private function grantPermissions(User $user, array $slugs): void
    {
        $permissionIds = Permission::query()
            ->whereIn('slug', $slugs)
            ->pluck('id')
            ->all();

        $user->permissions()->syncWithoutDetaching($permissionIds);
    }
}
