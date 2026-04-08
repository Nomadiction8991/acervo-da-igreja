<x-app-layout>
    @section('title', 'Painel administrativo | Acervo da Igreja')

    <main class="space-y-6">

                {{-- Header --}}
                <section class="surface-strong panel-padding">
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                        <div>
                            <p class="eyebrow">Painel administrativo</p>
                            <h1 class="display-title mt-4">
                                Gestão de igrejas, documentos e auditoria.
                            </h1>
                        </div>
                        @can('create', App\Models\Igreja::class)
                            <a href="{{ route('igrejas.create') }}" class="button button-primary self-start whitespace-nowrap">
                                + Nova Igreja
                            </a>
                        @endcan
                    </div>

                    {{-- Cards de resumo --}}
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

                <div class="grid gap-6 2xl:grid-cols-[minmax(0,1.2fr)_minmax(320px,0.8fr)]">

                    {{-- Lista de igrejas recentes --}}
                    <section class="surface panel-padding">
                        <div class="flex items-center justify-between gap-3 mb-5">
                            <div>
                                <p class="eyebrow">Igrejas</p>
                                <h2 class="section-title mt-1">Últimas atualizadas</h2>
                            </div>
                            <a href="{{ route('igrejas.index') }}" class="button button-muted text-xs">Ver todas</a>
                        </div>

                        @if ($igrejas->isEmpty())
                            <div class="text-center py-8">
                                <p class="text-[var(--text-secondary)] mb-4">Nenhuma igreja cadastrada ainda.</p>
                                @can('create', App\Models\Igreja::class)
                                    <a href="{{ route('igrejas.create') }}" class="button button-primary">Cadastrar primeira igreja</a>
                                @endcan
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach ($igrejas as $igreja)
                                    <article class="linked-card" onclick="window.location='{{ route('igrejas.show', $igreja) }}'">
                                        <div>
                                            <p class="font-semibold tracking-[-0.02em] text-[var(--text-primary)]">
                                                {{ $igreja->nome_fantasia }}
                                            </p>
                                            <p class="mt-1 text-sm text-[var(--text-secondary)]">
                                                {{ $igreja->codigo_controle }}
                                                @if ($igreja->cidade)
                                                    · {{ $igreja->cidade }}, {{ $igreja->estado }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="flex flex-wrap gap-2 justify-end">
                                            <span class="stat-pill">{{ $igreja->fotos_count }} foto(s)</span>
                                            <a href="{{ route('igrejas.edit', $igreja) }}"
                                               class="button button-ghost text-xs"
                                               onclick="event.stopPropagation()">Editar</a>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </section>

                    {{-- Auditoria --}}
                    <section id="auditoria" class="surface panel-padding">
                        <div class="flex items-center justify-between gap-3 mb-5">
                            <div>
                                <p class="eyebrow">Auditoria</p>
                                <h2 class="section-title mt-1">Atividade recente</h2>
                            </div>
                            <span class="stat-pill">{{ $totalLogs }} total</span>
                        </div>

                        @if ($logs->isEmpty())
                            <p class="text-sm text-[var(--text-secondary)]">Nenhuma ação registrada ainda.</p>
                        @else
                            <div class="space-y-3">
                                @foreach ($logs as $log)
                                    <article class="linked-card linked-card--static">
                                        <div>
                                            <p class="font-semibold tracking-[-0.02em] text-[var(--text-primary)]">
                                                {{ $log->acao }} · {{ $log->modulo }}
                                            </p>
                                            <p class="mt-1 text-sm text-[var(--text-secondary)]">
                                                {{ $log->user?->name ?? 'Sistema' }}
                                                @if ($log->entidade)
                                                    · {{ $log->entidade }} #{{ $log->entidade_id }}
                                                @endif
                                            </p>
                                        </div>
                                        <span class="text-xs uppercase tracking-[0.12em] text-[var(--text-soft)] whitespace-nowrap">
                                            {{ $log->created_at->diffForHumans() }}
                                        </span>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </section>
                </div>
    </main>
</x-app-layout>
