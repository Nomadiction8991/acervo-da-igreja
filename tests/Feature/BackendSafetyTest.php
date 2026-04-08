<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Documento;
use App\Models\Foto;
use App\Models\Igreja;
use App\Models\User;
use App\Services\FotoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class BackendSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_photo_update_accepts_unchecked_checkbox_booleans(): void
    {
        $user = User::factory()->admin()->create();
        $igreja = Igreja::factory()->create();
        $foto = Foto::factory()->create([
            'igreja_id' => $igreja->id,
            'is_public' => true,
            'is_principal' => true,
            'disk' => 'local',
        ]);

        $this->actingAs($user)
            ->patch(route('fotos.update', [$igreja, $foto]), [])
            ->assertRedirect(route('fotos.show', [$igreja, $foto]));

        $foto->refresh();

        $this->assertFalse($foto->is_public);
        $this->assertFalse($foto->is_principal);
    }

    public function test_missing_private_document_returns_not_found(): void
    {
        Storage::fake('local');

        $user = User::factory()->admin()->create();
        $igreja = Igreja::factory()->create();
        $documento = Documento::query()->create([
            'igreja_id' => $igreja->id,
            'titulo' => 'Arquivo ausente',
            'path' => 'uploads/arquivo-ausente.pdf',
            'disk' => 'local',
            'tipo' => 'pdf',
            'mime_type' => 'application/pdf',
            'tamanho' => 512,
            'publico' => false,
        ]);

        $this->actingAs($user)
            ->get(route('files.documentos.show', $documento))
            ->assertNotFound();
    }

    public function test_document_cannot_be_created_for_soft_deleted_church(): void
    {
        Storage::fake('local');

        $user = User::factory()->admin()->create();
        $igreja = Igreja::factory()->create();
        $igreja->delete();

        $this->actingAs($user)
            ->from(route('documentos.create'))
            ->post(route('documentos.store'), [
                'igreja_id' => $igreja->id,
                'titulo' => 'Documento invalido',
                'tipo' => 'pdf',
                'arquivo' => UploadedFile::fake()->create('invalido.pdf', 64, 'application/pdf'),
            ])
            ->assertRedirect(route('documentos.create'))
            ->assertSessionHasErrors('igreja_id');

        $this->assertSame(0, Documento::query()->count());
    }

    public function test_church_with_related_records_cannot_be_deleted(): void
    {
        $user = User::factory()->admin()->create();
        $igreja = Igreja::factory()->create();

        Documento::query()->create([
            'igreja_id' => $igreja->id,
            'titulo' => 'Documento vinculado',
            'path' => 'uploads/vinculado.pdf',
            'disk' => 'local',
            'tipo' => 'pdf',
            'mime_type' => 'application/pdf',
            'tamanho' => 1024,
            'publico' => false,
        ]);

        $this->actingAs($user)
            ->from(route('igrejas.edit', $igreja))
            ->delete(route('igrejas.destroy', $igreja))
            ->assertRedirect(route('igrejas.edit', $igreja))
            ->assertSessionHasErrors('igreja');

        $igreja->refresh();

        $this->assertFalse($igreja->trashed());
    }

    public function test_deleting_principal_photo_promotes_the_next_photo(): void
    {
        Storage::fake('local');

        $igreja = Igreja::factory()->create();
        $principal = Foto::factory()->create([
            'igreja_id' => $igreja->id,
            'disk' => 'local',
            'caminho' => 'uploads/fotos/principal.jpg',
            'is_public' => true,
            'is_principal' => true,
            'ordem' => 1,
        ]);
        $nextPhoto = Foto::factory()->create([
            'igreja_id' => $igreja->id,
            'disk' => 'local',
            'caminho' => 'uploads/fotos/secundaria.jpg',
            'is_public' => true,
            'is_principal' => false,
            'ordem' => 2,
        ]);

        Storage::disk('local')->put($principal->caminho, 'principal');
        Storage::disk('local')->put($nextPhoto->caminho, 'secundaria');

        app(FotoService::class)->delete($principal);

        $this->assertTrue($nextPhoto->fresh()->is_principal);
    }
}
