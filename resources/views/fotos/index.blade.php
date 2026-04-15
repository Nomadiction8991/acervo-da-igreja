<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('igrejas.show', $igreja) }}" class="text-sm opacity-75 hover:underline">← {{ $igreja->nome_fantasia }}</a>
                <h1 class="section-title mt-1">Fotos</h1>
            </div>
            <a href="{{ route('fotos.create', $igreja) }}" class="button button-primary">+ Adicionar fotos</a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="status-banner status-banner--success mb-4">{{ session('success') }}</div>
    @endif

    @if ($errors->has('drive_sync'))
        <div class="status-banner status-banner--danger mb-4">{{ $errors->first('drive_sync') }}</div>
    @endif

    @if ($fotos->isEmpty())
        <div class="surface panel-padding text-center">
            <p class="text-[var(--text-secondary)]">Nenhuma foto cadastrada.</p>
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($fotos as $foto)
                <article class="surface panel-padding media-card">
                    <img src="{{ route('files.fotos.show', $foto) }}" alt="{{ $foto->nome_original }}" class="w-full h-48 object-cover rounded-xl mb-4">
                    <div class="media-card__footer">
                        <div class="text-sm media-card__details">
                            <p class="font-semibold">{{ $foto->nome_original }}</p>
                            <p class="text-[var(--text-secondary)]">{{ $foto->is_public ? 'Publica' : 'Privada' }}</p>
                            <p class="text-[var(--text-secondary)] mt-1">
                                Status Drive: {{ $foto->driveStatusLabel() }}
                            </p>
                            <p class="text-[var(--text-secondary)] mt-1">
                                @if ($foto->driveAccount)
                                    Drive: {{ $foto->driveAccount->nome }}{{ $foto->driveAccount->email ? ' · '.$foto->driveAccount->email : '' }}
                                @else
                                    Drive: sem conta vinculada
                                @endif
                            </p>
                        </div>
                        <div class="media-card__actions">
                            @can('update', $foto)
                                <form method="POST" action="{{ route('fotos.sync-drive', [$igreja, $foto]) }}" class="media-card__action-form">
                                    @csrf
                                    <button type="submit" class="button button-primary text-xs">Enviar ao Drive</button>
                                </form>
                            @endcan
                            <a href="{{ route('fotos.show', [$igreja, $foto]) }}" class="button button-muted text-xs">Ver</a>
                            <a href="{{ route('fotos.edit', [$igreja, $foto]) }}" class="button button-ghost text-xs">Editar</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $fotos->links() }}
        </div>
    @endif
</x-app-layout>
