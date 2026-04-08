<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Foto;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class FileAccessController extends Controller
{
    use AuthorizesRequests;

    public function foto(Foto $foto): StreamedResponse
    {
        $this->authorize('view', $foto);

        if (!Storage::disk($foto->disk)->exists($foto->caminho)) {
            Log::warning('Foto file not found', [
                'foto_id' => $foto->id,
                'path' => $foto->caminho,
                'disk' => $foto->disk,
            ]);
            abort(404, 'Arquivo de foto não encontrado.');
        }

        try {
            return Storage::disk($foto->disk)->response(
                path: $foto->caminho,
                name: $foto->nome_original,
                headers: [
                    'Content-Type' => $foto->mime_type,
                    'Cache-Control' => 'private, max-age=3600',
                ],
            );
        } catch (\Exception $e) {
            Log::error('Erro ao servir foto', [
                'foto_id' => $foto->id,
                'error' => $e->getMessage(),
            ]);
            abort(500, 'Erro ao servir arquivo de foto.');
        }
    }

    public function documento(Documento $documento): StreamedResponse
    {
        $this->authorize('view', $documento);

        if (!Storage::disk($documento->disk)->exists($documento->path)) {
            Log::warning('Documento file not found', [
                'documento_id' => $documento->id,
                'path' => $documento->path,
                'disk' => $documento->disk,
            ]);
            abort(404, 'Arquivo de documento não encontrado.');
        }

        try {
            return Storage::disk($documento->disk)->download(
                path: $documento->path,
                name: $documento->fileName(),
                headers: [
                    'Content-Type' => $documento->mime_type,
                    'X-Content-Type-Options' => 'nosniff',
                ],
            );
        } catch (\Exception $e) {
            Log::error('Erro ao servir documento', [
                'documento_id' => $documento->id,
                'error' => $e->getMessage(),
            ]);
            abort(500, 'Erro ao servir arquivo de documento.');
        }
    }

    public function documentoVisualizar(Documento $documento): StreamedResponse
    {
        $this->authorize('view', $documento);

        if (!Storage::disk($documento->disk)->exists($documento->path)) {
            Log::warning('Documento file not found for visualization', [
                'documento_id' => $documento->id,
                'path' => $documento->path,
                'disk' => $documento->disk,
            ]);
            abort(404, 'Arquivo de documento não encontrado.');
        }

        try {
            return Storage::disk($documento->disk)->response(
                path: $documento->path,
                name: $documento->fileName(),
                headers: [
                    'Content-Type' => $documento->mime_type,
                    'X-Content-Type-Options' => 'nosniff',
                ],
                disposition: 'inline',
            );
        } catch (\Exception $e) {
            Log::error('Erro ao visualizar documento', [
                'documento_id' => $documento->id,
                'error' => $e->getMessage(),
            ]);
            abort(500, 'Erro ao visualizar arquivo de documento.');
        }
    }
}
