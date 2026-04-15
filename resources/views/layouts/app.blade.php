<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#f5efe6">

        <title>@yield('title', config('app.name', 'Acervo da Igreja'))</title>

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
            $topbarLinks = [
                [
                    'label' => 'Portal',
                    'href' => route('portal.index'),
                    'active' => request()->routeIs('portal.index'),
                ],
            ];

            if (auth()->check()) {
                array_unshift(
                    $topbarLinks,
                    [
                        'label' => 'Painel',
                        'href' => route('admin.dashboard'),
                        'active' => request()->routeIs('admin.dashboard'),
                    ],
                    [
                        'label' => 'Igrejas',
                        'href' => route('igrejas.index'),
                        'active' => request()->routeIs('igrejas.*'),
                    ],
                );

                if (auth()->user()->hasPermission('documentos.visualizar')) {
                    $topbarLinks[] = [
                        'label' => 'Documentos',
                        'href' => route('documentos.index'),
                        'active' => request()->routeIs('documentos.*'),
                    ];
                }

                if (auth()->user()->hasPermission('grupos_documentos.visualizar')) {
                    $topbarLinks[] = [
                        'label' => 'Grupos de Documentos',
                        'href' => route('grupo-documentos.index'),
                        'active' => request()->routeIs('grupo-documentos.*'),
                    ];
                }

                if (auth()->user()->hasPermission('drive_accounts.visualizar')) {
                    $topbarLinks[] = [
                        'label' => 'Contas Drive',
                        'href' => route('drive-accounts.index'),
                        'active' => request()->routeIs('drive-accounts.*'),
                    ];
                }

                if (auth()->user()->hasPermission('tarefas.visualizar')) {
                    $topbarLinks[] = [
                        'label' => 'Tarefas',
                        'href' => route('tarefas.index'),
                        'active' => request()->routeIs('tarefas.*'),
                    ];
                }

                if (auth()->user()->hasPermission('users.visualizar')) {
                    $topbarLinks[] = [
                        'label' => 'Usuários',
                        'href' => route('users.index'),
                        'active' => request()->routeIs('users.*'),
                    ];
                }

                if (auth()->user()->hasPermission('logs.visualizar')) {
                    $topbarLinks[] = [
                        'label' => 'Auditoria',
                        'href' => route('audit-logs.index'),
                        'active' => request()->routeIs('audit-logs.*'),
                    ];
                }

                $topbarLinks[] = [
                    'label' => 'Perfil',
                    'href' => route('profile.edit'),
                    'active' => request()->routeIs('profile.*'),
                ];

                $topbarLinks[] = [
                    'label' => 'Suporte',
                    'href' => 'https://suporte.anvy.com.br',
                    'active' => false,
                    'target' => '_blank',
                    'rel' => 'noopener noreferrer',
                ];
            }
        @endphp

        <div class="app-shell">
            <div class="page-frame space-y-6">
                <x-menu
                    menu-id="main-app-menu-toggle"
                    :links="$topbarLinks"
                    :mobile-description="auth()->check() ? 'Acesse cadastros e rotinas administrativas.' : null"
                >
                    <x-slot name="desktopActions">
                        @guest
                            <a class="button button-primary" href="{{ route('login') }}">Acesso</a>
                        @endguest

                        @auth
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="button button-primary" type="submit">Sair</button>
                            </form>
                        @endauth
                    </x-slot>

                    <x-slot name="mobileFooter">
                        @guest
                            <a class="button button-primary w-full" href="{{ route('login') }}">Acesso</a>
                        @endguest

                        @auth
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="button button-primary w-full" type="submit">Sair</button>
                            </form>
                        @endauth
                    </x-slot>
                </x-menu>

                @isset($header)
                    <section class="surface panel-padding page-header-shell">
                        {{ $header }}
                    </section>
                @endisset

                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </div>
        </div>

        <div id="page-loader" class="page-loader" aria-hidden="true">
            <div class="page-loader__panel" role="status" aria-live="polite" aria-label="Carregando">
                <span class="page-loader__spinner" aria-hidden="true"></span>
                <span class="page-loader__text">Carregando...</span>
            </div>
        </div>
    </body>
</html>
