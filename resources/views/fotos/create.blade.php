<x-app-layout>
    @section('title', 'Adicionar Fotos')

    <section class="surface panel-padding max-w-2xl">
        <p class="eyebrow">Fotos</p>
        <h1 class="section-title mt-4">Adicionar Fotos</h1>

        <form method="POST" action="{{ route('fotos.store', $igreja) }}" enctype="multipart/form-data" class="mt-6 space-y-6">
            @csrf

            <div>
                <label for="fotos" class="field-block__label block">Selecione as fotos *</label>
                <input type="file" id="fotos" name="fotos[]" multiple accept="image/*"
                       class="field-control mt-2 @error('fotos') border-red-500 @enderror" required>
                <p class="text-sm text-[var(--text-secondary)] mt-2">Máximo 5MB por arquivo. Formatos: JPG, PNG, GIF, WebP.</p>
                @error('fotos')
                    <p class="field-errors">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="checkbox-line">
                    <input type="checkbox" name="is_public" value="1" checked>
                    <span>Fotos públicas (visíveis no portal)</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="button button-primary">Enviar</button>
                <a href="{{ route('igrejas.show', $igreja) }}" class="button button-muted">Cancelar</a>
            </div>
        </form>
    </section>
</x-app-layout>
