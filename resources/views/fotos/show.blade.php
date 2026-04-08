<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('fotos.index', $igreja) }}" class="text-sm opacity-75 hover:underline">← Fotos</a>
                <h1 class="section-title mt-1">{{ $foto->nome_original }}</h1>
            </div>
            <div class="flex gap-2">
                @can('update', $foto)
                    <form method="POST" action="{{ route('fotos.sync-drive', [$igreja, $foto]) }}">
                        @csrf
                        <button type="submit" class="button button-primary">Enviar ao Drive</button>
                    </form>
                @endcan
                <a href="{{ route('fotos.edit', [$igreja, $foto]) }}" class="button button-primary">Editar</a>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="status-banner status-banner--success mb-4">{{ session('success') }}</div>
    @endif

    @if ($errors->has('drive_sync'))
        <div class="status-banner status-banner--danger mb-4">{{ $errors->first('drive_sync') }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <div class="surface panel-padding">
            <img src="{{ route('files.fotos.show', $foto) }}" alt="{{ $foto->nome_original }}" class="w-full rounded-xl">
        </div>
        <div class="surface panel-padding space-y-4">
            <div class="data-row">
                <span class="data-row__label">Publica</span>
                <span class="data-row__value">{{ $foto->is_public ? 'Sim' : 'Nao' }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Principal</span>
                <span class="data-row__value">{{ $foto->is_principal ? 'Sim' : 'Nao' }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Tipo</span>
                <span class="data-row__value">{{ $foto->mime_type }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Tamanho</span>
                <span class="data-row__value">{{ number_format($foto->tamanho / 1024, 1, ',', '.') }} KB</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Status Drive</span>
                <span class="data-row__value">{{ $foto->driveStatusLabel() }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Conta Drive</span>
                <span class="data-row__value">{{ $foto->driveAccount?->nome ?? 'Sem sincronizacao configurada' }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Email Drive</span>
                <span class="data-row__value">{{ $foto->driveAccount?->email ?? 'Nao informado' }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Ultimo sync</span>
                <span class="data-row__value">{{ $foto->synced_at?->format('d/m/Y H:i') ?? 'nunca' }}</span>
            </div>
            @if ($foto->drive_link)
                <div>
                    <a href="{{ $foto->drive_link }}" target="_blank" class="text-sm hover:underline">Abrir no Drive</a>
                </div>
            @endif
            @if ($foto->sync_error)
                <p class="text-sm text-red-400">{{ $foto->sync_error }}</p>
            @endif
        </div>
    </div>
</x-app-layout>
