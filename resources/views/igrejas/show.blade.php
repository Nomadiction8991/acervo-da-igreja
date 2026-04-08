<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('igrejas.index') }}" class="text-sm opacity-75 hover:underline">← Igrejas</a>
                <h1 class="section-title mt-1">{{ $igreja->nome_fantasia }}</h1>
            </div>
            @can('update', $igreja)
                <a href="{{ route('igrejas.edit', $igreja) }}" class="button button-primary">Editar</a>
            @endcan
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_300px]">

        <div class="space-y-6">

            {{-- Dados --}}
            <div class="surface panel-padding">
                <p class="eyebrow mb-4">Informações</p>
                <div class="space-y-3">
                    <div class="data-row">
                        <span class="data-row__label">Código</span>
                        <span class="data-row__value font-mono">{{ $igreja->codigo_controle }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-row__label">Razão Social</span>
                        <span class="data-row__value">{{ $igreja->razao_social }}</span>
                    </div>
                    @if ($igreja->matricula)
                        <div class="data-row">
                            <span class="data-row__label">Matrícula</span>
                            <span class="data-row__value">{{ $igreja->matricula }}</span>
                        </div>
                    @endif
                    @if ($igreja->endereco)
                        <div class="data-row">
                            <span class="data-row__label">Endereço</span>
                            <span class="data-row__value">{{ $igreja->endereco }}</span>
                        </div>
                    @endif
                    @if ($igreja->cep)
                        <div class="data-row">
                            <span class="data-row__label">CEP</span>
                            <span class="data-row__value">{{ $igreja->cep }}</span>
                        </div>
                    @endif
                    @if ($igreja->cidade)
                        <div class="data-row">
                            <span class="data-row__label">Cidade</span>
                            <span class="data-row__value">{{ $igreja->cidade }}, {{ $igreja->estado }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Fotos --}}
            <div class="surface panel-padding">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <p class="eyebrow">Fotos</p>
                    @can('create', App\Models\Foto::class)
                        <a href="{{ route('fotos.create', $igreja) }}" class="button button-ghost text-xs">+ Adicionar</a>
                    @endcan
                </div>

                @php
                    $fotos = auth()->check() ? $igreja->fotos : $igreja->fotosPublicas();
                @endphp

                @if ($fotos->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach ($fotos as $foto)
                            <div class="relative group rounded-xl overflow-hidden">
                                <img src="{{ route('files.fotos.show', $foto) }}"
                                     alt="Foto"
                                     class="w-full h-36 object-cover">

                                <div class="absolute top-2 right-2 flex gap-1">
                                    @if ($foto->is_principal)
                                        <span class="chip chip--public text-[0.6rem]">Principal</span>
                                    @endif
                                    @if (auth()->check())
                                        <span class="chip chip--{{ $foto->is_public ? 'public' : 'private' }} text-[0.6rem]">
                                            {{ $foto->is_public ? 'Pública' : 'Privada' }}
                                        </span>
                                    @endif
                                </div>

                                @if (auth()->check() && (auth()->user()->ehAdmin() || auth()->user()->temPermissao('fotos.editar')))
                                    <form method="POST" action="{{ route('fotos.update', [$igreja, $foto]) }}"
                                          class="absolute bottom-0 inset-x-0 bg-black/70 px-3 py-2 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-3">
                                        @csrf
                                        @method('PATCH')
                                        <label class="flex items-center gap-1 text-white text-xs cursor-pointer">
                                            <input type="checkbox" name="is_principal" value="1" @if ($foto->is_principal) checked @endif>
                                            Principal
                                        </label>
                                        <label class="flex items-center gap-1 text-white text-xs cursor-pointer">
                                            <input type="checkbox" name="is_public" value="1" @if ($foto->is_public) checked @endif>
                                            Pública
                                        </label>
                                        <button type="submit" class="ml-auto button button-ghost text-xs text-white">Salvar</button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-[var(--text-secondary)]">Nenhuma foto adicionada.</p>
                @endif
            </div>

            <div class="surface panel-padding">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <p class="eyebrow">Documentos</p>
                    <div class="flex gap-2">
                        <a href="{{ route('documentos.index', ['igreja_id' => $igreja->id]) }}" class="button button-ghost text-xs">Listar</a>
                        @can('create', App\Models\Documento::class)
                            <a href="{{ route('documentos.create', ['igreja_id' => $igreja->id]) }}" class="button button-primary text-xs">+ Documento</a>
                        @endcan
                    </div>
                </div>

                @if ($igreja->documentos->isEmpty())
                    <p class="text-sm text-[var(--text-secondary)]">Nenhum documento vinculado.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($igreja->documentos as $documento)
                            <div class="linked-card linked-card--static">
                                <div>
                                    <p class="font-semibold">{{ $documento->titulo }}</p>
                                    <p class="text-sm text-[var(--text-secondary)]">{{ $documento->tipo }} · {{ $documento->publico ? 'Publico' : 'Privado' }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('files.documentos.show', $documento) }}" class="button button-muted text-xs">Baixar</a>
                                    <a href="{{ route('documentos.show', $documento) }}" class="button button-ghost text-xs">Ver</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="surface panel-padding">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <p class="eyebrow">Tarefas</p>
                    <div class="flex gap-2">
                        <a href="{{ route('tarefas.index', ['igreja_id' => $igreja->id]) }}" class="button button-ghost text-xs">Listar</a>
                        @can('create', App\Models\Tarefa::class)
                            <a href="{{ route('tarefas.create', ['igreja_id' => $igreja->id]) }}" class="button button-primary text-xs">+ Tarefa</a>
                        @endcan
                    </div>
                </div>

                @if ($igreja->tarefas->isEmpty())
                    <p class="text-sm text-[var(--text-secondary)]">Nenhuma tarefa associada.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($igreja->tarefas as $tarefa)
                            <a href="{{ route('tarefas.show', $tarefa) }}" class="linked-card">
                                <div>
                                    <p class="font-semibold">{{ $tarefa->titulo }}</p>
                                    <p class="text-sm text-[var(--text-secondary)]">{{ $tarefa->status->label() }} · {{ $tarefa->prioridade->label() }}</p>
                                </div>
                                <span class="text-xs text-[var(--text-secondary)]">{{ $tarefa->user?->name ?? 'Sem responsavel' }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar: visibilidade --}}
        <div class="space-y-4">
            <div class="surface panel-padding">
                <p class="eyebrow mb-3">Visibilidade pública</p>
                <div class="space-y-2">
                    @foreach ([
                        'codigo_controle' => 'Código',
                        'nome_fantasia'   => 'Nome Fantasia',
                        'razao_social'    => 'Razão Social',
                        'matricula'       => 'Matrícula',
                        'cep'             => 'CEP',
                        'endereco'        => 'Endereço',
                        'cidade'          => 'Cidade',
                        'estado'          => 'Estado',
                    ] as $key => $label)
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm text-[var(--text-secondary)]">{{ $label }}</span>
                            <span class="chip chip--{{ $igreja->esCampoPublico($key) ? 'public' : 'private' }} text-[0.65rem]">
                                {{ $igreja->esCampoPublico($key) ? 'Público' : 'Privado' }}
                            </span>
                        </div>
                    @endforeach
                </div>
                @can('update', $igreja)
                    <a href="{{ route('igrejas.edit', $igreja) }}" class="button button-ghost w-full text-center mt-4">Alterar visibilidade</a>
                @endcan
            </div>

            <div class="surface panel-padding">
                <p class="eyebrow mb-3">Portal público</p>
                <p class="text-sm text-[var(--text-secondary)] mb-3">Como os visitantes veem esta igreja.</p>
                <a href="{{ route('portal.show', $igreja) }}" target="_blank" class="button button-muted w-full text-center">Ver no portal ↗</a>
            </div>
        </div>
    </div>
</x-app-layout>
