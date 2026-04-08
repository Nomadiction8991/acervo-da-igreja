<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Arquivos</p>
                <h1 class="section-title mt-1">Documentos</h1>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('documentos.export') }}" class="button button-muted">📊 Exportar Excel</a>
                @can('create', App\Models\Documento::class)
                    <a href="{{ route('documentos.create') }}" class="button button-primary">+ Novo documento</a>
                @endcan
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="status-banner status-banner--success mb-4">{{ session('success') }}</div>
    @endif

    @if ($errors->has('drive_sync'))
        <div class="status-banner status-banner--danger mb-4">{{ $errors->first('drive_sync') }}</div>
    @endif

    <div class="surface panel-padding mb-6 space-y-4">
        <div>
            <label class="field-block__label" for="q">Buscar</label>
            <form method="GET" class="flex gap-3">
                <input type="text" id="q" name="q" placeholder="Procure por titulo, descrição, tipo..." value="{{ $search ?? '' }}" class="field-control flex-1">
                <button type="submit" class="button button-primary">Procurar</button>
                @if ($search)
                    <a href="{{ route('documentos.index') }}" class="button button-muted">Limpar</a>
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
                <label class="field-block__label" for="sync_status">Status Drive</label>
                <select id="sync_status" name="sync_status" class="field-control">
                    <option value="">Todos</option>
                    <option value="pending" @selected(request('sync_status') === 'pending')>Pendente</option>
                    <option value="synced" @selected(request('sync_status') === 'synced')>Sincronizado</option>
                    <option value="error" @selected(request('sync_status') === 'error')>Com erro</option>
                    <option value="sem_drive" @selected(request('sync_status') === 'sem_drive')>Sem drive</option>
                </select>
            </div>
            <div class="flex items-end gap-3">
                <button type="submit" class="button button-primary">Filtrar</button>
                <a href="{{ route('documentos.index') }}" class="button button-muted">Limpar</a>
            </div>
        </form>
    </div>

    <div class="surface rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[var(--border-subtle)]">
                    <th class="px-4 py-3 text-left">Titulo</th>
                    <th class="px-4 py-3 text-left hidden md:table-cell">Igreja</th>
                    <th class="px-4 py-3 text-left hidden lg:table-cell">Tipo</th>
                    <th class="px-4 py-3 text-left">Status Drive</th>
                    <th class="px-4 py-3 text-right">Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($documentos as $documento)
                    <tr class="border-b border-[var(--border-subtle)] last:border-0">
                        <td class="px-4 py-4">
                            <a href="{{ route('documentos.show', $documento) }}" class="font-semibold hover:underline">
                                {{ $documento->titulo }}
                            </a>
                            <div class="text-xs text-[var(--text-secondary)] mt-1">
                                {{ $documento->publico ? 'Publico' : 'Privado' }}
                            </div>
                            <div class="text-xs text-[var(--text-secondary)] mt-1">
                                @if ($documento->driveAccount)
                                    Drive: {{ $documento->driveAccount->nome }}{{ $documento->driveAccount->email ? ' · '.$documento->driveAccount->email : '' }}
                                @else
                                    Drive: sem conta vinculada
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 hidden md:table-cell">{{ $documento->igreja->nome_fantasia }}</td>
                        <td class="px-4 py-4 hidden lg:table-cell">{{ $documento->tipo }}</td>
                        <td class="px-4 py-4">
                            <span class="chip chip--{{ $documento->driveStatusChipVariant() }}">
                                {{ $documento->driveStatusLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex justify-end gap-2">
                                @can('update', $documento)
                                    @if ($documento->driveAccount)
                                        <form method="POST" action="{{ route('documentos.sync-drive', $documento) }}">
                                            @csrf
                                            <button type="submit" class="button button-primary text-xs">Enviar ao Drive</button>
                                        </form>
                                    @endif
                                @endcan
                                <a href="{{ route('files.documentos.show', $documento) }}" class="button button-muted text-xs">Baixar</a>
                                <a href="{{ route('documentos.show', $documento) }}" class="button button-ghost text-xs">Ver</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-[var(--text-secondary)]">Nenhum documento encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $documentos->links() }}
    </div>
</x-app-layout>
