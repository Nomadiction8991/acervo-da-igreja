<div class="grid gap-5 md:grid-cols-2">
    <div class="field-block">
        <label class="field-block__label" for="igreja_id">Igreja</label>
        <select id="igreja_id" name="igreja_id" class="field-control">
            @foreach ($igrejas as $igreja)
                <option value="{{ $igreja->id }}" @selected(old('igreja_id', $documento?->igreja_id ?? request('igreja_id')) == $igreja->id)>{{ $igreja->nome_fantasia }}</option>
            @endforeach
        </select>
    </div>

    <div class="field-block">
        <div class="flex items-center justify-between gap-3">
            <label class="field-block__label" for="grupo_documento_id">Grupo</label>
            @can('viewAny', App\Models\GrupoDocumento::class)
                <a href="{{ route('grupo-documentos.index') }}" class="text-xs text-[var(--text-secondary)] hover:underline">
                    Gerenciar grupos
                </a>
            @endcan
        </div>
        <select id="grupo_documento_id" name="grupo_documento_id" class="field-control">
            <option value="">Sem grupo</option>
            @foreach ($grupos as $grupo)
                <option value="{{ $grupo->id }}" @selected(old('grupo_documento_id', $documento?->grupo_documento_id ?? request('grupo_documento_id')) == $grupo->id)>{{ $grupo->nome }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="field-block">
    <label class="field-block__label" for="titulo">Titulo</label>
    <input id="titulo" name="titulo" type="text" class="field-control" value="{{ old('titulo', $documento?->titulo) }}" required>
</div>

<div class="grid gap-5 md:grid-cols-2">
    <div class="field-block">
        <label class="field-block__label" for="tipo">Tipo</label>
        <input id="tipo" name="tipo" type="text" class="field-control" value="{{ old('tipo', $documento?->tipo) }}" required>
    </div>
    <div class="field-block">
        <label class="field-block__label" for="arquivo">Arquivo {{ $documento ? '(opcional)' : '' }}</label>
        <input id="arquivo" name="arquivo" type="file" class="field-control" {{ $documento ? '' : 'required' }}>
    </div>
</div>

<div class="field-block">
    <label class="field-block__label" for="descricao">Descricao</label>
    <textarea id="descricao" name="descricao" rows="4" class="field-control">{{ old('descricao', $documento?->descricao) }}</textarea>
</div>

<div class="surface-inset">
    <span class="mini-label">Sincronizacao Google Drive</span>
    <p class="mt-2 text-sm text-[var(--text-secondary)]">
        Escolha a conta do Google Drive para este documento. O envio e manual: depois de salvar, use o botao "Enviar ao Drive".
    </p>
</div>

<div class="grid gap-5 md:grid-cols-2">
    <div class="field-block">
        <div class="flex items-center justify-between gap-3">
            <label class="field-block__label" for="drive_account_id">Conta Google Drive</label>
            @can('viewAny', App\Models\DriveAccount::class)
                <a href="{{ route('drive-accounts.index') }}" class="text-xs text-[var(--text-secondary)] hover:underline">
                    Gerenciar contas
                </a>
            @endcan
        </div>
        <select id="drive_account_id" name="drive_account_id" class="field-control">
            <option value="">Nao sincronizar com Drive</option>
            @foreach ($driveAccounts as $driveAccount)
                <option value="{{ $driveAccount->id }}" @selected(old('drive_account_id', $documento?->drive_account_id) == $driveAccount->id)>
                    {{ $driveAccount->nome }}{{ $driveAccount->email ? ' - '.$driveAccount->email : '' }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field-block">
        <label class="field-block__label" for="drive_folder_id">Folder ID do documento</label>
        <input
            id="drive_folder_id"
            name="drive_folder_id"
            type="text"
            class="field-control"
            value="{{ old('drive_folder_id', $documento?->drive_folder_id) }}"
        >
        <p class="text-xs text-[var(--text-secondary)] mt-2">
            Opcional. Em branco, o sistema usa a pasta padrao configurada na conta escolhida.
        </p>
    </div>
</div>

<label class="flex items-center gap-2 text-sm">
    <input type="checkbox" name="publico" value="1" @checked(old('publico', $documento?->publico))>
    Documento publico
</label>

<div class="flex gap-3">
    <button type="submit" class="button button-primary">{{ $documento ? 'Salvar alteracoes' : 'Criar documento' }}</button>
    <a href="{{ $documento ? route('documentos.show', $documento) : route('documentos.index') }}" class="button button-muted">Cancelar</a>
</div>
