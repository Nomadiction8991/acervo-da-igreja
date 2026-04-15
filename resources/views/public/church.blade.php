@extends('layouts.app')

@section('title', $church['name'].' | Acervo da Igreja')

@section('content')
    <div class="content-stack">
        <section class="surface-strong panel-padding">
            <div class="public-church-hero">
                <div>
                    <p class="eyebrow">{{ $church['city'] }} · {{ $church['district'] }}</p>
                    <h1 class="display-title mt-2">{{ $church['name'] }}</h1>
                    <p class="page-intro__text mt-4">{{ $church['summary'] }}</p>
                </div>

                <span class="chip chip--public">Visível</span>
            </div>

            <div class="summary-grid mt-8">
                <div class="metric-card">
                    <span class="metric-card__label">Fotos</span>
                    <strong class="metric-card__value">{{ count($church['public_gallery']) }}</strong>
                    <span class="metric-card__detail">Galeria filtrada para visitantes</span>
                </div>
                <div class="metric-card">
                    <span class="metric-card__label">Documentos</span>
                    <strong class="metric-card__value">{{ count($church['public_documents']) }}</strong>
                    <span class="metric-card__detail">Agrupados e liberados por permissão</span>
                </div>
                <div class="metric-card">
                    <span class="metric-card__label">Campos</span>
                    <strong class="metric-card__value">{{ $church['private_fields_count'] }}</strong>
                    <span class="metric-card__detail">Mantidos fora da página</span>
                </div>
                <div class="metric-card">
                    <span class="metric-card__label">Sincronização</span>
                    <strong class="metric-card__value">{{ $church['metrics']['sync_status'] }}</strong>
                    <span class="metric-card__detail">{{ $church['metrics']['synced_files'] }} arquivos espelhados</span>
                </div>
            </div>
        </section>

        <section class="content-grid content-grid--two-thirds">
            <article class="surface panel-padding">
                <p class="eyebrow">Dados para visitantes</p>
                <h2 class="section-title mt-2">Informacoes publicas</h2>

                <div class="stack-list mt-6">
                    @foreach ($church['public_fields'] as $field)
                        <div class="data-row">
                            <span class="data-row__label">{{ $field['label'] }}</span>
                            <span class="data-row__value">{{ $field['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </article>

            <aside class="content-stack">
                <section class="surface panel-padding">
                    <p class="eyebrow">Proteção automática</p>
                    <h2 class="section-title mt-2">Conteúdo reservado</h2>
                    <p class="page-intro__text mt-4">
                        {{ $church['private_fields_count'] }} campos, {{ $church['private_documents_count'] }} documentos
                        e {{ $church['private_gallery_count'] }} fotos seguem invisíveis no portal.
                    </p>

                    @auth
                        <div class="mt-6">
                            <a class="button button-primary" href="{{ route('admin.dashboard') }}">Ver painel</a>
                        </div>
                    @endauth
                </section>

                <section class="surface panel-padding">
                    <p class="eyebrow">Na mesma cidade</p>
                    <h2 class="section-title mt-2">Outras igrejas em {{ $church['city'] }}</h2>

                    <div class="stack-list mt-6">
                        @foreach ($relatedChurches as $relatedChurch)
                            <a class="linked-card" href="{{ route('portal.church', $relatedChurch['slug']) }}">
                                <div>
                                    <p class="linked-card__title">{{ $relatedChurch['name'] }}</p>
                                    <p class="linked-card__detail">{{ $relatedChurch['district'] }}</p>
                                </div>

                                <span class="stat-pill">{{ $relatedChurch['metrics']['public_documents'] }} docs</span>
                            </a>
                        @endforeach
                    </div>
                </section>
            </aside>
        </section>

        <section class="content-grid content-grid--two-thirds">
            <article class="surface panel-padding">
                <div class="section-head">
                    <div>
                        <p class="eyebrow">Galeria</p>
                        <h2 class="section-title mt-2">Fotos públicas</h2>
                    </div>

                    <span class="stat-pill">{{ count($church['public_gallery']) }} visíveis</span>
                </div>

                <div class="gallery-grid mt-6">
                    @foreach ($church['public_gallery'] as $photo)
                        <article class="gallery-card">
                            <div class="gallery-card__cover"></div>
                            <p class="gallery-card__title">{{ $photo['label'] }}</p>
                        </article>
                    @endforeach
                </div>
            </article>

            <article class="surface panel-padding">
                <div class="section-head">
                    <div>
                        <p class="eyebrow">Documentação</p>
                        <h2 class="section-title mt-2">Documentos públicos</h2>
                    </div>

                    <span class="chip chip--public">{{ count($church['public_documents']) }} liberados</span>
                </div>

                <div class="stack-list mt-6">
                    @foreach ($church['public_documents'] as $document)
                        <article class="doc-row">
                            <div>
                                <p class="doc-row__title">{{ $document['title'] }}</p>
                                <p class="doc-row__detail">{{ $document['group'] }} · {{ $document['type'] }}</p>
                            </div>

                            <span class="chip chip--public">{{ $document['visibility'] }}</span>
                        </article>
                    @endforeach
                </div>
            </article>
        </section>
    </div>
@endsection
