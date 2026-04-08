<div class="field-block">
    <label class="field-block__label" for="nome">Nome do grupo</label>
    <input
        id="nome"
        name="nome"
        type="text"
        class="field-control"
        value="{{ old('nome', $grupoDocumento?->nome) }}"
        required
    >
</div>

<div class="field-block">
    <label class="field-block__label" for="descricao">Descricao</label>
    <textarea
        id="descricao"
        name="descricao"
        rows="4"
        class="field-control"
    >{{ old('descricao', $grupoDocumento?->descricao) }}</textarea>
</div>

<label class="flex items-center gap-2 text-sm">
    <input type="checkbox" name="publico_padrao" value="1" @checked(old('publico_padrao', $grupoDocumento?->publico_padrao))>
    Documentos deste grupo sao publicos por padrao
</label>

<div class="flex gap-3">
    <button type="submit" class="button button-primary">
        {{ $grupoDocumento ? 'Salvar alteracoes' : 'Criar grupo' }}
    </button>
    <a href="{{ $grupoDocumento ? route('grupo-documentos.show', $grupoDocumento) : route('grupo-documentos.index') }}" class="button button-muted">
        Cancelar
    </a>
</div>
