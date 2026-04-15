<x-app-layout>
    <x-slot name="header">
        <div class="page-intro page-intro--split">
            <div class="page-intro__copy">
                <a href="{{ route('portal.index') }}" class="breadcrumb-trail">← Voltar ao portal</a>
                <p class="eyebrow mt-4">{{ $igreja->cidade ?? 'Portal público' }}</p>
                <h1 class="display-title mt-2">{{ $igreja->nome_fantasia }}</h1>
                <p class="page-intro__text">
                    {{ $igreja->esCampoPublico('endereco') && $igreja->endereco ? $igreja->endereco : 'Esta igreja aparece com os campos liberados para visitantes.' }}
                </p>
            </div>

            <div class="page-intro__side">
                <span class="chip chip--public">Visível</span>
                @auth
                    @can('update', $igreja)
                        <a href="{{ route('igrejas.edit', $igreja) }}" class="button button-primary">Editar no painel</a>
                    @endcan
                @endauth
            </div>
        </div>
    </x-slot>

    @php
        $fotoPrincipal = $igreja->fotos->first();
        $publicPhotos = $igreja->fotos;
        $publicDocuments = $igreja->documentos;
    @endphp

    <section class="portal-show-grid">
        <article class="surface-strong panel-padding">
            <div class="church-hero">
                @if ($fotoPrincipal)
                    <img
                        src="{{ route('files.fotos.show', $fotoPrincipal) }}"
                        alt="{{ $igreja->nome_fantasia }}"
                        class="church-hero__image"
                    >
                @else
                    <div class="church-hero__placeholder">
                        <span class="church-hero__placeholder-label">Sem foto pública</span>
                    </div>
                @endif
            </div>

            <div class="summary-grid mt-6">
                <div class="metric-card">
                    <span class="metric-card__label">Fotos</span>
                    <strong class="metric-card__value">{{ $publicPhotos->count() }}</strong>
                    <span class="metric-card__detail">Galeria liberada para visitantes</span>
                </div>
                <div class="metric-card">
                    <span class="metric-card__label">Documentos</span>
                    <strong class="metric-card__value">{{ $publicDocuments->count() }}</strong>
                    <span class="metric-card__detail">Itens públicos disponíveis</span>
                </div>
                <div class="metric-card">
                    <span class="metric-card__label">Campos ocultos</span>
                    <strong class="metric-card__value">{{ $igreja->private_fields_count ?? 0 }}</strong>
                    <span class="metric-card__detail">Dados internos não exibidos</span>
                </div>
            </div>
        </article>

        <aside class="surface panel-padding portal-show-aside">
            <p class="eyebrow">Resumo público</p>
            <h2 class="section-title mt-2">Dados para visitantes</h2>

            <div class="stack-list mt-6">
                @if ($igreja->esCampoPublico('endereco') && $igreja->endereco)
                    <div class="data-row">
                        <span class="data-row__label">Endereço</span>
                        <span class="data-row__value">{{ $igreja->endereco }}</span>
                    </div>
                @endif

                @if ($igreja->esCampoPublico('cidade') && $igreja->cidade)
                    <div class="data-row">
                        <span class="data-row__label">Cidade</span>
                        <span class="data-row__value">{{ $igreja->cidade }}, {{ $igreja->estado }}</span>
                    </div>
                @endif

                @if ($igreja->esCampoPublico('cep') && $igreja->cep)
                    <div class="data-row">
                        <span class="data-row__label">CEP</span>
                        <span class="data-row__value">{{ $igreja->cep }}</span>
                    </div>
                @endif
            </div>

            <div class="surface-inset mt-6">
                <p class="mini-label">Proteção automática</p>
                <p class="mini-value">{{ $igreja->private_fields_count ?? 0 }} campos reservados</p>
                <p class="mini-detail mt-2">
                    Documentos e fotos sem liberação seguem fora do portal.
                </p>
            </div>
        </aside>
    </section>

    <section class="portal-show-grid portal-show-grid--secondary">
        <article class="surface panel-padding">
            <div class="section-head">
                <div>
                    <p class="eyebrow">Galeria</p>
                    <h2 class="section-title mt-2">Fotos públicas</h2>
                </div>

                <span class="stat-pill">{{ $publicPhotos->count() }} visíveis</span>
            </div>

            @if ($publicPhotos->isEmpty())
                <div class="empty-state mt-6">
                    <p>Nenhuma foto pública cadastrada.</p>
                </div>
            @else
                <div class="gallery-grid mt-6">
                    @foreach ($publicPhotos as $photo)
                        <article class="gallery-card">
                            <img
                                src="{{ route('files.fotos.show', $photo) }}"
                                alt="Foto pública"
                                class="gallery-card__image"
                            >
                        </article>
                    @endforeach
                </div>
            @endif
        </article>

        <article class="surface panel-padding">
            <div class="section-head">
                <div>
                    <p class="eyebrow">Documentação</p>
                    <h2 class="section-title mt-2">Documentos públicos</h2>
                </div>

                <span class="chip chip--public">{{ $publicDocuments->count() }} liberados</span>
            </div>

            @if ($publicDocuments->isEmpty())
                <div class="empty-state mt-6">
                    <p>Nenhum documento público cadastrado.</p>
                </div>
            @else
                <div class="stack-list mt-6">
                    @foreach ($publicDocuments as $document)
                        <article class="doc-row">
                            <div>
                                <p class="doc-row__title">{{ $document->titulo }}</p>
                                <p class="doc-row__detail">
                                    {{ $document->grupoDocumento?->nome ?? 'Sem grupo' }} · {{ $document->tipo }}
                                </p>
                            </div>

                            <a class="button button-muted" href="{{ route('portal.documentos.show', $document) }}">
                                Abrir
                            </a>
                        </article>
                    @endforeach
                </div>
            @endif
        </article>
    </section>

    <section class="surface panel-padding">
        <p class="eyebrow">Contexto</p>
        <h2 class="section-title mt-2">Como esta página separa conteúdo institucional de operação interna</h2>

        <div class="summary-grid mt-6">
            <div class="surface-inset">
                <span class="mini-label">Visibilidade por campo</span>
                <strong class="mini-value">{{ $igreja->private_fields_count ?? 0 }} campos internos</strong>
                <span class="mini-detail">Matrícula, razão social e dados sensíveis continuam protegidos.</span>
            </div>
            <div class="surface-inset">
                <span class="mini-label">Arquivos reservados</span>
                <strong class="mini-value">{{ $igreja->private_documents_count ?? 0 }} privados</strong>
                <span class="mini-detail">O portal só exibe o que foi liberado.</span>
            </div>
            <div class="surface-inset">
                <span class="mini-label">Fotos reservadas</span>
                <strong class="mini-value">{{ $igreja->private_gallery_count ?? 0 }} internas</strong>
                <span class="mini-detail">Acervo operacional fica fora da navegação pública.</span>
            </div>
        </div>
    </section>
</x-app-layout>
