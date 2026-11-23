@extends('layouts.public')

@section('title', 'Mis Expedientes y Seguimiento')
@section('meta_description', 'Consulta el avance, dictámenes y resoluciones de tus trámites en el SETC.')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="breadcrumb-item">SETC</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <span class="breadcrumb-item current">Mis Expedientes</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-12 py-8 w-full space-y-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 rounded-full bg-[#eff4ff] dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300 text-xs font-semibold">Bandeja Ciudadana</span>
            <h1 class="text-3xl font-bold tracking-tight text-[#001229] dark:text-white mt-2" style="font-family: var(--font-display);">
                Seguimiento de Expedientes
            </h1>
            <p class="text-sm text-[#44474d] dark:text-slate-400 mt-1">
                Consulta la trazabilidad legal, ciclo de revisiones y descarga las resoluciones emitidas.
            </p>
        </div>
        <a href="{{ route('tramites.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#001229] dark:bg-blue-600 hover:bg-[#0f2744] dark:hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition-all shadow-sm shrink-0 cursor-pointer">
            <span class="material-symbols-outlined text-base">add</span>
            <span>Nuevo Trámite</span>
        </a>
    </div>

    {{-- Buscador por Folio --}}
    <div class="p-4 rounded-xl bg-white dark:bg-[#0e1c2e] border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <form action="{{ route('expedientes.index') }}" method="GET" class="relative flex items-center">
            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-lg pointer-events-none">travel_explore</span>
            <input type="text"
                   name="folio"
                   value="{{ $busquedaFolio ?? request('folio') }}"
                   placeholder="Buscar expediente por folio (ej. SETC-2026-00001)..."
                   class="w-full pl-10 pr-24 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0b1624] text-sm text-[#0b1c30] dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-[#0051d5] font-mono">
            <button type="submit" class="absolute right-1.5 px-4 py-1.5 bg-[#0051d5] hover:bg-[#003ea8] text-white rounded-md text-xs font-semibold transition-colors cursor-pointer">
                Buscar
            </button>
        </form>
    </div>

    {{-- Lista de Expedientes --}}
    <div class="space-y-4">
        @forelse ($solicitudes as $solicitud)
            @php
                $folioCodigo = $solicitud->folio?->Codigo_Folio ?? sprintf('SETC-%s-%05d', date('Y'), $solicitud->ID_Solicitud);
                $tieneResolucion = $solicitud->resoluciones->isNotEmpty();
                $documentosCount = $solicitud->documentos->count();
            @endphp
            <div class="bg-white dark:bg-[#0e1c2e] rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs hover:shadow-md transition-all flex flex-col md:flex-row md:items-center justify-between gap-6 group">
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="px-2.5 py-1 rounded-md bg-[#eff4ff] dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300 font-mono text-xs font-bold">
                            {{ $folioCodigo }}
                        </span>
                        @if ($tieneResolucion)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                Resolución Emitida
                            </span>
                        @elseif ($solicitud->Estado_Solicitud === 'Rechazado' || $solicitud->documentos->where('Estado_Doc', 'Rechazado')->isNotEmpty())
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                Rechazado
                            </span>
                        @elseif ($solicitud->Estado_Solicitud === 'Observado')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                Observado
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300">
                                {{ str_replace('_', ' ', $solicitud->Estado_Solicitud) }}
                            </span>
                        @endif
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-[#001229] dark:text-white group-hover:text-[#0051d5] dark:group-hover:text-blue-400 transition-colors">
                            {{ $solicitud->versionTramite?->tipoTramite?->Nombre ?? 'Expediente Telemático' }}
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                            {{ $solicitud->Descripcion ?? 'Sin observaciones iniciales de registro.' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400 dark:text-slate-500">
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">calendar_today</span>
                            Ingresado el {{ $solicitud->Fecha_Creacion_Solicitud ? $solicitud->Fecha_Creacion_Solicitud->format('d/m/Y') : ($solicitud->created_at ? $solicitud->created_at->format('d/m/Y') : '--') }}
                        </span>
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">attach_file</span>
                            {{ $documentosCount }} {{ Str::plural('documento', $documentosCount) }} cargados
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('expedientes.show', $folioCodigo) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#001229] dark:bg-blue-600 hover:bg-[#0f2744] dark:hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition-all shadow-sm cursor-pointer">
                        <span>Ver Trazabilidad</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="py-16 text-center space-y-4 bg-white dark:bg-[#0e1c2e] rounded-2xl border border-slate-200/80 dark:border-slate-800">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-[#eff4ff] dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl">folder_off</span>
                </div>
                <div class="space-y-1">
                    <h3 class="text-base font-bold text-[#001229] dark:text-white">No tienes expedientes registrados</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                        Inicia una nueva solicitud desde el catálogo de trámites oficiales para generar tu primer folio telemático.
                    </p>
                </div>
                <a href="{{ route('tramites.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#001229] dark:bg-blue-600 hover:bg-[#0f2744] dark:hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition-all shadow-sm cursor-pointer">
                    <span>Explorar Catálogo</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
