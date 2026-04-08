<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Gestão</p>
                <h1 class="section-title mt-1">Igrejas</h1>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('igrejas.export') }}" class="button button-muted">📊 Exportar Excel</a>
                @can('create', App\Models\Igreja::class)
                    <a href="{{ route('igrejas.create') }}" class="button button-primary">+ Nova Igreja</a>
                @endcan
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="status-banner status-banner--success">{{ session('success') }}</div>
    @endif

    <div class="surface panel-padding mb-6">
        <form method="GET" class="grid gap-3 md:grid-cols-[minmax(0,1fr)_220px_auto]">
            <div>
                <input type="text" name="q" placeholder="Procure por nome, código, cidade..." value="{{ $search ?? '' }}" class="field-control w-full">
            </div>
            <div>
                <select name="status" class="field-control w-full">
                    <option value="ativas" @selected(($status ?? 'ativas') === 'ativas')>Ativas</option>
                    <option value="inativas" @selected(($status ?? 'ativas') === 'inativas')>Inativas</option>
                    <option value="todas" @selected(($status ?? 'ativas') === 'todas')>Todas</option>
                </select>
            </div>
            <button type="submit" class="button button-primary">Procurar</button>
            @if ($search || ($status ?? 'ativas') !== 'ativas')
                <a href="{{ route('igrejas.index') }}" class="button button-muted">Limpar</a>
            @endif
        </form>
    </div>

    @if ($igrejas->isEmpty())
        <div class="surface panel-padding text-center">
            <p class="text-[var(--text-secondary)]">Nenhuma igreja cadastrada.</p>
            @can('create', App\Models\Igreja::class)
                <a href="{{ route('igrejas.create') }}" class="button button-primary mt-4 inline-block">Cadastrar primeira igreja</a>
            @endcan
        </div>
    @else
        <div class="surface rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--border-subtle)]">
                        <th class="px-5 py-3 text-left">
                            <x-sortable-header column="codigo_controle" label="Código" :sortBy="$sortBy" :sortDir="$sortDir" />
                        </th>
                        <th class="px-5 py-3 text-left">
                            <x-sortable-header column="nome_fantasia" label="Nome" :sortBy="$sortBy" :sortDir="$sortDir" />
                        </th>
                        <th class="px-5 py-3 text-left hidden md:table-cell">
                            <x-sortable-header column="cidade" label="Cidade" :sortBy="$sortBy" :sortDir="$sortDir" />
                        </th>
                        <th class="px-5 py-3 text-left hidden lg:table-cell">
                            <span class="field-block__label">Modulos</span>
                        </th>
                        <th class="px-5 py-3 text-right">
                            <span class="field-block__label">Ações</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($igrejas as $igreja)
                        <tr class="border-b border-[var(--border-subtle)] last:border-0 hover:bg-[var(--surface-inset)] transition-colors">
                            <td class="px-5 py-4 font-mono text-xs text-[var(--text-secondary)]">
                                {{ $igreja->codigo_controle }}
                            </td>
                            <td class="px-5 py-4 font-semibold text-[var(--text-primary)]">
                                @if ($igreja->trashed())
                                    <span>{{ $igreja->nome_fantasia }}</span>
                                @else
                                    <a href="{{ route('igrejas.show', $igreja) }}" class="hover:underline">
                                        {{ $igreja->nome_fantasia }}
                                    </a>
                                @endif
                                @if ($igreja->trashed())
                                    <span class="chip chip--private ml-2 text-[0.65rem]">Inativa</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-[var(--text-secondary)] hidden md:table-cell">
                                {{ $igreja->cidade ? $igreja->cidade.', '.$igreja->estado : '—' }}
                            </td>
                            <td class="px-5 py-4 text-[var(--text-secondary)] hidden lg:table-cell">
                                {{ $igreja->fotos_count }} foto(s) · {{ $igreja->documentos_count }} doc(s) · {{ $igreja->tarefas_count }} tarefa(s)
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    @if (! $igreja->trashed())
                                        <a href="{{ route('igrejas.show', $igreja) }}" class="button button-muted text-xs">Ver</a>
                                        @can('update', $igreja)
                                            <a href="{{ route('igrejas.edit', $igreja) }}" class="button button-ghost text-xs">Editar</a>
                                        @endcan
                                    @else
                                        <span class="button button-muted text-xs opacity-60 cursor-default">Inativa</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $igrejas->links() }}
        </div>
    @endif
</x-app-layout>
