@extends('layouts.app')

@section('title', 'Acervo da Igreja | Portal público')

@section('content')
    <div class="page-frame space-y-6">
        <header class="topbar surface">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-4">
                    <a class="brand-lockup" href="{{ route('portal.index') }}">
                        <span class="brand-mark">AP</span>
                        <span>
                            <span class="brand-title">Acervo da Igreja</span>
                            <span class="brand-subtitle">Portal público e governança interna de igrejas</span>
                        </span>
                    </a>
                </div>

                <div class="ml-auto flex flex-col gap-3 sm:flex-row sm:items-center">
                    <nav class="flex flex-wrap gap-2">
                        <a class="nav-link nav-link--active" href="{{ route('portal.index') }}">Portal</a>
                        <a class="nav-link" href="#cidades">Cidades</a>
                        <a class="nav-link" href="#governanca">Governança</a>
                    </nav>

                    <div class="flex items-center gap-2">
                        <button class="theme-toggle" type="button" data-theme-toggle>
                            <span class="theme-toggle__orb" aria-hidden="true"></span>
                            <span data-theme-label>Tema claro</span>
                        </button>

                        @auth
                            <a class="button button-primary" href="{{ route('admin.dashboard') }}">Painel</a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="button button-muted" type="submit">Sair</button>
                            </form>
                        @else
                            <a class="button button-primary" href="{{ route('login') }}">Acesso</a>
                        @endauth
                    </div>
                </div>
            </div>

            <div class="mt-5 vitrail-band"></div>
        </header>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.3fr)_minmax(320px,0.7fr)]">
            <article class="surface-strong panel-padding">
                <p class="eyebrow">Portal público + operação administrativa</p>
                <h1 class="display-title mt-4">
                    Cada igreja publica o que deve ser visto. O restante continua reservado dentro do sistema.
                </h1>
                <p class="mt-5 max-w-3xl text-base leading-7 text-[var(--text-secondary)]">
                    Esta primeira interface já separa bem três camadas do produto: diretório público, administração privada
                    e governança documental. O desenho foi pensado para secretarias, administradores e equipes pastorais que
                    precisam localizar rápido, decidir com segurança e manter rastreabilidade.
                </p>

                <div class="summary-grid mt-8">
                    <div class="metric-card">
                        <span class="metric-card__label">Igrejas públicas</span>
                        <strong class="metric-card__value">{{ $stats['churches'] }}</strong>
                        <span class="metric-card__detail">{{ $stats['cities'] }} cidades agrupadas por contexto</span>
                    </div>
                    <div class="metric-card">
                        <span class="metric-card__label">Documentos liberados</span>
                        <strong class="metric-card__value">{{ $stats['public_documents'] }}</strong>
                        <span class="metric-card__detail">Filtrados pelo backend antes de chegar ao visitante</span>
                    </div>
                    <div class="metric-card">
                        <span class="metric-card__label">Fotos públicas</span>
                        <strong class="metric-card__value">{{ $stats['public_photos'] }}</strong>
                        <span class="metric-card__detail">Galerias sem expor material interno</span>
                    </div>
                    <div class="metric-card">
                        <span class="metric-card__label">Arquivos sincronizados</span>
                        <strong class="metric-card__value">{{ $stats['synced_files'] }}</strong>
                        <span class="metric-card__detail">Servidor local mais cópia no Google Drive</span>
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a class="button button-primary" href="#cidades">Explorar igrejas</a>
                    @auth
                        <a class="button button-muted" href="{{ route('admin.dashboard') }}">Abrir painel administrativo</a>
                    @endauth
                </div>
            </article>

            <aside class="surface panel-padding">
                <div class="flex items-center justify-between gap-3">
                    <p class="eyebrow">Mapa operacional</p>
                    <span class="chip chip--public">Portal ativo</span>
                </div>

                <h2 class="section-title mt-4">Cidades, igrejas e camadas de visibilidade</h2>
                <p class="mt-4 leading-7 text-[var(--text-secondary)]">
                    A leitura pública nasce por cidade, mas o sistema continua pronto para tarefas, documentos, permissões e
                    auditoria por igreja.
                </p>

                <div class="mt-6 space-y-4">
                    @foreach ($cityGroups as $group)
                        <div class="city-summary">
                            <div>
                                <p class="city-summary__title">{{ $group['city'] }}</p>
                                <p class="city-summary__detail">{{ $group['church_count'] }} igrejas com portal público</p>
                            </div>

                            <div class="flex flex-wrap justify-end gap-2">
                                <span class="stat-pill">{{ $group['public_documents'] }} docs</span>
                                <span class="stat-pill">{{ $group['public_photos'] }} fotos</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="surface-inset mt-6">
                    <div class="flex items-center justify-between gap-3">
                        <span class="font-semibold text-[var(--text-primary)]">Faixa vitral</span>
                        <span class="text-xs uppercase tracking-[0.16em] text-[var(--text-soft)]">Assinatura</span>
                    </div>

                    <div class="mt-4 vitrail-band"></div>

                    <p class="mt-4 text-sm leading-6 text-[var(--text-secondary)]">
                        O mesmo gesto visual conecta portal, tarefas, permissões e documentos, sem cair no dashboard genérico
                        de SaaS.
                    </p>
                </div>
            </aside>
        </section>

        <section id="cidades" class="grid gap-6 xl:grid-cols-[minmax(0,1.45fr)_minmax(320px,0.55fr)]">
            <div class="space-y-6">
                @foreach ($cityGroups as $group)
                    <article class="surface panel-padding">
                        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                            <div>
                                <p class="eyebrow">{{ $group['state'] }}</p>
                                <h2 class="section-title mt-2">{{ $group['city'] }}</h2>
                                <p class="mt-3 max-w-2xl text-sm leading-6 text-[var(--text-secondary)]">
                                    Igrejas agrupadas para facilitar navegação pública, sem perder contexto da cidade e da
                                    cobertura documental disponível.
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <span class="stat-pill">{{ $group['church_count'] }} igrejas</span>
                                <span class="stat-pill">{{ $group['public_documents'] }} documentos públicos</span>
                                <span class="stat-pill">{{ $group['public_photos'] }} fotos públicas</span>
                            </div>
                        </div>

                        <div class="mt-6 grid gap-4 md:grid-cols-2">
                            @foreach ($group['churches'] as $church)
                                <article class="church-card church-card--{{ $church['tone'] }}">
                                    <div class="church-card__cover"></div>

                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h3 class="text-lg font-semibold tracking-[-0.03em] text-[var(--text-primary)]">
                                                {{ $church['name'] }}
                                            </h3>
                                            <p class="mt-1 text-sm text-[var(--text-secondary)]">
                                                {{ $church['district'] }} • {{ $church['city'] }}
                                            </p>
                                        </div>

                                        <span class="chip chip--public">Público</span>
                                    </div>

                                    <p class="text-sm leading-6 text-[var(--text-secondary)]">{{ $church['summary'] }}</p>

                                    <div class="grid gap-3 sm:grid-cols-2">
                                        <div class="surface-inset surface-inset--compact">
                                            <span class="mini-label">Documentos liberados</span>
                                            <strong class="mini-value">{{ $church['metrics']['public_documents'] }}</strong>
                                            <span class="mini-detail">Somente grupos públicos</span>
                                        </div>

                                        <div class="surface-inset surface-inset--compact">
                                            <span class="mini-label">Fotos visíveis</span>
                                            <strong class="mini-value">{{ $church['metrics']['public_photos'] }}</strong>
                                            <span class="mini-detail">Primeira foto como destaque</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-sm text-[var(--text-soft)]">
                                            {{ $church['private_fields_count'] }} campos privados seguem protegidos
                                        </span>

                                        <a class="button button-muted" href="{{ route('portal.church', $church['slug']) }}">
                                            Ver igreja
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>

            <aside id="governanca" class="space-y-6">
                <section class="surface panel-padding">
                    <p class="eyebrow">Governança embutida</p>
                    <h2 class="section-title mt-4">O mesmo sistema controla exibição pública, trabalho interno e auditoria.</h2>

                    <div class="mt-6 space-y-4">
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
                    <p class="eyebrow">Igreja em destaque</p>
                    <h2 class="section-title mt-4">{{ $featuredChurch['name'] }}</h2>
                    <p class="mt-4 text-sm leading-6 text-[var(--text-secondary)]">{{ $featuredChurch['summary'] }}</p>

                    <div class="mt-6 grid gap-3">
                        @foreach ($featuredChurch['public_fields'] as $field)
                            <div class="data-row">
                                <span class="data-row__label">{{ $field['label'] }}</span>
                                <span class="data-row__value">{{ $field['value'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 flex flex-wrap gap-2">
                        <span class="chip chip--public">{{ $featuredChurch['metrics']['public_documents'] }} docs públicos</span>
                        <span class="chip chip--private">{{ $featuredChurch['private_fields_count'] }} campos reservados</span>
                    </div>
                </section>
            </aside>
        </section>
    </div>
@endsection
