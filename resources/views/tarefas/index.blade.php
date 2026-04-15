<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Operacao</p>
                <h1 class="section-title mt-1">Tarefas</h1>
            </div>
            <div class="flex gap-2">
                @can('create', App\Models\Tarefa::class)
                    <a href="{{ route('tarefas.create') }}" class="button button-primary">+ Nova tarefa</a>
                @endcan
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="status-banner status-banner--success mb-4">{{ session('success') }}</div>
    @endif

    <div class="surface panel-padding mb-6 space-y-4">
        <div>
            <label class="field-block__label" for="q">Buscar</label>
            <form method="GET" class="flex gap-3">
                <input type="text" id="q" name="q" placeholder="Procure por titulo ou descrição..." value="{{ $search ?? '' }}" class="field-control flex-1">
                <button type="submit" class="button button-primary">Procurar</button>
                @if ($search)
                    <a href="{{ route('tarefas.index') }}" class="button button-muted">Limpar</a>
                @endif
            </form>
        </div>

        <form method="GET" class="grid gap-4 md:grid-cols-[minmax(0,1fr)_220px_auto]">
            @if ($search)
                <input type="hidden" name="q" value="{{ $search }}">
            @endif
            <div>
                <label class="field-block__label" for="igreja_id">Igreja</label>
                <select id="igreja_id" name="igreja_id" class="field-control">
                    <option value="">Todas</option>
                    @foreach ($igrejas as $igreja)
                        <option value="{{ $igreja->id }}" @selected(request('igreja_id') == $igreja->id)>{{ $igreja->nome_fantasia }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="field-block__label" for="status">Status</label>
                <select id="status" name="status" class="field-control">
                    <option value="">Todos</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-3">
                <button type="submit" class="button button-primary">Filtrar</button>
                <a href="{{ route('tarefas.index') }}" class="button button-muted">Limpar</a>
            </div>
        </form>
    </div>

    <div class="resource-table-shell">
        <div class="resource-table-scroll">
        <table class="resource-table">
            <thead>
                <tr>
                    <th>Titulo</th>
                    <th class="hidden md:table-cell">Igreja</th>
                    <th>Status</th>
                    <th class="hidden lg:table-cell">Prioridade</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tarefas as $tarefa)
                    <tr>
                        <td>
                            <div class="resource-table__main">
                                <a href="{{ route('tarefas.show', $tarefa) }}" class="resource-table__title">{{ $tarefa->titulo }}</a>
                                <div class="resource-table__meta">{{ $tarefa->user?->name ?? 'Sem responsavel' }}</div>
                            </div>
                        </td>
                        <td class="hidden md:table-cell"><span class="resource-table__meta">{{ $tarefa->igreja->nome_fantasia }}</span></td>
                        <td>
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium" style="background-color: {{ $tarefa->status->bgColor() }}; color: {{ $tarefa->status->color() }};">
                                <span class="w-2 h-2 rounded-full" style="background-color: {{ $tarefa->status->color() }};"></span>
                                {{ $tarefa->status->label() }}
                            </span>
                        </td>
                        <td class="hidden lg:table-cell"><span class="resource-table__meta">{{ $tarefa->prioridade->label() }}</span></td>
                        <td>
                            <div class="resource-table__actions">
                                <a href="{{ route('tarefas.show', $tarefa) }}" class="button button-muted text-xs">Ver</a>
                                <a href="{{ route('tarefas.edit', $tarefa) }}" class="button button-ghost text-xs">Editar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="resource-table__empty">Nenhuma tarefa encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $tarefas->links() }}
    </div>
</x-app-layout>
