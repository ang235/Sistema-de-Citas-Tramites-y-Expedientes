<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    {{-- SEO — portal interno (no indexable) --}}
    <title>@yield('title', 'Panel Operativo') — SETC Enterprise</title>
    <meta name="description" content="@yield('meta_description', 'Panel operativo interno del Sistema Estatal de Citas, Trámites y Expedientes.')">
    <meta name="robots" content="noindex, nofollow">

    {{-- Fuentes del sistema SETC --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Plus+Jakarta+Sans:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
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
    @stack('head')
</head>
<body class="min-h-screen flex overflow-hidden"
      style="background-color: var(--color-surface); color: var(--color-on-surface);">

    {{-- ════════════════════════════════════════════════════════ SIDEBAR ═════════ --}}
    {{--
        El sidebar recibe su contenido desde la vista con @section('sidebar') o usa
        la configuración del controlador via $sidebarConfig. Si ninguno existe, se
        usa el nav por defecto de SETC Enterprise.
    --}}
    <aside id="sidebar"
           class="flex-shrink-0 flex flex-col h-screen sticky top-0 z-40 transition-all duration-300"
           style="width: 240px; background-color: var(--color-primary);"
           aria-label="Panel de navegación">

        {{-- Logo & Contexto operativo --}}
        <div class="px-4 py-5 shrink-0">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                     style="background-color: rgba(255,255,255,0.12);">
                    <span class="material-symbols-outlined text-white text-lg">account_balance</span>
                </div>
                <div class="flex flex-col leading-tight min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-white font-bold text-sm" style="font-family: var(--font-display);">S.E.T.C.</span>
                        <span class="px-1.5 py-0.5 rounded text-white font-bold"
                              style="font-size: 9px; background-color: var(--color-secondary); letter-spacing: 0.05em;">ENTERPRISE</span>
                    </div>
                    <span class="text-xs truncate" style="color: rgba(255,255,255,0.5);">Mesa de Control GOV</span>
                </div>
            </div>

            {{-- Contexto institucional --}}
            <div class="px-3 py-2 rounded-lg" style="background-color: rgba(255,255,255,0.06);">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-label-sm uppercase" style="color: rgba(255,255,255,0.4); letter-spacing: 0.08em;">Contexto Institucional</span>
                    <div class="flex items-center gap-1">
                        <div class="w-1.5 h-1.5 rounded-full" style="background-color: var(--color-tertiary-fixed-dim);"></div>
                        <span style="font-size: 10px; color: var(--color-tertiary-fixed-dim);">Seguro</span>
                    </div>
                </div>
                <select class="w-full text-white text-xs bg-transparent border-none outline-none cursor-pointer"
                        id="sede-selector" aria-label="Seleccionar sede">
                    <option value="" style="background-color: #001229;">{{ auth()->user()->sede ?? 'Administración Central' }}</option>
                </select>
            </div>
        </div>

        {{-- Navegación scrollable según rol --}}
        <nav class="flex-1 overflow-y-auto px-3 pb-4 space-y-0.5" aria-label="Menú operativo" style="scrollbar-width:none;">

            @hasSection('sidebar')
                @yield('sidebar')
            @else
                @php
                    $u = auth()->user();
                    $isAdmin = $u ? $u->esAdministrador() : false;
                    $isRevisor = $u ? $u->esRevisor() : false;
                    $isSupervisor = $u ? $u->esSupervisor() : false;
                @endphp

                @if($isAdmin)
                    {{-- ─── ADMINISTRADOR ────────────────────────────────────────── --}}
                    <div class="sidebar-section-label">Panel Principal</div>
                    <a href="{{ route('portal.dashboard') }}"
                       class="sidebar-item {{ Request::routeIs('portal.dashboard') ? 'active' : '' }}"
                       @if(Request::routeIs('portal.dashboard')) aria-current="page" @endif>
                        <span class="material-symbols-outlined text-base">dashboard</span>
                        Dashboard Operativo
                    </a>

                    <div class="sidebar-section-label">Configuración & Seguridad</div>
                    <a href="{{ route('portal.usuarios') }}"
                       class="sidebar-item {{ Request::routeIs('portal.usuarios*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined text-base">manage_accounts</span>
                        Gestión de Usuarios
                    </a>
                    <a href="{{ route('portal.parametros') }}"
                       class="sidebar-item {{ Request::routeIs('portal.parametros*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined text-base">tune</span>
                        Trámites y Requisitos
                    </a>
                    <a href="{{ route('portal.auditoria') }}"
                       class="sidebar-item {{ Request::routeIs('portal.auditoria*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined text-base">policy</span>
                        Auditoría & Logs PIDE
                    </a>

                    <div class="sidebar-section-label">Control Operativo</div>
                    <a href="{{ route('portal.matriz-cargas') }}"
                       class="sidebar-item {{ Request::routeIs('portal.matriz-cargas*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined text-base">balance</span>
                        Matriz de Cargas
                    </a>
                    <a href="{{ route('portal.metricas') }}"
                       class="sidebar-item {{ Request::routeIs('portal.metricas*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined text-base">monitoring</span>
                        Métricas Globales
                    </a>
                    <a href="{{ route('portal.citas') }}"
                       class="sidebar-item {{ Request::routeIs('portal.citas*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined text-base">calendar_month</span>
                        Agenda de Citas
                    </a>

                @elseif($isRevisor)
                    {{-- ─── REVISOR TÉCNICO ──────────────────────────────────────── --}}
                    <div class="sidebar-section-label">Mesa Técnica</div>
                    <a href="{{ route('portal.mesa-dictamen') }}"
                       class="sidebar-item {{ Request::routeIs('portal.mesa-dictamen*') ? 'active' : '' }}"
                       @if(Request::routeIs('portal.mesa-dictamen*')) aria-current="page" @endif>
                        <span class="material-symbols-outlined text-base">gavel</span>
                        Mesa de Dictamen
                    </a>
                    <a href="{{ route('portal.expedientes') }}"
                       class="sidebar-item {{ Request::routeIs('portal.expedientes*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined text-base">folder_open</span>
                        Expedientes Asignados
                        @if(($observedCount ?? 0) > 0)
                            <span class="ml-auto px-2 py-0.5 rounded-full text-white font-bold"
                                  style="font-size: 11px; background-color: var(--color-secondary); min-width: 1.25rem; text-align:center;">
                                {{ $observedCount }}
                            </span>
                        @endif
                    </a>

                    <div class="sidebar-section-label">Atención</div>
                    <a href="{{ route('portal.citas') }}"
                       class="sidebar-item {{ Request::routeIs('portal.citas*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined text-base">calendar_month</span>
                        Agenda de Citas
                    </a>

                @elseif($isSupervisor)
                    {{-- ─── SUPERVISOR / DICTAMINADOR ────────────────────────────── --}}
                    <div class="sidebar-section-label">Supervisión & Cargas</div>
                    <a href="{{ route('portal.matriz-cargas') }}"
                       class="sidebar-item {{ Request::routeIs('portal.matriz-cargas*') ? 'active' : '' }}"
                       @if(Request::routeIs('portal.matriz-cargas*')) aria-current="page" @endif>
                        <span class="material-symbols-outlined text-base">balance</span>
                        Matriz de Cargas
                    </a>
                    <a href="{{ route('portal.metricas') }}"
                       class="sidebar-item {{ Request::routeIs('portal.metricas*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined text-base">monitoring</span>
                        Métricas y Desempeño
                    </a>
                    <a href="{{ route('portal.mesa-dictamen') }}"
                       class="sidebar-item {{ Request::routeIs('portal.mesa-dictamen*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined text-base">gavel</span>
                        Mesa de Dictamen
                    </a>
                    <a href="{{ route('portal.expedientes') }}"
                       class="sidebar-item {{ Request::routeIs('portal.expedientes*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined text-base">folder_open</span>
                        Expedientes Supervisados
                    </a>

                    <div class="sidebar-section-label">Atención</div>
                    <a href="{{ route('portal.citas') }}"
                       class="sidebar-item {{ Request::routeIs('portal.citas*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined text-base">calendar_month</span>
                        Agenda de Citas
                    </a>

                @else
                    {{-- ─── GENERAL / OPERATIVO ──────────────────────────────────── --}}
                    <div class="sidebar-section-label">Operación</div>
                    <a href="{{ route('portal.dashboard') }}"
                       class="sidebar-item {{ Request::routeIs('portal.dashboard') ? 'active' : '' }}">
                        <span class="material-symbols-outlined text-base">dashboard</span>
                        Dashboard Operativo
                    </a>
                    <a href="{{ route('portal.expedientes') }}"
                       class="sidebar-item {{ Request::routeIs('portal.expedientes*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined text-base">folder_open</span>
                        Expedientes
                    </a>
                    <a href="{{ route('portal.citas') }}"
                       class="sidebar-item {{ Request::routeIs('portal.citas*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined text-base">calendar_month</span>
                        Agenda de Citas
                    </a>
                @endif
            @endif
        </nav>

        {{-- Usuario actual (pie del sidebar) --}}
        <div class="shrink-0 px-3 py-4" style="border-top: 1px solid rgba(255,255,255,0.1);">
            <div class="flex items-center gap-3 p-2 rounded-lg" style="background-color: rgba(255,255,255,0.06);">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0"
                     style="background-color: var(--color-secondary);">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white font-semibold truncate" style="font-size: 13px;">
                        {{ Str::limit(auth()->user()->name ?? 'Usuario', 20) }}
                    </p>
                    <p class="truncate" style="font-size: 11px; color: rgba(255,255,255,0.5);">
                        {{ auth()->user()->role_label ?? 'Revisor' }}
                    </p>
                </div>
                <div class="relative flex-shrink-0" x-data="{ open: false }">
                    <button @click="open = !open" class="btn-ghost" style="color: rgba(255,255,255,0.6);"
                            aria-label="Opciones de usuario" id="btn-user-sidebar">
                        <span class="material-symbols-outlined text-base">more_vert</span>
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         x-transition class="dropdown-menu absolute bottom-full mb-2 left-0" role="menu">
                        <a href="{{ route('profile.show') }}" class="dropdown-item" role="menuitem">
                            <span class="material-symbols-outlined text-base">person</span>Mi Perfil
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item danger" role="menuitem">
                                <span class="material-symbols-outlined text-base">logout</span>Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-1.5 mt-2 px-2">
                <div class="status-dot-active"></div>
                <span style="font-size: 11px; color: rgba(255,255,255,0.4);">2FA verificado</span>
                <span class="ml-auto" style="font-size: 11px; color: rgba(255,255,255,0.3);">v3.4.2</span>
            </div>
        </div>
    </aside>

    {{-- ════════════════════════════════════════════════════════ MAIN WRAPPER ════ --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- ── Topbar ── --}}
        <header class="shrink-0 flex items-center justify-between gap-4 px-6 h-16 z-30"
                style="background-color: var(--color-surface-container-lowest); border-bottom: 1px solid var(--color-outline-variant); box-shadow: var(--shadow-header);">

            {{-- Toggle sidebar (mobile) + Buscador --}}
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <button id="btn-sidebar-toggle" class="btn-ghost lg:hidden" aria-label="Toggle sidebar">
                    <span class="material-symbols-outlined">menu</span>
                </button>

                {{-- Global search --}}
                <form action="{{ route('portal.expedientes') }}" method="GET" class="relative flex-1 max-w-sm hidden sm:flex items-center" role="search">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-sm pointer-events-none"
                          style="color: var(--color-on-surface-variant);">search</span>
                    <input type="text"
                           name="q"
                           class="input pl-9 text-sm w-full"
                           style="height: 36px;"
                           placeholder="Buscar por Folio, Administrado, RFC..."
                           aria-label="Búsqueda global"
                           id="topbar-search"
                           value="{{ request('q') }}">
                    <span class="absolute right-2 top-1/2 -translate-y-1/2 px-1.5 py-0.5 rounded text-xs font-mono"
                          style="background-color: var(--color-surface-container-high); color: var(--color-on-surface-variant);">⌘K</span>
                </form>
            </div>

            {{-- Indicadores de estado del sistema --}}
            <div class="flex items-center gap-3 shrink-0">

                {{-- Red segura --}}
                <div class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold"
                     style="background-color: var(--color-success-bg); color: var(--color-success-text);">
                    <span class="material-symbols-outlined text-sm">lock</span>
                    Red Segura PKI
                </div>

                {{-- Sede actual --}}
                <div class="hidden lg:flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold"
                     style="background-color: var(--color-surface-container-low); color: var(--color-on-surface-variant);">
                    <span class="material-symbols-outlined text-sm">location_on</span>
                    {{ auth()->user()->sede ?? 'Sede Central Metropolitana' }}
                </div>

                {{-- Notificaciones con Dropdown --}}
                <div class="relative" x-data="{ openNotif: false }">
                    <button @click="openNotif = !openNotif" class="btn-ghost relative" aria-label="Notificaciones" id="btn-topbar-notifications">
                        <span class="material-symbols-outlined">notifications</span>
                        @if(($notificationCount ?? 0) > 0)
                            <span class="absolute top-1 right-1 w-2.5 h-2.5 rounded-full ring-2 ring-white"
                                  style="background-color: var(--color-secondary);"></span>
                        @endif
                    </button>
                    <div x-show="openNotif" @click.outside="openNotif = false"
                         x-transition
                         class="dropdown-menu absolute right-0 top-full mt-2 w-80 z-50 bg-white rounded-xl shadow-lg border border-slate-200" role="dialog" aria-label="Panel de notificaciones" style="display: none;">
                        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                            <span class="text-sm font-bold text-[#0b1c30]">Notificaciones</span>
                            <span class="text-xs text-slate-400 font-mono">{{ now()->format('d/m/Y') }}</span>
                        </div>
                        <div class="py-1 max-h-72 overflow-y-auto divide-y divide-slate-100">
                            @php
                                $bitacorasRecientes = auth()->check() ? auth()->user()->bitacoras()->latest('Fecha_Hora')->take(5)->get() : collect();
                            @endphp
                            @forelse($bitacorasRecientes as $bit)
                                <div class="p-3 hover:bg-slate-50 transition-colors flex items-start gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-[#eff4ff] text-[#0051d5] flex items-center justify-center shrink-0 mt-0.5">
                                        <span class="material-symbols-outlined text-sm">info</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-[#0b1c30] truncate">{{ $bit->Accion ?? 'Actividad' }}</p>
                                        <p class="text-[11px] text-slate-400">{{ $bit->Fecha_Hora ? \Carbon\Carbon::parse($bit->Fecha_Hora)->diffForHumans() : '' }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-6 text-center text-xs text-slate-400">
                                    <span class="material-symbols-outlined text-2xl block mb-1 opacity-30">notifications_none</span>
                                    Sin notificaciones recientes
                                </div>
                            @endforelse
                        </div>
                        <div class="border-t border-slate-100 px-4 py-2.5 bg-slate-50 rounded-b-xl flex justify-between items-center">
                            <a href="{{ route('portal.auditoria') }}" class="text-xs text-[#0051d5] font-semibold hover:underline">Ver auditoría completa →</a>
                            <button @click="openNotif = false" class="text-xs text-slate-400 hover:text-slate-600">Cerrar</button>
                        </div>
                    </div>
                </div>

                {{-- Ayuda con Dropdown --}}
                <div class="relative" x-data="{ openAyuda: false }">
                    <button @click="openAyuda = !openAyuda" class="btn-ghost" aria-label="Ayuda y documentación">
                        <span class="material-symbols-outlined">help_outline</span>
                    </button>
                    <div x-show="openAyuda" @click.outside="openAyuda = false"
                         x-transition
                         class="dropdown-menu absolute right-0 top-full mt-2 w-72 z-50 bg-white rounded-xl shadow-lg border border-slate-200" role="dialog" style="display: none;">
                        <div class="px-4 py-3 border-b border-slate-100">
                            <span class="text-sm font-bold text-[#0b1c30]">Mesa de Ayuda SETC</span>
                        </div>
                        <div class="p-3 space-y-3">
                            <div class="space-y-1.5">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Atajos Rápidos</span>
                                <div class="flex items-center justify-between text-xs py-1 border-b border-slate-100">
                                    <span class="text-slate-600">Búsqueda global</span>
                                    <kbd class="px-1.5 py-0.5 rounded bg-slate-100 text-[11px] font-mono font-bold text-slate-700">⌘K</kbd>
                                </div>
                                <div class="flex items-center justify-between text-xs py-1 border-b border-slate-100">
                                    <span class="text-slate-600">Nueva Cita</span>
                                    <kbd class="px-1.5 py-0.5 rounded bg-slate-100 text-[11px] font-mono font-bold text-slate-700">C</kbd>
                                </div>
                            </div>
                            <div class="pt-1">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Canales de Soporte</span>
                                <div class="space-y-1">
                                    <a href="mailto:soporte@setc.gob.mx" class="flex items-center gap-2 text-xs text-[#0051d5] hover:underline">
                                        <span class="material-symbols-outlined text-sm">mail</span>
                                        soporte@setc.gob.mx
                                    </a>
                                    <p class="text-xs text-slate-500 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">call</span>
                                        800-SETC-GOB (Ext. 401)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Avatar usuario --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-2 px-2 py-1.5 rounded-lg transition-colors"
                            @mouseenter="$el.style.backgroundColor='var(--color-surface-container-low)'"
                            @mouseleave="$el.style.backgroundColor='transparent'"
                            aria-haspopup="true" :aria-expanded="open" id="btn-topbar-user">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm"
                             style="background-color: var(--color-secondary);">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                        </div>
                        <div class="hidden sm:flex flex-col text-left">
                            <span class="text-label-md font-semibold" style="color: var(--color-on-surface);">
                                {{ Str::limit(auth()->user()->name ?? 'Admin', 18) }}
                            </span>
                            <span class="text-label-sm" style="color: var(--color-on-surface-variant);">
                                {{ auth()->user()->role_label ?? 'Administrador' }}
                            </span>
                        </div>
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         x-transition class="dropdown-menu absolute right-0 top-full mt-2" role="menu">
                        <a href="{{ route('profile.show') }}" class="dropdown-item" role="menuitem">
                            <span class="material-symbols-outlined text-base">person</span>Mi Perfil
                        </a>
                        <a href="{{ route('portal.metricas') }}" class="dropdown-item" role="menuitem">
                            <span class="material-symbols-outlined text-base">monitoring</span>Mis Métricas
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item danger" role="menuitem">
                                <span class="material-symbols-outlined text-base">logout</span>Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- ── Área de contenido scrollable ── --}}
        <div class="flex-1 overflow-y-auto" style="background-color: var(--color-surface);">

            {{-- Breadcrumb interno --}}
            @hasSection('breadcrumb')
                <div class="px-6 lg:px-8 pt-4">
                    <nav class="breadcrumb" aria-label="Ruta de navegación">
                        <a href="{{ route('portal.dashboard') }}">Inicio</a>
                        @yield('breadcrumb')
                    </nav>
                </div>
            @endif

            {{-- Flash messages --}}
            @if(session('success') || session('error') || session('warning') || session('info'))
                <div class="px-6 lg:px-8 pt-3" id="flash-messages">
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

            {{-- Errores de validación --}}
            @if($errors->any())
                <div class="px-6 lg:px-8 pt-3">
                    <div class="alert alert-danger" role="alert">
                        <span class="material-symbols-outlined text-base shrink-0">error</span>
                        <ul class="list-none space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- CONTENIDO PRINCIPAL --}}
            <main id="main-content" role="main" class="px-6 lg:px-8 py-6 pb-12">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════ SCRIPTS ═════════ --}}
    <script>
        // ── Toggle sidebar mobile ──────────────────────────────────────────────
        const btnSidebar = document.getElementById('btn-sidebar-toggle');
        const sidebar    = document.getElementById('sidebar');
        if (btnSidebar && sidebar) {
            btnSidebar.addEventListener('click', () => {
                const hidden = sidebar.classList.toggle('-translate-x-full');
                sidebar.style.position = 'fixed';
            });
        }

        // ── Auto-ocultar flash messages ────────────────────────────────────────
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
