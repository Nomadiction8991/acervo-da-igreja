<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('igrejas.index') }}" class="text-sm opacity-75 hover:underline">← Igrejas</a>
            <h1 class="section-title mt-1">Nova Igreja</h1>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('igrejas.store') }}" class="surface panel-padding space-y-5">
            @csrf

            <div class="field-block">
                <label class="field-block__label" for="codigo_controle">Código de Controle *</label>
                <input class="field-control @error('codigo_controle') border-red-500 @enderror"
                       type="text" id="codigo_controle" name="codigo_controle"
                       value="{{ old('codigo_controle') }}" required>
                @error('codigo_controle')
                    <div class="field-errors">{{ $message }}</div>
                @enderror
            </div>

            <div class="field-block">
                <label class="field-block__label" for="nome_fantasia">Nome Fantasia *</label>
                <input class="field-control @error('nome_fantasia') border-red-500 @enderror"
                       type="text" id="nome_fantasia" name="nome_fantasia"
                       value="{{ old('nome_fantasia') }}" required>
                @error('nome_fantasia')
                    <div class="field-errors">{{ $message }}</div>
                @enderror
            </div>

            <div class="field-block">
                <label class="field-block__label" for="razao_social">Razão Social *</label>
                <input class="field-control @error('razao_social') border-red-500 @enderror"
                       type="text" id="razao_social" name="razao_social"
                       value="{{ old('razao_social') }}" required>
                @error('razao_social')
                    <div class="field-errors">{{ $message }}</div>
                @enderror
            </div>

            <div class="field-block">
                <label class="field-block__label" for="matricula">Matrícula</label>
                <input class="field-control" type="text" id="matricula" name="matricula"
                       value="{{ old('matricula') }}">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="field-block">
                    <label class="field-block__label" for="cep">CEP</label>
                    <input class="field-control" type="text" id="cep" name="cep"
                           value="{{ old('cep') }}">
                </div>
                <div class="field-block">
                    <label class="field-block__label" for="endereco">Endereço</label>
                    <input class="field-control" type="text" id="endereco" name="endereco"
                           value="{{ old('endereco') }}">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div class="field-block">
                    <label class="field-block__label" for="cidade">Cidade</label>
                    <input class="field-control" type="text" id="cidade" name="cidade"
                           value="{{ old('cidade') }}">
                </div>
                <div class="field-block">
                    <label class="field-block__label" for="estado">UF</label>
                    <input class="field-control" type="text" id="estado" name="estado"
                           value="{{ old('estado') }}" maxlength="2">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="button button-primary">Criar Igreja</button>
                <a href="{{ route('igrejas.index') }}" class="button button-muted">Cancelar</a>
            </div>
        </form>
    </div>
</x-app-layout>
