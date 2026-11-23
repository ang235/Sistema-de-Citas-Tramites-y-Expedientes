@extends('layouts.public')

@php
    $folioCodigo = $folio->Codigo_Folio ?? sprintf('SETC-%s-%05d', date('Y'), $solicitud->ID_Solicitud);
    $tramiteNombre = $solicitud->versionTramite?->tipoTramite?->Nombre ?? 'Expediente Institucional';
    $tieneResolucion = $solicitud->resoluciones->isNotEmpty();
    $ultimaResolucion = $solicitud->resoluciones->last();
    $asignacion = $solicitud->asignaciones->last();
    $revisor = $asignacion?->revisor;
    $revision = $asignacion?->revisiones->last();
    $observacionesAbiertas = collect();

    // SOLO recopilar observaciones si el expediente está formalmente en estado 'Observado' y no tiene resolución
    if ($solicitud->Estado_Solicitud === 'Observado' && ! $tieneResolucion && $asignacion) {
        $ultimaRevision = $asignacion->revisiones->last();
        if ($ultimaRevision) {
            foreach ($ultimaRevision->observaciones as $obs) {
                if ($obs->estaAbierta()) {
                    $observacionesAbiertas->push($obs);
                }
            }
        }
    }

    $totalDocs = $solicitud->documentos->count();
    $aprobadosDocs = $solicitud->documentos->where('Estado_Doc', 'Aprobado')->count();
    $subsanadosDocs = $solicitud->documentos->where('Estado_Doc', 'Subsanado')->count();
    $observadosDocs = $solicitud->documentos->where('Estado_Doc', 'Observado')->count();
    $documentosRechazados = $solicitud->documentos->where('Estado_Doc', 'Rechazado');
    $rechazadosDocs = $documentosRechazados->count();

    $hayRechazo = $rechazadosDocs > 0
        || $solicitud->Estado_Solicitud === 'Rechazado'
        || ($revision && $revision->Resultado === 'Rechazado')
        || ($tieneResolucion && in_array(strtolower($ultimaResolucion->Tipo_Resolucion), ['rechazado', 'rechazada', 'negativa', 'rechazo'], true));

    $porcentajeAvance = $totalDocs > 0 ? (int) round(($aprobadosDocs / $totalDocs) * 100) : ($tieneResolucion ? 100 : 40);

    $hayObsAbiertas = $observacionesAbiertas->isNotEmpty() && ! $hayRechazo;
    $haySubsanacionesPorRevisar = ! $hayRechazo && ($subsanadosDocs > 0 || ($solicitud->Estado_Solicitud === 'En_Revision' && $solicitud->asignaciones->flatMap->revisiones->flatMap->observaciones->where('Estado_Obs', 'Subsanada')->isNotEmpty() && $aprobadosDocs < $totalDocs));
@endphp

@section('title', 'Expediente ' . $folioCodigo)
@section('meta_description', 'Línea de tiempo y seguimiento telemático para el expediente ' . $folioCodigo)

