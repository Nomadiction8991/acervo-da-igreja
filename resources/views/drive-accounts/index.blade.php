<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Integracoes</p>
                <h1 class="section-title mt-1">Contas Google Drive</h1>
            </div>
            @can('create', App\Models\DriveAccount::class)
                <a href="{{ route('drive-accounts.create') }}" class="button button-primary">+ Nova conta</a>
            @endcan
        </div>
    </x-slot>

    @if (session('success'))
        <div class="status-banner status-banner--success mb-4">{{ session('success') }}</div>
    @endif

    @if ($errors->has('drive_account'))
        <div class="status-banner status-banner--danger mb-4">{{ $errors->first('drive_account') }}</div>
    @endif

    <div class="resource-table-shell">
        <div class="resource-table-scroll">
        <table class="resource-table">
            <thead>
                <tr>
                    <th>Conta</th>
                    <th class="hidden md:table-cell">Folder</th>
                    <th>Conexao</th>
                    <th class="hidden lg:table-cell">Syncs</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($driveAccounts as $driveAccount)
                    <tr>
                        <td>
                            <div class="resource-table__main">
                                <a href="{{ route('drive-accounts.show', $driveAccount) }}" class="resource-table__title">
                                    {{ $driveAccount->nome }}
                                </a>
                                <div class="resource-table__meta">
                                    {{ $driveAccount->email ?? 'Email sera preenchido apos o teste de conexao' }}
                                </div>
                            </div>
                        </td>
                        <td class="hidden md:table-cell">
                            <span class="resource-table__meta">{{ $driveAccount->folder_id ?? 'Pasta raiz da conta' }}</span>
                        </td>
                        <td>
                            <div class="resource-table__stack">
                            <span class="chip chip--{{ $driveAccount->refresh_token ? 'public' : 'private' }}">
                                {{ $driveAccount->refresh_token ? 'Conectada' : 'Pendente' }}
                            </span>
                            <div class="resource-table__subtle">
                                {{ data_get($driveAccount->metadata, 'connection_method') ?? 'manual ou oauth' }}
                            </div>
                            </div>
                        </td>
                        <td class="hidden lg:table-cell"><span class="resource-table__count">{{ $driveAccount->file_sync_logs_count }}</span></td>
                        <td>
                            <div class="resource-table__actions">
                                <a href="{{ route('drive-accounts.show', $driveAccount) }}" class="button button-muted text-xs">Ver</a>
                                @can('update', $driveAccount)
                                    <a href="{{ route('drive-accounts.edit', $driveAccount) }}" class="button button-ghost text-xs">Editar</a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="resource-table__empty">
                            Nenhuma conta Google Drive cadastrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $driveAccounts->links() }}
    </div>
</x-app-layout>
