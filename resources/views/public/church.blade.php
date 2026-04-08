@extends('layouts.app')

@section('title', $church['name'].' | Acervo da Igreja')

@section('content')
    <div class="page-frame space-y-6">
        <header class="topbar surface">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-col gap-3">
                    <a class="brand-lockup" href="{{ route('portal.index') }}">
                        <span class="brand-mark">AP</span>
                        <span>
                            <span class="brand-title">Acervo da Igreja</span>
                            <span class="brand-subtitle">Página pública da igreja</span>
                        </span>
                    </a>

                    <div class="breadcrumb-trail">
                        <a href="{{ route('portal.index') }}">Portal</a>
                        <span>/</span>
                        <span>{{ $church['city'] }}</span>
                        <span>/</span>
                        <span>{{ $church['name'] }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button class="theme-toggle" type="button" data-theme-toggle>
                        <span class="theme-toggle__orb" aria-hidden="true"></span>
                        <span data-theme-label>Tema claro</span>
                    </button>

                    <a class="button button-muted" href="{{ route('portal.index') }}">Voltar ao portal</a>
                    @auth
                        <a class="button button-primary" href="{{ route('admin.dashboard') }}">Painel</a>
                    @else
                        <a class="button button-primary" href="{{ route('login') }}">Acesso seguro</a>
                    @endauth
                </div>
            </div>

            <div class="mt-5 vitrail-band"></div>
        </header>

        <section class="grid gap-6 2xl:grid-cols-[minmax(0,1.2fr)_360px]">
            <article class="surface-strong panel-padding">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="eyebrow">{{ $church['city'] }} • {{ $church['district'] }}</p>
                        <h1 class="display-title mt-4">{{ $church['name'] }}</h1>
                        <p class="mt-5 max-w-3xl text-base leading-7 text-[var(--text-secondary)]">{{ $church['summary'] }}</p>
                    </div>

                    <span class="chip chip--public">Somente conteúdo público</span>
                </div>

                <div class="church-hero church-card church-card--{{ $church['tone'] }} mt-8">
                    <div class="church-card__cover church-card__cover--hero"></div>
                </div>

                <div class="summary-grid mt-8">
                    <div class="metric-card">
                        <span class="metric-card__label">Fotos públicas</span>
                        <strong class="metric-card__value">{{ count($church['public_gallery']) }}</strong>
                        <span class="metric-card__detail">Galeria filtrada para visitantes</span>
                    </div>
                    <div class="metric-card">
                        <span class="metric-card__label">Documentos públicos</span>
                        <strong class="metric-card__value">{{ count($church['public_documents']) }}</strong>
                        <span class="metric-card__detail">Agrupados e publicados por permissão</span>
                    </div>
                    <div class="metric-card">
                        <span class="metric-card__label">Campos reservados</span>
                        <strong class="metric-card__value">{{ $church['private_fields_count'] }}</strong>
                        <span class="metric-card__detail">Mantidos fora da página pública</span>
                    </div>
                    <div class="metric-card">
                        <span class="metric-card__label">Sincronização</span>
                        <strong class="metric-card__value">{{ $church['metrics']['sync_status'] }}</strong>
                        <span class="metric-card__detail">{{ $church['metrics']['synced_files'] }} arquivos espelhados</span>
                    </div>
                </div>
            </article>

            <aside class="surface panel-padding self-start 2xl:sticky 2xl:top-6">
                <p class="eyebrow">Resumo público</p>
                <h2 class="section-title mt-4">Dados liberados para visitantes</h2>

                <div class="mt-6 grid gap-3">
                    @foreach ($church['public_fields'] as $field)
                        <div class="data-row">
                            <span class="data-row__label">{{ $field['label'] }}</span>
                            <span class="data-row__value">{{ $field['value'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="surface-inset mt-6">
                    <span class="mini-label">Proteção automática</span>
                    <p class="mt-3 text-sm leading-6 text-[var(--text-secondary)]">
                        {{ $church['private_fields_count'] }} campos privados, {{ $church['private_documents_count'] }} documentos
                        internos e {{ $church['private_gallery_count'] }} fotos reservadas seguem invisíveis no portal.
                    </p>
                </div>

                <div class="mt-6 flex flex-wrap gap-2">
                    <a class="button button-primary" href="{{ route('admin.dashboard') }}">Ver painel</a>
                    <a class="button button-muted" href="{{ route('login') }}">Fluxo de login</a>
                </div>
            </aside>
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(320px,0.85fr)]">
            <article class="surface panel-padding">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="eyebrow">Galeria</p>
                        <h2 class="section-title mt-2">Fotos públicas</h2>
                    </div>

                    <span class="stat-pill">{{ count($church['public_gallery']) }} visíveis</span>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    @foreach ($church['public_gallery'] as $photo)
                        <article class="gallery-card church-card church-card--{{ $church['tone'] }}">
                            <div class="church-card__cover gallery-card__cover"></div>
                            <h3 class="text-base font-semibold tracking-[-0.02em] text-[var(--text-primary)]">{{ $photo['label'] }}</h3>
                            <p class="text-sm text-[var(--text-secondary)]">Disponível para o visitante, sem exposição de arquivos internos.</p>
                        </article>
                    @endforeach
                </div>
            </article>

            <article class="surface panel-padding">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="eyebrow">Documentação</p>
                        <h2 class="section-title mt-2">Documentos públicos</h2>
                    </div>

                    <span class="chip chip--public">{{ count($church['public_documents']) }} liberados</span>
                </div>

                <div class="mt-6 space-y-3">
                    @foreach ($church['public_documents'] as $document)
                        <article class="doc-row">
                            <div>
                                <p class="font-semibold tracking-[-0.02em] text-[var(--text-primary)]">{{ $document['title'] }}</p>
                                <p class="mt-1 text-sm text-[var(--text-secondary)]">
                                    {{ $document['group'] }} • {{ $document['type'] }} • {{ $document['updated_at'] }}
                                </p>
                            </div>

                            <span class="chip chip--public">{{ $document['visibility'] }}</span>
                        </article>
                    @endforeach
                </div>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(320px,0.75fr)]">
            <article class="surface panel-padding">
                <p class="eyebrow">Segurança do portal</p>
                <h2 class="section-title mt-4">Como esta página separa conteúdo institucional de operação interna</h2>

                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="surface-inset">
                        <span class="mini-label">Visibilidade por campo</span>
                        <strong class="mini-value">{{ $church['private_fields_count'] }} campos internos</strong>
                        <span class="mini-detail">Matrícula, razão social e dados sensíveis ficam protegidos.</span>
                    </div>
                    <div class="surface-inset">
                        <span class="mini-label">Arquivos reservados</span>
                        <strong class="mini-value">{{ $church['private_documents_count'] }} privados</strong>
                        <span class="mini-detail">Documentos administrativos não vazam para visitantes.</span>
                    </div>
                    <div class="surface-inset">
                        <span class="mini-label">Fotos reservadas</span>
                        <strong class="mini-value">{{ $church['private_gallery_count'] }} internas</strong>
                        <span class="mini-detail">Áreas sensíveis e acervo operacional continuam fora do portal.</span>
                    </div>
                </div>
            </article>

            @if ($relatedChurches->isNotEmpty())
                <aside class="surface panel-padding">
                    <p class="eyebrow">Na mesma cidade</p>
                    <h2 class="section-title mt-4">Outras igrejas em {{ $church['city'] }}</h2>

                    <div class="mt-6 space-y-3">
                        @foreach ($relatedChurches as $relatedChurch)
                            <a class="linked-card" href="{{ route('portal.church', $relatedChurch['slug']) }}">
                                <div>
                                    <p class="font-semibold tracking-[-0.02em] text-[var(--text-primary)]">{{ $relatedChurch['name'] }}</p>
                                    <p class="mt-1 text-sm text-[var(--text-secondary)]">{{ $relatedChurch['district'] }}</p>
                                </div>

                                <span class="stat-pill">{{ $relatedChurch['metrics']['public_documents'] }} docs</span>
                            </a>
                        @endforeach
                    </div>
                </aside>
            @endif
        </section>
    </div>
@endsection
