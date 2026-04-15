<x-app-layout>
    @section('title', 'Painel administrativo | Acervo da Igreja')

    <main class="dashboard-shell">
        <section class="surface-strong panel-padding">
            <div class="dashboard-hero">
                <div>
                    <p class="eyebrow">Painel administrativo</p>
                    <h1 class="display-title mt-2">Gestão de igrejas, documentos e auditoria.</h1>
                    <p class="page-intro__text">
                        Uma interface mais editorial e menos genérica para acompanhar o acervo com clareza.
                    </p>
                </div>
            </div>

            <div class="summary-grid mt-8">
                <article class="metric-card">
                    <span class="metric-card__label">Igrejas</span>
                    <strong class="metric-card__value">{{ $totalIgrejas }}</strong>
                    <span class="metric-card__detail">
                        {{ $totalCidades }} {{ Str::plural('cidade', $totalCidades) }} cadastrada{{ $totalCidades !== 1 ? 's' : '' }}
                    </span>
                </article>
                <article class="metric-card">
                    <span class="metric-card__label">Fotos públicas</span>
                    <strong class="metric-card__value">{{ $totalFotosPublicas }}</strong>
                    <span class="metric-card__detail">Visíveis no portal público</span>
                </article>
                <article class="metric-card">
                    <span class="metric-card__label">Registros de auditoria</span>
                    <strong class="metric-card__value">{{ $totalLogs }}</strong>
                    <span class="metric-card__detail">Ações rastreadas no sistema</span>
                </article>
            </div>
        </section>

        <section class="dashboard-grid">
            <article class="surface panel-padding">
                <div class="section-head">
                    <div>
                        <p class="eyebrow">Igrejas</p>
                        <h2 class="section-title mt-2">Últimas atualizadas</h2>
                    </div>
                </div>

                @if ($igrejas->isEmpty())
                    <div class="empty-state mt-6">
                        <p>Nenhuma igreja cadastrada ainda.</p>
                    </div>
                @else
                    <div class="stack-list mt-6">
                        @foreach ($igrejas as $igreja)
                            <article class="linked-card" onclick="window.location='{{ route('igrejas.show', $igreja) }}'">
                                <div>
                                    <p class="linked-card__title">{{ $igreja->nome_fantasia }}</p>
                                    <p class="linked-card__detail">
                                        {{ $igreja->codigo_controle }}
                                        @if ($igreja->cidade)
                                            · {{ $igreja->cidade }}, {{ $igreja->estado }}
                                        @endif
                                    </p>
                                </div>

                                <div class="linked-card__actions">
                                    <span class="stat-pill">{{ $igreja->fotos_count }} foto(s)</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </article>

            <article id="auditoria" class="surface panel-padding">
                <div class="section-head">
                    <div>
                        <p class="eyebrow">Auditoria</p>
                        <h2 class="section-title mt-2">Atividade recente</h2>
                    </div>

                    <span class="stat-pill">{{ $totalLogs }} total</span>
                </div>

                @if ($logs->isEmpty())
                    <div class="empty-state mt-6">
                        <p>Nenhuma ação registrada ainda.</p>
                    </div>
                @else
                    <div class="stack-list mt-6">
                        @foreach ($logs as $log)
                            <article class="linked-card linked-card--static">
                                <div>
                                    <p class="linked-card__title">{{ $log->acao }} · {{ $log->modulo }}</p>
                                    <p class="linked-card__detail">
                                        {{ $log->user?->name ?? 'Sistema' }}
                                        @if ($log->entidade)
                                            · {{ $log->entidade }} #{{ $log->entidade_id }}
                                        @endif
                                    </p>
                                </div>

                                <span class="linked-card__stamp">{{ $log->created_at->diffForHumans() }}</span>
                            </article>
                        @endforeach
                    </div>
                @endif
            </article>
        </section>
    </main>
</x-app-layout>
