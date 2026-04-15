<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#f5efe6">

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
                const themeMeta = document.querySelector('meta[name="theme-color"]');

                document.documentElement.dataset.theme = theme;

                if (themeMeta) {
                    themeMeta.setAttribute('content', theme === 'dark' ? '#0a0d14' : '#f4ede2');
                }
            })();
        </script>

        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body>
        @php
            $guestTopbarLinks = [
                [
                    'label' => 'Portal',
                    'href' => route('portal.index'),
                    'active' => request()->routeIs('portal.index'),
                ],
            ];
        @endphp

        <div class="app-shell">
            <div class="page-frame">
                <x-menu
                    menu-id="guest-app-menu-toggle"
                    :links="$guestTopbarLinks"
                >
                    <x-slot name="desktopActions">
                        <a class="button button-primary" href="{{ route('login') }}">Acesso</a>
                    </x-slot>

                    <x-slot name="mobileFooter">
                        <a class="button button-primary w-full" href="{{ route('login') }}">Acesso</a>
                    </x-slot>
                </x-menu>

                <section class="panel-padding">
                    {{ $slot }}
                </section>
            </div>
        </div>
    </body>
</html>
