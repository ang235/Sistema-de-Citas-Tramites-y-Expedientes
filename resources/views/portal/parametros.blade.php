@extends('layouts.portal')

@section('title', 'Gestion de tramites y requisitos')

@section('breadcrumb')
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span>Administración Central</span>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="current">Gestion de tramites y requisitos</span>
@endsection

@section('content')

<div x-data="{
    showNewTramiteModal: false,
    showEditTramiteModal: false,
    showNewVersionModal: false,
    showNewReqModal: false,
    expandedTramiteId: null,
    currentTramiteId: null,
    currentTramiteName: '',
    currentTramiteDesc: '',
    currentTramiteEstado: 'Activo',
    currentVersionId: null
}">

{{-- ═══════════════════════════════════ HEADER / TITULAR ══ --}}
<div class="mb-6 bg-white rounded-xl border border-slate-200/90 shadow-xs p-6 relative overflow-hidden">
    <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-blue-500/5 pointer-events-none blur-3xl"></div>
    <nav class="flex items-center gap-1.5 text-xs text-slate-500 mb-4">
        <a class="hover:text-blue-700 transition-colors" href="{{ route('portal.dashboard') }}">Inicio</a>
        <span class="material-symbols-outlined text-sm text-slate-400">chevron_right</span>
        <a class="hover:text-blue-700 transition-colors" href="#">Administración Central</a>
        <span class="material-symbols-outlined text-sm text-slate-400">chevron_right</span>
        <span class="text-blue-900 font-semibold">Gestion de tramites y requisitos</span>
    </nav>
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="max-w-3xl">
            <div class="flex items-start gap-3.5">
                <div class="w-11 h-11 rounded-lg bg-[#0f2744] text-white flex items-center justify-center shadow-sm shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-xl">account_tree</span>
                </div>
                <div>
                    <h1 class="text-2xl lg:text-[26px] font-bold text-slate-900 tracking-tight leading-snug" style="font-family: var(--font-display);">
                        Gestion de tramites y requisitos
                    </h1>
                    <p class="text-xs lg:text-sm text-slate-600 mt-1 leading-relaxed">
                        Administración del catálogo normativo, parametrización de recaudos admisibles, tiempos de SLA y gestión de requisitos por versión. Haz clic en cualquier trámite para visualizar sus requisitos.
                    </p>
                </div>
            </div>
        </div>
        <div class="flex items-center flex-wrap gap-2.5 self-start lg:self-center">
            <a href="{{ route('portal.parametros', ['tab' => 'versiones']) }}"
               class="px-4 py-2 rounded-lg {{ ($tab ?? 'activos') === 'versiones' ? 'bg-purple-700 text-white' : 'bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200' }} font-semibold text-xs flex items-center gap-1.5 shadow-xs transition-all cursor-pointer">
                <span class="material-symbols-outlined text-base">history_edu</span>
                <span>Historial de Versiones</span>
            </a>
            <button type="button"
                    @click="showNewTramiteModal = true"
                    class="px-4 py-2 rounded-lg bg-[#001229] hover:bg-[#0f2744] text-white font-semibold text-xs flex items-center gap-1.5 shadow-sm hover:shadow transition-all cursor-pointer">
                <span class="material-symbols-outlined text-base">add_circle</span>
                <span>+ Nuevo Trámite</span>
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════ KPIS RIBBON ══ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-xs flex items-center justify-between hover:border-slate-300 transition-all">
        <div class="flex flex-col">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Trámites Catalogados</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-bold text-slate-900">{{ $kpis['total_tramites'] ?? 47 }}</span>
                <span class="text-xs text-slate-400">/ 50 límite institucional</span>
            </div>
        </div>
        <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-lg">list_alt</span>
        </div>
    </div>

    <a href="{{ route('portal.parametros', ['tab' => 'versiones']) }}"
       class="bg-white p-4 rounded-xl border {{ ($tab ?? 'activos') === 'versiones' ? 'border-purple-400 ring-2 ring-purple-100' : 'border-slate-200/90' }} shadow-xs flex items-center justify-between hover:border-purple-400 hover:shadow-xs transition-all group cursor-pointer">
        <div class="flex flex-col">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider group-hover:text-purple-700 transition-colors">Historial de Versiones</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-bold text-emerald-700">{{ $kpis['versiones_activas'] ?? 12 }}</span>
                <span class="text-xs text-purple-600 font-semibold group-hover:underline">Ver todas ({{ $totalVersiones ?? 0 }}) →</span>
            </div>
        </div>
        <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-lg">history_edu</span>
        </div>
    </a>

    <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-xs flex items-center justify-between hover:border-slate-300 transition-all">
        <div class="flex flex-col">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Requisitos Registrados</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-bold text-slate-900">{{ $kpis['total_requisitos'] ?? 183 }}</span>
            </div>
        </div>
        <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-lg">checklist</span>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-xs flex items-center justify-between hover:border-slate-300 transition-all">
        <div class="flex flex-col">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Pendientes Revisión</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-bold text-amber-700">{{ $kpis['pendientes_revision'] ?? 5 }}</span>
                <span class="text-xs text-amber-600">por aprobar</span>
            </div>
        </div>
        <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-lg">pending_actions</span>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════ TABLA CATÁLOGO DE TRÁMITES ══ --}}
