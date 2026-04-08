<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('portal.index') }}" class="text-sm opacity-75 hover:underline">← Voltar</a>
                <h2 class="text-2xl font-bold mt-1">{{ $igreja->nome_fantasia }}</h2>
            </div>
            @auth
                @can('update', $igreja)
                    <a href="{{ route('igrejas.edit', $igreja) }}" class="button button-primary">Editar</a>
                @endcan
            @endauth
        </div>
    </x-slot>

    @php $fotoPrincipal = $igreja->fotosPublicas()->first(); @endphp

    @if ($fotoPrincipal)
        <img src="{{ route('files.fotos.show', $fotoPrincipal) }}"
             alt="{{ $igreja->nome_fantasia }}"
             class="w-full h-96 object-cover rounded-lg mb-6">
    @endif

    <div class="surface rounded-lg p-6 mb-6">
        <h3 class="text-xl font-bold mb-4">Informações</h3>

        @if ($igreja->esCampoPublico('endereco') && $igreja->endereco)
            <p class="mb-2"><strong>Endereço:</strong> {{ $igreja->endereco }}</p>
        @endif

        @if ($igreja->esCampoPublico('cidade') && $igreja->cidade)
            <p class="mb-2"><strong>Cidade:</strong> {{ $igreja->cidade }}, {{ $igreja->estado }}</p>
        @endif

        @if ($igreja->esCampoPublico('cep') && $igreja->cep)
            <p class="mb-2"><strong>CEP:</strong> {{ $igreja->cep }}</p>
        @endif
    </div>

    @php $fotos = $igreja->fotosPublicas(); @endphp

    @if ($fotos->count() > 0)
        <div class="surface rounded-lg p-6">
            <h3 class="text-xl font-bold mb-4">Galeria</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach ($fotos as $foto)
                    <img src="{{ route('files.fotos.show', $foto) }}"
                         alt="Foto"
                         class="w-full h-40 object-cover rounded">
                @endforeach
            </div>
        </div>
    @endif

    @if ($igreja->documentos->isNotEmpty())
        <div class="surface rounded-lg p-6 mt-6">
            <h3 class="text-xl font-bold mb-4">Documentos publicos</h3>
            <div class="space-y-3">
                @foreach ($igreja->documentos as $documento)
                    <a href="{{ route('portal.documentos.show', $documento) }}" class="block p-4 rounded-lg border border-[var(--border-subtle)] hover:bg-[var(--surface-inset)]">
                        <strong>{{ $documento->titulo }}</strong>
                        <span class="block text-sm text-[var(--text-secondary)]">{{ $documento->tipo }}</span>
                        <span class="block text-xs text-[var(--text-secondary)] mt-2">Clique para visualizar</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</x-app-layout>
