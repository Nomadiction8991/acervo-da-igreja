<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\Tarefa\StoreTarefaRequest;
use App\Http\Requests\Tarefa\UpdateTarefaRequest;
use App\Models\Igreja;
use App\Models\Tarefa;
use App\Models\User;
use App\Services\TarefaService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class TarefaController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Tarefa::class);

        $search = $request->input('q');

        $tarefas = Tarefa::query()
            ->with(['igreja', 'user'])
            ->search($search)
            ->when($request->filled('status'), static function ($query) use ($request): void {
                $query->where('status', $request->string('status')->toString());
            })
            ->when($request->filled('igreja_id'), static function ($query) use ($request): void {
                $query->where('igreja_id', $request->integer('igreja_id'));
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('tarefas.index', [
            'tarefas' => $tarefas,
            'igrejas' => Igreja::query()->orderBy('nome_fantasia')->get(),
            'statuses' => TaskStatus::cases(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Tarefa::class);

        return view('tarefas.create', $this->formData());
    }

    public function store(StoreTarefaRequest $request, TarefaService $service): RedirectResponse
    {
        $this->authorize('create', Tarefa::class);

        $tarefa = $service->store($request->validated());

        return redirect()
            ->route('tarefas.show', $tarefa)
            ->with('success', 'Tarefa criada com sucesso.');
    }

    public function show(Tarefa $tarefa): View
    {
        $this->authorize('view', $tarefa);

        $tarefa->load(['igreja', 'user']);

        return view('tarefas.show', compact('tarefa'));
    }

    public function edit(Tarefa $tarefa): View
    {
        $this->authorize('update', $tarefa);

        return view('tarefas.edit', [
            ...$this->formData(),
            'tarefa' => $tarefa,
        ]);
    }

    public function update(
        UpdateTarefaRequest $request,
        Tarefa $tarefa,
        TarefaService $service,
    ): RedirectResponse {
        $this->authorize('update', $tarefa);

        $service->update($tarefa, $request->validated());

        return redirect()
            ->route('tarefas.show', $tarefa)
            ->with('success', 'Tarefa atualizada com sucesso.');
    }

    public function destroy(Tarefa $tarefa, TarefaService $service): RedirectResponse
    {
        $this->authorize('delete', $tarefa);

        $service->delete($tarefa);

        return redirect()
            ->route('tarefas.index')
            ->with('success', 'Tarefa removida com sucesso.');
    }

    /**
     * @return array{
     *     igrejas: \Illuminate\Database\Eloquent\Collection<int, Igreja>,
     *     users: \Illuminate\Database\Eloquent\Collection<int, User>,
     *     statuses: array<int, TaskStatus>,
     *     priorities: array<int, TaskPriority>
     * }
     */
    private function formData(): array
    {
        return [
            'igrejas' => Igreja::query()->orderBy('nome_fantasia')->get(),
            'users' => User::query()->where('is_active', true)->orderBy('name')->get(),
            'statuses' => TaskStatus::cases(),
            'priorities' => TaskPriority::cases(),
        ];
    }
}
