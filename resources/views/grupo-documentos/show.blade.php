<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('grupo-documentos.index') }}" class="text-sm opacity-75 hover:underline">← Grupos</a>
                <h1 class="section-title mt-1">{{ $grupoDocumento->nome }}</h1>
            </div>
            <div class="flex gap-2">
                @can('update', $grupoDocumento)
                    <a href="{{ route('grupo-documentos.edit', $grupoDocumento) }}" class="button button-primary">Editar</a>
                @endcan
                @can('delete', $grupoDocumento)
                    <form method="POST" action="{{ route('grupo-documentos.destroy', $grupoDocumento) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button button-muted" @disabled($grupoDocumento->documentos->isNotEmpty())>
                            Excluir
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="status-banner status-banner--success mb-4">{{ session('success') }}</div>
    @endif

    @if ($errors->has('grupo_documento'))
        <div class="status-banner status-banner--danger mb-4">{{ $errors->first('grupo_documento') }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
        <div class="surface panel-padding space-y-4">
            <div class="data-row">
                <span class="data-row__label">Descricao</span>
                <span class="data-row__value">{{ $grupoDocumento->descricao ?: 'Sem descricao cadastrada.' }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Visibilidade padrao</span>
                <span class="data-row__value">{{ $grupoDocumento->publico_padrao ? 'Publico' : 'Privado' }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Documentos vinculados</span>
                <span class="data-row__value">{{ $grupoDocumento->documentos->count() }}</span>
            </div>
        </div>

        <div class="surface panel-padding">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <p class="eyebrow">Vinculos</p>
                    <h2 class="text-lg font-semibold">Documentos deste grupo</h2>
                </div>
                @can('create', App\Models\Documento::class)
                    <a href="{{ route('documentos.create', ['grupo_documento_id' => $grupoDocumento->id]) }}" class="button button-muted">
                        Novo documento
                    </a>
                @endcan
            </div>

            <div class="space-y-3">
                @forelse ($grupoDocumento->documentos as $documento)
                    <div class="border border-[var(--border-subtle)] rounded-lg p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold">{{ $documento->titulo }}</p>
                                <p class="text-sm text-[var(--text-secondary)]">
                                    {{ $documento->igreja->nome_fantasia }} · {{ $documento->publico ? 'Publico' : 'Privado' }}
                                </p>
                            </div>
                            <a href="{{ route('documentos.show', $documento) }}" class="text-sm hover:underline">Ver</a>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-[var(--text-secondary)]">Nenhum documento vinculado a este grupo ainda.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
