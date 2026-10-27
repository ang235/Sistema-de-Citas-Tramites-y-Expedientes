{{--
    Vista: Portal Ciudadano (Público)
    Layout: layouts.public
    URL: /
--}}
@extends('layouts.public')

@section('title', 'Inicio')
@section('meta_description', 'Inicia tus trámites digitales, agenda citas y consulta el estado de tus expedientes en el Sistema Estatal de Citas, Trámites y Expedientes.')

@section('breadcrumb')
    <span class="current">Inicio</span>
@endsection

@section('content')
<div class="max-w-screen-xl mx-auto px-6 lg:px-12 py-8 space-y-12">

    {{-- ══════════════════════════════════════════ HERO ══ --}}
    <section class="relative overflow-hidden rounded-2xl p-10 lg:p-14"
             style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-container) 100%);"
             aria-labelledby="hero-title">

        {{-- Glow decorativo --}}
        <div class="absolute right-0 top-0 w-96 h-96 rounded-full pointer-events-none"
             style="background: rgba(49,107,243,0.15); filter: blur(80px);" aria-hidden="true"></div>

        <div class="relative z-10 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full mb-4 text-label-sm"
                 style="background-color: rgba(255,255,255,0.1); color: rgba(255,255,255,0.8);">
                <span class="w-1.5 h-1.5 rounded-full" style="background-color: var(--color-tertiary-fixed-dim);"></span>
                Sistema Estatal de Citas, Trámites y Expedientes
            </div>
            <h1 id="hero-title" class="text-headline-xl text-white mb-4" style="font-family: var(--font-display);">
                Tus trámites,<br>digitales y sin filas
            </h1>
            <p class="text-body-lg mb-8" style="color: rgba(255,255,255,0.7); max-width: 50ch;">
                Inicia, sigue y resuelve tus trámites ante el gobierno desde cualquier dispositivo.
                Rápido, seguro y en todo momento.
            </p>
            <div class="flex flex-wrap gap-3">
                <x-setc-button href="/tramites" icon="description" size="md" class="!h-12 !px-6">
                    Iniciar un Trámite
                </x-setc-button>
                <x-setc-button href="/expedientes" variant="secondary" icon="search" size="md"
                               class="!h-12 !px-6 !bg-white/10 !text-white !border-white/20 hover:!bg-white/20">
                    Consultar Expediente
                </x-setc-button>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════ KPIs CIUDADANO ══ --}}
    <section aria-labelledby="kpis-title">
        <h2 id="kpis-title" class="sr-only">Indicadores del sistema</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-setc-kpi-card label="Trámites disponibles" value="148"
                             icon="description" trend="+12 este mes" trend-positive />
            <x-setc-kpi-card label="Ciudadanos atendidos" value="42,318"
                             icon="people" trend="Esta semana" />
            <x-setc-kpi-card label="Resolución digital" value="94%"
                             icon="verified" trend="↑ 3% vs mes ant." trend-positive />
            <x-setc-kpi-card label="Tiempo promedio" value="~2 min"
                             icon="timer" trend="Por trámite" />
        </div>
    </section>

    {{-- ══════════════════════════════════════════ CATEGORÍAS DE TRÁMITES ══ --}}
    <section aria-labelledby="tramites-title">
        <div class="flex items-center justify-between mb-6">
            <h2 id="tramites-title" class="text-headline-md" style="font-family: var(--font-display); color: var(--color-primary);">
                Trámites y Servicios
            </h2>
            <x-setc-button href="/tramites" variant="secondary" icon-end="arrow_forward" size="sm">
                Ver todos
            </x-setc-button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach([
                ['icon'=>'engineering', 'title'=>'Licencias de Construcción', 'count'=>'23 trámites', 'status'=>'info'],
                ['icon'=>'store',       'title'=>'Licencias Comerciales',     'count'=>'18 trámites', 'status'=>'success'],
                ['icon'=>'local_fire_department', 'title'=>'ITSE — Inspecciones', 'count'=>'12 trámites', 'status'=>'warning'],
                ['icon'=>'landscape',  'title'=>'Impacto Ambiental',         'count'=>'9 trámites',  'status'=>'success'],
                ['icon'=>'business',   'title'=>'Habilitación Urbana',        'count'=>'7 trámites',  'status'=>'info'],
                ['icon'=>'gavel',      'title'=>'Recursos Administrativos',   'count'=>'14 trámites', 'status'=>'neutral'],
            ] as $cat)
                <a href="/tramites/{{ Str::slug($cat['title']) }}"
                   class="card group flex flex-col gap-4 p-5 hover:shadow-hover transition-shadow duration-200 no-underline">
                    <div class="flex items-start justify-between">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                             style="background-color: var(--color-surface-container-low);">
                            <span class="material-symbols-outlined" style="color: var(--color-secondary);">{{ $cat['icon'] }}</span>
                        </div>
                        <x-setc-badge :status="$cat['status']">{{ $cat['count'] }}</x-setc-badge>
                    </div>
                    <div>
                        <h3 class="text-label-md font-semibold" style="color: var(--color-on-surface);">
                            {{ $cat['title'] }}
                        </h3>
                    </div>
                    <div class="flex items-center gap-1 text-label-sm" style="color: var(--color-secondary);">
                        <span>Ver trámites</span>
                        <span class="material-symbols-outlined text-sm transition-transform group-hover:translate-x-0.5">arrow_forward</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ══════════════════════════════════════════ ACCESOS RÁPIDOS ══ --}}
    <section class="grid grid-cols-1 md:grid-cols-2 gap-6" aria-label="Accesos rápidos">

        {{-- Consultar folio --}}
        <x-setc-card>
            <x-slot:header>
                <h2 class="text-headline-sm flex items-center gap-2" style="font-family: var(--font-display); color: var(--color-primary);">
                    <span class="material-symbols-outlined" style="color: var(--color-secondary);">search</span>
                    Consultar Estado de Expediente
                </h2>
            </x-slot:header>

            <form action="/expedientes/consultar" method="GET" class="flex flex-col gap-4" role="search">
                <x-setc-input
                    name="folio"
                    label="Número de Folio"
                    placeholder="Ej. SETC-2025-8841"
                    icon="tag"
                    helper="Ingresa el folio que recibiste al iniciar tu trámite"
                    required />
                <x-setc-button type="submit" icon="search">
                    Consultar Estado
                </x-setc-button>
            </form>

            <x-slot:footer>
                <p class="text-body-sm" style="color: var(--color-on-surface-variant);">
                    ¿No tienes tu folio?
                    <a href="/tramites/buscar" class="font-semibold" style="color: var(--color-secondary);">Buscar por CURP</a>
                </p>
            </x-slot:footer>
        </x-setc-card>

        {{-- Agendar cita --}}
        <x-setc-card>
            <x-slot:header>
                <h2 class="text-headline-sm flex items-center gap-2" style="font-family: var(--font-display); color: var(--color-primary);">
                    <span class="material-symbols-outlined" style="color: var(--color-secondary);">calendar_month</span>
                    Agendar Cita Presencial o Virtual
                </h2>
            </x-slot:header>

            <div class="space-y-3">
                <p class="text-body-sm" style="color: var(--color-on-surface-variant);">
                    Elige la modalidad que prefieras para la revisión técnica de tu expediente.
                </p>
                <div class="grid grid-cols-2 gap-3">
                    @foreach([
                        ['icon'=>'apartment',  'title'=>'Presencial', 'sub'=>'Sede Central', 'route'=>'/citas/presencial'],
                        ['icon'=>'videocam',   'title'=>'Virtual',    'sub'=>'GovMeet cifrado', 'route'=>'/citas/virtual'],
                    ] as $mod)
                        <a href="{{ $mod['route'] }}"
                           class="flex flex-col items-start gap-2 p-4 rounded-xl border transition-colors hover:shadow-hover no-underline"
                           style="border-color: var(--color-outline-variant); background-color: var(--color-surface-container-low);">
                            <span class="material-symbols-outlined" style="color: var(--color-secondary);">{{ $mod['icon'] }}</span>
                            <div>
                                <p class="text-label-md font-semibold" style="color: var(--color-on-surface);">{{ $mod['title'] }}</p>
                                <p class="text-body-sm" style="color: var(--color-on-surface-variant);">{{ $mod['sub'] }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <x-slot:footer>
                <div class="flex items-center gap-2 text-body-sm" style="color: var(--color-on-surface-variant);">
                    <span class="material-symbols-outlined text-sm" style="color: var(--color-success);">timer</span>
                    Tiempo promedio de agenda: <strong>~2 minutos</strong>
                </div>
            </x-slot:footer>
        </x-setc-card>
    </section>

</div>
@endsection
