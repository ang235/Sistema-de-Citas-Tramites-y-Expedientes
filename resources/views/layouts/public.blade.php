<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    {{-- SEO --}}
    <title>@yield('title', 'Portal Ciudadano') — SETC</title>
    <meta name="description" content="@yield('meta_description', 'Sistema Estatal de Citas, Trámites y Expedientes. Realiza tus trámites de forma digital, rápida y segura.')">
    <meta name="robots" content="index, follow">

    {{-- Open Graph --}}
    <meta property="og:title"       content="@yield('title', 'Portal Ciudadano') — SETC">
    <meta property="og:description" content="@yield('meta_description', 'Portal ciudadano del SETC.')">
    <meta property="og:type"        content="website">

    {{-- Icons --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    {{-- Fuentes del sistema SETC --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Plus+Jakarta+Sans:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    {{-- Vite assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Slots de head adicionales --}}
    @stack('head')
</head>
<body class="min-h-screen flex flex-col" style="background-color: var(--color-background); color: var(--color-on-surface);">

    {{-- ═══════════════════════════════════════════════════════ HEADER PÚBLICO ══ --}}
    <header id="main-header" class="fixed top-0 left-0 right-0 z-50"
            style="background-color: rgba(255,255,255,0.92); backdrop-filter: blur(16px); box-shadow: var(--shadow-header);">
        <div class="h-20 max-w-screen-xl mx-auto px-6 lg:px-12 flex items-center justify-between gap-4">

            {{-- Logo + Nombre --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0" aria-label="Ir al inicio SETC">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                     style="background-color: var(--color-primary-container);">
                    <span class="material-symbols-outlined text-white text-lg">account_balance</span>
                </div>
                <div class="flex flex-col leading-tight">
                    <span class="text-headline-sm" style="color: var(--color-primary); font-family: var(--font-display);">SETC</span>
                    <span class="text-label-sm" style="color: var(--color-on-surface-variant);">Portal Ciudadano</span>
                </div>
            </a>

            {{-- Buscador (desktop) --}}
            <div class="hidden xl:flex flex-1 max-w-md mx-6">
                <div class="relative w-full">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-sm pointer-events-none"
                          style="color: var(--color-on-surface-variant);">search</span>
                    <input type="text"
                           class="input pl-9"
                           placeholder="Buscar trámites, requisitos o folios..."
                           aria-label="Buscador de trámites">
                </div>
            </div>

            {{-- Navegación principal (desktop) --}}
            <nav class="hidden lg:flex items-center gap-1 shrink-0" role="navigation" aria-label="Navegación principal">
                @foreach ($publicNav ?? [] as $item)
                    <a href="{{ $item['url'] }}"
                       class="px-3 py-2 rounded-lg text-label-md transition-colors"
                       style="{{ Request::is($item['match'] ?? $item['url']) ? 'background-color: var(--color-surface-container); color: var(--color-on-surface); font-weight:600;' : 'color: var(--color-on-surface-variant);' }}"
                       @if(Request::is($item['match'] ?? $item['url'])) aria-current="page" @endif>
                        {{ $item['label'] }}
                    </a>
                @endforeach

                {{-- Nav por defecto si no se pasan ítems --}}
                @if(empty($publicNav))
                    @foreach([
                        ['label'=>'Inicio',              'url'=>'/',               'match'=>'/'],
                        ['label'=>'Trámites y Servicios','url'=>'/tramites',       'match'=>'tramites*'],
                        ['label'=>'Mis Expedientes',     'url'=>'/expedientes',    'match'=>'expedientes*'],
                        ['label'=>'Agendar Cita',        'url'=>'/citas',          'match'=>'citas*'],
                        ['label'=>'Mesa de Ayuda',       'url'=>'/ayuda',          'match'=>'ayuda*'],
                    ] as $item)
                        <a href="{{ $item['url'] }}"
                           class="px-3 py-2 rounded-lg text-label-md transition-colors"
                           style="{{ Request::is($item['match']) ? 'background-color: var(--color-surface-container); color: var(--color-on-surface); font-weight:600;' : 'color: var(--color-on-surface-variant);' }}"
                           @if(Request::is($item['match'])) aria-current="page" @endif>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                @endif
            </nav>

            {{-- Acciones de usuario --}}
            <div class="flex items-center gap-2 shrink-0">

                {{-- Notificaciones --}}
                @auth
                    <div class="relative">
                        <button class="btn-ghost relative" aria-label="Notificaciones" id="btn-notifications">
                            <span class="material-symbols-outlined">notifications</span>
                            @if(($notificationCount ?? 0) > 0)
                                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full ring-2 ring-white"
                                      style="background-color: var(--color-secondary);"></span>
                            @endif
                        </button>
                    </div>
                @endauth

                {{-- Usuario autenticado --}}
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center gap-2.5 pl-2 rounded-lg py-1.5 pr-3 transition-colors"
                                style="transition: background-color 150ms ease;"
                                @mouseenter="$el.style.backgroundColor='var(--color-surface-container-low)'"
                                @mouseleave="$el.style.backgroundColor='transparent'"
                                id="btn-user-menu"
                                aria-haspopup="true"
                                :aria-expanded="open">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm"
                                 style="background-color: var(--color-secondary);">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden sm:flex flex-col text-left">
                                <span class="text-label-md font-semibold leading-none" style="color: var(--color-on-surface);">
                                    {{ Str::limit(auth()->user()->name, 18) }}
                                </span>
                                <span class="text-label-sm leading-tight" style="color: var(--color-on-surface-variant);">
                                    {{ auth()->user()->email ? Str::limit(auth()->user()->email, 20) : 'Ciudadano' }}
                                </span>
                            </div>
                            <span class="material-symbols-outlined text-sm" style="color: var(--color-on-surface-variant);">expand_more</span>
                        </button>

                        {{-- Dropdown usuario --}}
                        <div x-show="open" @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="dropdown-menu absolute right-0 top-full mt-2"
                             role="menu" aria-labelledby="btn-user-menu">
                            <a href="{{ route('profile.show') }}" class="dropdown-item" role="menuitem">
                                <span class="material-symbols-outlined text-base">person</span>
                                Mi Perfil
                            </a>
                            <a href="{{ route('expedientes.index') }}" class="dropdown-item" role="menuitem">
                                <span class="material-symbols-outlined text-base">folder_open</span>
                                Mis Expedientes
                            </a>
                            <a href="{{ route('citas.index') }}" class="dropdown-item" role="menuitem">
                                <span class="material-symbols-outlined text-base">calendar_month</span>
                                Mis Citas
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item danger" role="menuitem">
                                    <span class="material-symbols-outlined text-base">logout</span>
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn-secondary hidden sm:inline-flex">
                        <span class="material-symbols-outlined text-base">login</span>
                        Ingresar
                    </a>
                    <a href="{{ route('register') }}" class="btn-primary">
                        <span class="material-symbols-outlined text-base">person_add</span>
                        Registrarse
                    </a>
                @endauth

                {{-- Hamburguesa mobile --}}
                <button class="btn-ghost lg:hidden" id="btn-mobile-menu" aria-label="Menú" aria-expanded="false" aria-controls="mobile-menu">
                    <span class="material-symbols-outlined">menu</span>
                </button>
            </div>
        </div>

        {{-- ── Menú mobile ── --}}
        <div id="mobile-menu" class="hidden lg:hidden border-t" style="border-color: var(--color-outline-variant); background-color: var(--color-surface-container-lowest);">
            {{-- Buscador mobile --}}
            <div class="px-4 py-3">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-sm pointer-events-none"
                          style="color: var(--color-on-surface-variant);">search</span>
                    <input type="text" class="input pl-9" placeholder="Buscar trámites..." aria-label="Buscador mobile">
                </div>
            </div>
            <nav class="px-4 pb-4 flex flex-col gap-1" aria-label="Menú mobile">
                @foreach([
                    ['label'=>'Inicio',              'url'=>'/',            'icon'=>'home'],
                    ['label'=>'Trámites y Servicios','url'=>'/tramites',    'icon'=>'description'],
                    ['label'=>'Mis Expedientes',     'url'=>'/expedientes', 'icon'=>'folder_open'],
                    ['label'=>'Agendar Cita',        'url'=>'/citas',       'icon'=>'calendar_month'],
                    ['label'=>'Mesa de Ayuda',       'url'=>'/ayuda',       'icon'=>'support_agent'],
                ] as $item)
                    <a href="{{ $item['url'] }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-label-md transition-colors"
                       style="color: var(--color-on-surface);">
                        <span class="material-symbols-outlined text-base" style="color: var(--color-secondary);">{{ $item['icon'] }}</span>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>
    </header>

    {{-- ═══════════════════════════════════════════════════════ BREADCRUMB ══════ --}}
    @hasSection('breadcrumb')
        <div class="pt-20">
            <div class="max-w-screen-xl mx-auto px-6 lg:px-12 py-3">
                <nav class="breadcrumb" aria-label="Ruta de navegación">
                    @yield('breadcrumb')
                </nav>
            </div>
        </div>
    @else
        <div class="pt-20"></div>
    @endif

    {{-- ═══════════════════════════════════════════════════════ FLASH MESSAGES ══ --}}
    @if(session('success') || session('error') || session('warning') || session('info'))
        <div class="max-w-screen-xl mx-auto px-6 lg:px-12 py-3" id="flash-messages">
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    <span class="material-symbols-outlined text-base shrink-0">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger" role="alert">
                    <span class="material-symbols-outlined text-base shrink-0">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning" role="alert">
                    <span class="material-symbols-outlined text-base shrink-0">warning</span>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info" role="alert">
                    <span class="material-symbols-outlined text-base shrink-0">info</span>
                    <span>{{ session('info') }}</span>
                </div>
            @endif
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════ MAIN CONTENT ══ --}}
    <main id="main-content" class="flex-1 w-full" role="main">
        @yield('content')
    </main>

    {{-- ═══════════════════════════════════════════════════════ FOOTER PÚBLICO ══ --}}
    <footer role="contentinfo" style="background-color: var(--color-primary); color: var(--color-on-primary);" class="mt-16">
        <div class="max-w-screen-xl mx-auto px-6 lg:px-12 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

                {{-- Identidad --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                             style="background-color: rgba(255,255,255,0.12);">
                            <span class="material-symbols-outlined text-white text-base">account_balance</span>
                        </div>
                        <span class="text-headline-sm text-white" style="font-family: var(--font-display);">SETC</span>
                        <span class="badge badge-info text-xs">Oficial</span>
                    </div>
                    <p class="text-body-sm" style="color: rgba(255,255,255,0.6); line-height: 20px;">
                        Sistema Institucional de Gestión de Citas, Trámites y Expedientes Digitales.
                        Plataforma interoperable del Gobierno Digital.
                    </p>
                </div>

                {{-- Atención Ciudadana --}}
                <div class="space-y-3">
                    <h3 class="text-label-sm uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Atención Ciudadana</h3>
                    <ul class="space-y-2">
                        @foreach([
                            'Línea Directa 800-SETC-01',
                            'Mesa de Ayuda Telemática',
                            'Módulos Presenciales',
                            'Guía de Trámites y Requisitos',
                        ] as $link)
                            <li><a href="#" class="text-body-sm hover:text-white transition-colors" style="color: rgba(255,255,255,0.65);">{{ $link }}</a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Normatividad --}}
                <div class="space-y-3">
                    <h3 class="text-label-sm uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Normatividad y Transparencia</h3>
                    <ul class="space-y-2">
                        @foreach([
                            'Aviso de Privacidad Integral',
                            'Términos de Servicio Gov-ID',
                            'Marco Jurídico de Actuación',
                            'Portal Nacional de Transparencia',
                        ] as $link)
                            <li><a href="#" class="text-body-sm hover:text-white transition-colors" style="color: rgba(255,255,255,0.65);">{{ $link }}</a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Certificaciones --}}
                <div class="space-y-3">
                    <h3 class="text-label-sm uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Certificaciones</h3>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2 text-body-sm" style="color: rgba(255,255,255,0.65);">
                            <span class="material-symbols-outlined text-sm" style="color: var(--color-tertiary-fixed-dim);">verified_user</span>
                            Encriptación SHA-256
                        </li>
                        <li class="flex items-center gap-2 text-body-sm" style="color: rgba(255,255,255,0.65);">
                            <span class="material-symbols-outlined text-sm" style="color: var(--color-tertiary-fixed-dim);">lock</span>
                            Conformidad Accesibilidad WCAG 2.1 AA
                        </li>
                        <li class="flex items-center gap-2 text-body-sm" style="color: rgba(255,255,255,0.65);">
                            <span class="material-symbols-outlined text-sm" style="color: var(--color-tertiary-fixed-dim);">security</span>
                            TLS 1.3 Red Gubernamental
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Bottom bar --}}
            <div class="mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3"
                 style="border-top: 1px solid rgba(255,255,255,0.12);">
                <p class="text-body-sm" style="color: rgba(255,255,255,0.5);">
                    &copy; {{ date('Y') }} Sistema Estatal de Citas, Trámites y Expedientes (SETC). Todos los derechos reservados.
                </p>
                <div class="flex items-center gap-4">
                    @foreach(['Accesibilidad', 'Privacidad', 'Mapa del Sitio'] as $link)
                        <a href="#" class="text-label-sm hover:text-white transition-colors" style="color: rgba(255,255,255,0.5);">{{ $link }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>

    {{-- ═══════════════════════════════════════════════════════ SCRIPTS ═════════ --}}
    <script>
        // ── Toggle menú mobile ──────────────────────────────────────────
        const btnMenu   = document.getElementById('btn-mobile-menu');
        const mobileNav = document.getElementById('mobile-menu');
        if (btnMenu && mobileNav) {
            btnMenu.addEventListener('click', () => {
                const isOpen = mobileNav.classList.toggle('hidden');
                btnMenu.setAttribute('aria-expanded', String(!isOpen));
            });
        }

        // ── Auto-ocultar flash messages ─────────────────────────────────
        setTimeout(() => {
            const flash = document.getElementById('flash-messages');
            if (flash) {
                flash.style.transition = 'opacity 400ms ease';
                flash.style.opacity    = '0';
                setTimeout(() => flash?.remove(), 400);
            }
        }, 5000);
    </script>

    @stack('scripts')
</body>
</html>
