<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Organizacao</p>
                <h1 class="section-title mt-1">Grupos de documentos</h1>
            </div>
            @can('create', App\Models\GrupoDocumento::class)
                <a href="{{ route('grupo-documentos.create') }}" class="button button-primary">+ Novo grupo</a>
            @endcan
        </div>
    </x-slot>

    @if (session('success'))
        <div class="status-banner status-banner--success mb-4">{{ session('success') }}</div>
    @endif

    @if ($errors->has('grupo_documento'))
        <div class="status-banner status-banner--danger mb-4">{{ $errors->first('grupo_documento') }}</div>
    @endif

    <div class="resource-table-shell">
        <div class="resource-table-scroll">
        <table class="resource-table">
            <thead>
                <tr>
                    <th>Grupo</th>
                    <th class="hidden md:table-cell">Descricao</th>
                    <th>Padrao</th>
                    <th class="hidden lg:table-cell">Documentos</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($grupos as $grupoDocumento)
                    <tr>
                        <td>
                            <a href="{{ route('grupo-documentos.show', $grupoDocumento) }}" class="resource-table__title">
                                {{ $grupoDocumento->nome }}
                            </a>
                        </td>
                        <td class="hidden md:table-cell">
                            <span class="resource-table__meta">
                                {{ $grupoDocumento->descricao ?: 'Sem descricao cadastrada.' }}
                            </span>
                        </td>
                        <td>
                            <span class="chip chip--{{ $grupoDocumento->publico_padrao ? 'public' : 'private' }}">
                                {{ $grupoDocumento->publico_padrao ? 'Publico' : 'Privado' }}
                            </span>
                        </td>
                        <td class="hidden lg:table-cell"><span class="resource-table__count">{{ $grupoDocumento->documentos_count }}</span></td>
                        <td>
                            <div class="resource-table__actions">
                                <a href="{{ route('grupo-documentos.show', $grupoDocumento) }}" class="button button-muted text-xs">Ver</a>
                                @can('update', $grupoDocumento)
                                    <a href="{{ route('grupo-documentos.edit', $grupoDocumento) }}" class="button button-ghost text-xs">Editar</a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="resource-table__empty">Nenhum grupo cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $grupos->links() }}
    </div>
</x-app-layout>
