<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('documentos.index') }}" class="text-sm opacity-75 hover:underline">← Documentos</a>
                <h1 class="section-title mt-1">{{ $documento->titulo }}</h1>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('files.documentos.show', $documento) }}" class="button button-muted">Baixar</a>
                @can('update', $documento)
                    @if ($documento->driveAccount)
                        <form method="POST" action="{{ route('documentos.sync-drive', $documento) }}">
                            @csrf
                            <button type="submit" class="button button-primary">Enviar ao Drive</button>
                        </form>
                    @endif
                @endcan
                @can('update', $documento)
                    <a href="{{ route('documentos.edit', $documento) }}" class="button button-primary">Editar</a>
                @endcan
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="status-banner status-banner--success mb-4">{{ session('success') }}</div>
    @endif

    @if ($errors->has('drive_sync'))
        <div class="status-banner status-banner--danger mb-4">{{ $errors->first('drive_sync') }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
        <div class="surface panel-padding space-y-4">
            <div class="data-row">
                <span class="data-row__label">Igreja</span>
                <span class="data-row__value">{{ $documento->igreja->nome_fantasia }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Grupo</span>
                <span class="data-row__value">{{ $documento->grupoDocumento?->nome ?? 'Sem grupo' }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Tipo</span>
                <span class="data-row__value">{{ $documento->tipo }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Visibilidade</span>
                <span class="data-row__value">{{ $documento->publico ? 'Publico' : 'Privado' }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Conta Drive</span>
                <span class="data-row__value">{{ $documento->driveAccount?->nome ?? 'Sem sincronizacao configurada' }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Email da conta Drive</span>
                <span class="data-row__value">{{ $documento->driveAccount?->email ?? 'Nao informado' }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Pasta destino</span>
                <span class="data-row__value">{{ $documento->drive_folder_id ?? ($documento->driveAccount?->folder_id ?? 'Pasta padrao da conta ou sem sync') }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Status Drive</span>
                <span class="data-row__value">{{ $documento->driveStatusLabel() }}</span>
            </div>
            @if ($documento->descricao)
                <div>
                    <p class="field-block__label mb-2">Descricao</p>
                    <p class="text-sm text-[var(--text-secondary)]">{{ $documento->descricao }}</p>
                </div>
            @endif
        </div>

        <div class="space-y-4">
            <div class="surface panel-padding">
                <p class="eyebrow mb-3">Google Drive</p>
                <div class="space-y-2 text-sm text-[var(--text-secondary)]">
                    <p>Conta: {{ $documento->driveAccount?->nome ?? 'Nenhuma selecionada' }}</p>
                    <p>Email: {{ $documento->driveAccount?->email ?? 'Nao informado' }}</p>
                    <p>Pasta: {{ $documento->drive_folder_id ?? ($documento->driveAccount?->folder_id ?? 'Pasta padrao da conta') }}</p>
                    <p>Status Drive: {{ $documento->driveStatusLabel() }}</p>
                    <p>Ultimo sync: {{ $documento->synced_at?->format('d/m/Y H:i') ?? 'nunca' }}</p>
                    @if ($documento->drive_link)
                        <p><a href="{{ $documento->drive_link }}" target="_blank" class="hover:underline">Abrir no Drive</a></p>
                    @endif
                    @if ($documento->sync_error)
                        <p class="text-red-400">{{ $documento->sync_error }}</p>
                    @endif
                </div>
            </div>

            <div class="surface panel-padding">
                <p class="eyebrow mb-3">Historico de sync</p>
                <div class="space-y-3">
                    @forelse ($documento->fileSyncLogs as $log)
                        <div class="border border-[var(--border-subtle)] rounded-lg p-3 text-sm">
                            <p class="font-semibold">{{ $log->status }}</p>
                            <p class="text-[var(--text-secondary)]">{{ $log->message ?? 'Sem mensagem' }}</p>
                            <p class="text-xs text-[var(--text-secondary)] mt-1">{{ $log->attempted_at?->format('d/m/Y H:i') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-[var(--text-secondary)]">Nenhuma tentativa registrada.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
