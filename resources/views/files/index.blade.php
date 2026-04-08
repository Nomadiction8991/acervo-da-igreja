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

    <div class="surface rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[var(--border-subtle)]">
                    <th class="px-4 py-3 text-left">Arquivo</th>
                    <th class="px-4 py-3 text-left hidden md:table-cell">Igreja</th>
                    <th class="px-4 py-3 text-left">Drive</th>
                    <th class="px-4 py-3 text-right">Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($arquivos as $arquivo)
                    <tr class="border-b border-[var(--border-subtle)] last:border-0">
                        <td class="px-4 py-4">
                            <div class="font-semibold">{{ $arquivo->titulo }}</div>
                            <div class="text-xs text-[var(--text-secondary)]">{{ $arquivo->sync_error ?? $arquivo->tipo }}</div>
                        </td>
                        <td class="px-4 py-4 hidden md:table-cell">{{ $arquivo->igreja->nome_fantasia }}</td>
                        <td class="px-4 py-4">{{ $arquivo->sync_status ?? 'sem sync' }}</td>
                        <td class="px-4 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('files.documentos.show', $arquivo) }}" class="button button-muted text-xs">Baixar</a>
                                <a href="{{ route('documentos.show', $arquivo) }}" class="button button-ghost text-xs">Detalhes</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-[var(--text-secondary)]">Nenhum arquivo encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $arquivos->links() }}
    </div>
</x-app-layout>
