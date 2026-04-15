@props([
    'menuId' => 'app-menu-toggle',
    'brandHref' => null,
    'brandTitle' => null,
    'links' => [],
    'mobileEyebrow' => 'Menu',
    'mobileDescription' => null,
])

@php
    $brandHref = $brandHref ?? route('portal.index');
    $brandTitle = $brandTitle ?? config('app.name', 'Acervo da Igreja');
    $normalizedLinks = collect($links)
        ->filter(static fn ($link): bool => is_array($link) && filled($link['label'] ?? null) && filled($link['href'] ?? null))
        ->values();
    $hasDesktopActions = isset($desktopActions) && trim((string) $desktopActions) !== '';
    $hasMobileFooter = isset($mobileFooter) && trim((string) $mobileFooter) !== '';
@endphp

<div class="app-menu-shell" data-menu-root>
    <input id="{{ $menuId }}" class="app-menu__toggle" type="checkbox" data-menu-toggle>

    <header class="app-menu surface">
        <div class="app-menu__bar panel-padding">
            <div class="app-menu__content">
                <div class="app-menu__left">
                    <a class="app-menu__brand" href="{{ $brandHref }}" aria-label="{{ $brandTitle }}">
                        <span class="app-menu__brand-mark" aria-hidden="true"></span>
                    </a>

                    @if ($normalizedLinks->isNotEmpty())
                        <nav class="app-menu__nav" aria-label="Navegação principal">
                            @foreach ($normalizedLinks as $link)
                                <a
                                    class="app-menu__link {{ !empty($link['active']) ? 'app-menu__link--active' : '' }}"
                                    href="{{ $link['href'] }}"
                                    @if (!empty($link['target'])) target="{{ $link['target'] }}" @endif
                                    @if (!empty($link['rel'])) rel="{{ $link['rel'] }}" @endif
                                >
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                        </nav>
                    @endif
                </div>

                <div class="app-menu__right">
                    <div class="app-menu__actions">
                        @if ($hasDesktopActions)
                            {{ $desktopActions }}
                        @endif

                        <button class="theme-toggle app-menu__theme" type="button" data-theme-toggle aria-label="Alternar tema">
                            <span class="theme-toggle__icon theme-toggle__icon--sun" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="4.5" />
                                    <path d="M12 2.5v2" />
                                    <path d="M12 19.5v2" />
                                    <path d="M4.93 4.93l1.41 1.41" />
                                    <path d="M17.66 17.66l1.41 1.41" />
                                    <path d="M2.5 12h2" />
                                    <path d="M19.5 12h2" />
                                    <path d="M4.93 19.07l1.41-1.41" />
                                    <path d="M17.66 6.34l1.41-1.41" />
                                </svg>
                            </span>
                            <span class="theme-toggle__icon theme-toggle__icon--moon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 14.8A8.5 8.5 0 1 1 9.2 3a7 7 0 0 0 11.8 11.8Z" />
                                </svg>
                            </span>
                        </button>
                    </div>

                    <div class="app-menu__mobile-actions">
                        <button class="theme-toggle app-menu__theme" type="button" data-theme-toggle aria-label="Alternar tema">
                            <span class="theme-toggle__icon theme-toggle__icon--sun" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="4.5" />
                                    <path d="M12 2.5v2" />
                                    <path d="M12 19.5v2" />
                                    <path d="M4.93 4.93l1.41 1.41" />
                                    <path d="M17.66 17.66l1.41 1.41" />
                                    <path d="M2.5 12h2" />
                                    <path d="M19.5 12h2" />
                                    <path d="M4.93 19.07l1.41-1.41" />
                                    <path d="M17.66 6.34l1.41-1.41" />
                                </svg>
                            </span>
                            <span class="theme-toggle__icon theme-toggle__icon--moon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 14.8A8.5 8.5 0 1 1 9.2 3a7 7 0 0 0 11.8 11.8Z" />
                                </svg>
                            </span>
                        </button>

                        <label class="button button-muted app-menu__icon-button" for="{{ $menuId }}" aria-label="Abrir menu">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M4 7h16" />
                                <path d="M4 12h16" />
                                <path d="M4 17h16" />
                            </svg>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="app-menu__drawer" aria-hidden="true">
        <label class="app-menu__backdrop" for="{{ $menuId }}" aria-label="Fechar menu"></label>

        <aside class="app-menu__panel surface-strong">
            <div class="app-menu__panel-body panel-padding">
                <div class="app-menu__panel-head">
                    <div>
                        <p class="eyebrow">{{ $mobileEyebrow }}</p>
                        @if (filled($mobileDescription))
                            <p class="app-menu__panel-description">{{ $mobileDescription }}</p>
                        @endif
                    </div>

                    <label class="button button-muted app-menu__icon-button" for="{{ $menuId }}" aria-label="Fechar menu">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M6 6l12 12" />
                            <path d="M18 6L6 18" />
                        </svg>
                    </label>
                </div>

                @if ($normalizedLinks->isNotEmpty())
                    <nav class="app-menu__panel-nav" aria-label="Navegação móvel">
                        @foreach ($normalizedLinks as $link)
                            <a
                                class="app-menu__link app-menu__link--block {{ !empty($link['active']) ? 'app-menu__link--active' : '' }}"
                                href="{{ $link['href'] }}"
                                data-menu-nav-link
                                @if (!empty($link['target'])) target="{{ $link['target'] }}" @endif
                                @if (!empty($link['rel'])) rel="{{ $link['rel'] }}" @endif
                            >
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </nav>
                @endif

                @if ($hasMobileFooter)
                    <div class="app-menu__panel-footer">
                        {{ $mobileFooter }}
                    </div>
                @endif
            </div>
        </aside>
    </div>
</div>
