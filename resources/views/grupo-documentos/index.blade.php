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

    <div class="surface rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[var(--border-subtle)]">
                    <th class="px-4 py-3 text-left">Grupo</th>
                    <th class="px-4 py-3 text-left hidden md:table-cell">Descricao</th>
                    <th class="px-4 py-3 text-left">Padrao</th>
                    <th class="px-4 py-3 text-left hidden lg:table-cell">Documentos</th>
                    <th class="px-4 py-3 text-right">Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($grupos as $grupoDocumento)
                    <tr class="border-b border-[var(--border-subtle)] last:border-0">
                        <td class="px-4 py-4">
                            <a href="{{ route('grupo-documentos.show', $grupoDocumento) }}" class="font-semibold hover:underline">
                                {{ $grupoDocumento->nome }}
                            </a>
                        </td>
                        <td class="px-4 py-4 hidden md:table-cell">
                            <span class="text-[var(--text-secondary)]">
                                {{ $grupoDocumento->descricao ?: 'Sem descricao cadastrada.' }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="chip chip--{{ $grupoDocumento->publico_padrao ? 'public' : 'private' }}">
                                {{ $grupoDocumento->publico_padrao ? 'Publico' : 'Privado' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 hidden lg:table-cell">{{ $grupoDocumento->documentos_count }}</td>
                        <td class="px-4 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('grupo-documentos.show', $grupoDocumento) }}" class="button button-muted text-xs">Ver</a>
                                @can('update', $grupoDocumento)
                                    <a href="{{ route('grupo-documentos.edit', $grupoDocumento) }}" class="button button-ghost text-xs">Editar</a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-[var(--text-secondary)]">Nenhum grupo cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $grupos->links() }}
    </div>
</x-app-layout>
