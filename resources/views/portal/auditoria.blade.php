@extends('layouts.portal')

@section('title', 'Bitácora de Expedientes')

@section('breadcrumb')
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <a href="{{ route('portal.dashboard') }}">Administración Central</a>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="current">Bitácora de Expedientes</span>
@endsection

@section('content')
<div
    class="space-y-4 sm:space-y-6"
    x-data="{
        abierto: null,
        toggle(id) {
            this.abierto = this.abierto === id ? null : id;
        }
    }"
>

    {{-- ══ CABECERA ══ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 bg-white dark:bg-[#111f33] p-3.5 sm:p-5 rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs">
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#eff4ff] dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300">Trazabilidad</span>
                <span class="text-xs text-slate-400 dark:text-slate-500">•</span>
                <span class="text-xs text-slate-500 dark:text-slate-400 break-words">Registro Histórico Institucional SETC</span>
            </div>
            <h1 class="text-base sm:text-xl font-bold text-slate-900 dark:text-white leading-snug break-words">Bitácora de Expedientes Tramitados</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                Haz clic en cualquier expediente para desplegar los documentos y recaudos asociados al trámite.
            </p>
        </div>
        <div class="shrink-0 w-full sm:w-auto">
            <a href="{{ route('portal.auditoria', array_merge(request()->query(), ['export' => 'csv'])) }}"
               class="inline-flex items-center justify-center w-full sm:w-auto gap-2 px-3.5 py-2 rounded-lg bg-white dark:bg-[#111f33] border border-slate-200 dark:border-slate-800 hover:bg-slate-50 text-slate-700 dark:text-slate-200 text-xs font-semibold shadow-xs transition-colors">
                <span class="material-symbols-outlined text-base text-emerald-600">sim_card_download</span>
                Exportar CSV
            </a>
        </div>
    </div>

    {{-- ══ KPIs RESPONSIVOS ══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
        <div class="bg-white dark:bg-[#111f33] p-3 sm:p-4 rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Expedientes</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-sm sm:text-base">folder_open</span>
                </div>
            </div>
            <div class="mt-1.5 sm:mt-2">
                <span class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($kpis['total_expedientes']) }}</span>
                <span class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 block mt-0.5">Registros en sistema</span>
            </div>
        </div>

        <div class="bg-white dark:bg-[#111f33] p-3 sm:p-4 rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Ingresados Hoy</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-sm sm:text-base">today</span>
                </div>
            </div>
            <div class="mt-1.5 sm:mt-2">
                <span class="text-xl sm:text-2xl font-bold text-amber-700 dark:text-amber-400">{{ $kpis['expedientes_hoy'] }}</span>
                <span class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 block mt-0.5">Jornada actual</span>
            </div>
        </div>

        <div class="bg-white dark:bg-[#111f33] p-3 sm:p-4 rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Con Cita</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-sm sm:text-base">event_available</span>
                </div>
            </div>
            <div class="mt-1.5 sm:mt-2">
                <span class="text-xl sm:text-2xl font-bold text-teal-700 dark:text-teal-400">{{ $kpis['con_cita'] }}</span>
                <span class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 block mt-0.5">Turno agendado</span>
            </div>
        </div>

        <div class="bg-white dark:bg-[#111f33] p-3 sm:p-4 rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Concluidos</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-sm sm:text-base">check_circle</span>
                </div>
            </div>
            <div class="mt-1.5 sm:mt-2">
                <span class="text-xl sm:text-2xl font-bold text-emerald-700 dark:text-emerald-400">{{ $kpis['concluidos'] }}</span>
                <span class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 block mt-0.5">Finalizados</span>
            </div>
        </div>
    </div>

    {{-- ══ FILTROS ══ --}}
    <div class="p-3.5 sm:p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs">
        <form action="{{ route('portal.auditoria') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Ciudadano Solicitante</label>
                <select name="id_usuario" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 rounded-lg text-xs text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-secondary/20 cursor-pointer">
                    <option value="">Todos los ciudadanos</option>
                    @foreach($ciudadanos as $ciudadano)
                        <option value="{{ $ciudadano->ID_Usuarios }}" @selected((string) $idUsuarioFiltro === (string) $ciudadano->ID_Usuarios)>
                            {{ $ciudadano->Nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Tipo de Trámite</label>
                <input type="text" name="accion" value="{{ $accionFiltro }}" placeholder="Ej. Licencia..."
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 rounded-lg text-xs text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-secondary/20">
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Estado del Expediente</label>
                <select name="estado" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 rounded-lg text-xs text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-secondary/20 cursor-pointer">
                    <option value="">Todos los estados</option>
                    <option value="Pendiente"   @selected($estadoFiltro === 'Pendiente')>Pendiente</option>
                    <option value="En Revisión" @selected($estadoFiltro === 'En Revisión')>En Revisión</option>
                    <option value="Aprobado"    @selected($estadoFiltro === 'Aprobado')>Aprobado</option>
                    <option value="Rechazado"   @selected($estadoFiltro === 'Rechazado')>Rechazado</option>
                    <option value="Concluido"   @selected($estadoFiltro === 'Concluido')>Concluido</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Fecha de Solicitud</label>
                <input type="date" name="fecha" value="{{ $fechaFiltro }}"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 rounded-lg text-xs text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-secondary/20">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-primary-container hover:bg-[#1a3860] dark:bg-blue-600 dark:hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition-colors cursor-pointer">
                    Filtrar
                </button>
                @if($idUsuarioFiltro || $accionFiltro || $fechaFiltro || $estadoFiltro)
                    <a href="{{ route('portal.auditoria') }}" class="py-2 px-3 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 rounded-lg border border-slate-200 dark:border-slate-700 transition-colors">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ══ LISTADO DE EXPEDIENTES (DUAL: TABLA DESKTOP + TARJETAS MÓVILES) ══ --}}
    <div class="bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
        {{-- Barra superior de la sección --}}
        <div class="px-4 sm:px-5 py-3.5 bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2 min-w-0">
                <span class="material-symbols-outlined text-primary-container dark:text-blue-400 text-lg shrink-0">history_edu</span>
                <span class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate">Historial de Expedientes</span>
                <span class="hidden sm:inline text-[11px] text-slate-400 dark:text-slate-500 ml-1">— Clic para ver documentos</span>
            </div>
            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium shrink-0">
                {{ $solicitudes->total() }} registros
            </span>
        </div>

        {{-- ── 1. VISTA MÓVIL: TARJETAS TOUCH (Menor a md: 768px) ── --}}
        <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($solicitudes as $sol)
                @php
                    $cita    = $sol->citas->first();
                    $revisor = $sol->asignaciones->first()?->revisor ?? $cita?->revisor ?? null;
                    $solId   = $sol->ID_Solicitud;
                    $estadoClass = match($sol->Estado_Solicitud) {
                        'Aprobado', 'Concluido' => 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                        'En Revisión'           => 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                        'Rechazado'             => 'bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                        default                 => 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                    };
                @endphp
                <div class="p-3.5 space-y-3 transition-colors"
                     :class="abierto === {{ $solId }} ? 'bg-blue-50/40 dark:bg-blue-950/20' : ''">

                    {{-- Cabecera Card: Folio + Estado --}}
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm text-[#0051d5] dark:text-blue-400">description</span>
                            <span class="font-mono font-bold text-xs text-[#0051d5] dark:text-blue-400">
                                {{ $sol->folio?->Codigo_Folio ?? 'Sin folio' }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono">#{{ $solId }}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $estadoClass }}">
                            {{ $sol->Estado_Solicitud }}
                        </span>
                    </div>

                    {{-- Ciudadano --}}
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-blue-50 dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300 flex items-center justify-center font-bold text-[10px] shrink-0">
                            {{ strtoupper(substr($sol->solicitante?->Nombre ?? 'C', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <span class="font-semibold text-slate-900 dark:text-white text-xs block truncate">
                                {{ $sol->solicitante?->Nombre ?? 'Ciudadano' }}
                            </span>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 block truncate">
                                {{ $sol->solicitante?->Email }}
                            </span>
                        </div>
                    </div>

                    {{-- Trámite --}}
                    <div class="text-xs bg-slate-50 dark:bg-slate-800/50 p-2 rounded-lg border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider block">Trámite</span>
                        <span class="font-medium text-slate-800 dark:text-slate-200 block truncate">
                            {{ $sol->versionTramite?->tipoTramite?->Nombre ?? 'Sin trámite' }}
                        </span>
                    </div>

                    {{-- Cita y Revisor en 2 columnas --}}
                    <div class="grid grid-cols-2 gap-2 text-[11px] pt-1">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-medium">Turno de Cita:</span>
                            @if($cita)
                                <span class="font-semibold text-slate-800 dark:text-slate-200 block">
                                    {{ \Carbon\Carbon::parse($cita->Fecha)->format('d/m/Y') }} {{ substr((string) $cita->Hora, 0, 5) }}
                                </span>
                                <span class="text-[10px] text-emerald-600 dark:text-emerald-400">{{ $cita->Estado_Citas }}</span>
                            @else
                                <span class="text-slate-400 italic text-[10px]">Sin cita agendada</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block font-medium">Revisor Técnico:</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200 block truncate">
                                {{ $revisor?->Nombre ?? 'Sin asignar' }}
                            </span>
                        </div>
                    </div>

                    {{-- Botón Desplegable Touch para Móvil --}}
                    <button
                        type="button"
                        data-toggle-exp="{{ $solId }}"
                        @click="toggle({{ $solId }})"
                        class="w-full mt-2 flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold transition-all shadow-2xs cursor-pointer"
                        :class="abierto === {{ $solId }}
                            ? 'bg-[#0051d5] text-white shadow-blue-500/20'
                            : 'bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300 border border-blue-200 dark:border-blue-800'"
                    >
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">folder_open</span>
                            <span>{{ $sol->documentos->count() }} documentos adjuntos</span>
                        </span>
                        <span class="material-symbols-outlined text-sm transition-transform duration-200"
                              :class="abierto === {{ $solId }} ? 'rotate-180' : ''">
                            expand_more
                        </span>
                    </button>

                    {{-- Desplegable Móvil con los documentos --}}
                    <div
                        id="desplegable-mobile-{{ $solId }}"
                        x-show="abierto === {{ $solId }}"
                        x-cloak
                        class="pt-2 space-y-2.5"
                    >
                        @forelse($sol->documentos as $doc)
                            @php
                                $ultVersion = $doc->versionesDoc->last();
                                $docEstado = $doc->Estado_Doc;
                                $docClass = match($docEstado) {
                                    'Aprobado'  => 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                    'Rechazado' => 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                                    'Observado' => 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                    default     => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-600',
                                };
                            @endphp
                            <div class="p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#0e1c2e] space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <span class="font-semibold text-slate-900 dark:text-white text-xs block leading-tight">
                                        {{ $doc->requisito?->Nombre ?? 'Documento #'.$doc->ID_Documento }}
                                    </span>
                                    <span class="shrink-0 px-2 py-0.5 rounded-full text-[9px] font-bold border {{ $docClass }}">
                                        {{ $docEstado }}
                                    </span>
                                </div>

                                <div class="text-[10px] text-slate-400 space-y-0.5">
                                    <div class="flex items-center gap-1 truncate">
                                        <span class="material-symbols-outlined text-xs">attach_file</span>
                                        <span class="truncate">{{ $ultVersion?->Nombre_Original ?? 'Sin archivo digital' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between pt-1">
                                        <span>{{ $doc->versionesDoc->count() }} versión(es)</span>
                                        <span>{{ $ultVersion?->Fecha_Carga ? \Carbon\Carbon::parse($ultVersion->Fecha_Carga)->format('d/m/Y') : '' }}</span>
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                    <span class="text-[9px] font-mono text-slate-400">{{ $doc->requisito?->Formato ?? 'PDF' }}</span>
                                    <a href="{{ route('portal.documentos.archivo', $doc->ID_Documento) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold bg-blue-50 text-[#0051d5] dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800"
                                    >
                                        <span class="material-symbols-outlined text-xs">visibility</span>
                                        <span>Ver Archivo</span>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic text-center py-2">Sin documentos registrados.</p>
                        @endforelse
                    </div>

                </div>
            @empty
                <div class="p-8 text-center text-slate-400">
                    <span class="material-symbols-outlined text-3xl block mb-1">manage_search</span>
                    <span class="text-xs">No se encontraron expedientes con los filtros aplicados.</span>
                </div>
            @endforelse
        </div>

        {{-- ── 2. VISTA DESKTOP: TABLA COMPLETA CON DESPLEGABLE (md: 768px en adelante) ── --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Folio / Expediente</th>
                        <th class="py-3 px-4">Ciudadano Solicitante</th>
                        <th class="py-3 px-4">Trámite</th>
                        <th class="py-3 px-4 text-center">Estado</th>
                        <th class="py-3 px-4">Fecha Solicitud</th>
                        <th class="py-3 px-4">Fecha y Hora de Cita</th>
                        <th class="py-3 px-4">Revisor Técnico</th>
                        <th class="py-3 px-4 text-center">Documentos</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($solicitudes as $sol)
                        @php
                            $cita    = $sol->citas->first();
                            $revisor = $sol->asignaciones->first()?->revisor ?? $cita?->revisor ?? null;
                            $solId   = $sol->ID_Solicitud;
                        @endphp

                        {{-- Fila Principal del Expediente --}}
                        <tr
                            data-toggle-exp="{{ $solId }}"
                            @click="toggle({{ $solId }})"
                            class="hover:bg-blue-50/50 dark:hover:bg-blue-950/30 cursor-pointer transition-colors group select-none"
                            :class="abierto === {{ $solId }} ? 'bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-l-[#0051d5]' : 'border-l-4 border-l-transparent'"
                            title="Haz clic para desplegar los documentos de este expediente"
                        >
                            {{-- Folio --}}
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-xs text-slate-400 group-hover:text-[#0051d5] transition-transform duration-200"
                                          :class="abierto === {{ $solId }} ? 'rotate-90 text-[#0051d5]' : ''">
                                        chevron_right
                                    </span>
                                    <div>
                                        <span class="font-mono font-bold text-[#0051d5] dark:text-blue-400 group-hover:underline block">
                                            {{ $sol->folio?->Codigo_Folio ?? 'Sin folio' }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 block">
                                            #EXP-{{ $solId }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Ciudadano --}}
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-blue-50 dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300 flex items-center justify-center font-bold text-[10px] shrink-0">
                                        {{ strtoupper(substr($sol->solicitante?->Nombre ?? 'C', 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-semibold text-slate-900 dark:text-white block">
                                            {{ $sol->solicitante?->Nombre ?? 'Ciudadano' }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500">
                                            {{ $sol->solicitante?->Email }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Trámite --}}
                            <td class="py-3.5 px-4 max-w-[180px]">
                                <span class="font-semibold text-slate-900 dark:text-white block truncate" title="{{ $sol->versionTramite?->tipoTramite?->Nombre }}">
                                    {{ $sol->versionTramite?->tipoTramite?->Nombre ?? 'Sin trámite' }}
                                </span>
                                @if($sol->versionTramite)
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500">v{{ $sol->versionTramite->No_Version }}</span>
                                @endif
                            </td>

                            {{-- Estado --}}
                            <td class="py-3.5 px-4 text-center">
                                @php
                                    $estadoClass = match($sol->Estado_Solicitud) {
                                        'Aprobado', 'Concluido' => 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                        'En Revisión'           => 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                        'Rechazado'             => 'bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                                        default                 => 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-semibold border {{ $estadoClass }}">
                                    {{ $sol->Estado_Solicitud }}
                                </span>
                            </td>

                            {{-- Fecha Solicitud --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-semibold text-slate-700 dark:text-slate-300">
                                    {{ $sol->Fecha_Creacion_Solicitud?->translatedFormat('d \d\e M, Y') ?? '--' }}
                                </div>
                                <div class="text-[10px] text-slate-400 dark:text-slate-500">
                                    {{ $sol->Fecha_Creacion_Solicitud?->format('H:i') ?? '' }}
                                </div>
                            </td>

                            {{-- Fecha/Hora Cita --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($cita)
                                    <div class="font-semibold text-slate-700 dark:text-slate-300">
                                        {{ \Carbon\Carbon::parse($cita->Fecha)->translatedFormat('d \d\e M, Y') }}
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="font-mono text-[11px] font-bold text-primary-container dark:text-blue-300">
                                            {{ substr((string) $cita->Hora, 0, 5) }} hrs
                                        </span>
                                        @php
                                            $citaClass = match($cita->Estado_Citas) {
                                                'Atendida'   => 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                                'No asistió' => 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800',
                                                default      => 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                            };
                                        @endphp
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold border {{ $citaClass }}">
                                            {{ $cita->Estado_Citas }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400 dark:text-slate-500 italic">Sin cita agendada</span>
                                @endif
                            </td>

                            {{-- Revisor --}}
                            <td class="py-3.5 px-4">
                                @if($revisor)
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-6 h-6 rounded-full bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 flex items-center justify-center font-bold text-[10px] shrink-0">
                                            {{ strtoupper(substr($revisor->Nombre, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-semibold text-slate-900 dark:text-white block text-xs">{{ $revisor->Nombre }}</span>
                                            <span class="text-[10px] text-teal-600 dark:text-teal-400">Revisor Técnico</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                                        Sin asignar
                                    </span>
                                @endif
                            </td>

                            {{-- Botón Desplegable con Contador --}}
                            <td class="py-3.5 px-4 text-center">
                                <button
                                    type="button"
                                    data-toggle-exp="{{ $solId }}"
                                    @click.stop="toggle({{ $solId }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all shadow-2xs cursor-pointer"
                                    :class="abierto === {{ $solId }}
                                        ? 'bg-[#0051d5] text-white shadow-blue-500/20'
                                        : 'bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/60 dark:hover:bg-blue-900/60 text-[#0051d5] dark:text-blue-300 border border-blue-200 dark:border-blue-800'"
                                    title="Desplegar documentos de este expediente"
                                >
                                    <span class="material-symbols-outlined text-sm">folder_open</span>
                                    <span>{{ $sol->documentos->count() }} docs</span>
                                    <span class="material-symbols-outlined text-xs transition-transform duration-200"
                                          :class="abierto === {{ $solId }} ? 'rotate-180' : ''">
                                        expand_more
                                    </span>
                                </button>
                            </td>
                        </tr>

                        {{-- ══ FILA DESPLEGABLE CON LOS DOCUMENTOS (DESKTOP) ══ --}}
                        <tr
                            id="desplegable-{{ $solId }}"
                            x-show="abierto === {{ $solId }}"
                            x-cloak
                            class="bg-slate-50/90 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-800"
                        >
                            <td colspan="8" class="p-4 sm:p-6">
                                <div class="bg-white dark:bg-[#0e1c2e] rounded-xl border border-blue-200 dark:border-slate-700 shadow-md p-4 sm:p-5 space-y-4">

                                    {{-- Cabecera del Desplegable --}}
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300 flex items-center justify-center shrink-0">
                                                <span class="material-symbols-outlined text-lg">folder_open</span>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-slate-900 dark:text-white text-sm">
                                                        Documentos del Expediente {{ $sol->folio?->Codigo_Folio ?? '#EXP-'.$solId }}
                                                    </span>
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                                        {{ $sol->documentos->count() }} adjunto(s)
                                                    </span>
                                                </div>
                                                <span class="text-xs text-slate-500 dark:text-slate-400">
                                                    {{ $sol->solicitante?->Nombre }} • {{ $sol->versionTramite?->tipoTramite?->Nombre }}
                                                </span>
                                            </div>
                                        </div>

                                        <button
                                            type="button"
                                            @click="abierto = null"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold transition-colors cursor-pointer self-start sm:self-auto"
                                        >
                                            <span class="material-symbols-outlined text-sm">expand_less</span>
                                            <span>Ocultar</span>
                                        </button>
                                    </div>

                                    {{-- Tarjetas de Documentos --}}
                                    @if($sol->documentos->isNotEmpty())
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                                            @foreach($sol->documentos as $doc)
                                                @php
                                                    $ultVersion = $doc->versionesDoc->last();
                                                    $docEstado = $doc->Estado_Doc;
                                                    $docClass = match($docEstado) {
                                                        'Aprobado'  => 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                                        'Rechazado' => 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                                                        'Observado' => 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                                        default     => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-600',
                                                    };
                                                    $iconName = match($docEstado) {
                                                        'Aprobado'  => 'verified',
                                                        'Rechazado' => 'cancel',
                                                        'Observado' => 'warning',
                                                        default     => 'description',
                                                    };
                                                @endphp
                                                <div class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-800/40 hover:border-blue-300 dark:hover:border-slate-600 transition-colors">
                                                    {{-- Icono Estado --}}
                                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 mt-0.5 {{ $docClass }}">
                                                        <span class="material-symbols-outlined text-base">{{ $iconName }}</span>
                                                    </div>

                                                    {{-- Info Documento --}}
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-start justify-between gap-2">
                                                            <span class="font-semibold text-slate-900 dark:text-white text-xs block leading-tight">
                                                                {{ $doc->requisito?->Nombre ?? 'Documento #'.$doc->ID_Documento }}
                                                            </span>
                                                            <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $docClass }}">
                                                                {{ $docEstado }}
                                                            </span>
                                                        </div>

                                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2 text-[10px] text-slate-500 dark:text-slate-400">
                                                            <span class="flex items-center gap-1 truncate max-w-[200px]" title="{{ $ultVersion?->Nombre_Original ?? 'Sin archivo digital' }}">
                                                                <span class="material-symbols-outlined text-xs text-slate-400">attach_file</span>
                                                                <span>{{ $ultVersion?->Nombre_Original ?? 'Sin archivo digital' }}</span>
                                                            </span>

                                                            <span class="flex items-center gap-1">
                                                                <span class="material-symbols-outlined text-xs text-slate-400">history</span>
                                                                <span>{{ $doc->versionesDoc->count() }} ver.</span>
                                                            </span>

                                                            @if($ultVersion?->Fecha_Carga)
                                                                <span class="flex items-center gap-1">
                                                                    <span class="material-symbols-outlined text-xs text-slate-400">schedule</span>
                                                                    <span>{{ \Carbon\Carbon::parse($ultVersion->Fecha_Carga)->format('d/m/Y H:i') }}</span>
                                                                </span>
                                                            @endif

                                                            @if($doc->requisito?->Prioridad === 'Obligatorio')
                                                                <span class="px-1.5 py-0.2 rounded bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 font-semibold border border-rose-200/60 dark:border-rose-800">
                                                                    Obligatorio
                                                                </span>
                                                            @endif
                                                        </div>

                                                        {{-- Botón para ver archivo original --}}
                                                        <div class="mt-2.5 pt-2 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between">
                                                            <span class="text-[10px] text-slate-400 font-mono">
                                                                Formato: {{ $doc->requisito?->Formato ?? 'PDF' }}
                                                            </span>
                                                            <a href="{{ route('portal.documentos.archivo', $doc->ID_Documento) }}"
                                                               target="_blank"
                                                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/60 dark:hover:bg-blue-900/60 text-[#0051d5] dark:text-blue-300 border border-blue-200/80 dark:border-blue-800 transition-colors"
                                                               title="Abrir archivo original en nueva pestaña"
                                                            >
                                                                <span class="material-symbols-outlined text-xs">visibility</span>
                                                                <span>Ver Archivo</span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="py-8 text-center text-slate-400 dark:text-slate-500">
                                            <span class="material-symbols-outlined text-3xl block mb-1 text-slate-300 dark:text-slate-600">folder_off</span>
                                            <span class="text-xs">Este expediente no tiene documentos registrados en el sistema.</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                <span class="material-symbols-outlined text-3xl mb-1 block text-slate-300 dark:text-slate-600">manage_search</span>
                                No se encontraron expedientes con los filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($solicitudes instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="p-3.5 sm:p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                {{ $solicitudes->links() }}
            </div>
        @endif
    </div>

</div>

{{-- Script de respaldo para alternar desplegable tanto en móvil como en escritorio --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-toggle-exp]').forEach(function (trigger) {
            trigger.addEventListener('click', function (e) {
                if (e.target.closest('a')) return;

                const expId = trigger.getAttribute('data-toggle-exp');
                const targetRow = document.getElementById('desplegable-' + expId);
                const targetMobile = document.getElementById('desplegable-mobile-' + expId);

                if (targetRow && (!window.Alpine || !targetRow._x_dataStack)) {
                    targetRow.classList.toggle('hidden');
                }
                if (targetMobile && (!window.Alpine || !targetMobile._x_dataStack)) {
                    targetMobile.classList.toggle('hidden');
                }
            });
        });
    });
</script>
@endsection
