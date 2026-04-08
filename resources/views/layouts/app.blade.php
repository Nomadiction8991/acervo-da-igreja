<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#f5efe6">

        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

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

                document.documentElement.dataset.theme = theme;
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="app-shell">
            <div class="page-frame space-y-6">
                <header class="topbar surface">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <a class="brand-lockup" href="{{ route('portal.index') }}">
                            <span>
                                <span class="brand-title">{{ config('app.name', 'Acervo da Igreja') }}</span>
                                <span class="brand-subtitle">Conta autenticada da equipe interna</span>
                            </span>
                        </a>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end flex-1">
                            <nav class="flex flex-wrap gap-1">
                                <a class="nav-link {{ request()->routeIs('portal.index') ? 'active' : '' }}" href="{{ route('portal.index') }}">Portal</a>
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Painel</a>
                                <a class="nav-link {{ request()->routeIs('relatorios.dashboard') ? 'active' : '' }}" href="{{ route('relatorios.dashboard') }}">Relatórios</a>
                                @auth
                                    @if (auth()->user()->hasPermission('igrejas.visualizar'))
                                        <a class="nav-link {{ request()->routeIs('igrejas.*') ? 'active' : '' }}" href="{{ route('igrejas.index') }}">Igrejas</a>
                                    @endif
                                    @if (auth()->user()->hasPermission('documentos.visualizar'))
                                        <a class="nav-link {{ request()->routeIs('documentos.*') ? 'active' : '' }}" href="{{ route('documentos.index') }}">Documentos</a>
                                    @endif
                                    @if (auth()->user()->hasPermission('drive_accounts.visualizar'))
                                        <a class="nav-link {{ request()->routeIs('drive-accounts.*') ? 'active' : '' }}" href="{{ route('drive-accounts.index') }}">Contas Drive</a>
                                    @endif
                                    @if (auth()->user()->hasPermission('tarefas.visualizar'))
                                        <a class="nav-link {{ request()->routeIs('tarefas.*') ? 'active' : '' }}" href="{{ route('tarefas.index') }}">Tarefas</a>
                                    @endif
                                    @if (auth()->user()->hasPermission('tags.visualizar'))
                                        <a class="nav-link {{ request()->routeIs('tags.*') ? 'active' : '' }}" href="{{ route('tags.index') }}">Tags</a>
                                    @endif
                                    @if (auth()->user()->hasPermission('users.visualizar'))
                                        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">Usuários</a>
                                    @endif
                                    @if (auth()->user()->hasPermission('logs.visualizar'))
                                        <a class="nav-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}" href="{{ route('audit-logs.index') }}">Auditoria</a>
                                    @endif
                                @endauth
                                <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">Perfil</a>
                                <a class="nav-link" href="https://suporte.anvy.com.br" target="_blank" rel="noopener noreferrer">Suporte</a>
                            </nav>

                            <div class="flex items-center gap-2">
                                <button class="theme-toggle" type="button" data-theme-toggle>
                                    <span class="theme-toggle__orb" aria-hidden="true"></span>
                                    <span data-theme-label>Tema claro</span>
                                </button>

                                @auth
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="button button-primary" type="submit">Sair</button>
                                    </form>
                                @else
                                    <a class="button button-primary" href="{{ route('login') }}">Acesso</a>
                                @endauth
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 vitrail-band"></div>
                </header>

                @isset($header)
                    <section class="surface panel-padding">
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
    </body>
</html>
