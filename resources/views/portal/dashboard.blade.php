@extends('layouts.portal')

@section('title', 'Dashboard Operativo')

@section('breadcrumb')
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <a href="{{ route('portal.dashboard') }}">Operación & Dictamen</a>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="current">Dashboard Operativo</span>
@endsection

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    {{-- ══════════════════════════════════════════ CABECERA DE SALA ══ --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 lg:p-8 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full bg-[#eff4ff] text-[#0051d5] text-xs font-semibold">
                    Turno Ordinario • Ventanilla Metropolitana
                </span>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-[#065f46] text-xs font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#10b981] animate-pulse"></span>
                    En línea
                </span>
            </div>
            <h1 class="text-2xl lg:text-3xl font-bold tracking-tight text-[#001229]" style="font-family: var(--font-display);">
                Mesa Técnica de Operación y Despacho
            </h1>
            <p class="text-xs text-[#44474d]">
                Monitoreo de SLAs, expedientes en cola de dictamen y cumplimiento de turnos ciudadanos.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('portal.matriz-cargas') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-[#0b1c30] text-xs font-semibold transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-base">balance</span>
                <span>Matriz de Cargas</span>
            </a>
            <a href="{{ route('portal.mesa-dictamen') }}" class="px-4 py-2.5 rounded-xl bg-[#001229] hover:bg-[#0f2744] text-white text-xs font-semibold transition-all flex items-center gap-2 shadow-xs">
                <span class="material-symbols-outlined text-base">gavel</span>
                <span>Mesa de Dictamen</span>
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════ METRICAS / KPIS ══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Expedientes --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between">
                <span class="text-xs text-[#44474d] font-medium">Expedientes Totales</span>
                <div class="w-8 h-8 rounded-lg bg-[#eff4ff] text-[#0051d5] flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">folder_open</span>
                </div>
            </div>
            <div>
                <span class="text-2xl font-bold text-[#001229]">{{ $metricas['total_solicitudes'] ?? 0 }}</span>
                <span class="text-[11px] text-slate-400 block mt-0.5">En la plataforma</span>
            </div>
        </div>

        {{-- Pendientes de Asignar --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between">
                <span class="text-xs text-[#44474d] font-medium">Por Asignar</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">assignment_late</span>
                </div>
            </div>
            <div>
                <span class="text-2xl font-bold text-amber-700">{{ $metricas['pendientes_asignar'] ?? 0 }}</span>
                <span class="text-[11px] text-slate-400 block mt-0.5">Requieren revisor técnico</span>
            </div>
        </div>

        {{-- En Dictamen Activo --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between">
                <span class="text-xs text-[#44474d] font-medium">En Revisión / Dictamen</span>
                <div class="w-8 h-8 rounded-lg bg-[#eff4ff] text-[#0051d5] flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">rate_review</span>
                </div>
            </div>
            <div>
                <span class="text-2xl font-bold text-[#0051d5]">{{ $metricas['en_revision'] ?? 0 }}</span>
                <span class="text-[11px] text-slate-400 block mt-0.5">L1 / L2 en proceso</span>
            </div>
        </div>

        {{-- Citas Programadas Hoy --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between">
                <span class="text-xs text-[#44474d] font-medium">Citas Hoy</span>
                <div class="w-8 h-8 rounded-lg bg-[#ecfdf5] text-[#059669] flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">calendar_today</span>
                </div>
            </div>
            <div>
                <span class="text-2xl font-bold text-[#059669]">{{ $metricas['citas_hoy'] ?? 0 }}</span>
                <span class="text-[11px] text-slate-400 block mt-0.5">Turnos agendados</span>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════ TABLA DE EXPEDIENTES RECIENTES ══ --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden space-y-4 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-lg font-bold text-[#001229]" style="font-family: var(--font-display);">
                    Expedientes Recientes y Estado de Dictamen
                </h2>
                <p class="text-xs text-[#44474d] mt-0.5">
                    Últimas solicitudes ingresadas a la ventanilla única telemática.
                </p>
            </div>
            <a href="{{ route('portal.matriz-cargas') }}" class="text-xs text-[#0051d5] font-semibold hover:underline flex items-center gap-1">
                <span>Gestionar asignaciones</span>
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 text-slate-400 uppercase tracking-wider font-semibold">
                        <th class="py-3 px-3">Folio</th>
                        <th class="py-3 px-3">Ciudadano</th>
                        <th class="py-3 px-3">Trámite Normativo</th>
                        <th class="py-3 px-3">Revisor Asignado</th>
                        <th class="py-3 px-3">Fecha Radicación</th>
                        <th class="py-3 px-3 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($solicitudesRecientes ?? [] as $sol)
                        @php
                            $folioCod = $sol->folio?->Codigo_Folio ?? '#' . $sol->ID_Solicitud;
                            $asignacion = $sol->asignaciones->last();
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3.5 px-3 font-mono font-bold text-[#0051d5]">
                                {{ $folioCod }}
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="font-semibold text-[#0b1c30] block">{{ $sol->solicitante?->Nombre ?? 'N/A' }}</span>
                                <span class="text-[11px] text-slate-400">{{ $sol->solicitante?->Email }}</span>
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="text-[#0b1c30] font-medium block">{{ $sol->versionTramite?->tipoTramite?->Nombre ?? 'Trámite' }}</span>
                                <span class="text-[11px] text-slate-400">Ver. {{ $sol->versionTramite?->No_Version ?? '1.0' }}</span>
                            </td>
                            <td class="py-3.5 px-3">
                                @if ($asignacion && $asignacion->revisor)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-[#eff4ff] text-[#0051d5] font-semibold text-[11px]">
                                        <span class="material-symbols-outlined text-xs">person</span>
                                        {{ $asignacion->revisor->Nombre }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-900 border border-amber-200 text-[11px] font-semibold">
                                        Sin asignar
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 text-slate-500">
                                {{ $sol->Fecha_Creacion_Solicitud ? $sol->Fecha_Creacion_Solicitud->format('d/m/Y H:i') : '' }}
                            </td>
                            <td class="py-3.5 px-3 text-right">
                                <a href="{{ route('portal.expedientes.evaluar', $sol->ID_Solicitud) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-[#001229] hover:bg-[#0f2744] text-white font-semibold transition-colors">
                                    <span>Evaluar</span>
                                    <span class="material-symbols-outlined text-xs">arrow_forward</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                No hay expedientes registrados aún en el sistema.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
