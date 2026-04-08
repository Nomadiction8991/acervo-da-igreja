<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('igrejas.show', $igreja) }}" class="text-sm opacity-75 hover:underline">← {{ $igreja->nome_fantasia }}</a>
                <h1 class="section-title mt-1">Editar Igreja</h1>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="status-banner status-banner--success mb-4">{{ session('success') }}</div>
    @endif

    @if ($errors->has('igreja'))
        <div class="status-banner status-banner--danger mb-4">{{ $errors->first('igreja') }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">

        {{-- Dados principais --}}
        <form method="POST" action="{{ route('igrejas.update', $igreja) }}" class="surface panel-padding space-y-5">
            @csrf
            @method('PUT')

            <div class="field-block">
                <label class="field-block__label" for="codigo_controle">Código de Controle *</label>
                <input class="field-control" type="text" id="codigo_controle" name="codigo_controle"
                       value="{{ old('codigo_controle', $igreja->codigo_controle) }}" required>
                @error('codigo_controle')
                    <div class="field-errors">{{ $message }}</div>
                @enderror
            </div>

            <div class="field-block">
                <label class="field-block__label" for="nome_fantasia">Nome Fantasia *</label>
                <input class="field-control" type="text" id="nome_fantasia" name="nome_fantasia"
                       value="{{ old('nome_fantasia', $igreja->nome_fantasia) }}" required>
                @error('nome_fantasia')
                    <div class="field-errors">{{ $message }}</div>
                @enderror
            </div>

            <div class="field-block">
                <label class="field-block__label" for="razao_social">Razão Social *</label>
                <input class="field-control" type="text" id="razao_social" name="razao_social"
                       value="{{ old('razao_social', $igreja->razao_social) }}" required>
                @error('razao_social')
                    <div class="field-errors">{{ $message }}</div>
                @enderror
            </div>

            <div class="field-block">
                <label class="field-block__label" for="matricula">Matrícula</label>
                <input class="field-control" type="text" id="matricula" name="matricula"
                       value="{{ old('matricula', $igreja->matricula) }}">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="field-block">
                    <label class="field-block__label" for="cep">CEP</label>
                    <input class="field-control" type="text" id="cep" name="cep"
                           value="{{ old('cep', $igreja->cep) }}">
                </div>
                <div class="field-block">
                    <label class="field-block__label" for="endereco">Endereço</label>
                    <input class="field-control" type="text" id="endereco" name="endereco"
                           value="{{ old('endereco', $igreja->endereco) }}">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div class="field-block">
                    <label class="field-block__label" for="cidade">Cidade</label>
                    <input class="field-control" type="text" id="cidade" name="cidade"
                           value="{{ old('cidade', $igreja->cidade) }}">
                </div>
                <div class="field-block">
                    <label class="field-block__label" for="estado">UF</label>
                    <input class="field-control" type="text" id="estado" name="estado"
                           value="{{ old('estado', $igreja->estado) }}" maxlength="2">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="button button-primary">Salvar alterações</button>
                <a href="{{ route('igrejas.show', $igreja) }}" class="button button-muted">Cancelar</a>
            </div>
        </form>

        {{-- Painel lateral: visibilidade + fotos + danger --}}
        <div class="space-y-4">

            {{-- Visibilidade --}}
            @can('alterarVisibilidade', $igreja)
                <div class="surface panel-padding">
                    <p class="eyebrow">Visibilidade pública</p>
                    <p class="text-sm text-[var(--text-secondary)] mt-2 mb-4">
                        Campos marcados aparecem no portal para visitantes.
                    </p>

                    <form method="POST" action="{{ route('igrejas.visibilidade', $igreja) }}" class="space-y-2">
                        @csrf
                        @foreach ([
                            'codigo_controle' => 'Código de Controle',
                            'nome_fantasia'   => 'Nome Fantasia',
                            'razao_social'    => 'Razão Social',
                            'matricula'       => 'Matrícula',
                            'cep'             => 'CEP',
                            'endereco'        => 'Endereço',
                            'cidade'          => 'Cidade',
                            'estado'          => 'Estado',
                        ] as $key => $label)
                            <label class="flex items-center gap-2 text-sm text-[var(--text-primary)] cursor-pointer">
                                <input type="checkbox" name="visibilidade[{{ $key }}]" value="1"
                                       @if ($igreja->esCampoPublico($key)) checked @endif>
                                {{ $label }}
                            </label>
                        @endforeach

                        <button type="submit" class="button button-ghost w-full mt-3">Salvar visibilidade</button>
                    </form>
                </div>
            @endcan

            {{-- Fotos --}}
            <div class="surface panel-padding">
                <p class="eyebrow">Fotos</p>
                <p class="text-sm text-[var(--text-secondary)] mt-2 mb-4">
                    {{ $igreja->fotos()->count() }} foto(s) cadastrada(s)
                </p>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('fotos.index', $igreja) }}" class="button button-muted w-full text-center">Gerenciar fotos</a>
                    @can('create', App\Models\Foto::class)
                        <a href="{{ route('fotos.create', $igreja) }}" class="button button-primary w-full text-center">+ Adicionar foto</a>
                    @endcan
                </div>
            </div>

            {{-- Zona de perigo --}}
            @can('delete', $igreja)
                <div class="surface panel-padding border border-red-900/30">
                    <p class="eyebrow text-red-400">Zona de perigo</p>
                    <p class="text-sm text-[var(--text-secondary)] mt-2 mb-4">
                        A inativacao so e permitida quando nao existem fotos, documentos ou tarefas vinculados.
                    </p>
                    <form method="POST" action="{{ route('igrejas.destroy', $igreja) }}" class="form-delete">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="button button-ghost w-full border-red-900/40 text-red-400 hover:bg-red-950/30"
                                onclick="return confirm('Tem certeza? Esta ação vai inativar a igreja e só é permitida quando não existem fotos, documentos ou tarefas vinculados.')">
                            Inativar Igreja
                        </button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
