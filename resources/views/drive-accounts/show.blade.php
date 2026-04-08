<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('drive-accounts.index') }}" class="text-sm opacity-75 hover:underline">← Contas Google Drive</a>
                <h1 class="section-title mt-1">{{ $driveAccount->nome }}</h1>
            </div>
            <div class="flex gap-2">
                @can('testConnection', $driveAccount)
                    <form method="POST" action="{{ route('drive-accounts.test', $driveAccount) }}">
                        @csrf
                        <button type="submit" class="button button-primary">Testar conexao</button>
                    </form>
                @endcan
                @can('update', $driveAccount)
                    <a href="{{ route('drive-accounts.edit', $driveAccount) }}" class="button button-muted">Editar</a>
                @endcan
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="status-banner status-banner--success mb-4">{{ session('success') }}</div>
    @endif

    @if ($errors->has('drive_account'))
        <div class="status-banner status-banner--danger mb-4">{{ $errors->first('drive_account') }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
        <div class="surface panel-padding space-y-4">
            <div class="data-row">
                <span class="data-row__label">Email autenticado</span>
                <span class="data-row__value">{{ $driveAccount->email ?? 'Nao validado ainda' }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Folder ID</span>
                <span class="data-row__value">{{ $driveAccount->folder_id ?? 'Pasta raiz da conta' }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Conexao pronta</span>
                <span class="data-row__value">{{ $driveAccount->refresh_token ? 'Sim' : 'Ainda nao' }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Client ID</span>
                <span class="data-row__value">{{ $driveAccount->client_id ? 'Configurado' : 'Nao informado' }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Client Secret</span>
                <span class="data-row__value">{{ $driveAccount->client_secret ? 'Configurado' : 'Nao informado' }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Refresh Token</span>
                <span class="data-row__value">{{ $driveAccount->refresh_token ? 'Configurado' : 'Nao informado' }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Metodo atual</span>
                <span class="data-row__value">{{ data_get($driveAccount->metadata, 'connection_method') ?? 'Nao definido' }}</span>
            </div>
        </div>

        <div class="space-y-4">
            <div class="surface panel-padding">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <div>
                        <p class="eyebrow">OAuth Google</p>
                        <p class="text-sm text-[var(--text-secondary)] mt-2">
                            Conecte a conta pelo navegador sem precisar colar refresh token manualmente.
                        </p>
                    </div>
                </div>

                <div class="space-y-2 text-sm text-[var(--text-secondary)]">
                    <p>Escopo recomendado: `drive.file`</p>
                    <p>Ultima conexao OAuth: {{ data_get($driveAccount->metadata, 'oauth_connected_at') ?? 'Ainda nao conectada' }}</p>
                </div>

                @if ($oauthAvailable)
                    @can('update', $driveAccount)
                        <a href="{{ route('drive-accounts.oauth.redirect', $driveAccount) }}" class="button button-primary mt-4">
                            Conectar com Google
                        </a>
                    @endcan
                @else
                    <p class="text-xs text-[var(--text-secondary)] mt-4">
                        Configure `GOOGLE_DRIVE_CLIENT_ID`, `GOOGLE_DRIVE_CLIENT_SECRET` e `GOOGLE_DRIVE_REDIRECT_URI` para habilitar o fluxo OAuth.
                    </p>
                @endif
            </div>

            <div class="surface panel-padding">
                <p class="eyebrow mb-3">Ultimo teste</p>
                <div class="space-y-2 text-sm text-[var(--text-secondary)]">
                    <p>Quando: {{ data_get($driveAccount->metadata, 'last_tested_at') ?? 'Ainda nao executado' }}</p>
                    <p>Nome no Google: {{ data_get($driveAccount->metadata, 'display_name') ?? 'Nao identificado' }}</p>
                    <p>Root folder: {{ data_get($driveAccount->metadata, 'root_folder_id') ?? 'Nao retornado' }}</p>
                    <p>
                        Pasta configurada:
                        {{ data_get($driveAccount->metadata, 'configured_folder.name') ?? 'Nao validada ou nao definida' }}
                    </p>
                    @if (data_get($driveAccount->metadata, 'configured_folder.web_view_link'))
                        <p>
                            <a href="{{ data_get($driveAccount->metadata, 'configured_folder.web_view_link') }}" target="_blank" class="hover:underline">
                                Abrir pasta no Drive
                            </a>
                        </p>
                    @endif
                </div>
            </div>

            <div class="surface panel-padding">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <p class="eyebrow">Historico recente</p>
                    <span class="text-xs text-[var(--text-secondary)]">{{ $driveAccount->fileSyncLogs->count() }} itens</span>
                </div>

                <div class="space-y-3">
                    @forelse ($driveAccount->fileSyncLogs as $log)
                        <div class="border border-[var(--border-subtle)] rounded-lg p-3 text-sm">
                            <p class="font-semibold">{{ $log->status }}</p>
                            <p class="text-[var(--text-secondary)]">
                                {{ $log->documento?->titulo ?? 'Documento removido' }}
                            </p>
                            <p class="text-[var(--text-secondary)]">{{ $log->message ?? 'Sem detalhes' }}</p>
                            <p class="text-xs text-[var(--text-secondary)] mt-1">{{ $log->attempted_at?->format('d/m/Y H:i') ?? 'Sem data' }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-[var(--text-secondary)]">Nenhuma sincronizacao registrada para esta conta ainda.</p>
                    @endforelse
                </div>
            </div>

            @can('delete', $driveAccount)
                <div class="surface panel-padding border border-red-900/30">
                    <p class="eyebrow text-red-400">Zona de perigo</p>
                    <p class="text-sm text-[var(--text-secondary)] mt-2 mb-4">
                        Remover a conta nao apaga os documentos do sistema, mas esta configuracao deixara de ser usada nas proximas sincronizacoes.
                    </p>
                    <form method="POST" action="{{ route('drive-accounts.destroy', $driveAccount) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button button-ghost w-full border-red-900/40 text-red-400 hover:bg-red-950/30">
                            Excluir conta
                        </button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