@section('breadcrumb')
    <a href="{{ route('home') }}" class="breadcrumb-item">SETC</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <a href="{{ route('expedientes.index') }}" class="breadcrumb-item">Expedientes</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <span class="breadcrumb-item current">{{ $folioCodigo }}</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-12 py-8 w-full space-y-8">

    {{-- ══════════════════════════════════════════ TOP STRIP ══ --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-2.5 h-2.5 rounded-full bg-[#0051d5] animate-pulse"></div>
            <span class="text-xs text-[#44474d] uppercase font-semibold tracking-wider">Expediente Telemático Unificado</span>
            <span class="text-slate-300">•</span>
            <span class="text-xs font-mono font-bold text-[#001229] bg-[#eff4ff] px-2.5 py-1 rounded-md border border-[#dce9ff]">
                {{ $folioCodigo }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="navigator.clipboard.writeText('{{ $folioCodigo }}'); alert('Folio copiado al portapapeles');"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-[#0b1c30] text-xs font-semibold transition-colors shadow-xs">
                <span class="material-symbols-outlined text-sm">content_copy</span>
                <span>Copiar Folio</span>
            </button>
            <button onclick="window.print();"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-[#0b1c30] text-xs font-semibold transition-colors shadow-xs">
                <span class="material-symbols-outlined text-sm">print</span>
                <span>Imprimir Resumen</span>
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════ HEADER EXPEDIENTE ══ --}}
    <div class="relative overflow-hidden bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 lg:p-8">
        <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-[#eff4ff] pointer-events-none blur-3xl"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-start justify-between gap-8">
            <div class="space-y-4 max-w-3xl">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#eff4ff] text-[#0051d5] text-xs font-semibold">
                        <span class="material-symbols-outlined text-sm">description</span>
                        {{ $solicitud->versionTramite?->No_Version ? 'Versión ' . $solicitud->versionTramite->No_Version : 'Norma Activa' }}
                    </span>
                    @if ($tieneResolucion)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#ecfdf5] text-[#065f46] text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-[#10b981]"></span>
                            Resolución Final: {{ $ultimaResolucion->Tipo_Resolucion }}
                        </span>
                    @elseif ($hayRechazo)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-900 border border-rose-200 text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                            Solicitud Rechazada
                        </span>
                    @elseif ($hayObsAbiertas)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-900 border border-amber-200/60 text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            En proceso de subsanación técnica
                        </span>
                    @elseif ($haySubsanacionesPorRevisar)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-900 border border-blue-200/60 text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-[#0051d5] animate-pulse"></span>
                            Subsanación recibida &mdash; En reevaluación técnica
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#eff4ff] text-[#0051d5] text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-[#0051d5] animate-pulse"></span>
                            En revisión técnica L1/L2
                        </span>
                    @endif
                </div>

                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold tracking-tight text-[#001229]" style="font-family: var(--font-display);">
                        {{ $tramiteNombre }}
                    </h1>
                    <p class="text-sm text-[#44474d] mt-1.5">
                        Titular: <span class="font-semibold text-[#0b1c30] dark:text-white">{{ $solicitud->solicitante?->Nombre ?? 'Ciudadano Registrado' }}</span>
                        <span class="text-xs text-slate-400">({{ $solicitud->solicitante?->Email }})</span>
                    </p>
                </div>

                {{-- Fichas informativas --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="flex items-start gap-3 p-3.5 rounded-xl bg-[#eff4ff]/60 dark:bg-slate-800/60 border border-[#dce9ff]/60 dark:border-slate-700">
                        <span class="material-symbols-outlined text-[#0051d5] dark:text-blue-400 text-xl mt-0.5">schedule</span>
                        <div>
                            <span class="text-xs text-[#44474d] dark:text-slate-400 block">Fecha de Registro</span>
                            <span class="text-sm text-[#0b1c30] dark:text-white font-semibold capitalize">
                                {{ $solicitud->Fecha_Creacion_Solicitud ? \Carbon\Carbon::parse($solicitud->Fecha_Creacion_Solicitud)->translatedFormat('d \d\e F \d\e Y, H:i') : now()->translatedFormat('d \d\e F \d\e Y') }}
                            </span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 block">SLA: 5 días hábiles</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3.5 rounded-xl bg-[#eff4ff]/60 dark:bg-slate-800/60 border border-[#dce9ff]/60 dark:border-slate-700">
                        <span class="material-symbols-outlined text-[#0051d5] dark:text-blue-400 text-xl mt-0.5">badge</span>
                        <div>
                            <span class="text-xs text-[#44474d] dark:text-slate-400 block">Especialista Revisor</span>
                            <span class="text-sm text-[#0b1c30] dark:text-white font-semibold">
                                {{ $revisor ? $revisor->Nombre : 'Mesa de Turno Automática' }}
                            </span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 block">Mesa de Dictamen SETC</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Avance General Box --}}
            <div class="lg:w-80 shrink-0 bg-[#eff4ff]/70 dark:bg-slate-800/60 border border-[#dce9ff] dark:border-slate-700 p-6 rounded-2xl space-y-5">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-[#001229] dark:text-white">Avance General</span>
                    <span class="text-2xl font-bold text-[#0051d5] dark:text-blue-400">{{ $porcentajeAvance }}%</span>
                </div>
                <div class="w-full bg-[#dce9ff] h-2.5 rounded-full overflow-hidden">
                    <div class="bg-[#0051d5] h-full rounded-full transition-all duration-700 ease-out" style="width: {{ $porcentajeAvance }}%;"></div>
                </div>
                <div class="grid grid-cols-3 text-center gap-2 pt-1 text-xs">
                    <div class="p-2 rounded-lg bg-white shadow-xs">
                        <span class="block text-sm font-bold text-[#0b1c30]">{{ $aprobadosDocs }}/{{ $totalDocs }}</span>
                        <span class="text-slate-500 text-[10px]">Recaudos</span>
                    </div>
                    <div class="p-2 rounded-lg bg-white shadow-xs">
                        @if ($hayRechazo)
                            <span class="block text-sm font-bold text-rose-600">{{ $rechazadosDocs }}</span>
                            <span class="text-slate-500 text-[10px]">Rechazados</span>
                        @else
                            <span class="block text-sm font-bold text-amber-700">{{ $observacionesAbiertas->count() }}</span>
                            <span class="text-slate-500 text-[10px]">Observados</span>
                        @endif
                    </div>
                    <div class="p-2 rounded-lg bg-white shadow-xs">
                        <span class="block text-sm font-bold text-[#0051d5]">{{ $solicitud->citas->count() }}</span>
                        <span class="text-slate-500 text-[10px]">Citas</span>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 text-xs text-slate-500 pt-1">
                    <span class="material-symbols-outlined text-sm text-[#059669]">lock</span>
                    <span>Trazabilidad auditada en bitácora</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════ LÍNEA DE TIEMPO / TIMELINE DINÁMICO ══ --}}
    @php
        $totalDocs = $solicitud->documentos->count();
        $aprobadosDocs = $solicitud->documentos->where('Estado_Doc', 'Aprobado')->count();
        $subsanadosDocs = $solicitud->documentos->where('Estado_Doc', 'Subsanado')->count();
        $observadosDocs = $solicitud->documentos->where('Estado_Doc', 'Observado')->count();

        // Subsanación activa / en curso
        $hayObsAbiertas = $observacionesAbiertas->isNotEmpty();
        $haySubsanacionesPorRevisar = $subsanadosDocs > 0 || ($solicitud->Estado_Solicitud === 'En_Revision' && $solicitud->asignaciones->flatMap->revisiones->flatMap->observaciones->where('Estado_Obs', 'Subsanada')->isNotEmpty() && $aprobadosDocs < $totalDocs);

        // Hito 1: Radicación (siempre completado)
        $step1_done = true;

        // Hito 2: Asignación Técnica
        $step2_done = (bool) $asignacion;
        $step2_active = !$step2_done;
        $step2_date = $asignacion?->Fecha_Asignacion;

        // Hito 3: Evaluación de Recaudos
        // Se considera completada SOLO si no hay observaciones abiertas, no hay documentos subsanados pendientes de calificar,
        // y todos los recaudos fueron aprobados o ya existe resolución final formal.
        $step3_done = $step2_done && !$hayObsAbiertas && !$haySubsanacionesPorRevisar && ($aprobadosDocs === $totalDocs || $tieneResolucion || ($revision && $revision->Resultado === 'Aprobado' && $solicitud->Estado_Solicitud === 'Aprobado'));
        $step3_active = $step2_done && !$step3_done;

        // Hito 4: Dictamen de Calificación
        // SOLO puede estar completado si la Fase 3 está completada, existe un dictamen emitido que corresponda al estado actual,
        // y NO se encuentra en proceso de subsanación o reevaluación.
        $step4_done = $step3_done && (bool) ($revision && in_array($revision->Resultado, ['Aprobado', 'Rechazado']) && in_array($solicitud->Estado_Solicitud, ['Aprobado', 'Rechazado']));
        $step4_active = $step3_done && !$step4_done;
        $step4_date = $step4_done ? $revision?->Fecha_Revision : null;

        // Hito 5: Resolución Final
        // SOLO puede estar activo o completado si el Dictamen (Fase 4) está completado.
        $step5_done = $step4_done && $tieneResolucion;
        $step5_active = $step4_done && !$step5_done;
        $step5_date = $step5_done ? $ultimaResolucion?->Fecha_Emision_Resolucion : null;
    @endphp
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 lg:p-8 shadow-xs space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-lg lg:text-xl font-bold text-[#001229]" style="font-family: var(--font-display);">
                    Línea de Tiempo y Trazabilidad del Expediente
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Estado en tiempo real de las 5 fases procedimentales de su trámite institucional.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sincronizado en Vivo
                </span>
            </div>
        </div>

        <div class="relative py-2">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 relative">

                {{-- Hito 1: Radicación --}}
                <div class="relative flex md:flex-col items-start gap-4 md:gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 font-bold text-xs bg-emerald-500 text-white shadow-xs">
                        <span class="material-symbols-outlined text-lg">check</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 block">Fase 1</span>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white mt-0.5">Radicación</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Expediente generado con folio oficial</p>
                        <span class="text-[11px] text-slate-400 dark:text-slate-500 font-mono mt-1 block capitalize">
                            {{ $solicitud->Fecha_Creacion_Solicitud ? \Carbon\Carbon::parse($solicitud->Fecha_Creacion_Solicitud)->translatedFormat('d \d\e F, Y H:i') : ($solicitud->created_at ? \Carbon\Carbon::parse($solicitud->created_at)->translatedFormat('d \d\e F, Y') : 'Completado') }}
                        </span>
                    </div>
                </div>

                {{-- Hito 2: Asignación Técnica --}}
                <div class="relative flex md:flex-col items-start gap-4 md:gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 font-bold text-xs {{ $step2_done ? 'bg-emerald-500 text-white' : ($step2_active ? 'bg-[#0051d5] text-white ring-4 ring-blue-100 dark:ring-blue-950' : 'bg-slate-200 dark:bg-slate-700 text-slate-500') }} shadow-xs">
                        <span class="material-symbols-outlined text-lg">{{ $step2_done ? 'check' : ($step2_active ? 'pending' : 'person_search') }}</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider {{ $step2_done ? 'text-emerald-700 dark:text-emerald-400' : ($step2_active ? 'text-[#0051d5] dark:text-blue-400' : 'text-slate-400') }} block">Fase 2</span>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white mt-0.5">Asignación Técnica</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $revisor ? 'Asignado a: ' . $revisor->Nombre : 'Esperando asignación' }}
                        </p>
                        @if($step2_date)
                            <span class="text-[11px] text-slate-400 dark:text-slate-500 font-mono mt-1 block capitalize">
                                {{ \Carbon\Carbon::parse($step2_date)->translatedFormat('d \d\e F, Y H:i') }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Hito 3: Evaluación de Recaudos --}}
                <div class="relative flex md:flex-col items-start gap-4 md:gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 font-bold text-xs {{ $hayRechazo ? 'bg-rose-500 text-white ring-4 ring-rose-100 dark:ring-rose-950' : ($step3_done ? 'bg-emerald-500 text-white' : ($hayObsAbiertas ? 'bg-amber-500 text-white ring-4 ring-amber-100 dark:ring-amber-950 animate-pulse' : ($haySubsanacionesPorRevisar ? 'bg-[#0051d5] text-white ring-4 ring-blue-100 dark:ring-blue-950 animate-pulse' : ($step3_active ? 'bg-[#0051d5] text-white ring-4 ring-blue-100 dark:ring-blue-950' : 'bg-slate-200 dark:bg-slate-700 text-slate-500')))) }} shadow-xs">
                        <span class="material-symbols-outlined text-lg">{{ $hayRechazo ? 'cancel' : ($step3_done ? 'check' : ($hayObsAbiertas ? 'warning' : ($haySubsanacionesPorRevisar ? 'published_with_changes' : ($step3_active ? 'hourglass_top' : 'folder')))) }}</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider {{ $hayRechazo ? 'text-rose-700 dark:text-rose-400' : ($step3_done ? 'text-emerald-700 dark:text-emerald-400' : ($hayObsAbiertas ? 'text-amber-700 dark:text-amber-400' : ($step3_active ? 'text-[#0051d5] dark:text-blue-400' : 'text-slate-400'))) }} block">Fase 3</span>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white mt-0.5">Evaluación de Recaudos</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            @if($hayRechazo)
                                <span class="text-rose-700 dark:text-rose-400 font-semibold">Recaudos con dictamen no favorable</span>
                            @elseif($hayObsAbiertas)
                                <span class="text-amber-800 dark:text-amber-300 font-medium">{{ $observacionesAbiertas->count() }} requerimiento(s) por solventar</span>
                            @elseif($haySubsanacionesPorRevisar)
                                <span class="text-blue-700 dark:text-blue-300 font-medium">Subsanación enviada &mdash; En revisión técnica</span>
                            @elseif($step3_done)
                                <span class="text-emerald-700 dark:text-emerald-400 font-medium">Recaudos técnicos validados</span>
                            @else
                                Revisión de recaudos en curso
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Hito 4: Dictamen Técnico --}}
                <div class="relative flex md:flex-col items-start gap-4 md:gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 font-bold text-xs {{ $hayRechazo ? 'bg-rose-500 text-white ring-4 ring-rose-100 dark:ring-rose-950' : ($step4_done ? 'bg-emerald-500 text-white' : ($step4_active ? 'bg-[#0051d5] text-white ring-4 ring-blue-100 dark:ring-blue-950' : 'bg-slate-200 dark:bg-slate-700 text-slate-500')) }} shadow-xs">
                        <span class="material-symbols-outlined text-lg">{{ $hayRechazo ? 'cancel' : ($step4_done ? 'check' : ($step4_active ? 'gavel' : 'balance')) }}</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider {{ $hayRechazo ? 'text-rose-700 dark:text-rose-400' : ($step4_done ? 'text-emerald-700 dark:text-emerald-400' : ($step4_active ? 'text-[#0051d5] dark:text-blue-400' : 'text-slate-400')) }} block">Fase 4</span>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white mt-0.5">Dictamen de Calificación</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            @if($hayRechazo)
                                <span class="text-rose-700 dark:text-rose-400 font-semibold">Calificación: Rechazado</span>
                            @else
                                {{ $step4_done && $revision ? 'Calificación: ' . $revision->Resultado : ($haySubsanacionesPorRevisar ? 'Pendiente de nuevo dictamen técnico' : 'Pendiente de emisión de dictamen') }}
                            @endif
                        </p>
                        @if($step4_date)
                            <span class="text-[11px] text-slate-400 dark:text-slate-500 font-mono mt-1 block capitalize">
                                {{ \Carbon\Carbon::parse($step4_date)->translatedFormat('d \d\e F, Y') }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Hito 5: Resolución Final --}}
                <div class="relative flex md:flex-col items-start gap-4 md:gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 font-bold text-xs {{ $step5_done ? 'bg-emerald-500 text-white ring-4 ring-emerald-100 dark:ring-emerald-950' : ($step5_active ? 'bg-indigo-600 text-white ring-4 ring-indigo-100 dark:ring-indigo-950' : 'bg-slate-200 dark:bg-slate-700 text-slate-500') }} shadow-xs">
                        <span class="material-symbols-outlined text-lg">{{ $step5_done ? 'verified' : ($step5_active ? 'draw' : 'task_alt') }}</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider {{ $step5_done ? 'text-emerald-700 dark:text-emerald-400' : ($step5_active ? 'text-indigo-700 dark:text-indigo-300' : 'text-slate-400') }} block">Fase 5</span>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white mt-0.5">Resolución Final</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $step5_done ? 'Acto emitido: ' . $ultimaResolucion->Tipo_Resolucion : 'Pendiente de firma resolutiva' }}
                        </p>
                        @if($step5_date)
                            <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold font-mono mt-1 block capitalize">
                                {{ \Carbon\Carbon::parse($step5_date)->translatedFormat('d \d\e F, Y') }}
                            </span>
                        @endif
                        @if($tieneResolucion)
                            <a href="{{ route('expedientes.resolucion.pdf', $folio->Codigo_Folio) }}"
                               target="_blank"
                               class="mt-2.5 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition-colors cursor-pointer">
                                <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                                <span>Descargar Resolución Oficial (PDF)</span>
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════ ESTADO DE RECHAZO / OBSERVACIONES / SUBSANACIONES ══ --}}
    @if ($hayRechazo)
        <div class="bg-rose-50 border-2 border-rose-300 rounded-2xl p-6 lg:p-8 space-y-4 shadow-xs">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 border border-rose-200">
                    <span class="material-symbols-outlined text-3xl">cancel</span>
                </div>
                <div class="space-y-2 flex-1">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h2 class="text-lg font-bold text-rose-900" style="font-family: var(--font-display);">
                            Se ha rechazado su solicitud
                        </h2>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-200 text-rose-900 border border-rose-300">
                            Expediente No Favorable
                        </span>
                    </div>
                    <p class="text-sm text-rose-800 leading-relaxed font-medium">
                        Se ha rechazado su solicitud, para volver a intentar debe comenzar de nuevo el trámite creando otro nuevamente.
                    </p>
                    @if ($documentosRechazados->isNotEmpty())
                        <div class="bg-white/80 p-3.5 rounded-xl border border-rose-200 space-y-1.5 mt-2">
                            <span class="text-xs font-bold text-rose-900 block">Recaudos calificados con rechazo:</span>
                            <ul class="list-disc list-inside text-xs text-rose-800 space-y-1">
                                @foreach ($documentosRechazados as $docRechazado)
                                    <li>
                                        <span class="font-semibold">{{ $docRechazado->requisito?->Nombre ?? 'Documento' }}</span> &mdash; Rechazado en revisión técnica
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="pt-3 flex flex-wrap items-center gap-3">
                        @if ($solicitud->versionTramite?->tipoTramite)
                            <a href="{{ route('solicitudes.create', $solicitud->versionTramite->tipoTramite->ID_Tipo_Tramite) }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-rose-700 hover:bg-rose-800 text-white text-xs font-semibold shadow-xs transition-colors cursor-pointer">
                                <span class="material-symbols-outlined text-base">restart_alt</span>
                                <span>Comenzar trámite de nuevo</span>
                            </a>
                        @endif
                        <a href="{{ route('tramites.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-rose-300 text-rose-800 hover:bg-rose-100 text-xs font-semibold transition-colors">
                            <span class="material-symbols-outlined text-base">arrow_back</span>
                            <span>Ver catálogo de trámites</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @elseif ($solicitud->Estado_Solicitud === 'Observado' && $observacionesAbiertas->isNotEmpty() && ! $tieneResolucion)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 lg:p-8 space-y-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-2xl">warning</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-amber-900" style="font-family: var(--font-display);">
                        Observaciones pendientes de subsanación
                    </h2>
                    <p class="text-xs text-amber-800 mt-0.5">
                        El especialista ha emitido los siguientes requerimientos. Por favor responde o carga el documento corregido.
                    </p>
                </div>
            </div>

            <div class="space-y-4">
                @foreach ($observacionesAbiertas as $obs)
                    <div class="bg-white rounded-xl p-5 border border-amber-200/80 shadow-xs space-y-4" x-data="{ enviando: false }">
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span class="font-semibold text-amber-800">Observación #{{ $obs->ID_Observacion }}</span>
                            <span>{{ $obs->created_at ? $obs->created_at->diffForHumans() : '' }}</span>
                        </div>
                        <p class="text-sm text-[#0b1c30] leading-relaxed bg-slate-50 p-3.5 rounded-lg border border-slate-200">
                            {{ $obs->Detalles }}
                        </p>

                        {{-- Formulario de subsanación --}}
                        <form action="{{ route('expedientes.solventar', $obs->ID_Observacion) }}" method="POST" enctype="multipart/form-data" class="space-y-3 pt-2" @submit="enviando = true">
                            @csrf
                            <div>
                                <label for="mensaje_{{ $obs->ID_Observacion }}" class="block text-xs font-semibold text-[#0b1c30] mb-1">
                                    Respuesta o Aclaración
                                </label>
                                <textarea name="mensaje_respuesta"
                                          id="mensaje_{{ $obs->ID_Observacion }}"
                                          rows="2"
                                          required
                                          placeholder="Explica las correcciones realizadas o responde a la observación..."
                                          class="w-full p-3 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]"></textarea>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-[#0b1c30] mb-1">
                                        Adjuntar nuevo archivo corregido (opcional)
                                    </label>
                                    <input type="file"
                                           name="archivo"
                                           accept=".pdf,.png,.jpg,.jpeg"
                                           class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#eff4ff] file:text-[#0051d5] hover:file:bg-[#dce9ff]">
                                </div>
                                <button type="submit" :disabled="enviando" class="px-4 py-2 bg-[#001229] hover:bg-[#0f2744] disabled:opacity-50 text-white rounded-lg text-xs font-semibold transition-colors shrink-0 flex items-center gap-1.5 shadow-xs cursor-pointer">
                                    <span class="material-symbols-outlined text-sm" x-show="!enviando">send</span>
                                    <span class="material-symbols-outlined text-sm animate-spin" x-show="enviando" x-cloak>sync</span>
                                    <span x-text="enviando ? 'Enviando...' : 'Enviar Subsanación'">Enviar Subsanación</span>
                                </button>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif ($haySubsanacionesPorRevisar && ! $tieneResolucion && $solicitud->Estado_Solicitud === 'En_Revision')
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 flex items-start sm:items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-[#0051d5] flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl animate-pulse">hourglass_top</span>
            </div>
            <div>
                <h3 class="text-sm font-bold text-[#001229]">Subsanación enviada — En reevaluación técnica</h3>
                <p class="text-xs text-slate-600 mt-0.5">
                    Sus respuestas y documentos corregidos han sido recibidos con éxito y están en proceso de calificación por el especialista. No requiere realizar ninguna acción hasta que se emita un nuevo dictamen.
                </p>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════ RECAUDOS Y DOCUMENTOS DEL EXPEDIENTE ══ --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 lg:p-8 shadow-xs space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-xl font-bold text-[#001229]" style="font-family: var(--font-display);">
                    Recaudos y Documentos Vinculados
                </h2>
                <p class="text-xs text-[#44474d] mt-1">
                    Control de requisitos normativos y versiones digitales cargadas en el expediente.
                </p>
            </div>
            <span class="material-symbols-outlined text-slate-400">inventory_2</span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($solicitud->documentos as $doc)
                <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-lg bg-[#eff4ff] text-[#0051d5] flex items-center justify-center shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-xl">description</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-[#0b1c30]">
                                {{ $doc->requisito?->Nombre ?? 'Documento del Trámite' }}
                            </h3>
                            <div class="flex items-center gap-2 mt-1 text-xs text-slate-500">
                                <span>Formato: {{ $doc->requisito?->Formato ?? 'PDF' }}</span>
                                <span>•</span>
                                <span>{{ $doc->versionesDoc->count() }} {{ Str::plural('versión', $doc->versionesDoc->count()) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @if($doc->versionesDoc->isNotEmpty())
                            <a href="{{ route('expedientes.documentos.archivo', $doc->ID_Documento) }}"
                               target="_blank"
                               title="Previsualizar o descargar documento digital"
                               class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-[#eff4ff] dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300 border border-blue-200/60 dark:border-blue-800 hover:bg-[#dce9ff] dark:hover:bg-blue-900/60 transition-colors">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                                <span>Ver Archivo</span>
                            </a>
                        @endif

                        @php
                            $estado = $doc->Estado_Doc;
                            $badgeClass = match($estado) {
                                'Aprobado' => 'bg-[#ecfdf5] dark:bg-emerald-950/40 text-[#065f46] dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                'Observado' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                'Rechazado' => 'bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                                'Subsanado' => 'bg-blue-50 dark:bg-blue-950/40 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                default => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                            };
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                            {{ $estado }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-sm text-slate-500">
                    No hay recaudos adjuntos registrados en este expediente.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

