<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Igreja\StoreIgrejaRequest;
use App\Http\Requests\Igreja\UpdateIgrejaRequest;
use App\Http\Requests\Igreja\UpdateIgrejaVisibilityRequest;
use App\Models\Igreja;
use App\Services\IgrejaService;
use App\Traits\Sortable;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class IgrejaController extends Controller
{
    use AuthorizesRequests;
    use Sortable;

    public function index(): View
    {
        $this->authorize('viewAny', Igreja::class);

        $search = request()->input('q');
        $sortBy = request()->input('sort_by', 'nome_fantasia');
        $sortDir = request()->input('sort_dir', 'asc');
        $status = request()->input('status', 'ativas');

        $igrejas = Igreja::query()
            ->search($search)
            ->withCount(['fotos', 'documentos', 'tarefas']);

        if ($status === 'inativas') {
            $igrejas->onlyTrashed();
        } elseif ($status === 'todas') {
            $igrejas->withTrashed();
        }

        // Validar colunas para ordenação
        $allowedColumns = ['codigo_controle', 'nome_fantasia', 'cidade', 'created_at'];
        $igrejas = $this->applySorting($igrejas, request(), $allowedColumns, 'nome_fantasia');

        $igrejas = $igrejas->paginate(20)->withQueryString();

        return view('igrejas.index', compact('igrejas', 'search', 'sortBy', 'sortDir', 'status'));
    }

    public function create(): View
    {
        $this->authorize('create', Igreja::class);

        return view('igrejas.create');
    }

    public function store(StoreIgrejaRequest $request, IgrejaService $service): RedirectResponse
    {
        $this->authorize('create', Igreja::class);

        $igreja = $service->store($this->churchPayload($request));

        return redirect()
            ->route('igrejas.show', $igreja)
            ->with('success', 'Igreja criada com sucesso.');
    }

    public function show(Igreja $igreja): View
    {
        $this->authorize('view', $igreja);

        $igreja->load(['fotos', 'documentos.grupoDocumento', 'tarefas.user']);

        return view('igrejas.show', compact('igreja'));
    }

    public function edit(Igreja $igreja): View
    {
        $this->authorize('update', $igreja);

        return view('igrejas.edit', compact('igreja'));
    }

    public function update(
        UpdateIgrejaRequest $request,
        Igreja $igreja,
        IgrejaService $service,
    ): RedirectResponse
    {
        $this->authorize('update', $igreja);

        $service->update($igreja, $this->churchPayload($request));

        return redirect()
            ->route('igrejas.show', $igreja)
            ->with('success', 'Igreja atualizada com sucesso.');
    }

    public function destroy(Igreja $igreja, IgrejaService $service): RedirectResponse
    {
        $this->authorize('delete', $igreja);

        try {
            $service->delete($igreja);
        } catch (DomainException $exception) {
            return back()->withErrors(['igreja' => $exception->getMessage()]);
        }

        return redirect()
            ->route('igrejas.index')
            ->with('success', 'Igreja inativada com sucesso.');
    }

    public function atualizarVisibilidade(
        UpdateIgrejaVisibilityRequest $request,
        Igreja $igreja,
        IgrejaService $service,
    ): RedirectResponse
    {
        $this->authorize('alterarVisibilidade', $igreja);

        /** @var array<string, mixed> $raw */
        $raw = $request->validated('visibilidade');
        $visibilidade = [];

        foreach (Igreja::FIELD_VISIBILITY_MAP as $field => $column) {
            $visibilidade[$field] = (bool) ($raw[$field] ?? false);
        }

        $service->updateVisibility($igreja, $visibilidade);

        return redirect()
            ->route('igrejas.edit', $igreja)
            ->with('success', 'Visibilidade atualizada com sucesso.');
    }

    /**
     * @param StoreIgrejaRequest|UpdateIgrejaRequest $request
     * @return array<string, mixed>
     */
    private function churchPayload($request): array
    {
        $data = $request->validated();

        foreach (Igreja::FIELD_VISIBILITY_MAP as $field => $column) {
            if ($request->has($column)) {
                $data[$column] = $request->boolean($column);
            }
        }

        return $data;
    }
}
