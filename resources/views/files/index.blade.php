<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Operacao</p>
                <h1 class="section-title mt-1">Controle de arquivos</h1>
            </div>
            @can('viewAny', App\Models\DriveAccount::class)
                <a href="{{ route('drive-accounts.index') }}" class="button button-primary">Configurar Drive</a>
            @endcan
        </div>
    </x-slot>

    <div class="surface panel-padding mb-6">
        <form method="GET" class="flex flex-wrap gap-3">
            <select name="status" class="field-control max-w-xs">
                <option value="">Todos</option>
                <option value="sem_drive" @selected(request('status') === 'sem_drive')>Sem drive</option>
                <option value="com_erro" @selected(request('status') === 'com_erro')>Com erro</option>
                <option value="sincronizados" @selected(request('status') === 'sincronizados')>Sincronizados</option>
            </select>
            <button type="submit" class="button button-primary">Filtrar</button>
            <a href="{{ route('files.index') }}" class="button button-muted">Limpar</a>
        </form>
    </div>

    <div class="resource-table-shell">
        <div class="resource-table-scroll">
        <table class="resource-table">
            <thead>
                <tr>
                    <th>Arquivo</th>
                    <th class="hidden md:table-cell">Igreja</th>
                    <th>Drive</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($arquivos as $arquivo)
                    <tr>
                        <td>
                            <div class="resource-table__main">
                                <div class="resource-table__title">{{ $arquivo->titulo }}</div>
                                <div class="resource-table__meta">{{ $arquivo->sync_error ?? $arquivo->tipo }}</div>
                            </div>
                        </td>
                        <td class="hidden md:table-cell"><span class="resource-table__meta">{{ $arquivo->igreja->nome_fantasia }}</span></td>
                        <td><span class="resource-table__status resource-table__status--muted">{{ $arquivo->sync_status ?? 'sem sync' }}</span></td>
                        <td>
                            <div class="resource-table__actions">
                                <a href="{{ route('files.documentos.show', $arquivo) }}" class="button button-muted text-xs">Baixar</a>
                                <a href="{{ route('documentos.show', $arquivo) }}" class="button button-ghost text-xs">Detalhes</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="resource-table__empty">Nenhum arquivo encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $arquivos->links() }}
    </div>
</x-app-layout>
