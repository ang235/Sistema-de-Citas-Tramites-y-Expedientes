@extends('layouts.portal')

@section('title', 'Panel de Supervisión Operativa')

@section('breadcrumb')
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span>Supervisión Operativa</span>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="current">Mando de Supervisión</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ═══════════════════════════════════ HEADER SUPERVISOR GENERAL ══ --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-2 border-b border-slate-200/80 dark:border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-400 mb-1">
                <span class="material-symbols-outlined text-base">verified</span>
                <span>Mando de Supervisión & Dictamen Normativo</span>
            </div>
            <h1 class="text-2xl lg:text-3xl font-bold text-[#001229] dark:text-white tracking-tight" style="font-family: var(--font-display);">
                Control y Supervisión de Expedientes
            </h1>
            <p class="text-xs lg:text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">
                Supervise la distribución de carga a revisores técnicos, controle los plazos normativos (SLA) y formalice las resoluciones definitivas de expedientes.
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('portal.matriz-cargas') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#001229] hover:bg-[#0f2744] dark:bg-blue-600 dark:hover:bg-blue-700 text-white text-xs font-semibold shadow-xs transition-all">
                <span class="material-symbols-outlined text-sm">balance</span>
                <span>Matriz de Cargas</span>
            </a>
        </div>
    </div>

    {{-- KPIS DE SUPERVISIÓN --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        {{-- Sin Asignar --}}
        <div class="p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Sin Asignación</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">person_alert</span>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-rose-700 dark:text-rose-400">{{ $metricas['sin_asignar'] ?? 0 }}</div>
                <div class="flex items-center gap-1 mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                    <span>Requieren revisor técnico</span>
                </div>
            </div>
        </div>

        {{-- En Evaluación --}}
        <div class="p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">En Evaluación</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">pending_actions</span>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-[#0051d5] dark:text-blue-400">{{ $metricas['en_evaluacion'] ?? 0 }}</div>
                <div class="flex items-center gap-1 mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                    <span>En mesas de revisión</span>
                </div>
            </div>
        </div>

        {{-- Con Observaciones --}}
        <div class="p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Con Observaciones</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">warning</span>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-amber-800 dark:text-amber-300">{{ $metricas['con_observaciones'] ?? 0 }}</div>
                <div class="flex items-center gap-1 mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                    <span>En subsanación por ciudadano</span>
                </div>
            </div>
        </div>

        {{-- Pendientes de Resolución --}}
        <div class="p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Pend. Resolución</span>
                <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">gavel</span>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-purple-700 dark:text-purple-400">{{ $metricas['pendientes_resolucion'] ?? 0 }}</div>
                <div class="flex items-center gap-1 mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                    <span>Dictaminados sin firma</span>
                </div>
            </div>
        </div>

        {{-- Revisores Activos --}}
        <div class="p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Revisores Activos</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">group</span>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-emerald-700 dark:text-emerald-400">{{ $metricas['revisores_activos'] ?? 0 }}</div>
                <div class="flex items-center gap-1 mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                    <span>Cuerpo técnico operativo</span>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN PRINCIPAL: EXPEDIENTES POR ASIGNAR Y SUPERVISIÓN --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- COLUMNA IZQUIERDA: COLA DE ASIGNACIÓN (2 columnas) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- EXPEDIENTES SIN ASIGNAR (BACKLOG) --}}
            <div class="bg-white dark:bg-[#111f33] rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></div>
                        <h2 class="text-sm font-bold text-[#001229] dark:text-white" style="font-family: var(--font-display);">
                            Expedientes Nuevos Pendientes de Asignación
                        </h2>
                    </div>
                    <a href="{{ route('portal.matriz-cargas') }}" class="text-xs font-semibold text-[#0051d5] dark:text-blue-400 hover:underline flex items-center gap-1">
                        <span>Asignar en Matriz</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800 uppercase tracking-wider text-[10px]">
                                <th class="py-2.5 px-4">Folio</th>
                                <th class="py-2.5 px-4">Solicitante</th>
                                <th class="py-2.5 px-4">Trámite</th>
                                <th class="py-2.5 px-4">Fecha</th>
                                <th class="py-2.5 px-4 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($expedientesPorAsignar ?? [] as $exp)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="py-3 px-4 font-mono font-bold text-slate-800 dark:text-slate-200">
                                        {{ $exp->folio->Codigo_Folio ?? 'EXP-'.$exp->ID_Solicitud }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="font-medium text-slate-900 dark:text-white block">{{ $exp->solicitante->Nombre ?? 'N/D' }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $exp->solicitante->Email ?? '' }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="truncate block max-w-xs text-slate-700 dark:text-slate-300">
                                            {{ $exp->versionTramite->tipoTramite->Nombre ?? 'Trámite General' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 dark:text-slate-400">
                                        {{ $exp->Fecha_Registro ? \Carbon\Carbon::parse($exp->Fecha_Registro)->format('d/m/Y') : 'Hoy' }}
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('portal.matriz-cargas') }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-[#0051d5] hover:bg-[#0040a8] text-white text-[11px] font-semibold transition-colors">
                                            <span class="material-symbols-outlined text-xs">assignment_ind</span>
                                            <span>Asignar</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400">
                                        <span class="material-symbols-outlined text-3xl block mb-1 text-emerald-500">task_alt</span>
                                        <p class="font-medium text-slate-700 dark:text-slate-300">Todos los expedientes han sido asignados.</p>
                                        <p class="text-[11px] text-slate-400">No hay trámites rezagados pendientes de asignación en cola.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- EXPEDIENTES EN SUPERVISIÓN ACTIVA --}}
            <div class="bg-white dark:bg-[#111f33] rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-[#001229] dark:text-white" style="font-family: var(--font-display);">
                        Monitoreo de Expedientes en Trámite
                    </h2>
                    <a href="{{ route('portal.matriz-cargas') }}" class="text-xs font-semibold text-[#0051d5] dark:text-blue-400 hover:underline">
                        Ver Matriz de Cargas
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800 uppercase tracking-wider text-[10px]">
                                <th class="py-2.5 px-4">Folio</th>
                                <th class="py-2.5 px-4">Revisor Designado</th>
                                <th class="py-2.5 px-4">Estado Dictamen</th>
                                <th class="py-2.5 px-4 text-right">Resolución</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($expedientesSupervision ?? [] as $exp)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="py-3 px-4 font-mono font-bold text-[#0051d5] dark:text-blue-400">
                                        {{ $exp->folio->Codigo_Folio ?? 'EXP-'.$exp->ID_Solicitud }}
                                    </td>
                                    <td class="py-3 px-4">
                                        @php
                                            $rev = $exp->asignaciones->where('Estado_Asignacion', 'Activo')->last()->revisor ?? null;
                                        @endphp
                                        @if($rev)
                                            <span class="font-medium text-slate-800 dark:text-slate-200 block">{{ $rev->Nombre }}</span>
                                            <span class="text-[10px] text-slate-400">Mesa Técnica</span>
                                        @else
                                            <span class="text-slate-400 italic">Sin revisor activo</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @php
                                            $estadoStyles = [
                                                'Registrado' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                                'En_Revision' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300',
                                                'Observado' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300',
                                                'Aprobado' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
                                                'Rechazado' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $estadoStyles[$exp->Estado_Solicitud] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ str_replace('_', ' ', $exp->Estado_Solicitud) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        @if($exp->resoluciones->isNotEmpty())
                                            <span class="inline-flex items-center gap-1 text-[11px] text-emerald-600 font-semibold">
                                                <span class="material-symbols-outlined text-xs">verified</span>
                                                <span>Emitida</span>
                                            </span>
                                        @elseif(in_array($exp->Estado_Solicitud, ['Aprobado', 'Rechazado']))
                                            <a href="{{ route('portal.expedientes.show', $exp->ID_Solicitud) }}"
                                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-purple-600 hover:bg-purple-700 text-white text-[11px] font-semibold transition-colors">
                                                <span class="material-symbols-outlined text-xs">gavel</span>
                                                <span>Formalizar</span>
                                            </a>
                                        @else
                                            <span class="text-slate-400 text-[11px]">En examen</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-slate-400">
                                        No hay expedientes en curso.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- COLUMNA DERECHA: CARGA DE REVISORES TÉCNICOS (1 columna) --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-[#111f33] rounded-2xl border border-slate-200/90 dark:border-slate-800 p-5 shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Carga por Revisor Técnico
                    </h3>
                    <a href="{{ route('portal.matriz-cargas') }}" class="text-[11px] font-semibold text-[#0051d5] dark:text-blue-400 hover:underline">
                        Balancear
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($revisores ?? [] as $rev)
                        <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-900 dark:text-white">{{ $rev->Nombre }}</span>
                                <span class="font-mono font-bold text-blue-600 dark:text-blue-400">{{ $rev->asignados_activos ?? 0 }} asignados</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                @php
                                    $porcentaje = min(100, (($rev->asignados_activos ?? 0) / 10) * 100);
                                @endphp
                                <div class="bg-[#0051d5] dark:bg-blue-500 h-1.5 rounded-full" style="width: {{ $porcentaje }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400 text-center py-4">
                            No hay revisores registrados.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Resumen Normativo --}}
            <div class="bg-gradient-to-br from-[#001229] to-[#0a2540] rounded-2xl p-5 text-white shadow-xs">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-300 mb-2">
                    <span class="material-symbols-outlined text-sm">schedule</span>
                    <span>Plazos Normativos</span>
                </div>
                <h4 class="text-sm font-bold text-white mb-1">Supervisión de Calidad</h4>
                <p class="text-xs text-slate-300 leading-relaxed mb-4">
                    Asegure que los dictámenes técnicos emitidos por los revisores cuenten con motivación fundada antes de firmar resoluciones.
                </p>
                <a href="{{ route('portal.matriz-cargas') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-300 hover:text-white transition-colors">
                    <span>Ir a Matriz de Distribución</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
