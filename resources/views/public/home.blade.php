@extends('layouts.app')

@section('title', 'Acervo da Igreja | Portal público')

@section('content')
    <div class="content-stack">
        <section class="surface-strong panel-padding">
            <div class="public-hero">
                <div class="public-hero__copy">
                    <p class="eyebrow">Portal público + operação administrativa</p>
                    <h1 class="display-title mt-2">
                        Cada igreja publica o que deve ser visto. O restante continua reservado dentro do sistema.
                    </h1>
                    <p class="page-intro__text">
                        A interface foi desenhada para ser clara para visitantes e consistente para a equipe interna, sem
                        parecer um dashboard genérico.
                    </p>
                </div>

                <div class="public-hero__aside surface-inset">
                    <p class="mini-label">Faixa vitral</p>
                    <div class="vitrail-band mt-4"></div>
                    <p class="mini-detail mt-4">
                        Uma assinatura visual única que conecta portal, gestão e consulta documental.
                    </p>
                </div>
            </div>

            <div class="summary-grid mt-8">
                <div class="metric-card">
                    <span class="metric-card__label">Igrejas</span>
                    <strong class="metric-card__value">{{ $stats['churches'] }}</strong>
                    <span class="metric-card__detail">{{ $stats['cities'] }} cidades agrupadas</span>
                </div>
                <div class="metric-card">
                    <span class="metric-card__label">Documentos liberados</span>
                    <strong class="metric-card__value">{{ $stats['public_documents'] }}</strong>
                    <span class="metric-card__detail">Filtrados pelo backend antes de aparecer</span>
                </div>
                <div class="metric-card">
                    <span class="metric-card__label">Fotos públicas</span>
                    <strong class="metric-card__value">{{ $stats['public_photos'] }}</strong>
                    <span class="metric-card__detail">Galerias sem expor material interno</span>
                </div>
                <div class="metric-card">
                    <span class="metric-card__label">Arquivos sincronizados</span>
                    <strong class="metric-card__value">{{ $stats['synced_files'] }}</strong>
                    <span class="metric-card__detail">Servidor local e cópia no Drive</span>
                </div>
            </div>
        </section>

        <section class="content-grid content-grid--two-thirds">
            <article class="surface panel-padding">
                <div class="section-head">
                    <div>
                        <p class="eyebrow">Cidades</p>
                        <h2 class="section-title mt-2">Igrejas por contexto urbano</h2>
                    </div>

                    <a class="button button-primary" href="#governanca">Ver governança</a>
                </div>

                <div class="stack-list mt-6">
                    @foreach ($cityGroups as $group)
                        <article class="city-card">
                            <div class="city-card__head">
                                <div>
                                    <p class="city-card__title">{{ $group['city'] }}</p>
                                    <p class="city-card__detail">{{ $group['state'] }}</p>
                                </div>

                                <div class="city-card__metrics">
                                    <span class="stat-pill">{{ $group['church_count'] }} igrejas</span>
                                    <span class="stat-pill">{{ $group['public_documents'] }} docs</span>
                                    <span class="stat-pill">{{ $group['public_photos'] }} fotos</span>
                                </div>
                            </div>

                            <div class="city-card__churches">
                                @foreach ($group['churches'] as $church)
                                    <article class="linked-card linked-card--static">
                                        <div>
                                            <p class="linked-card__title">{{ $church['name'] }}</p>
                                            <p class="linked-card__detail">{{ $church['district'] }} · {{ $church['city'] }}</p>
                                        </div>

                                        <a class="button button-muted" href="{{ route('portal.church', $church['slug']) }}">Abrir</a>
                                    </article>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            </article>

            <aside id="governanca" class="content-stack">
                <section class="surface panel-padding">
                    <p class="eyebrow">Mapa operacional</p>
                    <h2 class="section-title mt-2">O mesmo sistema controla exibição pública, trabalho interno e auditoria.</h2>

                    <div class="stack-list mt-6">
                        <div class="info-row">
                            <span class="info-row__title">Visibilidade por campo</span>
                            <span class="info-row__detail">Nome, endereço e CEP podem ser públicos; matrícula continua privada.</span>
                        </div>
                        <div class="info-row">
                            <span class="info-row__title">Fotos e documentos</span>
                            <span class="info-row__detail">Cada item escolhe se entra no portal ou permanece apenas no admin.</span>
                        </div>
                        <div class="info-row">
                            <span class="info-row__title">Logs e permissões</span>
                            <span class="info-row__detail">Toda ação sensível pode ser rastreada por usuário, módulo, antes e depois.</span>
                        </div>
                        <div class="info-row">
                            <span class="info-row__title">Storage híbrido</span>
                            <span class="info-row__detail">Servidor local com cópia no Google Drive e fila de reenvio quando necessário.</span>
                        </div>
                    </div>
                </section>

                <section class="surface panel-padding">
                    @if ($featuredChurch)
                        <p class="eyebrow">Igreja em destaque</p>
                        <h2 class="section-title mt-2">{{ $featuredChurch['name'] }}</h2>
                        <p class="page-intro__text mt-4">{{ $featuredChurch['summary'] }}</p>

                        <div class="stack-list mt-6">
                            @foreach ($featuredChurch['public_fields'] as $field)
                                <div class="data-row">
                                    <span class="data-row__label">{{ $field['label'] }}</span>
                                    <span class="data-row__value">{{ $field['value'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="chip-row mt-6">
                            <span class="chip chip--public">{{ $featuredChurch['metrics']['public_documents'] }} docs públicos</span>
                            <span class="chip">{{ $featuredChurch['private_fields_count'] }} campos reservados</span>
                        </div>
                    @else
                        <p class="eyebrow">Igreja em destaque</p>
                        <h2 class="section-title mt-2">Nenhuma igreja cadastrada ainda</h2>
                        <p class="page-intro__text mt-4">
                            Quando houver registros no diretório, esta área vai mostrar um destaque com os campos públicos.
                        </p>
                    @endif
                </section>
            </aside>
        </section>
    </div>
@endsection
