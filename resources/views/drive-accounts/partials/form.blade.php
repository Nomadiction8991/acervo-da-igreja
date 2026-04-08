<div class="grid gap-5 md:grid-cols-2">
    <div class="field-block">
        <label class="field-block__label" for="nome">Nome da conta</label>
        <input id="nome" name="nome" type="text" class="field-control" value="{{ old('nome', $driveAccount?->nome) }}" required>
    </div>

    <div class="field-block">
        <label class="field-block__label" for="email">Email do Google</label>
        <input id="email" name="email" type="email" class="field-control" value="{{ old('email', $driveAccount?->email) }}">
    </div>
</div>

<div class="field-block">
    <label class="field-block__label" for="folder_id">Folder ID padrao</label>
    <input id="folder_id" name="folder_id" type="text" class="field-control" value="{{ old('folder_id', $driveAccount?->folder_id) }}">
    <p class="text-xs text-[var(--text-secondary)] mt-2">
        Opcional. Se informado, os arquivos sincronizados vao para esta pasta do Google Drive.
    </p>
</div>

<div class="grid gap-5">
    <div class="surface-inset">
        <span class="mini-label">Configuracao manual</span>
        <p class="mt-2 text-sm text-[var(--text-secondary)]">
            Esses campos sao opcionais quando voce vai usar o fluxo OAuth. Se preencher tudo aqui, a conta tambem funciona em modo manual.
        </p>
    </div>

    <div class="field-block">
        <label class="field-block__label" for="client_id">Client ID</label>
        <textarea id="client_id" name="client_id" rows="3" class="field-control">{{ old('client_id') }}</textarea>
        @if ($driveAccount)
            <p class="text-xs text-[var(--text-secondary)] mt-2">Deixe em branco para manter o valor atual.</p>
        @endif
    </div>

    <div class="field-block">
        <label class="field-block__label" for="client_secret">Client Secret</label>
        <textarea id="client_secret" name="client_secret" rows="3" class="field-control">{{ old('client_secret') }}</textarea>
        @if ($driveAccount)
            <p class="text-xs text-[var(--text-secondary)] mt-2">Deixe em branco para manter o valor atual.</p>
        @endif
    </div>

    <div class="field-block">
        <label class="field-block__label" for="refresh_token">Refresh Token</label>
        <textarea id="refresh_token" name="refresh_token" rows="4" class="field-control">{{ old('refresh_token') }}</textarea>
        @if ($driveAccount)
            <p class="text-xs text-[var(--text-secondary)] mt-2">Deixe em branco para manter o valor atual.</p>
        @endif
    </div>
</div>

<div class="flex gap-3">
    <button type="submit" class="button button-primary">
        {{ $driveAccount ? 'Salvar alteracoes' : 'Criar conta' }}
    </button>
    <a href="{{ $driveAccount ? route('drive-accounts.show', $driveAccount) : route('drive-accounts.index') }}" class="button button-muted">
        Cancelar
    </a>
</div>
