<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('fotos.show', [$igreja, $foto]) }}" class="text-sm opacity-75 hover:underline">← {{ $foto->nome_original }}</a>
            <h1 class="section-title mt-1">Editar foto</h1>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <form method="POST" action="{{ route('fotos.update', [$igreja, $foto]) }}" class="surface panel-padding space-y-5">
            @csrf
            @method('PATCH')

            <label class="flex items-center gap-2 text-sm">
                <input type="hidden" name="is_public" value="0">
                <input type="checkbox" name="is_public" value="1" @checked(old('is_public', $foto->is_public))>
                Foto publica
            </label>

            <label class="flex items-center gap-2 text-sm">
                <input type="hidden" name="is_principal" value="0">
                <input type="checkbox" name="is_principal" value="1" @checked(old('is_principal', $foto->is_principal))>
                Foto principal
            </label>

            <div class="flex gap-3">
                <button type="submit" class="button button-primary">Salvar</button>
                <a href="{{ route('fotos.show', [$igreja, $foto]) }}" class="button button-muted">Cancelar</a>
            </div>
        </form>

        <div class="surface panel-padding">
            <img src="{{ route('files.fotos.show', $foto) }}" alt="{{ $foto->nome_original }}" class="w-full rounded-xl mb-4">
            <form method="POST" action="{{ route('fotos.destroy', [$igreja, $foto]) }}">
                @csrf
                @method('DELETE')
                <button class="button button-ghost w-full border-red-900/40 text-red-400" type="submit" onclick="return confirm('Remover esta foto?')">
                    Deletar foto
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