<div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
    {{-- Pestañas de estado: Activos vs Inactivos --}}
    <div class="px-6 pt-4 border-b border-slate-100 flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-1 border-b-2 border-transparent -mb-px">
            <a href="{{ route('portal.parametros', ['tab' => 'activos']) }}"
               class="pb-3 px-3 text-xs font-bold border-b-2 transition-colors flex items-center gap-1.5 {{ ($tab ?? 'activos') === 'activos' ? 'border-[#0051d5] text-[#0051d5]' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                <span class="material-symbols-outlined text-sm">task_alt</span>
                <span>Trámites Activos ({{ $totalActivos ?? 0 }})</span>
            </a>
            <a href="{{ route('portal.parametros', ['tab' => 'inactivos']) }}"
               class="pb-3 px-3 text-xs font-bold border-b-2 transition-colors flex items-center gap-1.5 {{ ($tab ?? 'activos') === 'inactivos' ? 'border-amber-600 text-amber-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                <span class="material-symbols-outlined text-sm">block</span>
                <span>Trámites Inactivos / Deshabilitados ({{ $totalInactivos ?? 0 }})</span>
            </a>
            <a href="{{ route('portal.parametros', ['tab' => 'versiones']) }}"
               class="pb-3 px-3 text-xs font-bold border-b-2 transition-colors flex items-center gap-1.5 {{ ($tab ?? 'activos') === 'versiones' ? 'border-purple-600 text-purple-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                <span class="material-symbols-outlined text-sm">history_edu</span>
                <span>Historial de Versiones ({{ $totalVersiones ?? 0 }})</span>
            </a>
        </div>

        <form method="GET" action="{{ route('portal.parametros') }}" class="flex items-center gap-2 pb-3">
            <input type="hidden" name="tab" value="{{ $tab ?? 'activos' }}">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none">search</span>
                <input name="buscar" value="{{ $buscar ?? '' }}"
                       class="pl-9 pr-4 py-1.5 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 w-56"
                       placeholder="Buscar por nombre o descripción..." type="text">
            </div>
            <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#001229] hover:bg-[#0f2744] text-white text-xs font-semibold transition-colors">
                Buscar
            </button>
            @if(!empty($buscar))
                <a href="{{ route('portal.parametros', ['tab' => $tab ?? 'activos']) }}" class="px-2 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        @if(($tab ?? 'activos') === 'versiones')
        {{-- ═══ TABLA HISTORIAL DE VERSIONES POR TRÁMITE ══ --}}
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50 text-slate-400 uppercase tracking-wider font-semibold text-[11px]">
                    <th class="py-3 px-6">Código</th>
                    <th class="py-3 px-4">Nombre del Trámite</th>
                    <th class="py-3 px-4">Versión Vigente</th>
                    <th class="py-3 px-4">Total Versiones</th>
                    <th class="py-3 px-4">Estado Catálogo</th>
                    <th class="py-3 px-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @forelse ($tramitesVersiones ?? [] as $tramiteVer)
                    @php
                        $verVigente = $tramiteVer->versionesTramite->where('Estado_Ver_Tr', 'Vigente')->first();
                        $noVerVigente = $verVigente ? (Str::startsWith($verVigente->No_Version, 'v') ? $verVigente->No_Version : 'v'.$verVigente->No_Version) : 'Sin versión vigente';
                        $cantVersiones = $tramiteVer->versionesTramite->count();
                        $esInactivo = ($tramiteVer->Estado_Tipo_Tr === 'Inactivo');
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors {{ $esInactivo ? 'bg-slate-50/70 opacity-80' : '' }}"
                        :class="expandedTramiteId === {{ $tramiteVer->ID_Tipo_Tramite }} ? 'bg-purple-50/30' : ''">
                        <td class="py-3.5 px-6 cursor-pointer"
                            @click="expandedTramiteId = (expandedTramiteId === {{ $tramiteVer->ID_Tipo_Tramite }} ? null : {{ $tramiteVer->ID_Tipo_Tramite }})">
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-purple-600 transition-transform duration-200"
                                      :class="expandedTramiteId === {{ $tramiteVer->ID_Tipo_Tramite }} ? 'rotate-90' : ''">chevron_right</span>
                                <span class="font-mono font-bold text-purple-700 text-[11px]">TRM-{{ str_pad($tramiteVer->ID_Tipo_Tramite, 3, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 cursor-pointer group"
                            @click="expandedTramiteId = (expandedTramiteId === {{ $tramiteVer->ID_Tipo_Tramite }} ? null : {{ $tramiteVer->ID_Tipo_Tramite }})">
                            <div class="flex items-center gap-1">
                                <span class="font-semibold text-[#0b1c30] group-hover:text-purple-700 transition-colors">{{ $tramiteVer->Nombre }}</span>
                                <span class="text-[10px] text-purple-600 font-medium px-1.5 py-0.2 rounded bg-purple-50 opacity-0 group-hover:opacity-100 transition-opacity">Ver versiones</span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">{{ $tramiteVer->Descripcion ?? '' }}</p>
                        </td>
                        <td class="py-3.5 px-4">
                            @if ($verVigente)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $noVerVigente }}
                                </span>
                            @else
                                <span class="text-slate-400 italic">Sin vigente</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 cursor-pointer"
                            @click="expandedTramiteId = (expandedTramiteId === {{ $tramiteVer->ID_Tipo_Tramite }} ? null : {{ $tramiteVer->ID_Tipo_Tramite }})">
                            <button type="button" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 hover:bg-purple-100 text-purple-700 transition-colors cursor-pointer">
                                <span class="material-symbols-outlined text-xs">history_edu</span>
                                <span>{{ $cantVersiones }} {{ Str::plural('versión', $cantVersiones) }}</span>
                            </button>
                        </td>
                        <td class="py-3.5 px-4">
                            @if ($esInactivo)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Inactivo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $tramiteVer->Estado_Tipo_Tr ?? 'Activo' }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                <button type="button"
                                        @click="expandedTramiteId = (expandedTramiteId === {{ $tramiteVer->ID_Tipo_Tramite }} ? null : {{ $tramiteVer->ID_Tipo_Tramite }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 text-[11px] font-semibold transition-colors cursor-pointer"
                                        title="Ver historial de versiones de este trámite">
                                    <span class="material-symbols-outlined text-xs">visibility</span>
                                    <span>Ver Historial</span>
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- DETALLE DESPLEGABLE: HISTORIAL DE VERSIONES (SOLO LECTURA) --}}
                    <tr x-show="expandedTramiteId === {{ $tramiteVer->ID_Tipo_Tramite }}" x-cloak class="bg-purple-50/40 border-b border-purple-100">
                        <td colspan="6" class="p-5">
                            <div class="bg-white rounded-xl border border-purple-200/80 p-5 shadow-xs space-y-4"
                                 x-data="{ expandedVerId: {{ $verVigente?->ID_Version_Tramite ?? 'null' }} }">

                                {{-- Cabecera --}}
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3 flex-wrap gap-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-lg">history_edu</span>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-[#0b1c30]">Historial de Versiones: {{ $tramiteVer->Nombre }}</h4>
                                            <p class="text-xs text-slate-500">
                                                {{ $cantVersiones }} versión(es) archivada(s)
                                                &bull; Versión vigente:
                                                <span class="font-semibold text-emerald-700">{{ $noVerVigente }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                            <span class="material-symbols-outlined text-xs">lock</span>
                                            <span>Historial no modificable (Solo lectura)</span>
                                        </span>
                                    </div>
                                </div>

                                {{-- Lista de versiones del trámite (Solo Lectura) --}}
                                @if($tramiteVer->versionesTramite->isEmpty())
                                    <div class="p-6 text-center rounded-xl bg-slate-50 border border-dashed border-slate-200">
                                        <span class="material-symbols-outlined text-3xl text-slate-300">history_edu</span>
                                        <p class="text-xs text-slate-500 mt-1">Este trámite no tiene versiones registradas.</p>
                                    </div>
                                @else
                                    <div class="space-y-2.5">
                                        @foreach($tramiteVer->versionesTramite as $ver)
                                            @php
                                                $noVer = Str::startsWith($ver->No_Version,'v') ? $ver->No_Version : 'v'.$ver->No_Version;
                                                $esVerVigente  = $ver->Estado_Ver_Tr === 'Vigente';
                                                $esVerBorrador = $ver->Estado_Ver_Tr === 'Borrador';
                                            @endphp
                                            <div class="rounded-xl border transition-all"
                                                 :class="expandedVerId === {{ $ver->ID_Version_Tramite }} ? 'border-purple-300 bg-purple-50/20 shadow-xs' : 'border-slate-200 bg-slate-50/50'">

                                                {{-- Fila de la versión --}}
                                                <div class="flex items-center justify-between gap-3 p-3.5 cursor-pointer"
                                                     @click="expandedVerId = (expandedVerId === {{ $ver->ID_Version_Tramite }} ? null : {{ $ver->ID_Version_Tramite }})">
                                                    <div class="flex items-center gap-2.5 min-w-0 flex-wrap">
                                                        <span class="material-symbols-outlined text-sm text-purple-600 transition-transform duration-200"
                                                              :class="expandedVerId === {{ $ver->ID_Version_Tramite }} ? 'rotate-90' : ''">chevron_right</span>
                                                        <span class="font-mono font-bold text-[13px] text-[#0b1c30]">{{ $noVer }}</span>

                                                        @if($esVerVigente)
                                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Vigente (Activa)
                                                            </span>
                                                        @elseif($esVerBorrador)
                                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>Borrador
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Histórica (Archivada)
                                                            </span>
                                                        @endif

                                                        <span class="text-xs text-slate-500">
                                                            &bull; {{ $ver->requisitos->count() }} requisito(s)
                                                            @if($ver->Fecha_Inicio_Vig)
                                                                &bull; Vigencia desde {{ $ver->Fecha_Inicio_Vig->format('d/m/Y') }}
                                                            @endif
                                                        </span>
                                                    </div>

                                                    <div class="flex items-center gap-2 shrink-0">
                                                        <span class="text-[11px] text-purple-700 font-medium flex items-center gap-1">
                                                            <span class="material-symbols-outlined text-xs">visibility</span>
                                                            <span x-text="expandedVerId === {{ $ver->ID_Version_Tramite }} ? 'Ocultar requisitos' : 'Ver requisitos'"></span>
                                                        </span>
                                                    </div>
                                                </div>

                                                {{-- Requisitos desplegables de esta versión (Solo Lectura) --}}
                                                <div x-show="expandedVerId === {{ $ver->ID_Version_Tramite }}" x-cloak class="px-4 pb-4 border-t border-slate-100 pt-3">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span class="text-xs font-semibold text-slate-700">Requisitos normativos registrados en {{ $noVer }}:</span>
                                                        <span class="text-[10px] text-slate-400 italic">Registro histórico inmutable</span>
                                                    </div>

                                                    @if($ver->requisitos->isNotEmpty())
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-1">
                                                            @foreach($ver->requisitos as $ri => $req)
                                                                <div class="flex items-start justify-between gap-2 p-2.5 rounded-lg bg-white border border-slate-200">
                                                                    <div class="flex items-start gap-2 min-w-0">
                                                                        <span class="w-5 h-5 rounded-full {{ $esVerVigente ? 'bg-purple-700' : 'bg-slate-400' }} text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">{{ $ri + 1 }}</span>
                                                                        <div class="min-w-0">
                                                                            <p class="text-[11px] font-bold text-slate-900 leading-snug truncate">{{ $req->Nombre }}</p>
                                                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                                                <span class="font-mono uppercase text-[9px] px-1 py-0.5 rounded bg-slate-100 border border-slate-200">{{ $req->Formato ?? 'PDF' }}</span>
                                                                                <span class="text-[10px] font-medium {{ $req->Prioridad === 'Obligatorio' ? 'text-amber-700' : 'text-slate-500' }}">{{ $req->Prioridad }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="p-4 text-center rounded-lg bg-slate-50 border border-dashed border-slate-200">
                                                            <p class="text-[11px] text-slate-500">Sin requisitos registrados en esta versión.</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl block mb-2 text-slate-300">history_edu</span>
                            No hay trámites registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @else
        {{-- ═══ TABLA DE TRÁMITES ACTIVOS / INACTIVOS ══ --}}
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50 text-slate-400 uppercase tracking-wider font-semibold text-[11px]">
                    <th class="py-3 px-6">Código de Trámite</th>
                    <th class="py-3 px-4">Nombre del Procedimiento</th>
                    <th class="py-3 px-4">Versión Activa</th>
                    <th class="py-3 px-4">Requisitos</th>
                    <th class="py-3 px-4">SLA Legal</th>
                    <th class="py-3 px-4">Estado Catálogo</th>
                    <th class="py-3 px-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @forelse ($tiposTramite ?? [] as $tipo)
                    @php
                        $version = $tipo->versionesTramite->where('Estado_Ver_Tr', 'Vigente')->first() ?? $tipo->versionesTramite->last();
                        $esInactivo = ($tipo->Estado_Tipo_Tr === 'Inactivo');
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors {{ $esInactivo ? 'bg-slate-50/70 opacity-80' : '' }}"
                        :class="expandedTramiteId === {{ $tipo->ID_Tipo_Tramite }} ? 'bg-blue-50/30' : ''">
                        <td class="py-3.5 px-6 cursor-pointer"
                            @click="expandedTramiteId = (expandedTramiteId === {{ $tipo->ID_Tipo_Tramite }} ? null : {{ $tipo->ID_Tipo_Tramite }})">
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-blue-600 transition-transform duration-200"
                                      :class="expandedTramiteId === {{ $tipo->ID_Tipo_Tramite }} ? 'rotate-90' : ''">chevron_right</span>
                                <span class="font-mono font-bold text-blue-700 text-[11px]">TRM-{{ str_pad($tipo->ID_Tipo_Tramite, 3, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 cursor-pointer group"
                            @click="expandedTramiteId = (expandedTramiteId === {{ $tipo->ID_Tipo_Tramite }} ? null : {{ $tipo->ID_Tipo_Tramite }})">
                            <div class="flex items-center gap-1">
                                <span class="font-semibold text-[#0b1c30] group-hover:text-blue-700 transition-colors">{{ $tipo->Nombre }}</span>
                                <span class="text-[10px] text-blue-600 font-medium px-1.5 py-0.2 rounded bg-blue-50 opacity-0 group-hover:opacity-100 transition-opacity">Ver requisitos</span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">{{ $tipo->Descripcion ?? '' }}</p>
                        </td>
                        <td class="py-3.5 px-4">
                            @if ($version)
                                <span class="font-mono text-slate-600">{{ Str::startsWith($version->No_Version, 'v') ? $version->No_Version : 'v'.$version->No_Version }}</span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 cursor-pointer"
                            @click="expandedTramiteId = (expandedTramiteId === {{ $tipo->ID_Tipo_Tramite }} ? null : {{ $tipo->ID_Tipo_Tramite }})">
                            <button type="button" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 hover:bg-blue-100 text-blue-700 transition-colors cursor-pointer">
                                <span class="material-symbols-outlined text-xs">checklist</span>
                                <span>{{ $version?->requisitos->count() ?? '0' }} reqs</span>
                            </button>
                        </td>
                        <td class="py-3.5 px-4 text-slate-600">{{ $tipo->SLA_dias ?? '15' }}d</td>
                        <td class="py-3.5 px-4">
                            @if ($esInactivo)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Inactivo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $tipo->Estado_Tipo_Tr ?? 'Activo' }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                <button type="button"
                                        @click="expandedTramiteId = (expandedTramiteId === {{ $tipo->ID_Tipo_Tramite }} ? null : {{ $tipo->ID_Tipo_Tramite }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-[11px] font-semibold transition-colors cursor-pointer"
                                        title="Ver requisitos de este trámite">
                                    <span class="material-symbols-outlined text-xs">visibility</span>
                                    <span>Requisitos</span>
                                </button>
                                <button type="button"
                                        @click="showEditTramiteModal = true; currentTramiteId = {{ $tipo->ID_Tipo_Tramite }}; currentTramiteName = '{{ addslashes($tipo->Nombre) }}'; currentTramiteDesc = '{{ addslashes($tipo->Descripcion ?? '') }}'; currentTramiteEstado = '{{ $tipo->Estado_Tipo_Tr ?? 'Activo' }}';"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-[11px] font-semibold transition-colors cursor-pointer"
                                        title="Editar procedimiento">
                                    <span class="material-symbols-outlined text-xs">edit</span>
                                    <span>Editar</span>
                                </button>
                                <button type="button"
                                        @click="showNewVersionModal = true; currentTramiteId = {{ $tipo->ID_Tipo_Tramite }}; currentTramiteName = '{{ addslashes($tipo->Nombre) }}';"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold transition-colors cursor-pointer"
                                        title="Nueva versión normativa">
                                    <span class="material-symbols-outlined text-xs">history_edu</span>
                                    <span>+ Versión</span>
                                </button>
                                @if($version)
                                    <button type="button"
                                            @click="showNewReqModal = true; currentVersionId = {{ $version->ID_Version_Tramite }}; currentTramiteName = '{{ addslashes($tipo->Nombre) }} (v{{ $version->No_Version }})';"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[11px] font-semibold transition-colors cursor-pointer"
                                            title="Agregar requisito a versión activa">
                                        <span class="material-symbols-outlined text-xs">add_task</span>
                                        <span>+ Requisito</span>
                                    </button>
                                @endif
                                <form method="POST" action="{{ route('portal.tipo-tramites.destroy', $tipo->ID_Tipo_Tramite) }}" onsubmit="return confirm('¿Está seguro de {{ $esInactivo ? 'reactivar' : 'deshabilitar' }} este trámite del catálogo? No se eliminarán sus registros históricos.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            title="{{ $esInactivo ? 'Reactivar trámite' : 'Deshabilitar trámite' }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold transition-colors cursor-pointer {{ $esInactivo ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-100 hover:bg-amber-50 hover:text-amber-800 text-slate-600' }}">
                                        <span class="material-symbols-outlined text-xs">{{ $esInactivo ? 'check_circle' : 'block' }}</span>
                                        <span>{{ $esInactivo ? 'Reactivar' : 'Deshabilitar' }}</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- DETALLE DESPLEGABLE: REQUISITOS DE LA VERSIÓN VIGENTE --}}
                    <tr x-show="expandedTramiteId === {{ $tipo->ID_Tipo_Tramite }}" x-cloak class="bg-blue-50/40 border-b border-blue-100">
                        <td colspan="7" class="p-5">
                            <div class="bg-white rounded-xl border border-blue-200/80 p-5 shadow-xs space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-[#0051d5] flex items-center justify-center font-bold text-xs shrink-0">
                                            <span class="material-symbols-outlined text-lg">checklist</span>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-[#0b1c30]">
                                                Requisitos Normativos de: {{ $tipo->Nombre }}
                                            </h4>
                                            <p class="text-xs text-slate-500">
                                                Versión vigente: <span class="font-semibold text-slate-700">{{ $version ? (Str::startsWith($version->No_Version, 'v') ? $version->No_Version : 'v'.$version->No_Version) : 'Sin versión activa' }}</span>
                                                &bull; Total de requisitos: {{ $version?->requisitos->count() ?? 0 }}
                                            </p>
                                        </div>
                                    </div>

                                    @if($version)
                                        <button type="button"
                                                @click="showNewReqModal = true; currentVersionId = {{ $version->ID_Version_Tramite }}; currentTramiteName = '{{ addslashes($tipo->Nombre) }} ({{ Str::startsWith($version->No_Version, 'v') ? $version->No_Version : 'v'.$version->No_Version }})';"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition-colors cursor-pointer shrink-0">
                                            <span class="material-symbols-outlined text-sm">add_task</span>
                                            <span>+ Agregar Requisito</span>
                                        </button>
                                    @endif
                                </div>

                                {{-- Listado de requisitos de la versión vigente --}}
                                @if($version && $version->requisitos->isNotEmpty())
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($version->requisitos as $reqIndex => $req)
                                            <div class="flex items-start justify-between gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200 hover:border-slate-300 transition-all">
                                                <div class="flex items-start gap-2.5 min-w-0">
                                                    <span class="w-6 h-6 rounded-full bg-[#001229] text-white flex items-center justify-center text-[11px] font-bold shrink-0 mt-0.5">
                                                        {{ $reqIndex + 1 }}
                                                    </span>
                                                    <div class="min-w-0">
                                                        <h5 class="text-xs font-bold text-slate-900 leading-snug truncate">{{ $req->Nombre }}</h5>
                                                        <div class="flex items-center gap-2 text-[11px] text-slate-500 mt-1">
                                                            <span class="font-mono uppercase px-1.5 py-0.5 rounded bg-white border border-slate-200 text-[10px]">
                                                                {{ $req->Formato ?? 'PDF' }}
                                                            </span>
                                                            <span>&bull;</span>
                                                            <span class="font-medium {{ $req->Prioridad === 'Obligatorio' ? 'text-amber-700' : 'text-slate-600' }}">
                                                                {{ $req->Prioridad }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <form method="POST" action="{{ route('portal.requisitos.destroy', $req->ID_Requisitos) }}" onsubmit="return confirm('¿Desea eliminar este requisito normativo?');" class="shrink-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded transition-colors" title="Eliminar requisito">
                                                        <span class="material-symbols-outlined text-base">delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-6 text-center rounded-xl bg-slate-50 border border-dashed border-slate-200 space-y-2">
                                        <span class="material-symbols-outlined text-3xl text-slate-400">rule_folder</span>
                                        <p class="text-xs font-semibold text-slate-700">Este trámite no tiene requisitos registrados en su versión activa.</p>
                                        @if($version)
                                            <button type="button"
                                                    @click="showNewReqModal = true; currentVersionId = {{ $version->ID_Version_Tramite }}; currentTramiteName = '{{ addslashes($tipo->Nombre) }} ({{ Str::startsWith($version->No_Version, 'v') ? $version->No_Version : 'v'.$version->No_Version }})';"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-[#001229] text-white text-xs font-semibold hover:bg-[#0f2744] transition-colors cursor-pointer mt-1">
                                                <span class="material-symbols-outlined text-sm">add</span>
                                                <span>Crear el primer requisito</span>
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl block mb-2 text-slate-300">folder_off</span>
                            No hay trámites registrados en esta categoría ({{ ($tab ?? 'activos') === 'inactivos' ? 'Inactivos' : 'Activos' }}).
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @endif
    </div>

    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
        @if(($tab ?? 'activos') === 'versiones')
            @php $col = $tramitesVersiones ?? null; @endphp
            <span class="text-xs text-slate-500">{{ $col ? (method_exists($col, 'total') ? $col->total() : $col->count()) : 0 }} trámites con historial de versiones</span>
            @if(isset($col) && $col instanceof \Illuminate\Pagination\LengthAwarePaginator)
                {{ $col->links() }}
            @endif
        @else
            @php $tramitesCol = $tiposTramite ?? $tramites ?? null; @endphp
            <span class="text-xs text-slate-500">{{ $tramitesCol ? (method_exists($tramitesCol, 'total') ? $tramitesCol->total() : $tramitesCol->count()) : 0 }} procedimientos administrativos</span>
            @if(isset($tramitesCol) && $tramitesCol instanceof \Illuminate\Pagination\LengthAwarePaginator)
                {{ $tramitesCol->links() }}
            @endif
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════ MODAL NUEVO TRÁMITE ══ --}}
<div x-show="showNewTramiteModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
     style="display: none;">
    <div @click.outside="showNewTramiteModal = false"
         class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0051d5]">post_add</span>
                <h3 class="font-bold text-base text-[#0b1c30]">Nuevo Procedimiento Institucional</h3>
            </div>
            <button @click="showNewTramiteModal = false" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="{{ route('portal.tipo-tramites.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="nombre_tramite" class="block text-xs font-semibold text-[#0b1c30] mb-1">Denominación del Trámite</label>
                <input type="text" name="nombre" id="nombre_tramite" required placeholder="Ej. Certificado de Compatibilidad de Uso"
                       class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
            </div>

            <div>
                <label for="descripcion_tramite" class="block text-xs font-semibold text-[#0b1c30] mb-1">Descripción y Marco Normativo</label>
                <textarea name="descripcion" id="descripcion_tramite" rows="3" placeholder="Indique el alcance y base legal del trámite..."
                          class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]"></textarea>
            </div>

            <div>
                <label for="estado_tipo_tr" class="block text-xs font-semibold text-[#0b1c30] mb-1">Estado Catálogo</label>
                <select name="estado_tipo_tr" id="estado_tipo_tr" required
                        class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                    <option value="Activo">Activo (Habilitado para inicio)</option>
                    <option value="Inactivo">Inactivo / En revisión</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" @click="showNewTramiteModal = false"
                        class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-[#001229] hover:bg-[#0f2744] text-white text-xs font-semibold transition-colors">
                    Registrar Trámite
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════ MODAL EDITAR TRÁMITE ══ --}}
<div x-show="showEditTramiteModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
     style="display: none;">
    <div @click.outside="showEditTramiteModal = false"
         class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0051d5]">edit_document</span>
                <h3 class="font-bold text-base text-[#0b1c30]">Modificar Trámite</h3>
            </div>
            <button @click="showEditTramiteModal = false" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form :action="'{{ url('portal/tipo-tramites') }}/' + currentTramiteId" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="edit_nombre_tramite" class="block text-xs font-semibold text-[#0b1c30] mb-1">Nombre</label>
                <input type="text" name="nombre" id="edit_nombre_tramite" required x-model="currentTramiteName"
                       class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
            </div>

            <div>
                <label for="edit_desc_tramite" class="block text-xs font-semibold text-[#0b1c30] mb-1">Descripción</label>
                <textarea name="descripcion" id="edit_desc_tramite" rows="3" x-model="currentTramiteDesc"
                          class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]"></textarea>
            </div>

            <div>
                <label for="edit_estado_tipo_tr" class="block text-xs font-semibold text-[#0b1c30] mb-1">Estado Catálogo</label>
                <select name="estado_tipo_tr" id="edit_estado_tipo_tr" required x-model="currentTramiteEstado"
                        class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo / En revisión</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" @click="showEditTramiteModal = false"
                        class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-[#0051d5] hover:bg-[#003ea8] text-white text-xs font-semibold transition-colors">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════ MODAL NUEVA VERSIÓN ══ --}}
<div x-show="showNewVersionModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
     style="display: none;">
    <div @click.outside="showNewVersionModal = false"
         class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0051d5]">history_edu</span>
                <h3 class="font-bold text-base text-[#0b1c30]">Crear Nueva Versión Normativa</h3>
            </div>
            <button @click="showNewVersionModal = false" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <p class="text-xs text-slate-500">
            Trámite: <strong class="text-[#0b1c30]" x-text="currentTramiteName"></strong>
        </p>

        <form action="{{ route('portal.version-tramites.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="id_tipo_tramite" :value="currentTramiteId">

            <div>
                <label for="no_version" class="block text-xs font-semibold text-[#0b1c30] mb-1">Número de Versión</label>
                <input type="text" name="no_version" id="no_version" required placeholder="Ej. 2.0 o 2026-B"
                       class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
            </div>

            <div>
                <label for="estado_ver_tr" class="block text-xs font-semibold text-[#0b1c30] mb-1">Estado de la Versión</label>
                <select name="estado_ver_tr" id="estado_ver_tr" required
                        class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                    <option value="Vigente">Vigente (Reemplaza la versión activa previa)</option>
                    <option value="Borrador">Borrador / Preparación</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" @click="showNewVersionModal = false"
                        class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-[#001229] hover:bg-[#0f2744] text-white text-xs font-semibold transition-colors">
                    Guardar Versión
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════ MODAL NUEVO REQUISITO ══ --}}
<div x-show="showNewReqModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
     style="display: none;">
    <div @click.outside="showNewReqModal = false"
         class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0051d5]">add_task</span>
                <h3 class="font-bold text-base text-[#0b1c30]">Agregar Requisito Documental</h3>
            </div>
            <button @click="showNewReqModal = false" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <p class="text-xs text-slate-500">
            Versión destino: <strong class="text-[#0b1c30]" x-text="currentTramiteName"></strong>
        </p>

        <form action="{{ route('portal.requisitos.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="id_version_tramite" :value="currentVersionId">

            <div>
                <label for="nombre_req" class="block text-xs font-semibold text-[#0b1c30] mb-1">Nombre del Requisito</label>
                <input type="text" name="nombre" id="nombre_req" required placeholder="Ej. Memoria Descriptiva de Arquitectura"
                       class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
            </div>

            <div>
                <label for="prioridad_req" class="block text-xs font-semibold text-[#0b1c30] mb-1">Prioridad de Exigibilidad</label>
                <select name="prioridad" id="prioridad_req" required
                        class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                    <option value="Obligatorio">Obligatorio (Bloquea envío sin archivo)</option>
                    <option value="Opcional">Opcional / Facultativo</option>
                </select>
            </div>

            <div>
                <label for="formato_req" class="block text-xs font-semibold text-[#0b1c30] mb-1">Formato Admisible</label>
                <select name="formato" id="formato_req" required
                        class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                    <option value="PDF">Documento PDF (.pdf)</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" @click="showNewReqModal = false"
                        class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-[#0051d5] hover:bg-[#003ea8] text-white text-xs font-semibold transition-colors">
                    Agregar Requisito
                </button>
            </div>
        </form>
    </div>
</div>

</div> {{-- Cierre de x-data --}}
@endsection
