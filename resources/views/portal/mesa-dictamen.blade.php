@extends('layouts.portal')

@section('title', 'Mesa de Calificación Documental')

@section('breadcrumb')
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span>Revisión Técnica</span>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span>Mesa de Calificación</span>
    @if($solicitudSeleccionada)
        <span class="material-symbols-outlined text-xs">chevron_right</span>
        <span class="current">{{ $solicitudSeleccionada->folio?->Codigo_Folio ?? 'EXP-' . $solicitudSeleccionada->ID_Solicitud }}</span>
    @endif
@endsection

@section('content')

{{-- ═══════════════════════════════════ HEADER / CONTEXTO SALA ══ --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4 bg-white dark:bg-[#111f33] rounded-xl shadow-xs border border-slate-200/80 dark:border-slate-800 p-5">
    <div class="flex flex-col gap-1">
        <h1 class="font-bold text-xl text-[#0b1c30] dark:text-white tracking-tight" style="font-family: var(--font-display);">
            Mesa de Calificación Documental y Dictamen Técnico
        </h1>
        <div class="flex items-center gap-3 mt-1 flex-wrap">
            <span class="px-2.5 py-0.5 rounded-full bg-[#e5eeff] dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300 text-xs font-semibold">Ventanilla de Calificación Especializada</span>
            <div class="flex items-center gap-2 bg-[#eff4ff] dark:bg-slate-800/80 px-3 py-1.5 rounded-lg">
                <span class="relative flex items-center justify-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#0051d5]"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-[#0051d5] animate-ping absolute opacity-75"></span>
                </span>
                <div class="flex flex-col">
                    <span class="text-xs font-semibold text-[#0b1c30] dark:text-slate-200">Revisor Asignado: {{ auth()->user()->Nombre ?? auth()->user()->name ?? 'Personal SETC' }}</span>
                    <span class="text-[11px] text-[#44474d] dark:text-slate-400 font-mono">{{ auth()->user()->role_label ?? 'Revisor Técnico' }} • Examen de Documentos Asignados</span>
                </div>
            </div>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('portal.revisor-dashboard') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-[#0051d5] dark:text-blue-300 border border-blue-200 dark:border-blue-800 hover:bg-blue-100 text-xs font-semibold transition-colors">
            <span class="material-symbols-outlined text-base">monitoring</span>
            <span>Mando de Revisión Técnica</span>
        </a>
    </div>
</div>

{{-- ═══════════════════════════════════ KPI RIBBON ══ --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
    <div class="flex items-center gap-3 p-3.5 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="p-2 rounded-lg bg-[#eff4ff] dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-400 flex items-center justify-center">
            <span class="material-symbols-outlined text-base">assignment</span>
        </div>
        <div class="flex flex-col">
            <span class="text-xl font-bold text-[#0b1c30] dark:text-white leading-none">{{ $metricas['expedientes_asignados'] ?? 0 }}</span>
            <span class="text-[11px] text-[#44474d] dark:text-slate-400">Expedientes asignados</span>
        </div>
    </div>
    <div class="flex items-center gap-3 p-3.5 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center">
            <span class="material-symbols-outlined text-base">check_circle</span>
        </div>
        <div class="flex flex-col">
            <span class="text-xl font-bold text-emerald-700 dark:text-emerald-400 leading-none">{{ $metricas['revisados_hoy'] ?? 0 }}</span>
            <span class="text-[11px] text-[#44474d] dark:text-slate-400">Revisados hoy</span>
        </div>
    </div>
    <div class="flex items-center gap-3 p-3.5 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="p-2 rounded-lg bg-[#eff4ff] dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-400 flex items-center justify-center">
            <span class="material-symbols-outlined text-base">sync_problem</span>
        </div>
        <div class="flex flex-col">
            <span class="text-xl font-bold text-[#0051d5] dark:text-blue-300 leading-none">{{ $metricas['en_subsanacion'] ?? 0 }}</span>
            <span class="text-[11px] text-[#44474d] dark:text-slate-400">En subsanación</span>
        </div>
    </div>
    <div class="flex items-center gap-3 p-3.5 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="p-2 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 flex items-center justify-center">
            <span class="material-symbols-outlined text-base">timer</span>
        </div>
        <div class="flex flex-col">
            <span class="text-xl font-bold text-rose-700 dark:text-rose-400 leading-none">{{ $metricas['prioritarios_sla'] ?? 0 }}</span>
            <span class="text-[11px] text-[#44474d] dark:text-slate-400">Sin asignar / Prioritarios</span>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════ FILTROS ══ --}}
<div class="mb-4 flex flex-wrap items-center justify-between gap-3 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-4">
    <form method="GET" action="{{ route('portal.mesa-dictamen') }}" class="flex items-center gap-3 flex-wrap flex-1">
        <div class="relative flex-1 min-w-[200px] max-w-md">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">search</span>
            <input name="buscar" value="{{ request('buscar') }}"
                   class="w-full pl-9 pr-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-800 dark:text-slate-200 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0051d5]/20"
                   placeholder="Buscar folio, solicitante o trámite..." type="text"/>
        </div>
        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700">
            <span class="material-symbols-outlined text-sm text-slate-400">category</span>
            <select name="id_tipo_tramite" onchange="this.form.submit()" aria-label="Filtrar por tipo de trámite" class="bg-transparent text-xs text-slate-700 dark:text-slate-300 focus:outline-none cursor-pointer">
                <option value="">Todos los trámites</option>
                @foreach ($tiposTramite ?? [] as $tipo)
                    <option value="{{ $tipo->ID_Tipo_Tramite }}" @selected((string) request('id_tipo_tramite') === (string) $tipo->ID_Tipo_Tramite)>
                        {{ $tipo->Nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-[#001229] hover:bg-[#0f2744] text-white text-xs font-semibold">
            Filtrar
        </button>
        @if(request('buscar') || request('id_tipo_tramite'))
            <a href="{{ route('portal.mesa-dictamen') }}" class="text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">Limpiar</a>
        @endif
    </form>
</div>

{{-- ═══════════════════════════════════ WORKSPACE PRINCIPAL ══ --}}
<div class="grid grid-cols-12 gap-6 items-start">

    {{-- COLUMNA IZQUIERDA: Expedientes en Cola --}}
    <section class="col-span-12 xl:col-span-4 flex flex-col gap-4">
        <div class="p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
            <h2 class="text-sm font-bold text-[#0b1c30] dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0051d5] text-base">format_list_bulleted</span>
                Expedientes en Cola
            </h2>
            <span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[11px] font-semibold">
                {{ $solicitudes->total() ?? $solicitudes->count() }} registrados
            </span>
        </div>

        <div class="flex flex-col gap-3">
            @forelse ($solicitudes as $sol)
                @php
                    $folio = $sol->folio?->Codigo_Folio ?? 'EXP-' . $sol->ID_Solicitud;
                    $isSelected = $solicitudSeleccionada && $solicitudSeleccionada->ID_Solicitud === $sol->ID_Solicitud;
                @endphp
                <a href="{{ route('portal.mesa-dictamen', array_merge(request()->query(), ['expediente' => $sol->ID_Solicitud])) }}"
                   class="p-4 rounded-xl border transition-all cursor-pointer relative overflow-hidden flex flex-col gap-2.5 group {{ $isSelected ? 'bg-blue-50/70 dark:bg-blue-950/40 border-[#0051d5] shadow-sm' : 'bg-white dark:bg-[#111f33] border-slate-200/80 dark:border-slate-800 hover:border-slate-300 shadow-xs' }}">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $isSelected ? 'bg-[#0051d5]' : ($sol->Estado_Solicitud === 'Observado' ? 'bg-amber-500' : ($sol->Estado_Solicitud === 'Aprobado' ? 'bg-emerald-500' : 'bg-slate-400')) }}"></div>
                    <div class="flex items-start justify-between gap-2 pl-1">
                        <div class="flex flex-col min-w-0">
                            <span class="font-mono text-xs font-bold text-[#0051d5] dark:text-blue-400">{{ $folio }}</span>
                            <h3 class="text-xs font-bold text-[#0b1c30] dark:text-white truncate">{{ $sol->solicitante?->Nombre ?? 'Administrado' }}</h3>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ $sol->versionTramite?->tipoTramite?->Nombre ?? 'Trámite Institucional' }}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0 {{ $sol->Estado_Solicitud === 'Aprobado' ? 'bg-emerald-50 text-emerald-700' : ($sol->Estado_Solicitud === 'Observado' ? 'bg-amber-50 text-amber-800' : 'bg-blue-50 text-blue-700') }}">
                            {{ str_replace('_', ' ', $sol->Estado_Solicitud) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-400 pt-1 border-t border-slate-100 dark:border-slate-800/80 pl-1">
                        <span>Radicado: {{ $sol->Fecha_Creacion_Solicitud ? $sol->Fecha_Creacion_Solicitud->format('d/m/Y') : ($sol->created_at ? $sol->created_at->format('d/m/Y') : '--') }}</span>
                        <span class="text-[#0051d5] dark:text-blue-300 font-semibold flex items-center gap-0.5 group-hover:translate-x-0.5 transition-transform">
                            Examinar <span class="material-symbols-outlined text-xs">chevron_right</span>
                        </span>
                    </div>
                </a>
            @empty
                <div class="p-8 text-center bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/80 dark:border-slate-800 text-slate-400">
                    <span class="material-symbols-outlined text-3xl block mb-2 text-slate-300">assignment_late</span>
                    <p class="font-bold text-slate-700 dark:text-slate-300 text-xs">No tienes expedientes asignados</p>
                    <p class="text-[11px] text-slate-400 mt-1">El supervisor debe asignarle trámites en la Matriz de Cargas antes de poder calificar sus recaudos.</p>
                </div>
            @endforelse
        </div>

        @if($solicitudes->hasPages())
            <div class="p-2">
                {{ $solicitudes->links() }}
            </div>
        @endif
    </section>

    {{-- COLUMNA DERECHA: Visor y Consola de Dictamen Real --}}
    <section class="col-span-12 xl:col-span-8 flex flex-col gap-5">
        @if($solicitudSeleccionada)
            {{-- ENCABEZADO DEL EXPEDIENTE SELECCIONADO --}}
            <div class="p-5 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-blue-50 text-[#0051d5] dark:bg-blue-950/80 dark:text-blue-300">
                            {{ $solicitudSeleccionada->folio?->Codigo_Folio ?? 'EXP-' . $solicitudSeleccionada->ID_Solicitud }}
                        </span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                            {{ $solicitudSeleccionada->versionTramite?->tipoTramite?->Nombre ?? 'Trámite Institucional' }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded font-semibold {{ $solicitudSeleccionada->Estado_Solicitud === 'Aprobado' ? 'bg-emerald-50 text-emerald-700' : ($solicitudSeleccionada->Estado_Solicitud === 'Observado' ? 'bg-amber-50 text-amber-800' : 'bg-blue-50 text-blue-700') }}">
                            {{ str_replace('_', ' ', $solicitudSeleccionada->Estado_Solicitud) }}
                        </span>
                    </div>
                    <h2 class="text-base font-bold text-[#001229] dark:text-white truncate">
                        Titular: {{ $solicitudSeleccionada->solicitante?->Nombre ?? 'Administrado' }}
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Correo: {{ $solicitudSeleccionada->solicitante?->Email ?? 'N/A' }} • Fecha de ingreso: {{ $solicitudSeleccionada->Fecha_Creacion_Solicitud ? $solicitudSeleccionada->Fecha_Creacion_Solicitud->format('d/m/Y H:i') : ($solicitudSeleccionada->created_at ? $solicitudSeleccionada->created_at->format('d/m/Y H:i') : '--') }}
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('portal.expedientes.evaluar', $solicitudSeleccionada->ID_Solicitud) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#0051d5] hover:bg-[#0041ab] text-white text-xs font-semibold shadow-xs transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-base">fullscreen</span>
                        <span>Consola Completa y Versiones</span>
                    </a>
                </div>
            </div>

            {{-- RECAUDOS Y DOCUMENTOS DEL EXPEDIENTE CON VISOR INTEGRADO --}}
            <div class="p-5 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col gap-4"
                 x-data="{ activeDocIndex: 0, currentDocUrl: '' }">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-lg">attachment</span>
                        <h3 class="text-sm font-bold text-[#001229] dark:text-white">Recaudos Presentados ({{ $solicitudSeleccionada->documentos->count() }})</h3>
                    </div>
                    <span class="text-xs text-slate-400">Haga clic en un recaudo para visualizarlo y calificarlo</span>
                </div>

                @if($solicitudSeleccionada->documentos->isNotEmpty())
                    {{-- Tabs de Documentos --}}
                    <div class="flex items-center gap-2 overflow-x-auto pb-2">
                        @foreach($solicitudSeleccionada->documentos as $idx => $doc)
                            <button type="button"
                                    @click="activeDocIndex = {{ $idx }}; currentDocUrl = ''"
                                    :class="activeDocIndex === {{ $idx }} ? 'bg-[#001229] text-white border-transparent shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100'"
                                    class="px-3 py-2 rounded-lg text-xs font-semibold border flex items-center gap-2 shrink-0 transition-colors cursor-pointer">
                                <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                                <span>{{ Str::limit($doc->requisito?->Nombre ?? 'Documento #' . ($idx + 1), 22) }}</span>
                                <span class="text-[10px] opacity-75 font-mono">(v{{ $doc->versionesDoc->count() }})</span>
                                <span class="w-2 h-2 rounded-full {{ $doc->Estado_Doc === 'Aprobado' ? 'bg-emerald-400' : ($doc->Estado_Doc === 'Subsanado' ? 'bg-blue-400' : ($doc->Estado_Doc === 'Observado' ? 'bg-amber-400' : ($doc->Estado_Doc === 'Rechazado' ? 'bg-rose-400' : 'bg-slate-300'))) }}"></span>
                            </button>
                        @endforeach
                    </div>

                    {{-- Detalle y Visor del Documento Activo --}}
                    @foreach($solicitudSeleccionada->documentos as $idx => $doc)
                        @php
                            $v = $doc->versionesDoc->last();
                            $tieneArchivo = (bool) ($v && $v->Ruta_Archivo && \Illuminate\Support\Facades\Storage::disk('local')->exists($v->Ruta_Archivo));
                            if (!$tieneArchivo) {
                                $archivosDir = \Illuminate\Support\Facades\Storage::disk('local')->files("expedientes/{$doc->ID_Solicitud}");
                                $tieneArchivo = !empty($archivosDir);
                            }
                            $archivoUrl = route('portal.documentos.archivo', $doc->ID_Documento);
                        @endphp
                        <div x-show="activeDocIndex === {{ $idx }}" class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-slate-700">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                            {{ $doc->requisito?->Nombre ?? 'Requisito Reglamentario' }}
                                        </h4>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                            {{ $doc->requisito?->Formato ?? 'PDF' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                                        Estado:
                                        <span class="font-bold {{ $doc->Estado_Doc === 'Aprobado' ? 'text-emerald-600' : ($doc->Estado_Doc === 'Subsanado' ? 'text-blue-600 font-semibold' : ($doc->Estado_Doc === 'Observado' ? 'text-amber-600' : ($doc->Estado_Doc === 'Rechazado' ? 'text-rose-600' : 'text-slate-600'))) }}">
                                            {{ $doc->Estado_Doc }} ({{ $doc->versionesDoc->count() }} {{ Str::plural('versión', $doc->versionesDoc->count()) }})
                                        </span>
                                        @if($v?->Fecha_Carga)
                                            • Subido: {{ $v->Fecha_Carga->format('d/m/Y H:i') }}
                                        @endif
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 flex-wrap shrink-0">
                                    @if($tieneArchivo)
                                        <a href="{{ $archivoUrl }}"
                                           target="_blank"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 text-[#0051d5] dark:text-blue-300 text-xs font-semibold transition-colors"
                                           title="Abrir en pestaña nueva a pantalla completa">
                                            <span class="material-symbols-outlined text-sm">open_in_new</span>
                                            <span>Pantalla Completa</span>
                                        </a>
                                        <a href="{{ $archivoUrl }}?descargar=1"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-700 hover:bg-slate-100 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-xs font-semibold transition-colors"
                                           title="Descargar documento">
                                            <span class="material-symbols-outlined text-sm">download</span>
                                            <span>Descargar</span>
                                        </a>
                                    @endif

                                    {{-- Calificar Recaudo Rápido --}}
                                    <form action="{{ route('portal.documentos.evaluar', $doc->ID_Documento) }}" method="POST" class="flex items-center gap-1.5 shrink-0">
                                        @csrf
                                        <button type="submit" name="estado_doc" value="Aprobado" title="Aprobar documento" class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-semibold flex items-center gap-1 cursor-pointer">
                                            <span class="material-symbols-outlined text-sm">check</span> Aprobar
                                        </button>
                                        <button type="submit" name="estado_doc" value="Observado" title="Observar documento" class="px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 text-xs font-semibold flex items-center gap-1 cursor-pointer">
                                            <span class="material-symbols-outlined text-sm">warning</span> Observar
                                        </button>
                                        <button type="submit" name="estado_doc" value="Rechazado" title="Rechazar documento" class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-800 text-xs font-semibold flex items-center gap-1 cursor-pointer">
                                            <span class="material-symbols-outlined text-sm">close</span> Rechazar
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Selector de Versiones del Documento (v1 original vs v2 subsanada) --}}
                            @if($doc->versionesDoc->count() > 1)
                                <div class="flex flex-wrap items-center gap-2 p-2.5 rounded-lg bg-blue-50/70 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900 text-xs">
                                    <span class="font-bold text-blue-900 dark:text-blue-200 flex items-center gap-1 text-[11px]">
                                        <span class="material-symbols-outlined text-sm text-[#0051d5]">history</span>
                                        Versiones del Registro / Documento:
                                    </span>
                                    <div class="flex items-center gap-1.5">
                                        @foreach($doc->versionesDoc as $vIdx => $ver)
                                            @php $verUrl = route('portal.documentos.archivo', $doc->ID_Documento) . '?version_id=' . $ver->ID_Version_Doc; @endphp
                                            <button type="button"
                                                    @click="currentDocUrl = '{{ $verUrl }}'"
                                                    class="px-2.5 py-1 rounded-md text-[11px] font-semibold border transition-all cursor-pointer"
                                                    :class="(currentDocUrl === '{{ $verUrl }}' || (!currentDocUrl && {{ $vIdx }} === {{ $doc->versionesDoc->count() - 1 }})) ? 'bg-[#0051d5] text-white border-[#0051d5] font-bold shadow-xs' : 'bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-600 hover:bg-slate-50'">
                                                {{ $vIdx === 0 ? 'v1 (Original)' : 'v' . ($vIdx + 1) . ' (Subsanación)' }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Visor integrado del archivo PDF --}}
                            <div class="relative w-full rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-inner">
                                @if($tieneArchivo)
                                    <div class="w-full h-[520px]">
                                        <iframe :src="currentDocUrl || '{{ $archivoUrl }}'"
                                                class="w-full h-full border-0 bg-white"
                                                title="Visualizador de {{ $doc->requisito?->Nombre ?? 'Documento' }}"></iframe>
                                    </div>
                                @else
                                    <div class="w-full h-[260px] flex flex-col items-center justify-center p-8 text-center space-y-3 bg-slate-50/70 dark:bg-slate-800/40">
                                        <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-400 flex items-center justify-center shadow-xs">
                                            <span class="material-symbols-outlined text-2xl">file_present</span>
                                        </div>
                                        <div class="space-y-1 max-w-sm">
                                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Requisito sin archivo adjunto</h4>
                                            <p class="text-[11px] text-slate-500">El administrado no adjuntó un archivo digital para este requisito al registrar su solicitud.</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center justify-between text-xs text-slate-500 pt-1">
                                <span class="flex items-center gap-1.5 text-[11px]">
                                    <span class="material-symbols-outlined text-sm text-emerald-600">verified</span>
                                    <span>Archivo: {{ $v?->Nombre_Original ?? (($doc->requisito?->Nombre ?? 'Documento') . '.pdf') }}</span>
                                </span>
                                <span class="font-mono text-[11px] text-slate-400">DOC-ID: #{{ $doc->ID_Documento }}</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-6 text-center text-xs text-slate-400 bg-slate-50 dark:bg-slate-800/40 rounded-xl">
                        No hay documentos adjuntos en este expediente.
                    </div>
                @endif
            </div>

            {{-- HISTORIAL DE OBSERVACIONES Y SUBSANACIONES CIUDADANAS --}}
            @php
                $todasObsMesa = $solicitudSeleccionada->asignaciones->flatMap->revisiones->flatMap->observaciones;
            @endphp
            @if($todasObsMesa->isNotEmpty())
                <div class="p-5 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-base">published_with_changes</span>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-[#0b1c30] dark:text-white">Historial de Observaciones y Subsanaciones</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Requerimientos técnicos formulados y respuestas ingresadas por el ciudadano.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                            {{ $todasObsMesa->count() }} {{ Str::plural('observación', $todasObsMesa->count()) }}
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach($todasObsMesa as $obs)
                            <div class="p-3.5 rounded-xl border {{ $obs->estaAbierta() ? 'border-amber-200 bg-amber-50/40 dark:bg-amber-950/20' : 'border-emerald-200 bg-emerald-50/30 dark:bg-emerald-950/20' }} space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                        Observación #{{ $obs->ID_Observacion }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $obs->estaAbierta() ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ $obs->Estado_Obs }}
                                    </span>
                                </div>
                                <div class="p-2.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-300">
                                    <span class="font-semibold block text-[11px] text-slate-400 mb-0.5">Requerimiento del revisor:</span>
                                    {{ $obs->Detalles }}
                                </div>

                                @if($obs->respuestas->isNotEmpty())
                                    <div class="pl-3 border-l-2 border-emerald-500 space-y-2">
                                        @foreach($obs->respuestas as $resp)
                                            <div class="text-xs text-slate-800 dark:text-slate-200 space-y-1">
                                                <div class="flex items-center justify-between text-[11px] text-slate-500">
                                                    <span class="font-semibold text-emerald-700 dark:text-emerald-400 flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-sm">reply</span>
                                                        Subsanación del Ciudadano:
                                                    </span>
                                                    <span>{{ $resp->Fecha_Respuesta ? \Carbon\Carbon::parse($resp->Fecha_Respuesta)->format('d/m/Y H:i') : '' }}</span>
                                                </div>
                                                <p class="p-2.5 rounded-lg bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-900 text-emerald-950 dark:text-emerald-200 font-medium">
                                                    {{ $resp->Mensaje_Respuesta }}
                                                </p>
                                                @if($resp->versionDoc)
                                                    <div class="flex items-center gap-2 pt-1 text-[11px]">
                                                        <span class="material-symbols-outlined text-sm text-[#0051d5]">attach_file</span>
                                                        <span class="text-slate-600 dark:text-slate-400">Archivo subsanado adjunto:</span>
                                                        <span class="font-semibold text-[#0051d5] dark:text-blue-400">{{ $resp->versionDoc->Nombre_Original }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-amber-700 dark:text-amber-400 italic block">Pendiente de respuesta por el administrado.</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- CONSOLA DE DICTAMEN / RESOLUCIÓN REAL --}}
            <div class="p-6 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col gap-4"
                 x-data="{
                     activeMesaTab: '{{ in_array($solicitudSeleccionada->Estado_Solicitud, ['En_Revision', 'Observado']) || $solicitudSeleccionada->documentos->where('Estado_Doc', 'Subsanado')->isNotEmpty() || !($asignacionActiva && $asignacionActiva->revisiones->where('Resultado', 'Aprobado')->isNotEmpty()) ? 'dictamen' : 'resolucion' }}',
                     decision: 'Aprobado'
                 }">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-[#0b1c30] dark:text-white">
                            @if(auth()->user()->esSupervisor() || auth()->user()->esAdministrador())
                                Consola de Pronunciamiento y Resolución
                            @else
                                Pronunciamiento y Dictamen del Revisor Técnico
                            @endif
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Su pronunciamiento actualizará el expediente y notificará al administrado.
                        </p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-[#0051d5] dark:bg-blue-950 dark:text-blue-300">
                        {{ auth()->user()->role_label ?? 'Revisor' }}
                    </span>
                </div>

                @php
                    $resolucionExistente = $solicitudSeleccionada->resoluciones->last();
                @endphp

                @if($resolucionExistente)
                    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 text-xs text-emerald-900 dark:text-emerald-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-bold flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-emerald-600">verified</span>
                                Resolución Oficial Emitida: {{ $resolucionExistente->Tipo_Resolucion }}
                            </span>
                            <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-mono font-bold">
                                RES-{{ str_pad((string)$resolucionExistente->ID_Resolucion, 5, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                        <p class="text-[11px] text-emerald-700 dark:text-emerald-300">
                            Fecha de Emisión: {{ $resolucionExistente->Fecha_Emision_Resolucion ? \Carbon\Carbon::parse($resolucionExistente->Fecha_Emision_Resolucion)->format('d/m/Y H:i') : now()->format('d/m/Y') }}
                        </p>
                        <div class="pt-2 border-t border-emerald-200/60 dark:border-emerald-800/60">
                            <a href="{{ route('portal.resoluciones.pdf', $solicitudSeleccionada->ID_Solicitud) }}"
                               target="_blank"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition-colors">
                                <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                                <span>Descargar Resolución PDF</span>
                            </a>
                        </div>
                    </div>
                @else
                    {{-- Historial de dictámenes previos (si existen) --}}
                    @foreach($solicitudSeleccionada->asignaciones as $asig)
                        @foreach($asig->revisiones as $rev)
                            @if($rev->Resultado !== 'En Revision')
                                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-slate-800 dark:text-slate-200">Dictamen Técnico #{{ $rev->ID_Revision }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $rev->Resultado === 'Aprobado' ? 'bg-emerald-50 text-emerald-700' : ($rev->Resultado === 'Rechazado' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                            {{ $rev->Resultado }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-500">
                                        Revisor: {{ $asig->revisor?->Nombre ?? 'Especialista' }} &bull; {{ $rev->Fecha_Revision ? \Carbon\Carbon::parse($rev->Fecha_Revision)->format('d/m/Y H:i') : 'N/A' }}
                                    </p>
                                </div>
                            @endif
                        @endforeach
                    @endforeach

                    @if(auth()->user()->esSupervisor() || auth()->user()->esAdministrador())
                        {{-- Pestañas para alternar entre Dictamen Técnico y Resolución Final --}}
                        <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-800 rounded-lg">
                            <button type="button"
                                    @click="activeMesaTab = 'dictamen'"
                                    :class="activeMesaTab === 'dictamen' ? 'bg-white dark:bg-slate-700 text-[#0051d5] dark:text-blue-400 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400'"
                                    class="flex-1 py-1.5 px-3 rounded-md text-xs transition-all cursor-pointer text-center">
                                Dictamen Técnico (Revisor)
                            </button>
                            <button type="button"
                                    @click="activeMesaTab = 'resolucion'"
                                    :class="activeMesaTab === 'resolucion' ? 'bg-white dark:bg-slate-700 text-[#0051d5] dark:text-blue-400 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400'"
                                    class="flex-1 py-1.5 px-3 rounded-md text-xs transition-all cursor-pointer text-center">
                                Resolución Final (Supervisor)
                            </button>
                        </div>

                        {{-- Panel Resolución Final --}}
                        <div x-show="activeMesaTab === 'resolucion'" class="space-y-4" x-cloak>
                            <form action="{{ route('portal.resoluciones.store', $solicitudSeleccionada->ID_Solicitud) }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Sentido Resolutivo <span class="text-rose-500">*</span>
                                        </label>
                                        <select name="tipo_resolucion" required class="w-full px-3 py-2 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                                            <option value="Favorable">Favorable (Aprobado)</option>
                                            <option value="Desfavorable">Desfavorable (Denegatoria)</option>
                                            <option value="Inadmisible">Inadmisible / Archivo</option>
                                            <option value="Cancelada">Cancelada por el Administrado</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Fundamentación y Comentarios
                                        </label>
                                        <input type="text" name="comentarios" placeholder="Motivación jurídica de la resolución..." class="w-full px-3 py-2 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                                    </div>
                                </div>
                                <button type="submit" class="w-full py-2.5 rounded-xl bg-[#001229] hover:bg-[#0f2744] text-white text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                                    <span class="material-symbols-outlined text-base">verified</span>
                                    <span>Firmar y Emitir Resolución Oficial</span>
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Panel Dictamen Técnico (Revisor) --}}
                    <div x-show="activeMesaTab === 'dictamen'" class="space-y-4">
                        @if($asignacionActiva)
                            @php
                                $estadoSolMesa = $solicitudSeleccionada->Estado_Solicitud;
                                $puedeDictaminarMesa = in_array($estadoSolMesa, ['Pendiente', 'En_Revision'], true);
                            @endphp

                            @if($puedeDictaminarMesa)
                                <form action="{{ route('portal.revisiones.store', $asignacionActiva->ID_Asignacion) }}" method="POST" class="space-y-4" x-data="{ enviandoDictamen: false }" @submit="enviandoDictamen = true">
                                    @csrf
                                    <input type="hidden" name="resultado" :value="decision">

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <button type="button" @click="decision = 'Aprobado'"
                                                :class="decision === 'Aprobado' ? 'border-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'"
                                                class="p-3 rounded-xl border text-left flex items-start gap-2.5 transition-all cursor-pointer">
                                            <span class="material-symbols-outlined text-emerald-600 text-lg mt-0.5">check_circle</span>
                                            <div>
                                                <span class="text-xs font-bold block">Dictamen Favorable</span>
                                                <span class="text-[11px] text-slate-500 dark:text-slate-400">Cumple requisitos</span>
                                            </div>
                                        </button>

                                        <button type="button" @click="decision = 'Observado'"
                                                :class="decision === 'Observado' ? 'border-amber-600 bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'"
                                                class="p-3 rounded-xl border text-left flex items-start gap-2.5 transition-all cursor-pointer">
                                            <span class="material-symbols-outlined text-amber-600 text-lg mt-0.5">rule</span>
                                            <div>
                                                <span class="text-xs font-bold block">Solicitar Subsanación</span>
                                                <span class="text-[11px] text-slate-500 dark:text-slate-400">Observar recaudos</span>
                                            </div>
                                        </button>

                                        <button type="button" @click="decision = 'Rechazado'"
                                                :class="decision === 'Rechazado' ? 'border-rose-600 bg-rose-50 dark:bg-rose-950/40 text-rose-900 dark:text-rose-200' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'"
                                                class="p-3 rounded-xl border text-left flex items-start gap-2.5 transition-all cursor-pointer">
                                            <span class="material-symbols-outlined text-rose-600 text-lg mt-0.5">cancel</span>
                                            <div>
                                                <span class="text-xs font-bold block">Dictamen Rechazado</span>
                                                <span class="text-[11px] text-slate-500 dark:text-slate-400">Incumplimiento formal</span>
                                            </div>
                                        </button>
                                    </div>

                                    <div x-show="decision === 'Observado' || decision === 'Rechazado'" class="space-y-1.5" x-cloak>
                                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                            Observaciones Técnicas / Fundamentación <span class="text-rose-500">*</span>
                                        </label>
                                        <textarea name="observaciones[0][detalles]" rows="3"
                                                  placeholder="Describa la deficiencia encontrada y el plazo reglamentario para subsanar..."
                                                  class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs focus:outline-none focus:ring-2 focus:ring-[#0051d5]"></textarea>
                                    </div>

                                    <div class="flex items-center justify-between pt-2">
                                        <span class="text-xs text-slate-400 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-sm text-emerald-500">lock</span>
                                            Firma Digital del Revisor Habilitada
                                        </span>
                                        <button type="submit" :disabled="enviandoDictamen" class="px-5 py-2.5 rounded-xl bg-[#001229] hover:bg-[#0f2744] disabled:opacity-50 text-white text-xs font-bold transition-all shadow-sm flex items-center gap-2 cursor-pointer">
                                            <span class="material-symbols-outlined text-base">verified</span>
                                            <span x-text="enviandoDictamen ? 'Emitiendo dictamen...' : 'Emitir Dictamen Oficial'">Emitir Dictamen Oficial</span>
                                        </button>
                                    </div>
                                </form>
                            @elseif($estadoSolMesa === 'Observado')
                                <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-xs text-amber-900 dark:text-amber-200 space-y-1.5">
                                    <div class="flex items-center gap-2 font-bold">
                                        <span class="material-symbols-outlined text-amber-600 text-lg">hourglass_top</span>
                                        <span>Subsanación en curso — Esperando al administrado</span>
                                    </div>
                                    <p class="text-[11px] text-amber-800 dark:text-amber-300">
                                        El expediente tiene observaciones técnicas notificadas y no se pueden emitir nuevos dictámenes hasta que el ciudadano envíe su respuesta o documentos corregidos.
                                    </p>
                                </div>
                            @elseif($estadoSolMesa === 'Aprobado')
                                <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-900 dark:text-emerald-200 space-y-1.5">
                                    <div class="flex items-center gap-2 font-bold">
                                        <span class="material-symbols-outlined text-emerald-600 text-lg">check_circle</span>
                                        <span>Dictamen Técnico Favorable Emitido</span>
                                    </div>
                                    <p class="text-[11px] text-emerald-800 dark:text-emerald-300">
                                        Este expediente ya fue dictaminado favorablemente por el revisor. Se encuentra en etapa de supervisión para la firma de la resolución final.
                                    </p>
                                </div>
                            @elseif($estadoSolMesa === 'Rechazado')
                                <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs text-rose-900 dark:text-rose-200 space-y-1.5">
                                    <div class="flex items-center gap-2 font-bold">
                                        <span class="material-symbols-outlined text-rose-600 text-lg">cancel</span>
                                        <span>Dictamen Técnico Desfavorable Emitido</span>
                                    </div>
                                    <p class="text-[11px] text-rose-800 dark:text-rose-300">
                                        El expediente fue dictaminado como no procedente y no presenta subsanaciones pendientes de calificar.
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-400">
                                Sin asignación activa de revisor para este expediente. Asigne un especialista en la Matriz de Cargas para emitir dictámenes.
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @else
            <div class="p-12 text-center bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/80 dark:border-slate-800 text-slate-400">
                <span class="material-symbols-outlined text-5xl block mb-2 text-slate-300">inbox</span>
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300">Ningún expediente seleccionado</h3>
                <p class="text-xs text-slate-500 mt-1">Seleccione un expediente de la cola izquierda para visualizar sus recaudos y emitir dictamen.</p>
            </div>
        @endif
    </section>

</div>

@endsection
