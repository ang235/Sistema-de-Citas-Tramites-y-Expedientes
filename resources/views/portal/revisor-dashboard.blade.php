@extends('layouts.portal')

@section('title', 'Mando de Revisión Técnica')

@section('breadcrumb')
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span>Revisión Técnica</span>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="current">Mando de Revisión Técnica</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ═══════════════════════════════════ HEADER REVISOR TÉCNICO ══ --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-2 border-b border-slate-200/80 dark:border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400 mb-1">
                <span class="material-symbols-outlined text-base">verified</span>
                <span>Revisor Técnico Oficial • Calificación de Recaudos</span>
            </div>
            <h1 class="text-2xl lg:text-3xl font-bold text-[#001229] dark:text-white tracking-tight" style="font-family: var(--font-display);">
                Mando de Revisión Técnica
            </h1>
            <p class="text-xs lg:text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">
                Panel especializado para la calificación documental de expedientes asignados a su mesa por el supervisor.
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('portal.mesa-dictamen') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#001229] hover:bg-[#0f2744] dark:bg-blue-600 dark:hover:bg-blue-700 text-white text-xs font-semibold shadow-xs transition-all">
                <span class="material-symbols-outlined text-sm">fact_check</span>
                <span>Mesa de Calificación Documental</span>
            </a>
        </div>
    </div>

    {{-- KPIS DEL REVISOR TÉCNICO --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        {{-- Asignados por Supervisor --}}
        <div class="p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Asignados Activos</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">assignment_ind</span>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-[#001229] dark:text-white">{{ $metricas['asignados'] ?? 0 }}</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Expedientes en su carga</div>
            </div>
        </div>

        {{-- En Revisión Activa --}}
        <div class="p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">En Revisión</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">pending_actions</span>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-sky-700 dark:text-sky-400">{{ $metricas['en_revision'] ?? 0 }}</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">En dictamen activo</div>
            </div>
        </div>

        {{-- Con Observaciones --}}
        <div class="p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Observados</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">warning</span>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-amber-800 dark:text-amber-300">{{ $metricas['con_observaciones'] ?? 0 }}</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Esperando subsanación</div>
            </div>
        </div>

        {{-- Dictaminados --}}
        <div class="p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Dictaminados</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">check_circle</span>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-emerald-700 dark:text-emerald-400">{{ $metricas['dictaminados'] ?? 0 }}</div>
                <div class="mt-1 text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">Revisión concluida</div>
            </div>
        </div>

        {{-- Documentos Pendientes --}}
        <div class="p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Recaudos Pendientes</span>
                <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">description</span>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-purple-700 dark:text-purple-400">{{ $metricas['documentos_pendientes'] ?? 0 }}</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Por calificar</div>
            </div>
        </div>
    </div>

    {{-- TABLA DE EXPEDIENTES ASIGNADOS AL REVISOR --}}
    <div class="bg-white dark:bg-[#111f33] rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-[#001229] dark:text-white" style="font-family: var(--font-display);">
                    Expedientes en su Carga de Trabajo
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Expedientes formalmente asignados por el supervisor para evaluación de documentos y emisión de dictamen técnico.
                </p>
            </div>
            <a href="{{ route('portal.mesa-dictamen') }}" class="text-xs font-semibold text-[#0051d5] dark:text-blue-400 hover:underline inline-flex items-center gap-1">
                <span>Ir a consola completa</span>
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800 uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4">Folio / Fecha</th>
                        <th class="py-3 px-4">Solicitante</th>
                        <th class="py-3 px-4">Trámite Institucional</th>
                        <th class="py-3 px-4 text-center">Recaudos</th>
                        <th class="py-3 px-4">Estado</th>
                        <th class="py-3 px-4 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($expedientesAsignados as $sol)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-4">
                                <span class="font-mono font-bold text-[#0051d5] dark:text-blue-400 block">
                                    {{ $sol->folio->Codigo_Folio ?? 'EXP-'.$sol->ID_Solicitud }}
                                </span>
                                <span class="text-[10px] text-slate-400">
                                    {{ $sol->Fecha_Registro ? \Carbon\Carbon::parse($sol->Fecha_Registro)->format('d/m/Y H:i') : 'N/D' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-900 dark:text-white block">{{ $sol->solicitante->Nombre ?? 'N/D' }}</span>
                                <span class="text-[11px] text-slate-400">{{ $sol->solicitante->Email ?? '' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-slate-800 dark:text-slate-200 block truncate max-w-xs">
                                    {{ $sol->versionTramite->tipoTramite->Nombre ?? 'Trámite General' }}
                                </span>
                                <span class="text-[10px] text-slate-400">Versión {{ $sol->versionTramite->Numero_Version ?? '1.0' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @php
                                    $docsTotal = $sol->documentos->count();
                                    $docsAprobados = $sol->documentos->where('Estado_Doc', 'Aprobado')->count();
                                    $docsPendientes = $sol->documentos->where('Estado_Doc', 'Pendiente')->count();
                                @endphp
                                <span class="inline-flex items-center gap-1 font-mono font-bold px-2 py-0.5 rounded {{ $docsPendientes > 0 ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' }}">
                                    {{ $docsAprobados }}/{{ $docsTotal }} calificados
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $estadoStyles = [
                                        'Registrado' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                        'En_Revision' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300',
                                        'Observado' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300',
                                        'Aprobado' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
                                        'Rechazado' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $estadoStyles[$sol->Estado_Solicitud] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ str_replace('_', ' ', $sol->Estado_Solicitud) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('portal.mesa-dictamen', ['expediente' => $sol->ID_Solicitud]) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-[#001229] hover:bg-[#0f2744] dark:bg-blue-600 dark:hover:bg-blue-700 text-white text-xs font-semibold shadow-xs transition-colors">
                                    <span class="material-symbols-outlined text-sm">fact_check</span>
                                    <span>Calificar Recaudos</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl block mb-2 text-slate-300 dark:text-slate-600">assignment_late</span>
                                <p class="font-medium text-slate-700 dark:text-slate-300">No tienes expedientes asignados en este momento.</p>
                                <p class="text-xs text-slate-400 mt-1">El supervisor le asignará expedientes desde la Matriz de Cargas para que pueda examinarlos y dictaminarlos.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expedientesAsignados->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $expedientesAsignados->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

