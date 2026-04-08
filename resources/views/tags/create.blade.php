<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('tags.index') }}" class="text-sm opacity-75 hover:underline">← Tags</a>
        <h1 class="section-title mt-1">Nova Tag</h1>
    </x-slot>

    <form method="POST" action="{{ route('tags.store') }}" class="surface panel-padding max-w-2xl space-y-5">
        @csrf

        <div class="field-block">
            <label class="field-block__label" for="nome">Nome *</label>
            <input id="nome" name="nome" type="text" class="field-control @error('nome') border-red-500 @enderror" value="{{ old('nome') }}" required>
            @error('nome') <p class="field-errors">{{ $message }}</p> @enderror
        </div>

        <div class="field-block">
            <label class="field-block__label" for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" class="field-control" rows="3">{{ old('descricao') }}</textarea>
        </div>

        <div class="field-block">
            <label class="field-block__label" for="cor">Cor</label>
            <div class="flex gap-2">
                <input id="cor" name="cor" type="color" class="field-control w-20" value="{{ old('cor', '#3B82F6') }}">
                <input type="text" class="field-control flex-1" disabled value="{{ old('cor', '#3B82F6') }}" id="corText">
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="button button-primary">Criar Tag</button>
            <a href="{{ route('tags.index') }}" class="button button-muted">Cancelar</a>
        </div>
    </form>

    <script>
        document.getElementById('cor').addEventListener('change', (e) => {
            document.getElementById('corText').value = e.target.value;
        });
    </script>
</x-app-layout>
