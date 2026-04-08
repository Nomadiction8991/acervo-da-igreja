<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GrupoDocumento\StoreGrupoDocumentoRequest;
use App\Http\Requests\GrupoDocumento\UpdateGrupoDocumentoRequest;
use App\Models\GrupoDocumento;
use App\Services\GrupoDocumentoService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class GrupoDocumentoController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', GrupoDocumento::class);

        $grupos = GrupoDocumento::query()
            ->withCount('documentos')
            ->orderBy('nome')
            ->paginate(20);

        return view('grupo-documentos.index', compact('grupos'));
    }

    public function create(): View
    {
        $this->authorize('create', GrupoDocumento::class);

        return view('grupo-documentos.create');
    }

    public function store(
        StoreGrupoDocumentoRequest $request,
        GrupoDocumentoService $service,
    ): RedirectResponse {
        $this->authorize('create', GrupoDocumento::class);

        $grupoDocumento = $service->store([
            ...$request->validated(),
            'publico_padrao' => $request->boolean('publico_padrao'),
        ]);

        return redirect()
            ->route('grupo-documentos.show', $grupoDocumento)
            ->with('success', 'Grupo de documentos criado com sucesso.');
    }

    public function show(GrupoDocumento $grupoDocumento): View
    {
        $this->authorize('view', $grupoDocumento);

        $grupoDocumento->load(['documentos.igreja']);

        return view('grupo-documentos.show', compact('grupoDocumento'));
    }

    public function edit(GrupoDocumento $grupoDocumento): View
    {
        $this->authorize('update', $grupoDocumento);

        return view('grupo-documentos.edit', compact('grupoDocumento'));
    }

    public function update(
        UpdateGrupoDocumentoRequest $request,
        GrupoDocumento $grupoDocumento,
        GrupoDocumentoService $service,
    ): RedirectResponse {
        $this->authorize('update', $grupoDocumento);

        $service->update($grupoDocumento, [
            ...$request->validated(),
            'publico_padrao' => $request->boolean('publico_padrao'),
        ]);

        return redirect()
            ->route('grupo-documentos.show', $grupoDocumento)
            ->with('success', 'Grupo de documentos atualizado com sucesso.');
    }

    public function destroy(
        GrupoDocumento $grupoDocumento,
        GrupoDocumentoService $service,
    ): RedirectResponse {
        $this->authorize('delete', $grupoDocumento);

        if ($grupoDocumento->documentos()->exists()) {
            return back()->withErrors([
                'grupo_documento' => 'Nao e possivel excluir um grupo com documentos vinculados.',
            ]);
        }

        $service->delete($grupoDocumento);

        return redirect()
            ->route('grupo-documentos.index')
            ->with('success', 'Grupo de documentos removido com sucesso.');
    }
}
