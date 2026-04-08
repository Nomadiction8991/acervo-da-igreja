<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicIgrejaResource;
use App\Models\Igreja;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class PublicIgrejaController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $igrejas = Igreja::query()
            ->with([
                'fotos' => static fn ($query) => $query
                    ->where('is_public', true)
                    ->orderByDesc('is_principal')
                    ->orderBy('ordem'),
                'documentos' => static fn ($query) => $query
                    ->where('publico', true)
                    ->orderByDesc('created_at'),
            ])
            ->orderBy('nome_fantasia')
            ->paginate(20);

        return PublicIgrejaResource::collection($igrejas);
    }

    public function show(Igreja $igreja): PublicIgrejaResource
    {
        $igreja->load([
            'fotos' => static fn ($query) => $query
                ->where('is_public', true)
                ->orderByDesc('is_principal')
                ->orderBy('ordem'),
            'documentos' => static fn ($query) => $query
                ->where('publico', true)
                ->orderByDesc('created_at'),
        ]);

        return new PublicIgrejaResource($igreja);
    }
}
