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

        {{-- Navegación scrollable --}}
        <nav class="flex-1 overflow-y-auto px-3 pb-4 space-y-0.5" aria-label="Menú operativo" style="scrollbar-width:none;">

            @hasSection('sidebar')
                @yield('sidebar')
            @else
                {{-- ─── OPERACIÓN & DICTAMEN ──────────────────────────────────── --}}
                <div class="sidebar-section-label">Operación & Dictamen</div>

                <a href="{{ route('portal.dashboard') }}"
                   class="sidebar-item {{ Request::routeIs('portal.dashboard') ? 'active' : '' }}"
                   @if(Request::routeIs('portal.dashboard')) aria-current="page" @endif>
                    <span class="material-symbols-outlined text-base">dashboard</span>
                    Dashboard Operativo
                </a>
                <a href="{{ route('portal.mesa-dictamen') }}"
                   class="sidebar-item {{ Request::routeIs('portal.mesa-dictamen*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-base">gavel</span>
                    Mesa de Dictamen
                </a>
                <a href="{{ route('portal.expedientes') }}"
                   class="sidebar-item {{ Request::routeIs('portal.expedientes*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-base">folder_open</span>
                    Expedientes Observados
                    @if(($observedCount ?? 0) > 0)
                        <span class="ml-auto px-2 py-0.5 rounded-full text-white font-bold"
                              style="font-size: 11px; background-color: var(--color-warning); min-width: 1.25rem; text-align:center;">
                            {{ $observedCount }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('portal.citas') }}"
                   class="sidebar-item {{ Request::routeIs('portal.citas*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-base">calendar_month</span>
                    Agenda de Citas
                </a>

                {{-- ─── SUPERVISIÓN & SLA ─────────────────────────────────────── --}}
                <div class="sidebar-section-label">Supervisión & SLA</div>

                <a href="{{ route('portal.matriz-cargas') }}"
                   class="sidebar-item {{ Request::routeIs('portal.matriz-cargas*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-base">balance</span>
                    Matriz de Cargas
                </a>
                <a href="{{ route('portal.metricas') }}"
                   class="sidebar-item {{ Request::routeIs('portal.metricas*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-base">monitoring</span>
                    Métricas y Desempeño
                </a>

                {{-- ─── ADMINISTRACIÓN GOVTECH ────────────────────────────────── --}}
                @can('manage-users')
                    <div class="sidebar-section-label">Administración GovTech</div>

                    <a href="{{ route('portal.usuarios') }}"
                       class="sidebar-item {{ Request::routeIs('portal.usuarios*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined text-base">manage_accounts</span>
                        Gestión de Usuarios
                    </a>
                    <a href="{{ route('portal.parametros') }}"
                       class="sidebar-item {{ Request::routeIs('portal.parametros*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined text-base">tune</span>
                        Parámetros y Requisitos
                    </a>
                    <a href="{{ route('portal.auditoria') }}"
                       class="sidebar-item {{ Request::routeIs('portal.auditoria*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined text-base">policy</span>
                        Auditoría & Logs PIDE
                    </a>
                @endcan
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
                <div class="relative flex-1 max-w-sm hidden sm:block">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-sm pointer-events-none"
                          style="color: var(--color-on-surface-variant);">search</span>
                    <input type="text"
                           class="input pl-9 text-sm"
                           style="height: 36px;"
                           placeholder="Buscar por Folio, Administrado, RFC o Expediente..."
                           aria-label="Búsqueda global"
                           id="topbar-search">
                    <span class="absolute right-2 top-1/2 -translate-y-1/2 px-1.5 py-0.5 rounded text-xs font-mono"
                          style="background-color: var(--color-surface-container-high); color: var(--color-on-surface-variant);">⌘K</span>
                </div>
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

                {{-- Notificaciones --}}
                <div class="relative">
                    <button class="btn-ghost" aria-label="Notificaciones" id="btn-topbar-notifications">
                        <span class="material-symbols-outlined">notifications</span>
                        @if(($notificationCount ?? 0) > 0)
                            <span class="absolute top-1 right-1 w-2 h-2 rounded-full ring-2"
                                  style="background-color: var(--color-secondary); ring-color: var(--color-surface-container-lowest);"></span>
                        @endif
                    </button>
                </div>

                {{-- Ayuda --}}
                <button class="btn-ghost" aria-label="Ayuda y documentación">
                    <span class="material-symbols-outlined">help_outline</span>
                </button>

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
