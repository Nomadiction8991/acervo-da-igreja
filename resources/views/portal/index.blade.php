<x-app-layout>
    @if ($igrejas->isEmpty())
        <section class="empty-state">
            <p class="section-title">Nenhuma igreja cadastrada.</p>
            <p class="mt-3 text-secondary">Quando houver registros públicos, eles aparecerão aqui em cards por cidade e por igreja.</p>
        </section>
    @else
        <section class="church-grid">
            @foreach ($igrejas as $igreja)
                @php
                    $fotoPrincipal = $igreja->fotosPublicas()->first();
                @endphp

                <article class="church-card">
                    <div class="church-card__media">
                        @if ($fotoPrincipal)
                            <img
                                src="{{ route('files.fotos.show', $fotoPrincipal) }}"
                                alt="{{ $igreja->nome_fantasia }}"
                                class="church-card__image"
                            >
                        @else
                            <div class="church-card__fallback">
                                <span class="church-card__fallback-label">Sem foto pública</span>
                            </div>
                        @endif
                    </div>

                    <div class="church-card__body">
                        <div class="church-card__head">
                            <div>
                                <p class="eyebrow">{{ $igreja->cidade ?? 'Cidade não informada' }}</p>
                                <h2 class="section-title mt-2">
                                    <a href="{{ route('portal.show', $igreja) }}" class="church-card__title">
                                        {{ $igreja->nome_fantasia }}
                                    </a>
                                </h2>
                            </div>

                            <span class="chip chip--public">Visível</span>
                        </div>

                        <div class="church-card__details">
                            @if ($igreja->esCampoPublico('cidade'))
                                <div class="data-row">
                                    <span class="data-row__label">Cidade</span>
                                    <span class="data-row__value">{{ $igreja->cidade }}, {{ $igreja->estado }}</span>
                                </div>
                            @endif

                            @if ($igreja->esCampoPublico('endereco'))
                                <div class="data-row">
                                    <span class="data-row__label">Endereço</span>
                                    <span class="data-row__value">{{ $igreja->endereco }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="church-card__footer">
                            <span class="stat-pill">{{ $igreja->fotos->count() }} fotos</span>
                            <span class="stat-pill">{{ $igreja->documentos->count() }} documentos</span>
                            <a href="{{ route('portal.show', $igreja) }}" class="button button-primary">Ver detalhes</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        <div class="pagination-shell">
            {{ $igrejas->links() }}
        </div>
    @endif
</x-app-layout>
