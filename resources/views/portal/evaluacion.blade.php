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
                'versiones_count' => $d->versionesDoc->count(),
            ];
        })->values();
    @endphp

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg items-start"
         x-data="{
             docs: {{ json_encode($docsData) }},
             activeIndex: 0,
             get activeDoc() { return this.docs[this.activeIndex] || null; },
             selectDoc(idx) { this.activeIndex = idx; }
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
                                  'text-amber-700 dark:text-amber-400': activeDoc?.estado === 'Observado',
                                  'text-rose-700 dark:text-rose-400': activeDoc?.estado === 'Rechazado',
                                  'text-slate-600 dark:text-slate-400': activeDoc?.estado !== 'Aprobado' && activeDoc?.estado !== 'Observado' && activeDoc?.estado !== 'Rechazado'
                              }"
                              x-text="'Estado: ' + (activeDoc?.estado || 'Pendiente')"></span>
                    </div>
                </div>

                {{-- Visor Real del Archivo PDF Cargado por el Ciudadano --}}
                <div class="relative w-full rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900 shadow-inner">
                    {{-- Caso 1: Archivo disponible --}}
                    <div x-show="activeDoc?.tiene_archivo" class="w-full h-[620px]">
                        <iframe :src="activeDoc?.archivo_url"
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
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $doc->Estado_Doc === 'Aprobado' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($doc->Estado_Doc === 'Observado' ? 'bg-amber-50 text-amber-800 border-amber-200' : ($doc->Estado_Doc === 'Rechazado' ? 'bg-rose-50 text-rose-800 border-rose-200' : 'bg-slate-100 text-slate-700 border-slate-200')) }}">
                                    {{ $doc->Estado_Doc }}
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

        </div>

        {{-- COLUMNA DERECHA: CONSOLA DE DICTAMEN OFICIAL --}}
        <div class="xl:col-span-5 flex flex-col gap-space-md sticky top-20">

            {{-- BANNER SLA (solo si no es concluido) --}}
            @if (!($esSoloLectura ?? false))
                <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-amber-600 text-xl">schedule</span>
                        <div>
                            <span class="text-xs font-bold text-amber-900 dark:text-amber-200 block">Plazo Normativo SLA</span>
                            <span class="text-[11px] text-amber-700 dark:text-amber-400">Revise los documentos y emita su dictamen.</span>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200 text-[10px] font-bold uppercase">
                        En Proceso
                    </span>
                </div>
            @endif

            {{-- CONSOLA DE DICTAMEN / RESOLUCION --}}
            <div class="bg-white dark:bg-[#111f33] p-5 lg:p-6 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col gap-5" x-data="{ decision: 'Aprobado' }">
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

                @if ($esSoloLectura ?? false)
                    {{-- MODO SOLO LECTURA: mostrar resoluciones e historial de revisiones --}}
                    @php $ultimaResolucion = $solicitud->resoluciones->last(); @endphp
                    @if ($ultimaResolucion)
                        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-900 dark:text-emerald-200 space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-emerald-600 text-lg">verified</span>
                                <span class="font-bold">Resolucion Oficial Emitida</span>
                            </div>
                            <p class="text-[11px] text-emerald-800 dark:text-emerald-300">
                                <strong>Sentido:</strong> Resolucion {{ $ultimaResolucion->Tipo_Resolucion }}
                            </p>
                            <p class="text-[11px] text-emerald-700 dark:text-emerald-400">
                                <strong>Fecha de Emision:</strong> {{ $ultimaResolucion->Fecha_Emision_Resolucion ? \Carbon\Carbon::parse($ultimaResolucion->Fecha_Emision_Resolucion)->format('d/m/Y H:i') : '' }}
                            </p>
                            <span class="inline-block px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 text-[10px] font-mono font-bold">
                                RES-{{ str_pad($ultimaResolucion->ID_Resolucion, 5, '0', STR_PAD_LEFT) }}-{{ date('Y') }}
                            </span>
                        </div>
                    @endif

                    {{-- Historial de revisiones --}}
                    @foreach($solicitud->asignaciones as $asig)
                        @foreach($asig->revisiones as $rev)
                            @if($rev->Resultado !== 'En Revision')
                                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-slate-800 dark:text-slate-200">Revision #{{ $rev->ID_Revision }}</span>
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
                        Expediente en modo inmutable. Ninguna accion de modificacion esta disponible.
                    </div>

                @elseif (auth()->check() && auth()->user()->esSupervisor())
                    {{-- CONSOLA DE SUPERVISOR: EMISION DE RESOLUCION FINAL --}}
                    @php $ultimaResolucion = $solicitud->resoluciones->last(); @endphp

                    @if ($ultimaResolucion)
                        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-900 dark:text-emerald-200 space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-emerald-600 text-lg">verified</span>
                                <span class="font-bold">Resolucion Oficial Emitida</span>
                            </div>
                            <p class="text-[11px] text-emerald-800 dark:text-emerald-300">
                                <strong>Sentido:</strong> Resolucion {{ $ultimaResolucion->Tipo_Resolucion }}
                            </p>
                            <p class="text-[11px] text-emerald-700 dark:text-emerald-400">
                                <strong>Fecha de Emision:</strong> {{ $ultimaResolucion->Fecha_Emision_Resolucion ? \Carbon\Carbon::parse($ultimaResolucion->Fecha_Emision_Resolucion)->format('d/m/Y H:i') : '' }}
                            </p>
                            <span class="inline-block px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 text-[10px] font-mono font-bold">
                                RES-{{ str_pad($ultimaResolucion->ID_Resolucion, 5, '0', STR_PAD_LEFT) }}-{{ date('Y') }}
                            </span>
                        </div>
                    @else
                        <form action="{{ route('portal.resoluciones.store', $solicitud->ID_Solicitud) }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="p-3 bg-blue-50/70 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 rounded-xl space-y-1">
                                <span class="font-bold text-xs text-[#001229] dark:text-white flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[#0051d5] text-base">gavel</span>
                                    Emision de Resolucion de Supervision
                                </span>
                                <p class="text-[11px] text-slate-600 dark:text-slate-400">
                                    Como Supervisor / Dictaminador, usted suscribe el acto administrativo que concluye el expediente.
                                </p>
                            </div>

                            <div>
                                <label for="tipo_resolucion" class="block text-xs font-semibold text-slate-800 dark:text-slate-200 mb-1">
                                    Sentido de la Resolucion <span class="text-rose-500">*</span>
                                </label>
                                <select name="tipo_resolucion" id="tipo_resolucion" required
                                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                                    <option value="Favorable">Favorable (Aprobacion Definitiva del Tramite)</option>
                                    <option value="Desfavorable">Desfavorable (Denegatoria)</option>
                                    <option value="Inadmisible">Inadmisible / Archivo</option>
                                    <option value="Cancelada">Cancelada por el Administrado</option>
                                </select>
                            </div>

                            <div>
                                <label for="comentarios" class="block text-xs font-semibold text-slate-800 dark:text-slate-200 mb-1">
                                    Fundamentacion y Comentarios Resolutivos
                                </label>
                                <textarea name="comentarios" id="comentarios" rows="3"
                                          placeholder="Indique los antecedentes tecnicos de la revision y la motivacion juridica de la resolucion..."
                                          class="w-full p-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#0051d5]"></textarea>
                            </div>

                            <button type="submit" class="w-full h-11 bg-[#001229] hover:bg-[#0f2744] text-white rounded-xl text-xs font-semibold transition-all flex items-center justify-center gap-2 shadow-sm cursor-pointer">
                                <span class="material-symbols-outlined text-base">verified</span>
                                <span>Emitir y Firmar Resolucion Formal</span>
                            </button>
                        </form>
                    @endif

                @elseif (auth()->check() && auth()->user()->esAdministrador() && !auth()->user()->esRevisor())
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-300 space-y-1.5">
                        <span class="font-bold flex items-center gap-1.5 text-slate-800 dark:text-slate-200">
                            <span class="material-symbols-outlined text-base text-[#0051d5]">visibility</span>
                            Modo Visualizacion / Auditoria
                        </span>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Como Administrador, usted tiene acceso de solo lectura para auditar el expediente. La calificacion tecnica y emision de dictamen corresponde exclusivamente a los Revisores Tecnicos periciales asignados.
                        </p>
                    </div>

                @elseif ($asignacionActiva)
                    <form action="{{ route('portal.revisiones.store', $asignacionActiva->ID_Asignacion) }}" method="POST" class="space-y-4">
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

                        <button type="submit" class="w-full h-11 bg-primary-container hover:bg-[#1a3860] text-white rounded-xl text-xs font-semibold transition-all flex items-center justify-center gap-2 shadow-sm">
                            <span class="material-symbols-outlined text-base">gavel</span>
                            <span>Emitir Dictamen y Sellar Expediente</span>
                        </button>
                    </form>
                @else
                    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-300 space-y-1">
                        <span class="font-bold block">Sin Asignacion Activa</span>
                        <p class="text-[11px]">Para emitir un dictamen formal, el expediente debe tener asignado al revisor en la Matriz de Cargas.</p>
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
