@extends('layouts.portal')

@section('title', 'Evaluacion de Expediente #' . ($solicitud->folio?->Codigo_Folio ?? $solicitud->ID_Solicitud))

@section('breadcrumb')
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <a href="{{ auth()->user()?->esRevisor() && !auth()->user()?->esSupervisor() ? route('portal.revisor-dashboard') : route('portal.dashboard') }}">Operaciones</a>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <a href="{{ route('portal.mesa-dictamen') }}">Mesa de Dictamen</a>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="current">Expediente {{ $solicitud->folio?->Codigo_Folio ?? '#' . $solicitud->ID_Solicitud }}</span>
@endsection

@section('content')
<div class="flex flex-col w-full gap-space-lg">

    {{-- EXPEDIENTE HEADER --}}
    <div class="bg-white dark:bg-[#111f33] rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 lg:p-6 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-5">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-primary-container text-white flex items-center justify-center font-bold text-lg shadow-sm shrink-0">
                <span class="material-symbols-outlined text-2xl text-blue-200">folder_open</span>
            </div>
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-mono font-bold text-xs px-2.5 py-1 rounded-md bg-[#eff4ff] text-[#0051d5]">
                        {{ $solicitud->folio?->Codigo_Folio ?? 'EXP-' . str_pad($solicitud->ID_Solicitud, 5, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                        {{ $solicitud->versionTramite?->tipoTramite?->Nombre ?? 'Tramite Ordinario' }}
                    </span>
                    @if ($esSoloLectura ?? false)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-900 text-white border border-slate-700">
                            <span class="material-symbols-outlined text-[13px] text-blue-400">lock</span>
                            Solo Lectura / Inmutable
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            En Calificacion Tecnica
                        </span>
                    @endif
                </div>
                <h1 class="text-xl lg:text-2xl font-bold tracking-tight text-[#001229] dark:text-white" style="font-family: var(--font-display);">
                    Mesa de Dictamen y Calificacion Juridica
                </h1>
                <p class="text-xs text-slate-500">
                    Solicitante: <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $solicitud->solicitante?->Nombre ?? 'Ciudadano Registrado' }}</span>
                    &bull; Correo: {{ $solicitud->solicitante?->Email ?? 'N/A' }}
                    &bull; Fecha de Ingreso: {{ $solicitud->Fecha_Creacion_Solicitud ? $solicitud->Fecha_Creacion_Solicitud->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('portal.mesa-dictamen') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition-colors">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                <span>Cola de Expedientes</span>
            </a>
            @if($solicitud->resoluciones->isNotEmpty() && (auth()->user()?->esAdministrador() || auth()->user()?->esSupervisor()))
                <a href="{{ route('portal.resoluciones.pdf', $solicitud->ID_Solicitud) }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#001229] hover:bg-[#00224d] text-white text-xs font-semibold transition-colors shadow-xs">
                    <span class="material-symbols-outlined text-base">picture_as_pdf</span>
                    <span>Resolución PDF</span>
                </a>
            @endif
            @if(auth()->user()?->esAdministrador() || auth()->user()?->esSupervisor())
                <a href="{{ route('portal.auditoria') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-white dark:bg-[#0a1626] border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-slate-700 dark:text-slate-200 text-xs font-semibold shadow-xs transition-colors">
                    <span class="material-symbols-outlined text-base text-purple-600">policy</span>
                    <span>Bitacora</span>
                </a>
            @endif
        </div>
    </div>

    {{-- BANNER DE SOLO LECTURA INMUTABLE --}}
    @if ($esSoloLectura ?? false)
        <div class="p-4 rounded-xl bg-[#0a1626] text-white border border-slate-800 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0 border border-blue-400/30">
                    <span class="material-symbols-outlined text-xl">lock</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-white tracking-wide uppercase">Expediente Concluido &mdash; Modo de Solo Lectura</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#0051d5] text-white">Visualizacion sin modificar</span>
                    </div>
                    <p class="text-[11px] text-slate-300 mt-0.5">
                        Este expediente se encuentra formalmente dictaminado o resuelto. Esta habilitado estrictamente para su visualizacion y auditoria sin opciones de modificacion.
                    </p>
                </div>
            </div>
            <span class="hidden sm:inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-xs font-mono font-semibold">
                <span class="material-symbols-outlined text-sm">verified</span>
                Inmutable
            </span>
        </div>
    @endif

    {{-- SESSION-LEVEL ALERTS --}}
    @if (session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-base">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-base">error</span>
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-semibold space-y-1">
            <div class="flex items-center gap-2 font-bold">
                <span class="material-symbols-outlined text-base">error</span>
                <span>Por favor verifique los siguientes requerimientos:</span>
            </div>
            <ul class="list-disc list-inside pl-5 space-y-0.5 font-normal">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- WORKSPACE: DOCUMENTOS (IZQ) & DICTAMEN (DER) --}}
    @php
        $docsData = $solicitud->documentos->map(function($d) {
            $v = $d->versionesDoc->last();
            $tieneArchivo = (bool) ($v && $v->Ruta_Archivo && \Illuminate\Support\Facades\Storage::disk('local')->exists($v->Ruta_Archivo));
            if (!$tieneArchivo) {
                $archivosDir = \Illuminate\Support\Facades\Storage::disk('local')->files("expedientes/{$d->ID_Solicitud}");
                $tieneArchivo = !empty($archivosDir);
            }

            $versiones = $d->versionesDoc->map(function($ver, $vIdx) use ($d) {
                return [
                    'id' => $ver->ID_Version_Doc,
                    'numero' => $vIdx + 1,
                    'nombre' => $ver->Nombre_Original ?? ('Version_' . ($vIdx + 1) . '.pdf'),
                    'fecha' => $ver->Fecha_Carga ? $ver->Fecha_Carga->format('d/m/Y H:i') : null,
                    'url' => route('portal.documentos.archivo', $d->ID_Documento) . '?version_id=' . $ver->ID_Version_Doc,
                ];
            })->values();

            return [
                'id' => $d->ID_Documento,
                'nombre' => $d->requisito?->Nombre ?? ('Documento #' . $d->ID_Documento),
                'formato' => $d->requisito?->Formato ?? 'PDF',
                'prioridad' => $d->requisito?->Prioridad ?? 'Obligatorio',
                'estado' => $d->Estado_Doc,
                'tiene_archivo' => $tieneArchivo,
                'archivo_url' => route('portal.documentos.archivo', $d->ID_Documento),
                'archivo_nombre' => $v?->Nombre_Original ?? (($d->requisito?->Nombre ?? 'Documento') . '.pdf'),
                'fecha_carga' => $v?->Fecha_Carga ? $v->Fecha_Carga->format('d/m/Y H:i') : null,
                'versiones' => $versiones,
                'versiones_count' => $d->versionesDoc->count(),
            ];
        })->values();
    @endphp

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg items-start"
         x-data="{
             docs: {{ json_encode($docsData) }},
             activeIndex: 0,
             currentIframeUrl: '',
             get activeDoc() { return this.docs[this.activeIndex] || null; },
             selectDoc(idx) {
                 this.activeIndex = idx;
                 this.currentIframeUrl = this.activeDoc?.archivo_url || '';
             },
             init() {
                 this.currentIframeUrl = this.activeDoc?.archivo_url || '';
             },
             setVersion(url) {
                 this.currentIframeUrl = url;
             }
         }">

        {{-- COLUMNA IZQUIERDA: VISOR DE DOCUMENTOS Y RECAUDOS --}}
        <div class="xl:col-span-7 flex flex-col gap-space-md">

            {{-- VISOR INTERACTIVO DE DOCUMENTOS --}}
            <div class="p-5 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col gap-4">
                {{-- Selector de pestañas de recaudos --}}
                <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50 dark:bg-slate-800/60 p-2 rounded-xl">
                    <div class="flex items-center gap-1.5 overflow-x-auto max-w-full pb-1 sm:pb-0">
                        @forelse($solicitud->documentos as $idx => $doc)
                            <button type="button"
                                    @click="selectDoc({{ $idx }})"
                                    :class="activeIndex === {{ $idx }} ? 'bg-white dark:bg-slate-700 text-[#0051d5] dark:text-blue-400 shadow-xs font-bold ring-1 ring-blue-200 dark:ring-blue-900' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700/50'"
                                    class="px-3 py-2 rounded-lg text-xs font-semibold flex items-center gap-2 transition-all shrink-0 cursor-pointer">
                                <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                                <span class="truncate max-w-[140px]">{{ $doc->requisito?->Nombre ?? 'Doc #' . ($idx + 1) }}</span>
                                <span class="w-2 h-2 rounded-full shrink-0 {{ $doc->Estado_Doc === 'Aprobado' ? 'bg-emerald-500' : ($doc->Estado_Doc === 'Observado' ? 'bg-amber-500' : ($doc->Estado_Doc === 'Rechazado' ? 'bg-rose-500' : 'bg-slate-400')) }}"></span>
                            </button>
                        @empty
                            <span class="text-xs text-slate-500 px-3 py-2">Sin documentos adjuntos</span>
                        @endforelse
                    </div>

                    {{-- Herramientas de visualización rápida --}}
                    <div class="flex items-center gap-1.5 shrink-0 ml-auto" x-show="activeDoc?.tiene_archivo">
                        <a :href="activeDoc?.archivo_url"
                           target="_blank"
                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-[#0051d5] text-xs font-semibold transition-colors"
                           title="Abrir en pestaña nueva a pantalla completa">
                            <span class="material-symbols-outlined text-sm">open_in_new</span>
                            <span class="hidden sm:inline">Pantalla Completa</span>
                        </a>
                        <a :href="activeDoc?.archivo_url + '?descargar=1'"
                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors"
                           title="Descargar documento">
                            <span class="material-symbols-outlined text-sm">download</span>
                            <span class="hidden sm:inline">Descargar</span>
                        </a>
                    </div>
                </div>

                {{-- Ficha del Documento Activo --}}
                <div class="flex flex-wrap items-center justify-between gap-2 px-1 text-xs">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="font-bold text-[#001229] dark:text-white truncate" x-text="activeDoc?.nombre"></span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300" x-text="activeDoc?.formato || 'PDF'"></span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-500 text-[11px]">
                        <span x-show="activeDoc?.fecha_carga" x-text="'Cargado: ' + activeDoc?.fecha_carga"></span>
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <span class="font-semibold"
                              :class="{
                                  'text-emerald-700 dark:text-emerald-400': activeDoc?.estado === 'Aprobado',
                                  'text-blue-700 dark:text-blue-400': activeDoc?.estado === 'Subsanado',
                                  'text-amber-700 dark:text-amber-400': activeDoc?.estado === 'Observado',
                                  'text-rose-700 dark:text-rose-400': activeDoc?.estado === 'Rechazado',
                                  'text-slate-600 dark:text-slate-400': activeDoc?.estado !== 'Aprobado' && activeDoc?.estado !== 'Subsanado' && activeDoc?.estado !== 'Observado' && activeDoc?.estado !== 'Rechazado'
                              }"
                              x-text="'Estado: ' + (activeDoc?.estado || 'Pendiente')"></span>
                    </div>
                </div>

                {{-- Selector de Versiones (si el ciudadano subsanó y cargó una nueva versión) --}}
                <div x-show="activeDoc?.versiones?.length > 1" class="flex flex-wrap items-center gap-2 px-3 py-2 rounded-xl bg-blue-50/70 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900 text-xs">
                    <span class="font-bold text-blue-900 dark:text-blue-200 flex items-center gap-1.5 text-[11px]">
                        <span class="material-symbols-outlined text-sm text-[#0051d5]">history</span>
                        Historial de Versiones del Documento:
                    </span>
                    <div class="flex items-center gap-1.5">
                        <template x-for="ver in activeDoc?.versiones" :key="ver.id">
                            <button type="button"
                                    @click="setVersion(ver.url)"
                                    :class="(currentIframeUrl === ver.url || (!currentIframeUrl && ver.numero === activeDoc?.versiones?.length)) ? 'bg-[#0051d5] text-white shadow-xs font-bold' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700'"
                                    class="px-2.5 py-1 rounded-md text-[11px] transition-all cursor-pointer">
                                <span x-text="ver.numero === 1 ? 'v1 (Original)' : 'v' + ver.numero + ' (Subsanación)'"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Visor Real del Archivo PDF Cargado por el Ciudadano --}}
                <div class="relative w-full rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900 shadow-inner">
                    {{-- Caso 1: Archivo disponible --}}
                    <div x-show="activeDoc?.tiene_archivo" class="w-full h-[620px]">
                        <iframe :src="currentIframeUrl || activeDoc?.archivo_url"
                                class="w-full h-full border-0 bg-white"
                                title="Visualizador oficial del documento adjunto"></iframe>
                    </div>

                    {{-- Caso 2: Archivo no cargado aún por el ciudadano --}}
                    <div x-show="!activeDoc?.tiene_archivo"
                         class="w-full h-[400px] flex flex-col items-center justify-center p-8 text-center space-y-3 bg-slate-50/70 dark:bg-slate-800/40">
                        <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-400 flex items-center justify-center shadow-xs">
                            <span class="material-symbols-outlined text-3xl">file_present</span>
                        </div>
                        <div class="space-y-1 max-w-sm">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Requisito sin archivo adjunto</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                El ciudadano no adjuntó un archivo digital para este requisito al registrar su expediente telemático.
                            </p>
                        </div>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                            Requisito: <span x-text="activeDoc?.prioridad || 'Obligatorio'"></span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- LISTA Y CALIFICACION DE DOCUMENTOS INDIVIDUALES --}}
            <div class="bg-white dark:bg-[#111f33] p-5 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-[#001229] dark:text-white">Recaudos Aportados por el Ciudadano</h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            @if ($esSoloLectura ?? false)
                                Visualizacion inmutable del estado de cada recaudo (modo auditoria).
                            @else
                                Evalua el cumplimiento de cada requisito normativo antes del dictamen final.
                            @endif
                        </p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                        {{ $solicitud->documentos->count() }} {{ Str::plural('recaudo', $solicitud->documentos->count()) }}
                    </span>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($solicitud->documentos as $index => $doc)
                        <div class="py-3.5 space-y-2">
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                                @click="selectDoc({{ $index }})"
                                                class="text-left font-semibold text-xs text-slate-900 dark:text-slate-100 hover:text-[#0051d5] dark:hover:text-blue-400 flex items-center gap-1.5 transition-colors cursor-pointer">
                                            <span class="material-symbols-outlined text-base text-blue-600">visibility</span>
                                            <span>{{ $doc->requisito?->Nombre ?? 'Documento Normativo #' . $doc->ID_Documento }}</span>
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2 text-[11px] text-slate-400 pl-6">
                                        <span>Formato: {{ $doc->requisito?->Formato ?? 'PDF' }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $doc->versionesDoc->count() }} {{ Str::plural('version', $doc->versionesDoc->count()) }} cargadas</span>
                                        @if($doc->versionesDoc->last()?->Ruta_Archivo)
                                            <span>&bull;</span>
                                            <span class="text-emerald-700 dark:text-emerald-400 font-semibold flex items-center gap-0.5">
                                                <span class="material-symbols-outlined text-xs">attach_file</span> Archivo disponible
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $doc->Estado_Doc === 'Aprobado' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($doc->Estado_Doc === 'Subsanado' ? 'bg-blue-50 text-blue-700 border-blue-200 ring-2 ring-blue-400/20' : ($doc->Estado_Doc === 'Observado' ? 'bg-amber-50 text-amber-800 border-amber-200' : ($doc->Estado_Doc === 'Rechazado' ? 'bg-rose-50 text-rose-800 border-rose-200' : 'bg-slate-100 text-slate-700 border-slate-200'))) }}">
                                    @if($doc->Estado_Doc === 'Subsanado')
                                        <span class="material-symbols-outlined text-[13px] inline-block align-middle mr-0.5">published_with_changes</span>
                                    @endif
                                    {{ $doc->Estado_Doc }} ({{ $doc->versionesDoc->count() }} {{ Str::plural('versión', $doc->versionesDoc->count()) }})
                                </span>
                            </div>

                            {{-- Botones de calificacion: solo visibles si NO es modo de solo lectura --}}
                            @if (!($esSoloLectura ?? false))
                                <div class="flex flex-wrap items-center justify-between gap-2 pl-6 pt-1">
                                    <form action="{{ route('portal.documentos.evaluar', $doc->ID_Documento) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        <span class="text-[11px] text-slate-500 font-medium">Calificar:</span>
                                        <button type="submit" name="estado_doc" value="Aprobado" class="px-2.5 py-1 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-[11px] font-semibold transition-colors flex items-center gap-1 cursor-pointer">
                                            <span class="material-symbols-outlined text-[13px]">check</span> Aprobar
                                        </button>
                                        <button type="submit" name="estado_doc" value="Observado" class="px-2.5 py-1 rounded-md bg-amber-50 hover:bg-amber-100 text-amber-900 text-[11px] font-semibold transition-colors flex items-center gap-1 cursor-pointer">
                                            <span class="material-symbols-outlined text-[13px]">warning</span> Observar
                                        </button>
                                        <button type="submit" name="estado_doc" value="Rechazado" class="px-2.5 py-1 rounded-md bg-rose-50 hover:bg-rose-100 text-rose-800 text-[11px] font-semibold transition-colors flex items-center gap-1 cursor-pointer">
                                            <span class="material-symbols-outlined text-[13px]">close</span> Rechazar
                                        </button>
                                    </form>

                                    <button type="button"
                                            @click="selectDoc({{ $index }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium transition-colors cursor-pointer">
                                        <span class="material-symbols-outlined text-xs">preview</span>
                                        Ver en visor arriba
                                    </button>
                                </div>
                            @else
                                <div class="pl-6 pt-1 flex items-center justify-between gap-2 text-[11px] text-slate-400">
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[14px]">lock</span>
                                        Calificacion inmutable &mdash; solo lectura de auditoria
                                    </div>
                                    <button type="button"
                                            @click="selectDoc({{ $index }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium transition-colors cursor-pointer">
                                        <span class="material-symbols-outlined text-xs">preview</span>
                                        Ver en visor arriba
                                    </button>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            No hay documentos cargados en este expediente.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- HISTORIAL DE OBSERVACIONES Y SUBSANACIONES CIUDADANAS --}}
            @php
                $todasObservaciones = $solicitud->asignaciones->flatMap->revisiones->flatMap->observaciones;
            @endphp
            @if($todasObservaciones->isNotEmpty())
                <div class="bg-white dark:bg-[#111f33] p-5 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-base">published_with_changes</span>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-[#001229] dark:text-white">Historial de Observaciones y Subsanaciones</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Requerimientos técnicos formulados y respuestas ingresadas por el ciudadano.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                            {{ $todasObservaciones->count() }} {{ Str::plural('observación', $todasObservaciones->count()) }}
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach($todasObservaciones as $obs)
                            <div class="p-4 rounded-xl border {{ $obs->estaAbierta() ? 'border-amber-200 bg-amber-50/40 dark:bg-amber-950/20' : 'border-emerald-200 bg-emerald-50/30 dark:bg-emerald-950/20' }} space-y-3">
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

        </div>

        {{-- COLUMNA DERECHA: CONSOLA DE DICTAMEN OFICIAL --}}
        <div class="xl:col-span-5 flex flex-col gap-space-md sticky top-20">

            {{-- BANNER SLA (solo si no es concluido) --}}
            @if (!($esSoloLectura ?? false))
                <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-amber-600 text-xl">schedule</span>
                        <div>
                            <span class="text-xs font-bold text-amber-900 dark:text-amber-200 block">Emisión de dictamen</span>
                            <span class="text-[11px] text-amber-700 dark:text-amber-400">Revise los documentos y emita su dictamen.</span>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200 text-[10px] font-bold uppercase">
                        En Proceso
                    </span>
                </div>
            @endif

            {{-- CONSOLA DE DICTAMEN / RESOLUCION --}}
            <div class="bg-white dark:bg-[#111f33] p-5 lg:p-6 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col gap-5"
                 x-data="{
                     activeTab: '{{ in_array($solicitud->Estado_Solicitud, ['En_Revision', 'Observado']) || $solicitud->documentos->where('Estado_Doc', 'Subsanado')->isNotEmpty() || !($asignacionActiva && $asignacionActiva->revisiones->where('Resultado', 'Aprobado')->isNotEmpty()) ? 'dictamen' : 'resolucion' }}',
                     decision: 'Aprobado'
                 }">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h2 class="text-base font-bold text-[#001229] dark:text-white">
                        @if ($esSoloLectura ?? false)
                            Registro Inmutable del Dictamen
                        @else
                            Consola de Pronunciamiento
                        @endif
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        @if ($esSoloLectura ?? false)
                            El expediente esta cerrado. Se muestra el historial oficial de dictamenes y resoluciones.
                        @else
                            El dictamen suscrito emitira la cedula vinculante con sello institucional.
                        @endif
                    </p>
                </div>

                @if ($tieneResolucion ?? $solicitud->resoluciones->isNotEmpty())
                    {{-- ══ RESOLUCIÓN FORMAL YA EMITIDA (EXPEDIENTE CONCLUIDO) ══ --}}
                    @php $ultimaResolucion = $solicitud->resoluciones->last(); @endphp
                    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-900 dark:text-emerald-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-emerald-600 text-lg">verified</span>
                                <span class="font-bold text-sm">Resolución Oficial Emitida</span>
                            </div>
                            <span class="inline-block px-2.5 py-1 rounded bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 text-xs font-mono font-bold">
                                RES-{{ str_pad((string)$ultimaResolucion->ID_Resolucion, 5, '0', STR_PAD_LEFT) }}-{{ date('Y') }}
                            </span>
                        </div>
                        <p class="text-xs text-emerald-800 dark:text-emerald-300">
                            <strong>Sentido:</strong> Resolución {{ $ultimaResolucion->Tipo_Resolucion }}
                        </p>
                        <p class="text-xs text-emerald-700 dark:text-emerald-400">
                            <strong>Fecha de Emisión:</strong> {{ $ultimaResolucion->Fecha_Emision_Resolucion ? \Carbon\Carbon::parse($ultimaResolucion->Fecha_Emision_Resolucion)->format('d/m/Y H:i') : '' }}
                        </p>
                        <div class="pt-2 border-t border-emerald-200/60 dark:border-emerald-800/60">
                            <a href="{{ route('portal.resoluciones.pdf', $solicitud->ID_Solicitud) }}"
                               target="_blank"
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition-colors">
                                <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                                <span>Descargar Resolución Oficial (PDF)</span>
                            </a>
                        </div>
                    </div>

                    {{-- Historial de revisiones --}}
                    @foreach($solicitud->asignaciones as $asig)
                        @foreach($asig->revisiones as $rev)
                            @if($rev->Resultado !== 'En Revision')
                                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-slate-800 dark:text-slate-200">Revisión #{{ $rev->ID_Revision }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $rev->Resultado === 'Aprobado' ? 'bg-emerald-50 text-emerald-700' : ($rev->Resultado === 'Rechazado' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                            {{ $rev->Resultado }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-500">
                                        Fecha: {{ $rev->Fecha_Revision ? \Carbon\Carbon::parse($rev->Fecha_Revision)->format('d/m/Y H:i') : 'N/A' }}
                                        &bull; Revisor: {{ $asig->revisor?->Nombre ?? 'N/A' }}
                                    </p>
                                </div>
                            @endif
                        @endforeach
                    @endforeach

                    <div class="p-3 rounded-xl bg-slate-900 text-slate-300 text-[11px] flex items-center gap-2 font-mono">
                        <span class="material-symbols-outlined text-blue-400 text-base">lock</span>
                        Expediente formalmente concluido e inmutable.
                    </div>

                @else
                    {{-- Historial de revisiones técnicas previas (si existen) --}}
                    @foreach($solicitud->asignaciones as $asig)
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

                    @if (auth()->check() && (auth()->user()->esSupervisor() || auth()->user()->esAdministrador()))
                        {{-- Pestañas para Supervisor / Administrador --}}
                        <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-800 rounded-lg">
                            <button type="button"
                                    @click="activeTab = 'dictamen'"
                                    :class="activeTab === 'dictamen' ? 'bg-white dark:bg-slate-700 text-[#0051d5] dark:text-blue-400 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400'"
                                    class="flex-1 py-1.5 px-3 rounded-md text-xs transition-all cursor-pointer text-center">
                                Dictamen Técnico
                            </button>
                            <button type="button"
                                    @click="activeTab = 'resolucion'"
                                    :class="activeTab === 'resolucion' ? 'bg-white dark:bg-slate-700 text-[#0051d5] dark:text-blue-400 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400'"
                                    class="flex-1 py-1.5 px-3 rounded-md text-xs transition-all cursor-pointer text-center">
                                Resolución Final
                            </button>
                        </div>

                        {{-- Panel Resolución Final --}}
                        <div x-show="activeTab === 'resolucion'" class="space-y-4" x-cloak>
                            <form action="{{ route('portal.resoluciones.store', $solicitud->ID_Solicitud) }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="p-3 bg-blue-50/70 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 rounded-xl space-y-1">
                                    <span class="font-bold text-xs text-[#001229] dark:text-white flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[#0051d5] text-base">gavel</span>
                                        Emisión de Resolución Oficial
                                    </span>
                                    <p class="text-[11px] text-slate-600 dark:text-slate-400">
                                        Como autoridad resolutiva, usted suscribe el acto administrativo que concluye formalmente el expediente ciudadano.
                                    </p>
                                </div>

                                <div>
                                    <label for="tipo_resolucion" class="block text-xs font-semibold text-slate-800 dark:text-slate-200 mb-1">
                                        Sentido de la Resolución <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="tipo_resolucion" id="tipo_resolucion" required
                                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                                        <option value="Favorable">Favorable (Aprobación Definitiva del Trámite)</option>
                                        <option value="Desfavorable">Desfavorable (Denegatoria)</option>
                                        <option value="Inadmisible">Inadmisible / Archivo</option>
                                        <option value="Cancelada">Cancelada por el Administrado</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="comentarios" class="block text-xs font-semibold text-slate-800 dark:text-slate-200 mb-1">
                                        Fundamentación y Motivación Jurídica
                                    </label>
                                    <textarea name="comentarios" id="comentarios" rows="3"
                                              placeholder="Indique los antecedentes técnicos de la revisión y la motivación jurídica de la resolución..."
                                              class="w-full p-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#0051d5]"></textarea>
                                </div>

                                <button type="submit" class="w-full h-11 bg-[#001229] hover:bg-[#0f2744] text-white rounded-xl text-xs font-semibold transition-all flex items-center justify-center gap-2 shadow-sm cursor-pointer">
                                    <span class="material-symbols-outlined text-base">verified</span>
                                    <span>Emitir y Firmar Resolución Formal</span>
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Panel Dictamen Técnico (Visible para Revisor siempre, y para Supervisor/Admin bajo la pestaña 'dictamen') --}}
                    <div x-show="activeTab === 'dictamen'" class="space-y-4">
                        @if ($asignacionActiva)
                            @php
                                $estadoSolEval = $solicitud->Estado_Solicitud;
                                $puedeDictaminarEval = in_array($estadoSolEval, ['Pendiente', 'En_Revision'], true);
                            @endphp

                            @if($puedeDictaminarEval)
                                <form action="{{ route('portal.revisiones.store', $asignacionActiva->ID_Asignacion) }}" method="POST" class="space-y-4" x-data="{ enviandoEval: false }" @submit="enviandoEval = true">
                                    @csrf

                                    <div class="space-y-2">
                                        <label class="block text-xs font-semibold text-slate-800 dark:text-slate-200">Resultado Oficial</label>
                                        <input type="hidden" name="resultado" :value="decision">

                                        <div class="grid grid-cols-1 gap-2.5">
                                            <button type="button"
                                                    @click="decision = 'Aprobado'"
                                                    :class="decision === 'Aprobado' ? 'bg-emerald-50 border-2 border-emerald-600 shadow-xs' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700'"
                                                    class="p-3 rounded-xl text-left flex items-start gap-3 transition-all">
                                                <span class="material-symbols-outlined text-emerald-600 text-xl mt-0.5">check_circle</span>
                                                <div>
                                                    <span class="text-xs font-bold text-slate-900 dark:text-white block">Dictamen Favorable (Aprobado)</span>
                                                    <span class="text-[11px] text-slate-500">Cumple integramente los requisitos de admisibilidad.</span>
                                                </div>
                                            </button>

                                            <button type="button"
                                                    @click="decision = 'Observado'"
                                                    :class="decision === 'Observado' ? 'bg-amber-50 border-2 border-amber-500 shadow-xs' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700'"
                                                    class="p-3 rounded-xl text-left flex items-start gap-3 transition-all">
                                                <span class="material-symbols-outlined text-amber-600 text-xl mt-0.5">rule</span>
                                                <div>
                                                    <span class="text-xs font-bold text-slate-900 dark:text-white block">Observar y Requerir Subsanacion</span>
                                                    <span class="text-[11px] text-slate-500">Notifica al solicitante plazo de 48h para corregir recaudos.</span>
                                                </div>
                                            </button>

                                            <button type="button"
                                                    @click="decision = 'Rechazado'"
                                                    :class="decision === 'Rechazado' ? 'bg-rose-50 border-2 border-rose-600 shadow-xs' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700'"
                                                    class="p-3 rounded-xl text-left flex items-start gap-3 transition-all">
                                                <span class="material-symbols-outlined text-rose-600 text-xl mt-0.5">cancel</span>
                                                <div>
                                                    <span class="text-xs font-bold text-slate-900 dark:text-white block">Dictamen Desfavorable (Rechazado)</span>
                                                    <span class="text-[11px] text-slate-500">Incumplimiento insubsanable de la normativa aplicable vigente.</span>
                                                </div>
                                            </button>
                                        </div>
                                    </div>

                                    <div x-show="decision === 'Observado' || decision === 'Rechazado'" class="space-y-1.5 pt-2" x-cloak>
                                        <label class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                                            Fundamentacion Juridica / Observacion Tecnica <span class="text-rose-500">*</span>
                                        </label>
                                        <textarea name="observaciones[0][detalles]"
                                                  :disabled="decision === 'Aprobado'"
                                                  :required="decision === 'Observado' || decision === 'Rechazado'"
                                                  rows="4"
                                                  placeholder="Describa con precision los recaudos observados, articulos de la norma aplicable y el plazo conferido para subsanar..."
                                                  class="w-full p-3 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary"></textarea>
                                    </div>

                                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-between text-[11px]">
                                        <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300 font-medium">
                                            <span class="material-symbols-outlined text-secondary text-base">verified_user</span>
                                            Firma Digital Activa
                                        </span>
                                        <span class="font-mono text-emerald-700 font-semibold">Certificado #8941-OK</span>
                                    </div>

                                    <button type="submit" :disabled="enviandoEval" class="w-full h-11 bg-primary-container hover:bg-[#1a3860] disabled:opacity-50 text-white rounded-xl text-xs font-semibold transition-all flex items-center justify-center gap-2 shadow-sm cursor-pointer">
                                        <span class="material-symbols-outlined text-base">gavel</span>
                                        <span x-text="enviandoEval ? 'Emitiendo dictamen...' : 'Emitir Dictamen y Sellar Expediente'">Emitir Dictamen y Sellar Expediente</span>
                                    </button>
                                </form>
                            @elseif($estadoSolEval === 'Observado')
                                <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-xs text-amber-900 dark:text-amber-200 space-y-1.5">
                                    <div class="flex items-center gap-2 font-bold">
                                        <span class="material-symbols-outlined text-amber-600 text-lg">hourglass_top</span>
                                        <span>Subsanación en curso — Esperando al administrado</span>
                                    </div>
                                    <p class="text-[11px] text-amber-800 dark:text-amber-300">
                                        El expediente tiene observaciones técnicas notificadas y no se pueden emitir nuevos dictámenes hasta que el ciudadano envíe su respuesta o documentos corregidos.
                                    </p>
                                </div>
                            @elseif($estadoSolEval === 'Aprobado')
                                <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-900 dark:text-emerald-200 space-y-1.5">
                                    <div class="flex items-center gap-2 font-bold">
                                        <span class="material-symbols-outlined text-emerald-600 text-lg">check_circle</span>
                                        <span>Dictamen Técnico Favorable Emitido</span>
                                    </div>
                                    <p class="text-[11px] text-emerald-800 dark:text-emerald-300">
                                        Este expediente ya fue dictaminado favorablemente por el revisor técnico. Se encuentra a la espera de la firma de Resolución Oficial por el Supervisor.
                                    </p>
                                </div>
                            @elseif($estadoSolEval === 'Rechazado')
                                <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs text-rose-900 dark:text-rose-200 space-y-1.5">
                                    <div class="flex items-center gap-2 font-bold">
                                        <span class="material-symbols-outlined text-rose-600 text-lg">cancel</span>
                                        <span>Dictamen Técnico Desfavorable Emitido</span>
                                    </div>
                                    <p class="text-[11px] text-rose-800 dark:text-rose-300">
                                        El expediente fue dictaminado como no procedente y no presenta subsanaciones pendientes de evaluar.
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-300 space-y-1">
                                <span class="font-bold block">Sin Asignacion Activa</span>
                                <p class="text-[11px]">Para emitir un dictamen formal, el expediente debe tener asignado al revisor en la Matriz de Cargas.</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
