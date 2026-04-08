<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Documento;
use App\Models\Igreja;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class InterfacePagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_portal_page_loads(): void
    {
        $igreja = Igreja::factory()->create([
            'nome_fantasia' => 'Igreja Renovo Centro',
            'cidade' => 'Cuiaba',
            'estado' => 'MT',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee(config('app.name'))
            ->assertSee($igreja->nome_fantasia)
            ->assertSee('Cuiaba');
    }

    public function test_church_detail_page_loads(): void
    {
        $igreja = Igreja::factory()->create([
            'nome_fantasia' => 'Igreja Renovo Centro',
        ]);

        $documento = Documento::query()->create([
            'igreja_id' => $igreja->id,
            'titulo' => 'Regimento publico',
            'descricao' => 'Documento exposto ao portal',
            'path' => 'uploads/regimento.pdf',
            'disk' => 'local',
            'tipo' => 'pdf',
            'mime_type' => 'application/pdf',
            'tamanho' => 1024,
            'publico' => true,
        ]);

        $this->get(route('portal.show', $igreja))
            ->assertOk()
            ->assertSee('<meta name="theme-color" content="#f5efe6">', false)
            ->assertSee('Igreja Renovo Centro')
            ->assertSee('Documentos publicos')
            ->assertSee(route('portal.documentos.show', $documento), false);
    }

    public function test_public_document_viewer_page_loads(): void
    {
        $igreja = Igreja::factory()->create([
            'nome_fantasia' => 'Igreja Renovo Centro',
        ]);

        $documento = Documento::query()->create([
            'igreja_id' => $igreja->id,
            'titulo' => 'Estatuto publico',
            'descricao' => 'Documento exibido em pagina publica.',
            'path' => 'uploads/estatuto-publico.pdf',
            'disk' => 'local',
            'tipo' => 'pdf',
            'mime_type' => 'application/pdf',
            'tamanho' => 1024,
            'publico' => true,
        ]);

        $this->get(route('portal.documentos.show', $documento))
            ->assertOk()
            ->assertSee('Estatuto publico')
            ->assertSee('Abrir em nova aba');
    }

    public function test_access_preview_page_loads(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Acesso seguro')
            ->assertSee('Identificacao da equipe');
    }

    public function test_admin_dashboard_page_requires_authentication(): void
    {
        $this->get('/painel')
            ->assertRedirect(route('login'));
    }
}
