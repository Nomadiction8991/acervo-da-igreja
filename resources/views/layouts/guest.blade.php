<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#f5efe6">

        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <title>{{ config('app.name', 'Acervo da Igreja') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=JetBrains+Mono:wght@500;600&family=Manrope:wght@400;500;600;700;800&display=swap"
            rel="stylesheet"
        >

        <script>
            (() => {
                const storedTheme = localStorage.getItem('acervo-igreja-theme');
                const theme = storedTheme ?? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

                document.documentElement.dataset.theme = theme;
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="app-shell">
            <div class="page-frame">
                <section class="guest-shell surface">
                    <div class="panel-padding">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <a class="brand-lockup" href="{{ route('portal.index') }}">
                                <img src="{{ asset('logo-acervo.png') }}" alt="Logo Acervo da Igreja" class="brand-mark-img">
                                <span>
                                    <span class="brand-title">{{ config('app.name', 'Acervo da Igreja') }}</span>
                                    <span class="brand-subtitle">Fluxos protegidos da equipe interna</span>
                                </span>
                            </a>

                            <div class="flex items-center gap-2">
                                <button class="theme-toggle" type="button" data-theme-toggle>
                                    <span class="theme-toggle__orb" aria-hidden="true"></span>
                                    <span data-theme-label>Tema claro</span>
                                </button>

                                <a class="button button-muted" href="{{ route('portal.index') }}">Voltar ao portal</a>
                            </div>
                        </div>

                        <div class="mt-5 vitrail-band"></div>
                    </div>

                    <div class="guest-shell__body">
                        {{ $slot }}
                    </div>
                </section>
            </div>
        </div>
    </body>
</html>
