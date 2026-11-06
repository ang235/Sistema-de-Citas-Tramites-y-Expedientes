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
            <span class="px-3 py-1 rounded-full bg-[#eff4ff] text-[#0051d5] text-xs font-semibold">Bandeja Ciudadana</span>
            <h1 class="text-3xl font-bold tracking-tight text-[#001229] mt-2" style="font-family: var(--font-display);">
                Seguimiento de Expedientes
            </h1>
            <p class="text-sm text-[#44474d] mt-1">
                Consulta la trazabilidad legal, ciclo de revisiones y descarga las resoluciones emitidas.
            </p>
        </div>
        <a href="{{ route('tramites.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#001229] hover:bg-[#0f2744] text-white rounded-xl text-xs font-semibold transition-all shadow-sm shrink-0">
            <span class="material-symbols-outlined text-base">add</span>
            <span>Nuevo Trámite</span>
        </a>
    </div>

    {{-- Buscador por Folio --}}
    <div class="p-4 rounded-xl bg-white border border-slate-200/80 shadow-xs">
        <form action="{{ route('expedientes.index') }}" method="GET" class="relative flex items-center">
            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">travel_explore</span>
            <input type="text"
                   name="folio"
                   value="{{ $busquedaFolio ?? request('folio') }}"
                   placeholder="Buscar expediente por folio (ej. SETC-2026-00001)..."
                   class="w-full pl-10 pr-24 py-2.5 rounded-lg border border-slate-300 text-sm text-[#0b1c30] placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0051d5] font-mono">
            <button type="submit" class="absolute right-1.5 px-4 py-1.5 bg-[#0051d5] hover:bg-[#003ea8] text-white rounded-md text-xs font-semibold transition-colors">
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
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs hover:shadow-md transition-all flex flex-col md:flex-row md:items-center justify-between gap-6 group">
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="px-2.5 py-1 rounded-md bg-[#eff4ff] text-[#0051d5] font-mono text-xs font-bold">
                            {{ $folioCodigo }}
                        </span>
                        @if ($tieneResolucion)
                            <span class="px-2.5 py-0.5 rounded-full bg-[#ecfdf5] text-[#065f46] text-xs font-semibold flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#10b981]"></span>
                                Resuelto ({{ $solicitud->resoluciones->last()->Tipo_Resolucion }})
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-900 border border-amber-200/60 text-xs font-semibold flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                En trámite de revisión
                            </span>
                        @endif
                        <span class="text-xs text-slate-400">
                            {{ $solicitud->Fecha_Creacion_Solicitud ? $solicitud->Fecha_Creacion_Solicitud->format('d/m/Y H:i') : '' }}
                        </span>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-[#001229] group-hover:text-[#0051d5] transition-colors">
                            {{ $solicitud->versionTramite?->tipoTramite?->Nombre ?? 'Solicitud Administrativa' }}
                        </h2>
                        <p class="text-xs text-[#44474d] mt-1">
                            Versión {{ $solicitud->versionTramite?->No_Version ?? '1.0' }} • {{ $documentosCount }} {{ Str::plural('recaudo', $documentosCount) }} asociados
                        </p>
                    </div>
                </div>

                {{-- Botón a Timeline --}}
                <div class="shrink-0">
                    <a href="{{ route('expedientes.show', $folioCodigo) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-[#0051d5] text-[#0b1c30] hover:text-white text-xs font-semibold transition-all">
                        <span>Ver Timeline & Estado</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="py-16 text-center space-y-4 bg-white rounded-2xl border border-slate-200/80">
                <span class="material-symbols-outlined text-4xl text-slate-400">folder_off</span>
                <p class="text-base font-semibold text-[#001229]">No se encontraron expedientes registrados.</p>
                <a href="{{ route('tramites.index') }}" class="inline-flex items-center gap-1 text-xs text-[#0051d5] font-semibold hover:underline">
                    <span>Explorar catálogo para iniciar tu primer trámite</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        @endforelse
    </div>

    @if($solicitudes instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="pt-4">
            {{ $solicitudes->links() }}
        </div>
    @endif
</div>
@endsection

