<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold">Igrejas Públicas</h2>
            @guest
                <a href="{{ route('login') }}" class="button button-primary">
                    Entrar no Sistema
                </a>
            @endguest
        </div>
    </x-slot>

    @if ($igrejas->isEmpty())
        <p class="text-gray-500">Nenhuma igreja cadastrada.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($igrejas as $igreja)
                <div class="surface rounded-lg overflow-hidden hover:shadow-lg transition">
                    @php
                        $fotoPrincipal = $igreja->fotosPublicas()->first();
                    @endphp

                    @if ($fotoPrincipal)
                        <img src="{{ route('files.fotos.show', $fotoPrincipal) }}"
                             alt="{{ $igreja->nome_fantasia }}"
                             class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400">Sem foto</span>
                        </div>
                    @endif

                    <div class="p-4">
                        <h3 class="text-lg font-bold mb-2">
                            <a href="{{ route('portal.show', $igreja) }}" class="hover:underline">
                                {{ $igreja->nome_fantasia }}
                            </a>
                        </h3>

                        @if ($igreja->esCampoPublico('cidade'))
                            <p class="text-sm mb-1">
                                {{ $igreja->cidade }}, {{ $igreja->estado }}
                            </p>
                        @endif

                        @if ($igreja->esCampoPublico('endereco'))
                            <p class="text-sm mb-2">{{ $igreja->endereco }}</p>
                        @endif

                        <a href="{{ route('portal.show', $igreja) }}" class="button button-primary mt-4 inline-block">
                            Ver Detalhes
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $igrejas->links() }}
        </div>
    @endif
</x-app-layout>
