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

    if ($asignacion) {
        foreach ($asignacion->revisiones as $rev) {
            foreach ($rev->observaciones as $obs) {
                if ($obs->estaAbierta()) {
                    $observacionesAbiertas->push($obs);
                }
            }
        }
    }

    $totalDocs = $solicitud->documentos->count();
    $aprobadosDocs = $solicitud->documentos->where('Estado_Doc', 'Aprobado')->count();
    $porcentajeAvance = $totalDocs > 0 ? (int) round(($aprobadosDocs / $totalDocs) * 100) : ($tieneResolucion ? 100 : 40);
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
                <span>Imprimir Hoja de Ruta</span>
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
                    @elseif ($observacionesAbiertas->isNotEmpty())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-900 border border-amber-200/60 text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            En proceso de subsanación técnica
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
                        Titular: <span class="font-semibold text-[#0b1c30]">{{ $solicitud->solicitante?->Nombre ?? 'Ciudadano Registrado' }}</span>
                        <span class="text-xs text-slate-400">({{ $solicitud->solicitante?->Email }})</span>
                    </p>
                </div>

                {{-- Fichas informativas --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="flex items-start gap-3 p-3.5 rounded-xl bg-[#eff4ff]/60 border border-[#dce9ff]/60">
                        <span class="material-symbols-outlined text-[#0051d5] text-xl mt-0.5">schedule</span>
                        <div>
                            <span class="text-xs text-[#44474d] block">Fecha de Registro</span>
                            <span class="text-sm text-[#0b1c30] font-semibold">
                                {{ $solicitud->Fecha_Creacion_Solicitud ? $solicitud->Fecha_Creacion_Solicitud->format('d M Y, H:i') : now()->format('d M Y') }}
                            </span>
                            <span class="text-xs text-slate-500 block">SLA: 5 días hábiles</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3.5 rounded-xl bg-[#eff4ff]/60 border border-[#dce9ff]/60">
                        <span class="material-symbols-outlined text-[#0051d5] text-xl mt-0.5">badge</span>
                        <div>
                            <span class="text-xs text-[#44474d] block">Especialista Revisor</span>
                            <span class="text-sm text-[#0b1c30] font-semibold">
                                {{ $revisor ? $revisor->Nombre : 'Mesa de Turno Automática' }}
                            </span>
                            <span class="text-xs text-slate-500 block">Mesa de Dictamen SETC</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Avance General Box --}}
            <div class="lg:w-80 shrink-0 bg-[#eff4ff]/70 border border-[#dce9ff] p-6 rounded-2xl space-y-5">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-[#001229]">Avance General</span>
                    <span class="text-2xl font-bold text-[#0051d5]">{{ $porcentajeAvance }}%</span>
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
                        <span class="block text-sm font-bold text-amber-700">{{ $observacionesAbiertas->count() }}</span>
                        <span class="text-slate-500 text-[10px]">Observados</span>
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

    {{-- ══════════════════════════════════════════ OBSERVACIONES ABIERTAS (SI EXISTEN) ══ --}}
    @if ($observacionesAbiertas->isNotEmpty())
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
                    <div class="bg-white rounded-xl p-5 border border-amber-200/80 shadow-xs space-y-4">
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span class="font-semibold text-amber-800">Observación #{{ $obs->ID_Observacion }}</span>
                            <span>{{ $obs->created_at ? $obs->created_at->diffForHumans() : '' }}</span>
                        </div>
                        <p class="text-sm text-[#0b1c30] leading-relaxed bg-slate-50 p-3.5 rounded-lg border border-slate-200">
                            {{ $obs->Detalles }}
                        </p>

                        {{-- Formulario de subsanación --}}
                        <form action="{{ route('expedientes.solventar', $obs->ID_Observacion) }}" method="POST" enctype="multipart/form-data" class="space-y-3 pt-2">
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
                                <button type="submit" class="px-4 py-2 bg-[#001229] hover:bg-[#0f2744] text-white rounded-lg text-xs font-semibold transition-colors shrink-0 flex items-center gap-1.5 shadow-xs">
                                    <span class="material-symbols-outlined text-sm">send</span>
                                    <span>Enviar Subsanación</span>
                                </button>
                            </div>
                        </form>
                    </div>
                @endforeach
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
                        @php
                            $estado = $doc->Estado_Doc;
                            $badgeClass = match($estado) {
                                'Aprobado' => 'bg-[#ecfdf5] text-[#065f46] border-emerald-200',
                                'Observado' => 'bg-amber-50 text-amber-900 border-amber-200',
                                'Rechazado' => 'bg-rose-50 text-rose-800 border-rose-200',
                                'Subsanado' => 'bg-blue-50 text-blue-800 border-blue-200',
                                default => 'bg-slate-100 text-slate-700 border-slate-200',
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

