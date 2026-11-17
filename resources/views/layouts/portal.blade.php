<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    {{-- SEO — portal interno (no indexable) --}}
    <title>@yield('title', 'Panel Operativo') — SETC Enterprise</title>
    <meta name="description" content="@yield('meta_description', 'Panel operativo interno del Sistema de Expedientes de Trámites y Citas (SETC).')">
    <meta name="robots" content="noindex, nofollow">

    {{-- Detección inmediata de modo oscuro para evitar destello --}}
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

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
                        "primary": "#001229",
                        "secondary": "#0051d5",
                    }
                }
            }
        };
    </script>

    <style>
        /* ── Estilos para Sidebar en formato Botones de Lista con Colores ── */
        .sidebar-btn-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.55rem 0.75rem;
            border-radius: 0.75rem;
            font-size: 13px;
            font-weight: 500;
            color: #cbd5e1;
            background-color: rgba(15, 29, 46, 0.7);
            border: 1px solid rgba(51, 65, 85, 0.55);
            text-decoration: none;
            transition: all 160ms cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 0.35rem;
        }
        .sidebar-btn-item:hover {
            background-color: rgba(26, 44, 69, 0.95);
            border-color: rgba(96, 165, 250, 0.4);
            color: #ffffff;
            transform: translateX(2px);
        }
        .sidebar-btn-item.active {
            background: linear-gradient(135deg, #0051d5 0%, #1d4ed8 100%);
            border-color: rgba(147, 197, 253, 0.5);
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(0, 81, 213, 0.35);
        }
        .sidebar-btn-icon {
            width: 1.85rem;
            height: 1.85rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 150ms ease;
        }
        .sidebar-btn-item:hover .sidebar-btn-icon {
            transform: scale(1.08);
        }

        /* ── Scrollbars Visibles y Elegantes ── */
        .custom-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.5) transparent;
        }
        .custom-scroll::-webkit-scrollbar {
            width: 7px;
            height: 7px;
            display: block;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.4);
            border-radius: 9999px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 116, 139, 0.7);
        }
        .dark .custom-scroll {
            scrollbar-color: rgba(71, 85, 105, 0.6) transparent;
        }
        .dark .custom-scroll::-webkit-scrollbar-thumb {
            background: rgba(71, 85, 105, 0.6);
        }
        .dark .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 116, 139, 0.9);
        }

        /* ── Reglas Globales de Modo Oscuro para todo el Portal ── */
        html.dark {
            color-scheme: dark;
        }
        html.dark body {
            background-color: #070e17 !important;
            color: #f1f5f9 !important;
        }
        html.dark .bg-white {
            background-color: #0e1c2e !important;
            color: #f1f5f9;
        }
        html.dark .bg-slate-50,
        html.dark .bg-gray-50 {
            background-color: #0a1626 !important;
        }
        html.dark .bg-slate-100,
        html.dark .bg-gray-100 {
            background-color: #162740 !important;
        }
        html.dark .bg-slate-200,
        html.dark .bg-gray-200 {
            background-color: #1e3557 !important;
        }
        html.dark .bg-blue-50,
        html.dark .bg-sky-50,
        html.dark .bg-\[\#eff4ff\],
        html.dark .bg-\[\#e5eeff\],
        html.dark .bg-\[\#dce9ff\] {
            background-color: rgba(0, 81, 213, 0.22) !important;
        }
        html.dark .bg-emerald-50,
        html.dark .bg-green-50 {
            background-color: rgba(5, 150, 105, 0.22) !important;
        }
        html.dark .bg-amber-50,
        html.dark .bg-yellow-50 {
            background-color: rgba(217, 119, 6, 0.22) !important;
        }
        html.dark .bg-rose-50,
        html.dark .bg-red-50 {
            background-color: rgba(225, 29, 72, 0.22) !important;
        }
        html.dark .bg-purple-50,
        html.dark .bg-indigo-50 {
            background-color: rgba(99, 102, 241, 0.22) !important;
        }
        html.dark .text-\[\#001229\],
        html.dark .text-\[\#0b1c30\],
        html.dark .text-\[\#0f2744\],
        html.dark .text-slate-900,
        html.dark .text-slate-800,
        html.dark .text-gray-900,
        html.dark .text-gray-800 {
            color: #f8fafc !important;
        }
        html.dark .text-slate-700,
        html.dark .text-slate-600,
        html.dark .text-\[\#44474d\],
        html.dark .text-gray-700,
        html.dark .text-gray-600 {
            color: #cbd5e1 !important;
        }
        html.dark .text-slate-500,
        html.dark .text-gray-500 {
            color: #94a3b8 !important;
        }
        html.dark .text-slate-400,
        html.dark .text-gray-400 {
            color: #64748b !important;
        }
        html.dark .text-\[\#0051d5\],
        html.dark .text-blue-600,
        html.dark .text-blue-700 {
            color: #60a5fa !important;
        }
        html.dark .border-slate-100,
        html.dark .border-slate-200,
        html.dark .border-slate-200\/60,
        html.dark .border-slate-200\/80,
        html.dark .border-slate-200\/90,
        html.dark .border-slate-300,
        html.dark .border-gray-200,
        html.dark .border-gray-300,
        html.dark .divide-slate-100 > :not([hidden]) ~ :not([hidden]),
        html.dark .divide-slate-200 > :not([hidden]) ~ :not([hidden]) {
            border-color: rgba(51, 65, 85, 0.6) !important;
        }
        html.dark button.bg-\[\#001229\],
        html.dark a.bg-\[\#001229\],
        html.dark .bg-\[\#001229\]:not(header):not(aside) {
            background-color: #1a365d !important;
            color: #ffffff !important;
            border: 1px solid rgba(96, 165, 250, 0.35) !important;
        }
        html.dark input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="submit"]):not([type="button"]):not([type="hidden"]),
        html.dark select,
        html.dark textarea {
            background-color: #0b1624 !important;
            color: #f8fafc !important;
            border-color: #334155 !important;
        }
        html.dark input::placeholder,
        html.dark textarea::placeholder {
            color: #64748b !important;
        }
        html.dark select option {
            background-color: #0e1c2e !important;
            color: #f8fafc !important;
        }
        html.dark table thead,
        html.dark table thead th {
            background-color: #0a1626 !important;
            color: #94a3b8 !important;
            border-color: #1e293b !important;
        }
        html.dark table tbody tr {
            border-color: #1e293b !important;
            background-color: transparent !important;
        }
        html.dark table tbody tr:hover,
        html.dark .hover\:bg-slate-50:hover,
        html.dark .hover\:bg-slate-50\/70:hover {
            background-color: rgba(30, 41, 59, 0.55) !important;
        }
        html.dark table tbody td {
            color: #e2e8f0 !important;
        }
        html.dark .dropdown-menu,
        html.dark [role="dialog"],
        html.dark .modal,
        html.dark .shadow-xl,
        html.dark .shadow-2xl {
            background-color: #0e1c2e;
            border-color: #334155;
            color: #f1f5f9;
        }
    </style>

    @stack('head')
</head>
<body class="h-full flex overflow-hidden bg-slate-50 dark:bg-[#070e17] text-slate-900 dark:text-slate-100 antialiased">

    {{-- ════════════════════════════════════════════════════════ SIDEBAR ═════════ --}}
    <aside id="sidebar"
           class="flex-shrink-0 flex flex-col h-full z-40 transition-all duration-300 w-64 bg-[#0a1626] border-r border-slate-800 text-slate-200"
           aria-label="Panel de navegación">

        {{-- Logo & Contexto operativo --}}
        <div class="px-4 py-4 shrink-0 border-b border-slate-800/80 bg-[#08121f]">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 p-1 flex items-center justify-center shrink-0 border border-white/20">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SETC" class="h-8 w-auto object-contain">
                </div>
                <div class="flex flex-col leading-tight min-w-0">
                    <div class="flex items-center gap-1.5">
                        <span class="text-white font-bold text-base tracking-tight" style="font-family: var(--font-display);">S.E.T.C.</span>
                        <span class="px-1.5 py-0.5 rounded text-white font-bold text-[9px] bg-[#0051d5] tracking-wider">ENTERPRISE</span>
                    </div>
                    <span class="text-[11px] truncate text-slate-400 font-medium">Expedientes, Trámites y Citas</span>
                </div>
            </div>

            {{-- Identidad Institucional --}}
            <div class="px-3 py-2 rounded-xl bg-slate-900/80 border border-slate-800">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Identidad Institucional</span>
                    <div class="flex items-center gap-1">
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></div>
                        <span class="text-[10px] text-emerald-300 font-semibold">Oficial</span>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 text-white text-xs">
                    <span class="material-symbols-outlined text-sm text-blue-400">verified</span>
                    <span class="font-medium truncate">{{ auth()->user()->role_label ?? 'Personal Institucional' }}</span>
                </div>
            </div>
        </div>

        {{-- Navegación en Formato de Botones de Lista con Colores --}}
        <nav class="flex-1 overflow-y-auto custom-scroll px-3 py-3 space-y-1" aria-label="Menú operativo">

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
                    <div class="px-2 pt-2 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                        Panel Principal
                    </div>

                    <a href="{{ route('portal.dashboard') }}"
                       class="sidebar-btn-item {{ Request::routeIs('portal.dashboard') ? 'active' : '' }}">
                        <div class="sidebar-btn-icon bg-blue-500/20 text-blue-300 border border-blue-400/20">
                            <span class="material-symbols-outlined text-base">dashboard</span>
                        </div>
                        <span class="flex-1 truncate">Dashboard Operativo</span>
                    </a>

                    <div class="px-2 pt-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                        Configuración & Seguridad
                    </div>

                    <a href="{{ route('portal.usuarios') }}"
                       class="sidebar-btn-item {{ Request::routeIs('portal.usuarios*') ? 'active' : '' }}">
                        <div class="sidebar-btn-icon bg-sky-500/20 text-sky-300 border border-sky-400/20">
                            <span class="material-symbols-outlined text-base">manage_accounts</span>
                        </div>
                        <span class="flex-1 truncate">Gestión de Usuarios</span>
                    </a>

                    <a href="{{ route('portal.parametros') }}"
                       class="sidebar-btn-item {{ Request::routeIs('portal.parametros*') ? 'active' : '' }}">
                        <div class="sidebar-btn-icon bg-rose-500/20 text-rose-300 border border-rose-400/20">
                            <span class="material-symbols-outlined text-base">tune</span>
                        </div>
                        <span class="flex-1 truncate">Gestion de tramites y requisitos</span>
                    </a>

                    <a href="{{ route('portal.auditoria') }}"
                       class="sidebar-btn-item {{ Request::routeIs('portal.auditoria*') ? 'active' : '' }}">
                        <div class="sidebar-btn-icon bg-purple-500/20 text-purple-300 border border-purple-400/20">
                            <span class="material-symbols-outlined text-base">policy</span>
                        </div>
                        <span class="flex-1 truncate">Bitácora & Auditoría</span>
                    </a>

                    <div class="px-2 pt-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        Control Operativo
                    </div>

                    <a href="{{ route('portal.matriz-cargas') }}"
                       class="sidebar-btn-item {{ Request::routeIs('portal.matriz-cargas*') ? 'active' : '' }}">
                        <div class="sidebar-btn-icon bg-indigo-500/20 text-indigo-300 border border-indigo-400/20">
                            <span class="material-symbols-outlined text-base">balance</span>
                        </div>
                        <span class="flex-1 truncate">Matriz de Cargas</span>
                    </a>

                    <a href="{{ route('portal.citas') }}"
                       class="sidebar-btn-item {{ Request::routeIs('portal.citas*') ? 'active' : '' }}">
                        <div class="sidebar-btn-icon bg-teal-500/20 text-teal-300 border border-teal-400/20">
                            <span class="material-symbols-outlined text-base">calendar_month</span>
                        </div>
                        <span class="flex-1 truncate">Agenda de Citas</span>
                    </a>

                @elseif($isRevisor)
                    {{-- ─── REVISOR TÉCNICO (EXCLUSIVAMENTE SUS 2 APARTADOS DESIGNADOS) ── --}}
                    <div class="px-2 pt-2 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                        Revisión Técnica
                    </div>

                    <a href="{{ route('portal.revisor-dashboard') }}"
                       class="sidebar-btn-item {{ Request::routeIs('portal.revisor-dashboard*') ? 'active' : '' }}">
                        <div class="sidebar-btn-icon bg-blue-500/20 text-blue-300 border border-blue-400/20">
                            <span class="material-symbols-outlined text-base">monitoring</span>
                        </div>
                        <span class="flex-1 truncate">Mando de Revisión Técnica</span>
                    </a>

                    <a href="{{ route('portal.mesa-dictamen') }}"
                       class="sidebar-btn-item {{ Request::routeIs('portal.mesa-dictamen*') || Request::routeIs('portal.expedientes.evaluar*') ? 'active' : '' }}">
                        <div class="sidebar-btn-icon bg-amber-500/20 text-amber-300 border border-amber-400/20">
                            <span class="material-symbols-outlined text-base">fact_check</span>
                        </div>
                        <span class="flex-1 truncate">Mesa de Calificación Documental</span>
                    </a>

                @elseif($isSupervisor)
                    {{-- ─── SUPERVISOR / DICTAMINADOR ────────────────────────────── --}}
                    <div class="px-2 pt-2 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                        Supervisión & Mando
                    </div>

                    <a href="{{ route('portal.supervisor-dashboard') }}"
                       class="sidebar-btn-item {{ Request::routeIs('portal.supervisor-dashboard*') ? 'active' : '' }}">
                        <div class="sidebar-btn-icon bg-blue-500/20 text-blue-300 border border-blue-400/20">
                            <span class="material-symbols-outlined text-base">dashboard_customize</span>
                        </div>
                        <span class="flex-1 truncate">Mando de Supervisión</span>
                    </a>

                    <a href="{{ route('portal.matriz-cargas') }}"
                       class="sidebar-btn-item {{ Request::routeIs('portal.matriz-cargas*') ? 'active' : '' }}">
                        <div class="sidebar-btn-icon bg-indigo-500/20 text-indigo-300 border border-indigo-400/20">
                            <span class="material-symbols-outlined text-base">balance</span>
                        </div>
                        <span class="flex-1 truncate">Matriz de Asignaciones</span>
                    </a>

                    <a href="{{ route('portal.auditoria') }}"
                       class="sidebar-btn-item {{ Request::routeIs('portal.auditoria*') ? 'active' : '' }}">
                        <div class="sidebar-btn-icon bg-purple-500/20 text-purple-300 border border-purple-400/20">
                            <span class="material-symbols-outlined text-base">policy</span>
                        </div>
                        <span class="flex-1 truncate">Bitácora de Supervisión</span>
                    </a>

                    <div class="px-2 pt-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-teal-400"></span>
                        Atención & Agenda
                    </div>

                    <a href="{{ route('portal.citas') }}"
                       class="sidebar-btn-item {{ Request::routeIs('portal.citas*') ? 'active' : '' }}">
                        <div class="sidebar-btn-icon bg-teal-500/20 text-teal-300 border border-teal-400/20">
                            <span class="material-symbols-outlined text-base">calendar_month</span>
                        </div>
                        <span class="flex-1 truncate">Supervisión de Citas</span>
                    </a>

                @else
                    {{-- ─── PERSONAL INSTITUCIONAL GENERAL ──────────────────────── --}}
                    <div class="px-2 pt-2 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                        Panel Institucional
                    </div>

                    <a href="{{ route('home') }}"
                       class="sidebar-btn-item">
                        <div class="sidebar-btn-icon bg-blue-500/20 text-blue-300 border border-blue-400/20">
                            <span class="material-symbols-outlined text-base">home</span>
                        </div>
                        <span class="flex-1 truncate">Portal Principal</span>
                    </a>
                @endif
            @endif
        </nav>

        {{-- Usuario actual e interactividad de sesión (pie del sidebar) --}}
        <div class="shrink-0 px-3 py-3 border-t border-slate-800 bg-[#08121f]" x-data="{ openUser: false }">
            <div class="relative">
                <div @click="openUser = !openUser"
                     class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-900/90 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 transition-all cursor-pointer group">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-bold text-xs shrink-0 bg-[#0051d5] group-hover:scale-105 transition-transform">
                        {{ strtoupper(substr(auth()->user()->Nombre ?? auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white font-semibold truncate text-xs group-hover:text-blue-300 transition-colors">
                            {{ auth()->user()->Nombre ?? auth()->user()->name ?? 'Usuario' }}
                        </p>
                        <p class="truncate text-[10px] text-slate-400">
                            {{ auth()->user()->role_label ?? 'Personal SETC' }}
                        </p>
                    </div>

                    {{-- Botón interactivo directo para cerrar sesión --}}
                    <form method="POST" action="{{ route('logout') }}" @click.stop class="shrink-0">
                        @csrf
                        <button type="submit"
                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-colors cursor-pointer"
                                title="Cerrar Sesión Inmediatamente">
                            <span class="material-symbols-outlined text-base">logout</span>
                        </button>
                    </form>
                </div>

                {{-- Menú desplegable interactivo de sesión --}}
                <div x-show="openUser"
                     @click.outside="openUser = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                     class="absolute bottom-full mb-2 left-0 right-0 bg-[#0c1a2e] border border-slate-700/80 rounded-xl shadow-2xl p-2 z-50 text-slate-200"
                     style="display: none;">
                    <div class="px-2.5 py-1.5 border-b border-slate-800 mb-1">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Sesión Institucional</span>
                        <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->Email ?? auth()->user()->email }}</p>
                        <span class="text-[10px] text-emerald-400 font-medium block mt-0.5">{{ auth()->user()->role_label }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-2 px-2.5 py-2 text-xs font-semibold text-rose-400 hover:bg-rose-500/15 rounded-lg transition-colors cursor-pointer text-left">
                            <span class="material-symbols-outlined text-base">logout</span>
                            <span>Cerrar Sesión</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- ════════════════════════════════════════════════════════ MAIN WRAPPER ════ --}}
    <div class="flex-1 flex flex-col h-full min-w-0 overflow-hidden">

        {{-- ── Topbar ── --}}
        <header class="shrink-0 flex items-center justify-between gap-4 px-6 h-16 z-30 bg-white dark:bg-[#0a1626] border-b border-slate-200/90 dark:border-slate-800 shadow-xs">

            {{-- Toggle sidebar (mobile) + Buscador --}}
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <button id="btn-sidebar-toggle" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden" aria-label="Toggle sidebar">
                    <span class="material-symbols-outlined">menu</span>
                </button>

                {{-- Global search context-aware --}}
                @php
                    $searchAction = route('portal.dashboard');
                    $searchParam = 'buscar';
                    if (auth()->user()?->esRevisor()) {
                        $searchAction = route('portal.mesa-dictamen');
                        $searchParam = 'buscar';
                    } elseif (auth()->user()?->esSupervisor()) {
                        $searchAction = route('portal.matriz-cargas');
                        $searchParam = 'buscar';
                    }
                @endphp
                <form action="{{ $searchAction }}" method="GET" class="relative flex-1 max-w-sm hidden sm:flex items-center" role="search">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 pointer-events-none">search</span>
                    <input type="text"
                           name="{{ $searchParam }}"
                           class="w-full h-9 pl-9 pr-8 text-xs bg-slate-50 dark:bg-slate-800/80 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0051d5]/20 placeholder:text-slate-400"
                           placeholder="Buscar por Folio, Administrado, RFC..."
                           value="{{ request($searchParam) ?? request('q') }}">
                </form>
            </div>

            {{-- Indicadores de estado del sistema & Controles --}}
            <div class="flex items-center gap-2.5 shrink-0">

                {{-- Red segura --}}
                <div class="hidden md:flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                    <span class="material-symbols-outlined text-sm">lock</span>
                    Red Segura PKI
                </div>

                {{-- Sede actual --}}
                <div class="hidden lg:flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                    <span class="material-symbols-outlined text-sm">location_on</span>
                    {{ auth()->user()->sede ?? 'Sede Central' }}
                </div>

                {{-- BOTÓN MODO OSCURO / CLARO --}}
                <button type="button"
                        onclick="toggleDarkMode()"
                        class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        title="Alternar Modo Oscuro / Claro"
                        aria-label="Alternar Modo Oscuro">
                    <span class="material-symbols-outlined block dark:hidden text-amber-500 text-xl">dark_mode</span>
                    <span class="material-symbols-outlined hidden dark:block text-yellow-300 text-xl">light_mode</span>
                </button>

                {{-- Notificaciones con Dropdown --}}
                <div class="relative" x-data="{ openNotif: false }">
                    <button @click="openNotif = !openNotif" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 relative transition-colors" aria-label="Notificaciones" id="btn-topbar-notifications">
                        <span class="material-symbols-outlined text-xl">notifications</span>
                    </button>
                    <div x-show="openNotif" @click.outside="openNotif = false"
                         x-transition
                         class="dropdown-menu absolute right-0 top-full mt-2 w-80 z-50 bg-white dark:bg-[#111f33] rounded-xl shadow-lg border border-slate-200 dark:border-slate-800" role="dialog" aria-label="Panel de notificaciones" style="display: none;">
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <span class="text-sm font-bold text-[#0b1c30] dark:text-white">Notificaciones</span>
                            <span class="text-xs text-slate-400 font-mono">{{ now()->format('d/m/Y') }}</span>
                        </div>
                        <div class="py-1 max-h-80 overflow-y-auto custom-scroll divide-y divide-slate-100 dark:divide-slate-800">
                            @php
                                $u = auth()->user();
                                $notificaciones = collect();

                                if ($u) {
                                    if ($u->esAdministrador()) {
                                        $notificaciones = \App\Models\Bitacora::latest('ID_Bitacora')
                                            ->take(6)
                                            ->get()
                                            ->map(fn($b) => [
                                                'icon' => 'admin_panel_settings',
                                                'color' => 'bg-slate-100 text-[#001229]',
                                                'title' => $b->Accion ?? 'Evento de Sistema',
                                                'desc' => $b->Descripcion,
                                                'time' => $b->Fecha_Creacion ? $b->Fecha_Creacion->diffForHumans() : 'Reciente',
                                                'link' => route('portal.auditoria'),
                                            ]);
                                    } elseif ($u->esSupervisor()) {
                                        $sinAsignar = \App\Models\Solicitud::doesntHave('asignaciones')
                                            ->latest('ID_Solicitud')
                                            ->take(3)
                                            ->get()
                                            ->map(fn($s) => [
                                                'icon' => 'person_alert',
                                                'color' => 'bg-rose-50 text-rose-700',
                                                'title' => 'Expediente por Asignar',
                                                'desc' => 'Folio: ' . ($s->folio?->Codigo_Folio ?? ('EXP-' . $s->ID_Solicitud)),
                                                'time' => $s->created_at ? $s->created_at->diffForHumans() : 'Reciente',
                                                'link' => route('portal.matriz-cargas'),
                                            ]);
                                        $porResolver = \App\Models\Solicitud::whereIn('Estado_Solicitud', ['Aprobado', 'Rechazado'])
                                            ->doesntHave('resoluciones')
                                            ->latest('updated_at')
                                            ->take(3)
                                            ->get()
                                            ->map(fn($s) => [
                                                'icon' => 'gavel',
                                                'color' => 'bg-indigo-50 text-indigo-700',
                                                'title' => 'Listo para Resolución',
                                                'desc' => 'Folio: ' . ($s->folio?->Codigo_Folio ?? ('EXP-' . $s->ID_Solicitud)),
                                                'time' => $s->updated_at ? $s->updated_at->diffForHumans() : 'Reciente',
                                                'link' => route('portal.supervisor-dashboard'),
                                            ]);
                                        $notificaciones = $sinAsignar->concat($porResolver);
                                    } elseif ($u->esRevisor()) {
                                        $misAsignaciones = \App\Models\AsignacionRevisor::where('ID_Revisor', $u->ID_Usuarios)
                                            ->where('Estado_Asignacion', 'Activo')
                                            ->with('solicitud.folio')
                                            ->latest('ID_Asignacion')
                                            ->take(4)
                                            ->get()
                                            ->map(fn($a) => [
                                                'icon' => 'assignment_turned_in',
                                                'color' => 'bg-blue-50 text-[#0051d5]',
                                                'title' => 'Expediente Asignado a tu Mesa',
                                                'desc' => 'Folio: ' . ($a->solicitud?->folio?->Codigo_Folio ?? ('EXP-' . $a->ID_Solicitud)),
                                                'time' => $a->created_at ? $a->created_at->diffForHumans() : 'Reciente',
                                                'link' => route('portal.mesa-dictamen'),
                                            ]);
                                        $notificaciones = $misAsignaciones;
                                    }
                                }
                            @endphp

                            @forelse($notificaciones as $n)
                                <a href="{{ $n['link'] }}" class="flex items-start gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                    <div class="w-8 h-8 rounded-lg {{ $n['color'] }} flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-base">{{ $n['icon'] }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block truncate">{{ $n['title'] }}</span>
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 block truncate">{{ $n['desc'] }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $n['time'] }}</span>
                                    </div>
                                </a>
                            @empty
                                <div class="py-6 text-center text-xs text-slate-400">
                                    No tienes notificaciones pendientes.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Ayuda con Dropdown --}}
                <div class="relative" x-data="{ openAyuda: false }">
                    <button @click="openAyuda = !openAyuda" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" aria-label="Ayuda">
                        <span class="material-symbols-outlined text-xl">help_outline</span>
                    </button>
                    <div x-show="openAyuda" @click.outside="openAyuda = false"
                         x-transition
                         class="dropdown-menu absolute right-0 top-full mt-2 w-72 z-50 bg-white dark:bg-[#111f33] rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 p-3" role="dialog" style="display: none;">
                        <span class="text-xs font-bold text-slate-800 dark:text-white block border-b border-slate-100 dark:border-slate-800 pb-2 mb-2">Mesa de Ayuda SETC</span>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Para soporte técnico o asistencia normativa, contacte a soporte@setc.gob.pe o llame a la extensión interna 401.
                        </p>
                    </div>
                </div>

                {{-- Tarjeta de Usuario en Topbar: Exclusiva para visualización del rol institucional (Solo Vista) --}}
                <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/60 select-none shadow-2xs">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-bold text-xs bg-gradient-to-br from-[#0051d5] to-[#1e40af] shrink-0 shadow-xs">
                        {{ strtoupper(substr(auth()->user()->Nombre ?? auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="hidden sm:flex flex-col text-left leading-tight">
                        <span class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate max-w-[140px]">
                            {{ auth()->user()->Nombre ?? auth()->user()->name ?? 'Usuario' }}
                        </span>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0 animate-pulse"></span>
                            <span class="text-[10px] font-bold text-[#0051d5] dark:text-blue-300 font-mono tracking-tight uppercase truncate max-w-[140px]">
                                {{ auth()->user()->role_label ?? 'Personal SETC' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- ── ÁREA DE CONTENIDO SCROLLABLE (SOLUCIÓN DEFINITIVA DE SCROLL) ── --}}
        <div class="flex-1 min-h-0 overflow-y-auto custom-scroll bg-slate-50 dark:bg-[#070e17]" id="portal-content-scroll">

            {{-- Breadcrumb interno --}}
            @hasSection('breadcrumb')
                <div class="px-6 lg:px-8 pt-4">
                    <nav class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium" aria-label="Ruta de navegación">
                        <a href="{{ route('portal.dashboard') }}" class="hover:text-slate-800 dark:hover:text-slate-200">Inicio</a>
                        @yield('breadcrumb')
                    </nav>
                </div>
            @endif

            {{-- Flash messages --}}
            @if(session('success') || session('error') || session('warning') || session('info'))
                <div class="px-6 lg:px-8 pt-3" id="flash-messages">
                    @if(session('success'))
                        <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs flex items-center gap-2">
                            <span class="material-symbols-outlined text-base text-emerald-600">check_circle</span>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs flex items-center gap-2">
                            <span class="material-symbols-outlined text-base text-rose-600">error</span>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif
                    @if(session('warning'))
                        <div class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200 text-xs flex items-center gap-2">
                            <span class="material-symbols-outlined text-base text-amber-600">warning</span>
                            <span>{{ session('warning') }}</span>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Errores de validación --}}
            @if($errors->any())
                <div class="px-6 lg:px-8 pt-3">
                    <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs flex items-start gap-2">
                        <span class="material-symbols-outlined text-base text-rose-600 mt-0.5">error</span>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- CONTENIDO PRINCIPAL --}}
            <main id="main-content" role="main" class="px-6 lg:px-8 py-6 pb-16">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════ SCRIPTS ═════════ --}}
    <script>
        // ── Alternar Modo Oscuro / Claro ───────────────────────────────────────
        function toggleDarkMode() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }

        // ── Toggle sidebar mobile ──────────────────────────────────────────────
        const btnSidebar = document.getElementById('btn-sidebar-toggle');
        const sidebar    = document.getElementById('sidebar');
        if (btnSidebar && sidebar) {
            btnSidebar.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
                sidebar.classList.toggle('fixed');
                sidebar.classList.toggle('inset-y-0');
                sidebar.classList.toggle('left-0');
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
