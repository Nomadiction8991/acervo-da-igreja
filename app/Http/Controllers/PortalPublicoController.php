<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Igreja;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

final class PortalPublicoController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
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

        return view('portal.index', compact('igrejas'));
    }

    public function show(Igreja $igreja): View
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

        return view('portal.show', compact('igreja'));
    }

    public function documento(Documento $documento): View
    {
        $this->authorize('view', $documento);

        $documento->load(['igreja', 'grupoDocumento']);

        return view('portal.documento', compact('documento'));
    }
}
