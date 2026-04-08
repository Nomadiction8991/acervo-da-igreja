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

    <div class="surface rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[var(--border-subtle)]">
                    <th class="px-4 py-3 text-left">Conta</th>
                    <th class="px-4 py-3 text-left hidden md:table-cell">Folder</th>
                    <th class="px-4 py-3 text-left">Conexao</th>
                    <th class="px-4 py-3 text-left hidden lg:table-cell">Syncs</th>
                    <th class="px-4 py-3 text-right">Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($driveAccounts as $driveAccount)
                    <tr class="border-b border-[var(--border-subtle)] last:border-0">
                        <td class="px-4 py-4">
                            <a href="{{ route('drive-accounts.show', $driveAccount) }}" class="font-semibold hover:underline">
                                {{ $driveAccount->nome }}
                            </a>
                            <div class="text-xs text-[var(--text-secondary)] mt-1">
                                {{ $driveAccount->email ?? 'Email sera preenchido apos o teste de conexao' }}
                            </div>
                        </td>
                        <td class="px-4 py-4 hidden md:table-cell">
                            <span class="text-[var(--text-secondary)]">{{ $driveAccount->folder_id ?? 'Pasta raiz da conta' }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="chip chip--{{ $driveAccount->refresh_token ? 'public' : 'private' }}">
                                {{ $driveAccount->refresh_token ? 'Conectada' : 'Pendente' }}
                            </span>
                            <div class="text-xs text-[var(--text-secondary)] mt-1">
                                {{ data_get($driveAccount->metadata, 'connection_method') ?? 'manual ou oauth' }}
                            </div>
                        </td>
                        <td class="px-4 py-4 hidden lg:table-cell">{{ $driveAccount->file_sync_logs_count }}</td>
                        <td class="px-4 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('drive-accounts.show', $driveAccount) }}" class="button button-muted text-xs">Ver</a>
                                @can('update', $driveAccount)
                                    <a href="{{ route('drive-accounts.edit', $driveAccount) }}" class="button button-ghost text-xs">Editar</a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-[var(--text-secondary)]">
                            Nenhuma conta Google Drive cadastrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $driveAccounts->links() }}
    </div>
</x-app-layout>
