<x-app-layout>
    <x-slot name="header">
        <div class="page-intro page-intro--split">
            <div class="page-intro__copy">
                <a href="{{ route('portal.show', $documento->igreja) }}" class="breadcrumb-trail">← {{ $documento->igreja->nome_fantasia }}</a>
                <p class="eyebrow mt-4">Documento público</p>
                <h1 class="display-title mt-2">{{ $documento->titulo }}</h1>
                <p class="page-intro__text">
                    {{ $documento->descricao ?: 'Arquivo liberado para visualização pública.' }}
                </p>
            </div>

            <div class="page-intro__side">
                <a href="{{ route('files.documentos.preview', $documento) }}" target="_blank" class="button button-primary">Abrir em nova aba</a>
                <a href="{{ route('files.documentos.show', $documento) }}" class="button button-muted">Baixar</a>
            </div>
        </div>
    </x-slot>

    <section class="portal-document-grid">
        <article class="surface-strong panel-padding">
            @if ($documento->supportsInlinePreview())
                <iframe
                    src="{{ route('files.documentos.preview', $documento) }}"
                    title="{{ $documento->titulo }}"
                    class="doc-preview"
                ></iframe>
            @else
                <div class="empty-state doc-preview-empty">
                    <p class="section-title">Visualizacao integrada indisponivel</p>
                    <p class="mt-3 text-secondary">
                        Abra o arquivo em uma nova aba ou faça o download para consultar o documento.
                    </p>
                </div>
            @endif
        </article>

        <aside class="surface panel-padding portal-document-aside">
            <p class="eyebrow">Resumo</p>
            <h2 class="section-title mt-2">Informacoes do arquivo</h2>

            <div class="stack-list mt-6">
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
                    <span class="data-row__label">Arquivo</span>
                    <span class="data-row__value">{{ $documento->fileName() }}</span>
                </div>
            </div>

            @if ($documento->descricao)
                <div class="surface-inset mt-6">
                    <p class="mini-label">Descricao</p>
                    <p class="mini-detail mt-2">{{ $documento->descricao }}</p>
                </div>
            @endif
        </aside>
    </section>
</x-app-layout>
