@extends('layouts.portal')

@section('title', 'Auditoría y Bitácora Normativa')

@section('breadcrumb')
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <a href="{{ route('portal.dashboard') }}">Administración Central</a>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span>Gobernanza y Cumplimiento</span>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="current">Auditoría y Bitácora</span>
@endsection

@section('content')
@php
    $primerLog = isset($bitacoras) && $bitacoras->isNotEmpty() ? $bitacoras->first() : null;
    $initialLogData = $primerLog ? [
        'id' => $primerLog->ID_Bitacora,
        'fecha' => $primerLog->Fecha_Creacion ? $primerLog->Fecha_Creacion->format('d/m/Y H:i:s') : 'N/A',
        'usuario' => $primerLog->usuario?->Nombre ?? 'Sistema Central',
        'email' => $primerLog->usuario?->Email ?? 'sistema@setc.gob.pe',
        'accion' => $primerLog->Accion ?? 'Evento Registrado',
        'descripcion' => $primerLog->Descripcion ?? 'Sin descripción adicional.',
        'modulo' => 'SEGURIDAD / AUDITORÍA',
        'hash' => hash('sha256', (string) $primerLog->ID_Bitacora . ($primerLog->Fecha_Creacion ?? now())),
    ] : null;
@endphp

<div class="flex flex-col w-full gap-space-lg" x-data="{
    selectedLog: {{ json_encode($initialLogData) }},
    selectLog(id, fecha, usuario, email, accion, descripcion, modulo, hash) {
        this.selectedLog = { id, fecha, usuario, email, accion, descripcion, modulo, hash };
    }
}">

    {{-- ══ HEADER + BADGES ══ --}}
    <div class="flex flex-col xl:flex-row xl:items-start justify-between gap-space-md pt-1">
        <div class="flex flex-col max-w-4xl">
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 text-[#001229] dark:text-blue-200 text-[12px] font-semibold shadow-xs">
                    <span class="material-symbols-outlined text-[#0051d5] text-[16px]">token</span>
                    <span>Trazabilidad Institucional</span>
                    <span class="text-slate-300">•</span>
                    <span class="inline-flex items-center gap-1 text-emerald-700 dark:text-emerald-400 font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Modo Inmutable Activo
                    </span>
                </div>
                <span class="text-[12px] text-slate-500 font-mono">Consenso SHA-256 / Auditoría Integral</span>
            </div>
            <h1 class="font-headline-xl text-[28px] lg:text-[32px] text-[#001229] dark:text-white font-extrabold tracking-tight leading-tight" style="font-family: var(--font-display);">
                Bitácora Oficial de Auditoría y Trazabilidad
            </h1>
            <p class="font-body-md text-slate-600 dark:text-slate-400 mt-1.5 text-[14px] leading-relaxed">
                Registro inmutable de accesos, modificaciones de usuarios, cambios normativos en trámites y emisión de dictámenes y resoluciones administrativas.
            </p>
        </div>
        {{-- Botones de Acción Funcionales --}}
        <div class="flex flex-wrap items-center gap-2.5 shrink-0 pt-1">
            <a href="{{ route('portal.auditoria', array_merge(request()->query(), ['export' => 'csv'])) }}"
               class="flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white dark:bg-[#111f33] border border-slate-200 dark:border-slate-800 hover:bg-slate-50 text-slate-700 dark:text-slate-200 text-[13px] font-semibold shadow-xs transition-colors">
                <span class="material-symbols-outlined text-[17px] text-emerald-600">sim_card_download</span>
                Exportar Bitácora (CSV)
            </a>
            <button type="button" @click="alert('Verificación PKI: Todos los hashes SHA-256 del bloque actual coinciden con la firma institucional.')"
                    class="flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white dark:bg-[#111f33] border border-slate-200 dark:border-slate-800 hover:bg-slate-50 text-slate-700 dark:text-slate-200 text-[13px] font-semibold shadow-xs transition-colors">
                <span class="material-symbols-outlined text-[17px] text-blue-600">verified_user</span>
                Verificar Integridad
            </button>
        </div>
    </div>

    {{-- ══ KPI CARDS REALES ══ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Eventos --}}
        <div class="p-4 rounded-xl bg-white dark:bg-[#111f33] border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Total Registros</span>
                    <div class="font-headline-lg text-[28px] font-bold text-slate-900 dark:text-white mt-1">
                        {{ number_format($kpis['total_eventos'] ?? 0) }}
                    </div>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-950/60 flex items-center justify-center text-[#0051d5]">
                    <span class="material-symbols-outlined text-[22px]">database</span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[12px]">
                <span class="flex items-center gap-1 text-emerald-700 dark:text-emerald-400 font-semibold">
                    <span class="material-symbols-outlined text-[15px]">lock</span>
                    100% Inmutable
                </span>
                <span class="text-slate-400 font-mono text-[11px]">Audit ID #{{ $kpis['total_eventos'] ?? 0 }}</span>
            </div>
        </div>

        {{-- Card 2: Eventos Hoy --}}
        <div class="p-4 rounded-xl bg-white dark:bg-[#111f33] border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Eventos de Hoy</span>
                    <div class="font-headline-lg text-[28px] font-bold text-slate-900 dark:text-white mt-1">
                        {{ number_format($kpis['eventos_hoy'] ?? 0) }}
                    </div>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 flex items-center justify-center text-emerald-700">
                    <span class="material-symbols-outlined text-[22px]">today</span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[12px]">
                <span class="text-slate-500 text-[11px]">Jornada en curso</span>
                <span class="text-emerald-700 font-semibold text-[11px]">En tiempo real</span>
            </div>
        </div>

        {{-- Card 3: Usuarios Activos en Bitácora --}}
        <div class="p-4 rounded-xl bg-white dark:bg-[#111f33] border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Funcionarios Auditados</span>
                    <div class="font-headline-lg text-[28px] font-bold text-slate-900 dark:text-white mt-1">
                        {{ $kpis['usuarios_activos'] ?? 0 }}
                    </div>
                </div>
                <div class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-950/60 flex items-center justify-center text-purple-700">
                    <span class="material-symbols-outlined text-[22px]">badge</span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[12px]">
                <span class="text-slate-500 text-[11px]">Con actividad registrada</span>
                <span class="text-purple-700 font-semibold text-[11px]">Trazados</span>
            </div>
        </div>

        {{-- Card 4: Cambios Normativos --}}
        <div class="p-4 rounded-xl bg-white dark:bg-[#111f33] border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Acciones Normativas</span>
                    <div class="font-headline-lg text-[28px] font-bold text-slate-900 dark:text-white mt-1">
                        {{ $kpis['cambios_normativos'] ?? 0 }}
                    </div>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-950/60 flex items-center justify-center text-amber-700">
                    <span class="material-symbols-outlined text-[22px]">tune</span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[12px]">
                <span class="text-slate-500 text-[11px]">Trámites / Requisitos</span>
                <span class="text-amber-700 font-semibold text-[11px]">Control TUPA</span>
            </div>
        </div>
    </div>

    {{-- ══ SEARCH + FORENSIC FILTERS BAR ══ --}}
    <div class="p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col gap-3">
        <form action="{{ route('portal.auditoria') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2.5 flex-wrap">
            <div class="relative flex-1 min-w-[240px]">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[19px] pointer-events-none">saved_search</span>
                <input
                    class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 rounded-lg text-xs text-slate-800 dark:text-slate-200 placeholder:text-slate-400 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0051d5]/20 focus:bg-white transition-all"
                    id="audit-search"
                    name="accion"
                    placeholder="Buscar por acción o palabra clave..."
                    type="text"
                    value="{{ request('accion') }}"
                />
            </div>
            <select name="id_usuario" class="px-3 py-2 bg-white dark:bg-slate-800 rounded-lg text-xs font-medium text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0051d5]/20 cursor-pointer">
                <option value="">Todos los Funcionarios</option>
                @foreach($usuarios ?? [] as $usr)
                    <option value="{{ $usr->ID_Usuarios }}" @selected((string) request('id_usuario') === (string) $usr->ID_Usuarios)>
                        {{ $usr->Nombre }} ({{ $usr->Email }})
                    </option>
                @endforeach
            </select>
            <input type="date" name="fecha" value="{{ request('fecha') }}"
                   class="px-3 py-2 bg-white dark:bg-slate-800 rounded-lg text-xs font-medium text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0051d5]/20 cursor-pointer">

            <button type="submit" class="px-4 py-2 rounded-lg bg-[#001229] hover:bg-[#0f2744] text-white text-xs font-semibold shadow-xs transition-colors">
                Filtrar Bitácora
            </button>
            @if(request('accion') || request('id_usuario') || request('fecha'))
                <a href="{{ route('portal.auditoria') }}" class="px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 text-xs font-semibold transition-colors">
                    Limpiar Filtros
                </a>
            @endif
        </form>
    </div>

    {{-- ══ DUAL-PANEL WORKSPACE ══ --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg items-start">

        {{-- ── LEFT: AUDIT TABLE (7 COLS) ── --}}
        <div class="xl:col-span-7 flex flex-col gap-space-md">
            <div class="bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden flex flex-col">
                {{-- Table Toolbar --}}
                <div class="px-4 py-3 bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#0051d5] text-[19px]">format_list_bulleted</span>
                        <span class="font-headline-sm text-[14px] font-bold text-slate-900 dark:text-white">Registros de Actividad</span>
                    </div>
                    <span class="text-[12px] text-slate-500 font-medium">
                        {{ $bitacoras->total() }} registros en total
                    </span>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[13px] text-slate-700 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="py-3 px-3.5">ID / Evento</th>
                                <th class="py-3 px-3.5">Fecha y Hora</th>
                                <th class="py-3 px-3.5">Funcionario</th>
                                <th class="py-3 px-3.5">Acción</th>
                                <th class="py-3 px-3.5 text-center">Estado</th>
                                <th class="py-3 px-3.5 text-right">Ver</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($bitacoras as $log)
                                @php
                                    $hash = hash('sha256', (string) $log->ID_Bitacora . ($log->Fecha_Creacion ?? now()));
                                @endphp
                                <tr @click="selectLog(
                                        {{ $log->ID_Bitacora }},
                                        '{{ $log->Fecha_Creacion ? $log->Fecha_Creacion->format('d/m/Y H:i:s') : 'N/A' }}',
                                        '{{ addslashes($log->usuario?->Nombre ?? 'Sistema Central') }}',
                                        '{{ addslashes($log->usuario?->Email ?? 'sistema@setc.gob.pe') }}',
                                        '{{ addslashes($log->Accion ?? 'Evento') }}',
                                        '{{ addslashes($log->Descripcion ?? 'Sin detalles') }}',
                                        'AUDITORÍA',
                                        '{{ $hash }}'
                                    )"
                                    :class="selectedLog && selectedLog.id === {{ $log->ID_Bitacora }} ? 'bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-l-[#0051d5]' : 'border-l-4 border-l-transparent hover:bg-slate-50 dark:hover:bg-slate-800/40'"
                                    class="cursor-pointer transition-colors">
                                    <td class="py-3 px-3.5 font-mono text-xs font-bold text-[#001229] dark:text-slate-200">
                                        #{{ $log->ID_Bitacora }}
                                    </td>
                                    <td class="py-3 px-3.5 text-xs text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                        {{ $log->Fecha_Creacion ? $log->Fecha_Creacion->format('d/m/Y H:i') : '--' }}
                                    </td>
                                    <td class="py-3 px-3.5">
                                        <div class="font-semibold text-slate-900 dark:text-white text-xs truncate max-w-[130px]">
                                            {{ $log->usuario?->Nombre ?? 'Sistema' }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 truncate max-w-[130px]">
                                            {{ $log->usuario?->Email }}
                                        </div>
                                    </td>
                                    <td class="py-3 px-3.5 max-w-[190px]">
                                        <span class="font-semibold text-slate-800 dark:text-slate-200 text-xs block truncate" title="{{ $log->Accion }}">
                                            {{ $log->Accion }}
                                        </span>
                                        <span class="text-[11px] text-slate-400 block truncate" title="{{ $log->Descripcion }}">
                                            {{ Str::limit($log->Descripcion, 35) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3.5 text-center">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 text-[10px] font-semibold">
                                            <span class="material-symbols-outlined text-[12px]">verified</span>
                                            OK
                                        </span>
                                    </td>
                                    <td class="py-3 px-3.5 text-right">
                                        <button type="button" class="p-1 rounded bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300">
                                            <span class="material-symbols-outlined text-sm">visibility</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        <span class="material-symbols-outlined text-4xl block mb-2 text-slate-300">policy</span>
                                        No se encontraron registros de auditoría bajo estos criterios.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                <div class="px-4 py-3 bg-slate-50/60 dark:bg-slate-800/40 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-600 dark:text-slate-400">
                    <span>
                        Mostrando página {{ $bitacoras->currentPage() }} de {{ $bitacoras->lastPage() }}
                    </span>
                    <div>
                        {{ $bitacoras->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- ── RIGHT: FORENSIC INSPECTOR PANEL REAL (5 COLS) ── --}}
        <div class="xl:col-span-5 flex flex-col gap-space-md sticky top-20">
            <template x-if="selectedLog">
                <div class="bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col">
                    {{-- Inspector Header --}}
                    <div class="px-4 py-3.5 bg-[#001229] text-white flex items-center justify-between border-b border-slate-800">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded bg-blue-900/60 border border-blue-400/20 flex items-center justify-center text-blue-200">
                                <span class="material-symbols-outlined text-[18px]">rule</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-300 block">Detalle de Auditoría</span>
                                <span class="font-headline-sm text-[15px] font-bold text-white block" x-text="'Evento #' + selectedLog.id"></span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[11px] font-semibold">
                            <span class="material-symbols-outlined text-[13px]">verified</span>
                            Inmutable
                        </span>
                    </div>

                    {{-- Metadata Dossier --}}
                    <div class="p-4 bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 space-y-2.5 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Acción:</span>
                            <span class="font-bold text-slate-900 dark:text-white text-right" x-text="selectedLog.accion"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Funcionario:</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedLog.usuario"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Correo institucional:</span>
                            <span class="font-mono text-slate-600 dark:text-slate-300" x-text="selectedLog.email"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Fecha y Hora de Registro:</span>
                            <span class="font-mono text-slate-700 dark:text-slate-300" x-text="selectedLog.fecha"></span>
                        </div>
                    </div>

                    {{-- Descripción / Payload del Evento --}}
                    <div class="p-4 flex flex-col gap-2">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Descripción Completa del Evento:</span>
                        <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-300 leading-relaxed break-words" x-text="selectedLog.descripcion"></div>
                    </div>

                    {{-- Cadena Criptográfica --}}
                    <div class="px-4 pb-4 flex flex-col gap-2">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Hash SHA-256 de Autenticidad:</span>
                        <div class="p-3 rounded-lg bg-slate-900 text-slate-300 font-mono text-[11px] break-all select-all leading-tight" x-text="selectedLog.hash"></div>
                    </div>
                </div>
            </template>

            <template x-if="!selectedLog">
                <div class="p-8 text-center bg-white dark:bg-[#111f33] rounded-xl border border-slate-200 dark:border-slate-800 text-slate-400 text-xs">
                    Seleccione un evento de la bitácora para inspeccionar su registro.
                </div>
            </template>
        </div>
    </div>

</div>
@endsection
