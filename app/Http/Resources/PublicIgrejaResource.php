<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Documento;
use App\Models\Foto;
use App\Models\Igreja;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Igreja
 */
final class PublicIgrejaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Igreja $igreja */
        $igreja = $this->resource;

        return [
            ...$igreja->getPublicData(),
            'fotos' => $this->whenLoaded('fotos', function () use ($igreja): array {
                /** @var \Illuminate\Database\Eloquent\Collection<int, Foto> $fotos */
                $fotos = $igreja->fotos;

                return $fotos
                    ->filter(static fn (Foto $foto): bool => $foto->isPublic())
                    ->values()
                    ->map(static fn (Foto $foto): array => [
                        'id' => $foto->id,
                        'principal' => $foto->is_principal,
                        'url' => route('files.fotos.show', $foto),
                    ])
                    ->all();
            }, []),
            'documentos' => $this->whenLoaded('documentos', function () use ($igreja): array {
                /** @var \Illuminate\Database\Eloquent\Collection<int, Documento> $documentos */
                $documentos = $igreja->documentos;

                return $documentos
                    ->filter(static fn (Documento $documento): bool => $documento->publico)
                    ->values()
                    ->map(static fn (Documento $documento): array => [
                        'id' => $documento->id,
                        'titulo' => $documento->titulo,
                        'tipo' => $documento->tipo,
                        'viewer_url' => route('portal.documentos.show', $documento),
                        'url' => route('files.documentos.show', $documento),
                    ])
                    ->all();
            }, []),
        ];
    }
}
