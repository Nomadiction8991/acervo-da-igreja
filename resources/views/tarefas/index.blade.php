<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Operacao</p>
                <h1 class="section-title mt-1">Tarefas</h1>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('tarefas.export') }}" class="button button-muted">📊 Exportar Excel</a>
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

    <div class="surface rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[var(--border-subtle)]">
                    <th class="px-4 py-3 text-left">Titulo</th>
                    <th class="px-4 py-3 text-left hidden md:table-cell">Igreja</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left hidden lg:table-cell">Prioridade</th>
                    <th class="px-4 py-3 text-right">Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tarefas as $tarefa)
                    <tr class="border-b border-[var(--border-subtle)] last:border-0">
                        <td class="px-4 py-4">
                            <a href="{{ route('tarefas.show', $tarefa) }}" class="font-semibold hover:underline">{{ $tarefa->titulo }}</a>
                            <div class="text-xs text-[var(--text-secondary)] mt-1">{{ $tarefa->user?->name ?? 'Sem responsavel' }}</div>
                        </td>
                        <td class="px-4 py-4 hidden md:table-cell">{{ $tarefa->igreja->nome_fantasia }}</td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium" style="background-color: {{ $tarefa->status->bgColor() }}; color: {{ $tarefa->status->color() }};">
                                <span class="w-2 h-2 rounded-full" style="background-color: {{ $tarefa->status->color() }};"></span>
                                {{ $tarefa->status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-4 hidden lg:table-cell">{{ $tarefa->prioridade->label() }}</td>
                        <td class="px-4 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('tarefas.show', $tarefa) }}" class="button button-muted text-xs">Ver</a>
                                <a href="{{ route('tarefas.edit', $tarefa) }}" class="button button-ghost text-xs">Editar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-[var(--text-secondary)]">Nenhuma tarefa encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $tarefas->links() }}
    </div>
</x-app-layout>
