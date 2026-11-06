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

    {{-- Alpine.js Engine --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>

    {{-- Tailwind Stitch Civic Scheme Engine --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "inverse-surface": "#213145",
                        "on-tertiary": "#ffffff",
                        "primary-fixed": "#d4e3ff",
                        "on-secondary-fixed": "#00174b",
                        "on-primary": "#ffffff",
                        "secondary-container": "#316bf3",
                        "on-tertiary-fixed": "#002114",
                        "on-tertiary-fixed-variant": "#005137",
                        "on-secondary-fixed-variant": "#003ea8",
                        "on-surface-variant": "#44474d",
                        "surface-container-low": "#eff4ff",
                        "surface-container-high": "#dce9ff",
                        "secondary-fixed": "#dbe1ff",
                        "error-container": "#ffdad6",
                        "secondary-fixed-dim": "#b4c5ff",
                        "on-primary-fixed": "#021c39",
                        "surface-bright": "#f8f9ff",
                        "on-primary-container": "#798fb1",
                        "background": "#f8f9ff",
                        "on-background": "#0b1c30",
                        "error": "#ba1a1a",
                        "on-error": "#ffffff",
                        "tertiary-fixed": "#85f8c4",
                        "on-secondary-container": "#fefcff",
                        "surface": "#f8f9ff",
                        "primary": "#001229",
                        "secondary": "#0051d5",
                        "on-error-container": "#93000a",
                        "tertiary-fixed-dim": "#68dba9",
                        "outline": "#74777e",
                        "on-surface": "#0b1c30",
                        "on-tertiary-container": "#1fa072",
                        "on-primary-fixed-variant": "#324866",
                        "primary-fixed-dim": "#b2c8ed",
                        "surface-dim": "#cbdbf5",
                        "surface-container-highest": "#d3e4fe",
                        "surface-container": "#e5eeff",
                        "surface-container-lowest": "#ffffff",
                        "outline-variant": "#c4c6ce",
                        "inverse-primary": "#b2c8ed",
                        "inverse-on-surface": "#eaf1ff",
                        "surface-tint": "#4a5f7f",
                        "on-secondary": "#ffffff",
                        "primary-container": "#0f2744",
                        "surface-variant": "#d3e4fe",
                        "tertiary-container": "#002d1d",
                        "tertiary": "#00160c"
                    },
                    fontFamily: {
                        "headline-xl": ["Plus Jakarta Sans"],
                        "headline-lg": ["Plus Jakarta Sans"],
                        "headline-md": ["Plus Jakarta Sans"],
                        "headline-sm": ["Plus Jakarta Sans"],
                        "body-lg": ["Inter"],
                        "body-md": ["Inter"],
                        "body-sm": ["Inter"],
                        "label-md": ["Inter"],
                        "label-sm": ["Inter"],
                        "code-sm": ["Inter"]
                    },
                    fontSize: {
                        "code-sm": ["13px", { lineHeight: "18px", fontWeight: "500" }],
                        "label-sm": ["12px", { lineHeight: "16px", fontWeight: "600" }],
                        "headline-xl": ["36px", { lineHeight: "44px", fontWeight: "700" }],
                        "headline-lg": ["30px", { lineHeight: "38px", fontWeight: "600" }],
                        "body-sm": ["14px", { lineHeight: "20px", fontWeight: "400" }],
                        "label-md": ["14px", { lineHeight: "20px", fontWeight: "500" }],
                        "body-md": ["16px", { lineHeight: "24px", fontWeight: "400" }],
                        "body-lg": ["18px", { lineHeight: "28px", fontWeight: "400" }],
                        "headline-md": ["22px", { lineHeight: "28px", fontWeight: "600" }],
                        "headline-sm": ["18px", { lineHeight: "24px", fontWeight: "600" }]
                    }
                }
            }
        };
    </script>

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

            {{-- Buscador (desktop) funcional --}}
            <form action="{{ route('tramites.index') }}" method="GET" class="hidden xl:flex flex-1 max-w-md mx-6" role="search">
                <div class="relative w-full">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-sm pointer-events-none"
                          style="color: var(--color-on-surface-variant);">search</span>
                    <input type="text"
                           name="buscar"
                           value="{{ request('buscar') }}"
                           class="input pl-9 w-full"
                           placeholder="Buscar trámites, requisitos o folios..."
                           aria-label="Buscador de trámites">
                </div>
            </form>

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

                {{-- Notificaciones Ciudadanas Dinámicas --}}
                @auth
                    @php
                        $userCitasProximas = auth()->user()->citas()->where('Fecha', '>=', now()->toDateString())->orderBy('Fecha')->take(2)->get();
                        $userBitacoras = auth()->user()->bitacoras()->latest('Fecha_Hora')->take(3)->get();
                        $totalAvisos = $userCitasProximas->count() + $userBitacoras->count();
                    @endphp
                    <div class="relative" x-data="{ openNotif: false }">
                        <button @click="openNotif = !openNotif" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors relative cursor-pointer" aria-label="Notificaciones" id="btn-notifications">
                            <span class="material-symbols-outlined text-xl">notifications</span>
                            @if($totalAvisos > 0)
                                <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 rounded-full bg-[#0051d5] ring-2 ring-white"></span>
                            @endif
                        </button>

                        <div x-show="openNotif" @click.outside="openNotif = false" x-cloak
                             x-transition class="absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-200/90 py-2 z-50">
                            <div class="px-4 py-2 border-b border-slate-100 flex items-center justify-between">
                                <span class="text-xs font-bold text-[#001229]">Buzón de Notificaciones</span>
                                @if($totalAvisos > 0)
                                    <span class="text-[10px] font-semibold text-[#0051d5] bg-blue-50 px-2 py-0.5 rounded-full">{{ $totalAvisos }} Avisos</span>
                                @endif
                            </div>
                            <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto">
                                @forelse ($userCitasProximas as $cita)
                                    <a href="{{ route('citas.pase', $cita->ID_Citas) }}" class="p-3 hover:bg-slate-50 transition-colors flex items-start gap-2.5 block">
                                        <span class="material-symbols-outlined text-[#0051d5] text-lg mt-0.5">calendar_month</span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-slate-800 truncate">Turno Programado</p>
                                            <p class="text-[11px] text-slate-500">Cita para el {{ $cita->Fecha->format('d/m/Y') }} a las {{ $cita->Hora }} hrs.</p>
                                            <span class="text-[10px] text-[#0051d5] font-semibold mt-1 block">Ver pase digital →</span>
                                        </div>
                                    </a>
                                @empty
                                @endforelse

                                @forelse ($userBitacoras as $bit)
                                    <div class="p-3 hover:bg-slate-50 transition-colors flex items-start gap-2.5">
                                        <span class="material-symbols-outlined text-emerald-600 text-lg mt-0.5">info</span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-slate-800 truncate">{{ $bit->Accion }}</p>
                                            <p class="text-[11px] text-slate-500 line-clamp-2">{{ $bit->Descripcion }}</p>
                                            <span class="text-[10px] text-slate-400 mt-0.5 block">{{ $bit->Fecha_Hora ? \Carbon\Carbon::parse($bit->Fecha_Hora)->diffForHumans() : '' }}</span>
                                        </div>
                                    </div>
                                @empty
                                @endforelse

                                @if($totalAvisos === 0)
                                    <div class="p-6 text-center text-xs text-slate-400">
                                        <span class="material-symbols-outlined text-2xl block mb-1 opacity-30">notifications_none</span>
                                        No tienes notificaciones pendientes
                                    </div>
                                @endif
                            </div>
                            <div class="p-2 border-t border-slate-100 text-center">
                                <a href="{{ route('expedientes.index') }}" class="text-[11px] font-semibold text-[#0051d5] hover:underline">
                                    Ver todos mis expedientes y trámites
                                </a>
                            </div>
                        </div>
                    </div>
                @endauth

                {{-- Usuario autenticado dropdown --}}
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center gap-2.5 pl-2 rounded-lg py-1.5 pr-3 bg-slate-50 hover:bg-slate-100 border border-slate-200 transition-colors cursor-pointer"
                                id="btn-user-menu"
                                aria-haspopup="true"
                                :aria-expanded="open">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs bg-[#001229] shrink-0">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden sm:flex flex-col text-left">
                                <span class="text-xs font-bold text-slate-900 leading-none">
                                    {{ Str::limit(auth()->user()->name, 18) }}
                                </span>
                                <span class="text-[11px] text-slate-500 leading-tight">
                                    {{ auth()->user()->email ? Str::limit(auth()->user()->email, 20) : 'Ciudadano' }}
                                </span>
                            </div>
                            <span class="material-symbols-outlined text-sm text-slate-400">expand_more</span>
                        </button>

                        {{-- Dropdown usuario con formato y x-cloak --}}
                        <div x-show="open" @click.outside="open = false" x-cloak
                             x-transition
                             class="absolute right-0 top-full mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-200/90 py-2 z-50"
                             role="menu" aria-labelledby="btn-user-menu">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                            </div>

                            <div class="py-1">
                                <a href="{{ route('profile.show') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-[#0051d5] transition-colors" role="menuitem">
                                    <span class="material-symbols-outlined text-base text-slate-400">person</span>
                                    <span>Mi Perfil</span>
                                </a>
                                <a href="{{ route('expedientes.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-[#0051d5] transition-colors" role="menuitem">
                                    <span class="material-symbols-outlined text-base text-slate-400">folder_open</span>
                                    <span>Mis Expedientes</span>
                                </a>
                                <a href="{{ route('citas.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-[#0051d5] transition-colors" role="menuitem">
                                    <span class="material-symbols-outlined text-base text-slate-400">calendar_month</span>
                                    <span>Mis Citas</span>
                                </a>
                            </div>

                            <div class="border-t border-slate-100 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition-colors" role="menuitem">
                                        <span class="material-symbols-outlined text-base text-rose-500">logout</span>
                                        <span>Cerrar Sesión</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors hidden sm:inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base">login</span>
                        <span>Ingresar</span>
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-[#001229] hover:bg-[#0f2744] text-white text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-xs">
                        <span class="material-symbols-outlined text-base">person_add</span>
                        <span>Registrarse</span>
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
    <footer role="contentinfo" class="bg-[#001229] text-white border-t border-slate-800 mt-16">
        <div class="max-w-screen-xl mx-auto px-6 lg:px-12 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

                {{-- Identidad --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-white/10 text-blue-200">
                            <span class="material-symbols-outlined text-base">account_balance</span>
                        </div>
                        <span class="text-xl font-bold text-white" style="font-family: var(--font-display);">SETC</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-900/60 text-blue-200 border border-blue-400/30 uppercase">Oficial</span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Sistema Institucional de Gestión de Citas, Trámites y Expedientes Digitales.
                        Plataforma interoperable del Gobierno Digital conforme a la Ley N° 27444.
                    </p>
                </div>

                {{-- Atención Ciudadana --}}
                <div class="space-y-3">
                    <h3 class="text-xs font-bold text-slate-200 uppercase tracking-wider">Atención Ciudadana</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Línea Directa 800-SETC-01</a></li>
                        <li><a href="{{ route('ayuda.index') }}" class="text-slate-400 hover:text-white transition-colors">Mesa de Ayuda Telemática</a></li>
                        <li><a href="{{ route('citas.create') }}" class="text-slate-400 hover:text-white transition-colors">Módulos Presenciales</a></li>
                        <li><a href="{{ route('tramites.index') }}" class="text-slate-400 hover:text-white transition-colors">Guía de Trámites y Requisitos</a></li>
                    </ul>
                </div>

                {{-- Normatividad --}}
                <div class="space-y-3">
                    <h3 class="text-xs font-bold text-slate-200 uppercase tracking-wider">Normatividad y Transparencia</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Aviso de Privacidad Integral</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Términos de Servicio Gov-ID</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Marco Jurídico de Actuación</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Portal Nacional de Transparencia</a></li>
                    </ul>
                </div>

                {{-- Certificaciones --}}
                <div class="space-y-3">
                    <h3 class="text-xs font-bold text-slate-200 uppercase tracking-wider">Certificaciones</h3>
                    <ul class="space-y-2 text-xs">
                        <li class="flex items-center gap-2 text-slate-400">
                            <span class="material-symbols-outlined text-sm text-emerald-400">verified_user</span>
                            <span>Encriptación SHA-256</span>
                        </li>
                        <li class="flex items-center gap-2 text-slate-400">
                            <span class="material-symbols-outlined text-sm text-emerald-400">lock</span>
                            <span>Conformidad Accesibilidad WCAG 2.1 AA</span>
                        </li>
                        <li class="flex items-center gap-2 text-slate-400">
                            <span class="material-symbols-outlined text-sm text-emerald-400">security</span>
                            <span>TLS 1.3 Red Gubernamental</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Bottom bar --}}
            <div class="mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-800 text-xs text-slate-500">
                <p>
                    &copy; {{ date('Y') }} Sistema Estatal de Citas, Trámites y Expedientes (SETC). Todos los derechos reservados.
                </p>
                <div class="flex items-center gap-4">
                    <a href="#" class="hover:text-slate-300 transition-colors">Accesibilidad</a>
                    <span class="text-slate-700">•</span>
                    <a href="#" class="hover:text-slate-300 transition-colors">Privacidad</a>
                    <span class="text-slate-700">•</span>
                    <a href="#" class="hover:text-slate-300 transition-colors">Mapa del Sitio</a>
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
