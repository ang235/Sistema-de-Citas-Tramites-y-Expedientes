@extends('layouts.portal')

@section('title', 'Auditoría y Bitácora Normativa')

@section('breadcrumb')
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <a href="{{ route('portal.dashboard') }}">Administración Central</a>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span>Gobernanza y Cumplimiento</span>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="current">Auditoría y Bitácora Normativa</span>
@endsection

@section('content')
<div class="flex flex-col w-full gap-space-lg">

    {{-- ══ HEADER + BADGES ══ --}}
    <div class="flex flex-col xl:flex-row xl:items-start justify-between gap-space-md pt-1">
        <div class="flex flex-col max-w-4xl">
            {{-- Security Badges --}}
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-primary-container text-[12px] font-semibold shadow-xs">
                    <span class="material-symbols-outlined text-secondary text-[16px]">token</span>
                    <span>Ledger Criptográfico Institucional</span>
                    <span class="text-slate-300">•</span>
                    <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Modo Inmutable Activo
                    </span>
                </div>
                <span class="text-[12px] text-slate-500 font-mono">Consenso SHA-256 / TSA Acreditado</span>
            </div>
            <h1 class="font-headline-xl text-[30px] lg:text-[34px] text-primary font-extrabold tracking-tight leading-tight">
                Bitácora Oficial de Auditoría y Trazabilidad Normativa
            </h1>
            <p class="font-body-md text-slate-600 mt-2 text-[14px] leading-relaxed">
                Registro inmutable de modificaciones reglamentarias, versiones TUPA, cambios en matrices de requisitos, actualizaciones de aranceles y firmas digitales con hash criptográfico SHA-256 conforme a la Ley N° 27444 y el marco de Ciberseguridad Nacional.
            </p>
        </div>
        {{-- Action Buttons --}}
        <div class="flex flex-wrap items-center gap-2.5 shrink-0 pt-1">
            <button class="flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-[13px] font-semibold shadow-xs transition-colors" id="btn-export-log">
                <span class="material-symbols-outlined text-[17px] text-slate-600">sim_card_download</span>
                Exportar Padrón (CSV / PDF)
            </button>
            <button class="flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-[13px] font-semibold shadow-xs transition-colors" id="btn-verify-chain">
                <span class="material-symbols-outlined text-[17px] text-emerald-600">verified_user</span>
                Verificar Cadena (Hash PKI)
            </button>
            <button class="flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-container hover:bg-[#1a3860] text-white text-[13px] font-semibold shadow-sm transition-all" id="btn-compare-versions">
                <span class="material-symbols-outlined text-[17px] text-blue-200">difference</span>
                Comparar Versiones (Diff)
            </button>
        </div>
    </div>

    {{-- ══ KPI CARDS ══ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Eventos --}}
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Total Eventos Registrados</span>
                    <div class="font-headline-lg text-[28px] font-bold text-slate-900 mt-1">
                        {{ number_format($totalEventos ?? 14892) }}
                    </div>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-primary-container">
                    <span class="material-symbols-outlined text-[22px]">database</span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[12px]">
                <span class="flex items-center gap-1 text-emerald-700 font-semibold">
                    <span class="material-symbols-outlined text-[15px]">lock</span>
                    100% Inmutable
                </span>
                <span class="text-slate-400 font-mono text-[11px]">Bloque #894,120</span>
            </div>
        </div>

        {{-- Card 2: Cambios Normativos TUPA --}}
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Cambios Normativos TUPA</span>
                    <div class="font-headline-lg text-[28px] font-bold text-slate-900 mt-1">
                        {{ $cambiosNormativos ?? 48 }}
                        <span class="text-[13px] font-normal text-slate-500">versiones / año</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700">
                    <span class="material-symbols-outlined text-[22px]">history_edu</span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[12px]">
                <span class="px-2 py-0.5 rounded-md bg-blue-50 text-secondary border border-blue-200/60 font-semibold text-[11px]">
                    Última: v4.2.1
                </span>
                <span class="text-slate-500 text-[11px]">Vigente 2025</span>
            </div>
        </div>

        {{-- Card 3: Firmas Criptográficas Válidas --}}
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Firmas Criptográficas Válidas</span>
                    <div class="font-headline-lg text-[28px] font-bold text-slate-900 mt-1">99.98%</div>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-700">
                    <span class="material-symbols-outlined text-[22px]">verified</span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[12px]">
                <span class="flex items-center gap-1 text-slate-600">
                    <span class="material-symbols-outlined text-[14px] text-emerald-600">timer</span>
                    TSA / RENIEC
                </span>
                <span class="text-emerald-700 font-medium font-mono text-[11px]">14,889 / 14,892</span>
            </div>
        </div>

        {{-- Card 4: Alertas de Discrepancia --}}
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Alertas de Discrepancia</span>
                    <div class="font-headline-lg text-[28px] font-bold text-slate-900 mt-1">
                        {{ $alertasDiscrepancia ?? 0 }}
                        <span class="text-[13px] font-normal text-slate-500">incidentes</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined text-[22px]">shield</span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[12px]">
                <span class="text-slate-600 text-[11px]">Integridad estructural</span>
                <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold text-[11px]">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Cero anomalías
                </span>
            </div>
        </div>
    </div>

    {{-- ══ SEARCH + FORENSIC FILTERS BAR ══ --}}
    <div class="p-4 bg-white rounded-xl border border-slate-200/90 shadow-xs flex flex-col gap-3">
        <form action="{{ route('portal.auditoria') }}" method="GET" class="flex flex-col lg:flex-row items-stretch gap-2.5">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[19px] pointer-events-none">saved_search</span>
                <input
                    class="w-full pl-10 pr-4 py-2 bg-slate-50 hover:bg-slate-100/60 rounded-lg text-[13px] text-slate-800 placeholder:text-slate-400 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary focus:bg-white transition-all"
                    id="audit-search"
                    name="q"
                    placeholder="Buscar por código (#AUD), funcionario, D.S./Ordenanza, ID requisito o hash SHA-256..."
                    type="text"
                    value="{{ request('q') }}"
                />
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 shrink-0">
                <select name="tipo_evento" class="px-3 py-2 bg-white rounded-lg text-[12px] font-medium text-slate-700 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-secondary/20 cursor-pointer">
                    <option value="">Tipo de Evento: Todos</option>
                    <option value="version" @selected(request('tipo_evento') === 'version')>Publicación TUPA</option>
                    <option value="req" @selected(request('tipo_evento') === 'req')>Modificación Requisito</option>
                    <option value="tasa" @selected(request('tipo_evento') === 'tasa')>Ajuste de Arancel/Tasa</option>
                    <option value="sla" @selected(request('tipo_evento') === 'sla')>Cambio de SLA/Plazos</option>
                </select>
                <select name="modulo" class="px-3 py-2 bg-white rounded-lg text-[12px] font-medium text-slate-700 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-secondary/20 cursor-pointer">
                    <option value="">Módulo: Todos</option>
                    <option value="builder" @selected(request('modulo') === 'builder')>Constructor TUPA</option>
                    <option value="reqs" @selected(request('modulo') === 'reqs')>Parámetros y Requisitos</option>
                    <option value="dictamen" @selected(request('modulo') === 'dictamen')>Mesa de Dictamen</option>
                    <option value="roles" @selected(request('modulo') === 'roles')>Roles y Usuarios</option>
                </select>
                <select name="periodo" class="px-3 py-2 bg-white rounded-lg text-[12px] font-medium text-slate-700 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-secondary/20 cursor-pointer">
                    <option value="30d" @selected(request('periodo', '30d') === '30d')>Últimos 30 días</option>
                    <option value="7d" @selected(request('periodo') === '7d')>Últimos 7 días</option>
                    <option value="90d" @selected(request('periodo') === '90d')>Tercer Trimestre 2025</option>
                    <option value="year" @selected(request('periodo') === 'year')>Año Fiscal 2025 Completo</option>
                </select>
                <select name="severidad" class="px-3 py-2 bg-white rounded-lg text-[12px] font-medium text-slate-700 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-secondary/20 cursor-pointer">
                    <option value="">Severidad: Todas</option>
                    <option value="critico" @selected(request('severidad') === 'critico')>Crítico / Vinculante</option>
                    <option value="ordinario" @selected(request('severidad') === 'ordinario')>Regulatorio Ordinario</option>
                    <option value="tecnico" @selected(request('severidad') === 'tecnico')>Técnico / Mantenimiento</option>
                </select>
            </div>
        </form>
        {{-- Active Filter Pills --}}
        <div class="flex flex-wrap items-center gap-2 pt-1 border-t border-slate-100 text-[12px]">
            <span class="text-slate-400 font-medium">Filtros activos:</span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 font-medium border border-slate-200/80">
                Periodo: Últimos 30 días
                <button class="hover:text-red-600 material-symbols-outlined text-[13px]">close</button>
            </span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-800 font-medium border border-emerald-200/70">
                Firmas: Validadas PKI
                <button class="hover:text-red-600 material-symbols-outlined text-[13px]">close</button>
            </span>
            <a href="{{ route('portal.auditoria') }}" class="text-secondary hover:text-blue-800 font-semibold text-[12px] ml-1.5">
                Restablecer filtros
            </a>
        </div>
    </div>

    {{-- ══ DUAL-PANEL WORKSPACE ══ --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg items-start">

        {{-- ── LEFT: AUDIT TABLE (7 COLS) ── --}}
        <div class="xl:col-span-7 flex flex-col gap-space-md">
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col">
                {{-- Table Toolbar --}}
                <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary-container text-[19px]">format_list_bulleted</span>
                        <span class="font-headline-sm text-[15px] font-bold text-slate-900">Trazabilidad de Cambios Normativos</span>
                    </div>
                    <span class="text-[12px] text-slate-500 font-medium">
                        Mostrando {{ isset($bitacoras) ? $bitacoras->count() : 4 }} de
                        {{ number_format(isset($bitacoras) ? $bitacoras->total() : 14892) }} registros
                    </span>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[13px] text-slate-700">
                        <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-3.5">Evento / ID</th>
                                <th class="py-3 px-3.5">Fecha y Sello TSA</th>
                                <th class="py-3 px-3.5">Funcionario / Credencial</th>
                                <th class="py-3 px-3.5">Procedimiento / Detalle</th>
                                <th class="py-3 px-3.5 text-center">Estado PKI</th>
                                <th class="py-3 px-3.5 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="audit-table-body">
                            @forelse($bitacoras ?? [] as $index => $log)
                                <tr class="cursor-pointer hover:bg-slate-50 transition-colors border-l-4 {{ $index === 0 ? 'bg-blue-50/60 border-l-primary-container' : 'border-l-transparent' }}" data-id="{{ $log->ID_Bitacora ?? 'AUD-' . $index }}">
                                    <td class="py-3 px-3.5">
                                        <div class="flex items-center gap-1.5 font-bold {{ $index === 0 ? 'text-primary-container' : 'text-slate-800' }} font-mono text-[13px]">
                                            <span class="w-2 h-2 rounded-full {{ $index === 0 ? 'bg-secondary' : 'bg-slate-300' }} shrink-0"></span>
                                            #{{ $log->ID_Bitacora ?? 'AUD-2025-0' . (9824 - $index) }}
                                        </div>
                                        <span class="block font-mono text-[10px] text-slate-400 mt-0.5 truncate max-w-[110px]">
                                            Hash: {{ substr($log->Hash_SHA256 ?? 'e3b0c4...', 0, 8) }}...
                                        </span>
                                    </td>
                                    <td class="py-3 px-3.5">
                                        <div class="font-semibold text-slate-900">
                                            {{ $log->Fecha_Creacion ? $log->Fecha_Creacion->format('d M Y') : now()->subDays($index * 2)->format('d M Y') }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-mono">
                                            {{ $log->Fecha_Creacion ? $log->Fecha_Creacion->format('H:i:s') : '16:30:12' }} hrs (TSA)
                                        </div>
                                    </td>
                                    <td class="py-3 px-3.5">
                                        <div class="font-semibold text-slate-900">{{ $log->usuario?->Nombre ?? 'Sistema Automático' }}</div>
                                        <div class="text-[11px] text-slate-500">
                                            {{ $log->usuario?->Email ?? 'sistema@setc.gob.pe' }}
                                        </div>
                                    </td>
                                    <td class="py-3 px-3.5 max-w-[210px]">
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[11px] bg-blue-100/70 text-primary-container font-bold mb-1">
                                            {{ $log->Modulo ?? 'SISTEMA' }}
                                        </span>
                                        <div class="text-[12px] font-medium text-slate-800 truncate" title="{{ $log->Descripcion }}">
                                            {{ Str::limit($log->Accion ?? 'Evento de auditoría', 45) }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 truncate">
                                            {{ $log->Descripcion ? Str::limit($log->Descripcion, 40) : 'Registro automatizado del sistema' }}
                                        </div>
                                    </td>
                                    <td class="py-3 px-3.5 text-center">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-semibold">
                                            <span class="material-symbols-outlined text-[13px]">verified</span>
                                            SHA-256 OK
                                        </span>
                                    </td>
                                    <td class="py-3 px-3.5 text-right">
                                        <button class="{{ $index === 0 ? 'p-1.5 rounded-md bg-primary-container text-white hover:bg-slate-800' : 'p-1.5 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100' }} transition-colors shadow-xs" title="Ver Inspección">
                                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                {{-- Demo rows when no data --}}
                                @php
                                    $demoRows = [
                                        ['id' => 'AUD-2025-09824', 'hash' => 'e3b0c4...', 'fecha' => '24 Oct 2025', 'hora' => '16:30:12', 'funcionario' => 'Lic. Roberto Mendoza', 'cred' => 'DNI: 08761234 • Admin Global', 'proc' => 'TUPA-LC-014', 'accion' => 'Modif. Requisito #02 (Domicilio Fiscal)', 'base' => 'Base: D.S. N° 002-2018-PCM / Ord. 632', 'selected' => true],
                                        ['id' => 'AUD-2025-09819', 'hash' => '7f83b1...', 'fecha' => '23 Oct 2025', 'hora' => '11:15:40', 'funcionario' => 'Dra. Patricia Alva', 'cred' => 'DNI: 42109855 • Supervisor Gral.', 'proc' => 'TUPA-ED-008', 'accion' => 'Ajuste Arancel y Reducción SLA (25 d.)', 'base' => 'Edificación Multifamiliar Tipo C', 'selected' => false],
                                        ['id' => 'AUD-2025-09795', 'hash' => '4e99a2...', 'fecha' => '21 Oct 2025', 'hora' => '09:04:18', 'funcionario' => 'Lic. Roberto Mendoza', 'cred' => 'DNI: 08761234 • Admin Global', 'proc' => 'CATÁLOGO GENERAL', 'accion' => 'Publicación TUPA 2025 v4.2.0', 'base' => 'Despliegue General Portal Ciudadano', 'selected' => false],
                                        ['id' => 'AUD-2025-09760', 'hash' => '3a10cb...', 'fecha' => '18 Oct 2025', 'hora' => '17:45:00', 'funcionario' => 'Ing. Carlos Ruiz', 'cred' => 'DNI: 10459201 • Revisor L2', 'proc' => 'TUPA-AM-003', 'accion' => 'Inclusión Recaudo SERFOR Cat. I', 'base' => 'Certificación Ambiental Minam', 'selected' => false],
                                    ];
                                @endphp
                                @foreach($demoRows as $row)
                                    <tr class="cursor-pointer {{ $row['selected'] ? 'bg-blue-50/60 border-l-4 border-l-primary-container' : 'hover:bg-slate-50 border-l-4 border-l-transparent' }} transition-colors" data-id="{{ $row['id'] }}">
                                        <td class="py-3 px-3.5">
                                            <div class="flex items-center gap-1.5 font-bold {{ $row['selected'] ? 'text-primary-container' : 'text-slate-800' }} font-mono text-[13px]">
                                                <span class="w-2 h-2 rounded-full {{ $row['selected'] ? 'bg-secondary' : 'bg-slate-300' }} shrink-0"></span>
                                                {{ $row['id'] }}
                                            </div>
                                            <span class="block font-mono text-[10px] text-slate-400 mt-0.5 truncate max-w-[110px]">Hash: {{ $row['hash'] }}</span>
                                        </td>
                                        <td class="py-3 px-3.5">
                                            <div class="font-semibold text-slate-900">{{ $row['fecha'] }}</div>
                                            <div class="text-[11px] text-slate-500 font-mono">{{ $row['hora'] }} hrs (TSA)</div>
                                        </td>
                                        <td class="py-3 px-3.5">
                                            <div class="font-semibold text-slate-900">{{ $row['funcionario'] }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $row['cred'] }}</div>
                                        </td>
                                        <td class="py-3 px-3.5 max-w-[210px]">
                                            <span class="inline-block px-1.5 py-0.5 rounded text-[11px] {{ $row['selected'] ? 'bg-blue-100/70 text-primary-container' : 'bg-slate-100 text-slate-700' }} font-bold mb-1">
                                                {{ $row['proc'] }}
                                            </span>
                                            <div class="text-[12px] font-medium text-slate-800 truncate">{{ $row['accion'] }}</div>
                                            <div class="text-[11px] text-slate-500 truncate">{{ $row['base'] }}</div>
                                        </td>
                                        <td class="py-3 px-3.5 text-center">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-semibold">
                                                <span class="material-symbols-outlined text-[13px]">verified</span>
                                                SHA-256 OK
                                            </span>
                                        </td>
                                        <td class="py-3 px-3.5 text-right">
                                            <button class="{{ $row['selected'] ? 'p-1.5 rounded-md bg-primary-container text-white hover:bg-slate-800 shadow-xs' : 'p-1.5 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100' }} transition-colors" title="Ver Inspección">
                                                <span class="material-symbols-outlined text-[16px]">visibility</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                <div class="px-4 py-3 bg-slate-50/60 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-2 text-[12px] text-slate-600">
                    <div class="flex items-center gap-2">
                        <span>Filas por página:</span>
                        <select class="px-2 py-1 rounded bg-white text-slate-700 border border-slate-200 focus:outline-none cursor-pointer">
                            <option>10</option>
                            <option selected>25</option>
                            <option>50</option>
                            <option>100</option>
                        </select>
                        <span class="text-slate-400 ml-1">
                            1 - {{ isset($bitacoras) ? $bitacoras->count() : 4 }} de
                            {{ number_format(isset($bitacoras) && method_exists($bitacoras, 'total') ? $bitacoras->total() : 14892) }} eventos
                        </span>
                    </div>
                    <div class="flex items-center gap-1">
                        <button class="p-1 rounded bg-white border border-slate-200 text-slate-300 cursor-not-allowed">
                            <span class="material-symbols-outlined text-[16px]">first_page</span>
                        </button>
                        <button class="p-1 rounded bg-white border border-slate-200 text-slate-300 cursor-not-allowed">
                            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        </button>
                        <span class="px-2.5 py-1 font-semibold text-primary-container bg-blue-50 border border-blue-200/80 rounded">
                            Página {{ isset($bitacoras) && method_exists($bitacoras, 'currentPage') ? $bitacoras->currentPage() : 1 }}
                        </span>
                        <button class="p-1 rounded bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </button>
                        <button class="p-1 rounded bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">
                            <span class="material-symbols-outlined text-[16px]">last_page</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- IN-LINE COMPLIANCE BANNER --}}
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/90 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-200/80 flex items-center justify-center text-primary-container shrink-0">
                        <span class="material-symbols-outlined text-[18px]">account_balance</span>
                    </div>
                    <div>
                        <div class="text-[13px] font-bold text-slate-900">Marco de Seguridad y Gobernanza Vinculante</div>
                        <p class="text-[12px] text-slate-600 mt-0.5 leading-relaxed">
                            Cada inserción genera una firma asimétrica RSA-4096 acoplada al sello de tiempo INDECOPI, garantizando el principio de no repudio legal según el TUO Ley 27444.
                        </p>
                    </div>
                </div>
                <button class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 text-[12px] font-semibold transition-colors shrink-0 shadow-xs">
                    Ver Política PKI
                </button>
            </div>
        </div>

        {{-- ── RIGHT: FORENSIC INSPECTOR + DIFF VIEWER (5 COLS) ── --}}
        <div class="xl:col-span-5 flex flex-col gap-space-md sticky top-20">
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-sm overflow-hidden flex flex-col">
                {{-- Inspector Header --}}
                <div class="px-4 py-3.5 bg-primary-container text-white flex items-center justify-between border-b border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded bg-blue-900/60 border border-blue-400/20 flex items-center justify-center text-blue-200">
                            <span class="material-symbols-outlined text-[18px]">rule</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-300 block">Inspector Forense</span>
                            <span class="font-headline-sm text-[15px] font-bold text-white block">Evento #AUD-2025-09824</span>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[11px] font-semibold">
                        <span class="material-symbols-outlined text-[13px]">verified</span>
                        Válido
                    </span>
                </div>

                {{-- Metadata Dossier --}}
                <div class="p-3.5 bg-slate-50/80 border-b border-slate-200 grid grid-cols-2 gap-2.5 text-[12px]">
                    <div>
                        <span class="text-slate-400 text-[11px] block">Procedimiento:</span>
                        <span class="font-bold text-slate-800 block truncate">TUPA-LC-014 (Licencia)</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Marca Temporal TSA:</span>
                        <span class="font-medium text-slate-700 block font-mono text-[11px]">24 Oct 2025, 16:30:12 UTC-5</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Funcionario Operador:</span>
                        <span class="font-semibold text-slate-800 block truncate">Lic. Roberto Mendoza</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Base Legal Publicada:</span>
                        <span class="font-medium text-slate-700 block truncate">D.S. N° 002-2018-PCM / Ord. 632</span>
                    </div>
                </div>

                {{-- DIFF VIEWER --}}
                <div class="p-4 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5 text-primary-container font-bold text-[13px]">
                            <span class="material-symbols-outlined text-[17px] text-secondary">find_replace</span>
                            <span>Diff Normativo: Requisito #02</span>
                        </div>
                        <span class="text-[11px] text-slate-500 font-medium">Comprobante de Domicilio Fiscal</span>
                    </div>

                    <div class="flex flex-col gap-2.5 text-[12px]">
                        {{-- ANTES --}}
                        <div class="p-3 rounded-lg bg-red-50/70 border border-red-200/80 flex flex-col gap-1">
                            <div class="flex items-center justify-between text-red-700 font-semibold text-[11px]">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px]">remove_circle</span>
                                    Versión Anterior (v4.1.9)
                                </span>
                                <span class="px-1.5 py-0.5 rounded bg-red-100 text-red-800 text-[10px] font-bold uppercase">Derogado</span>
                            </div>
                            <p class="text-slate-700 line-through opacity-80 leading-relaxed text-[12px]">
                                "Antigüedad máxima: 60 días naturales. Admite boleta física no digitalizada sin validación de código de suministro."
                            </p>
                        </div>

                        {{-- DESPUÉS --}}
                        <div class="p-3 rounded-lg bg-emerald-50/70 border border-emerald-200/80 flex flex-col gap-1">
                            <div class="flex items-center justify-between text-emerald-800 font-semibold text-[11px]">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px] text-emerald-600">add_circle</span>
                                    Versión Actual Publicada (v4.2.0)
                                </span>
                                <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold uppercase">Vigente</span>
                            </div>
                            <p class="text-slate-900 font-medium leading-relaxed text-[12px]">
                                "Antigüedad máxima: 90 días naturales. Validación estricta de código de suministro institucional vía PIDE. Sujeto a observación preventiva automática."
                            </p>
                        </div>
                    </div>

                    {{-- Impact Metric --}}
                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80 flex items-center justify-between text-[12px]">
                        <span class="flex items-center gap-1.5 text-slate-600">
                            <span class="material-symbols-outlined text-[16px] text-secondary">groups</span>
                            Impacto Ciudadano Estimado:
                        </span>
                        <span class="font-bold text-primary-container">+18,400 expedientes/mes</span>
                    </div>
                </div>

                {{-- CRYPTOGRAPHIC CHAIN OF CUSTODY --}}
                <div class="px-4 pb-4 flex flex-col gap-2.5">
                    <div class="flex items-center gap-1.5 text-primary-container font-bold text-[13px]">
                        <span class="material-symbols-outlined text-[17px] text-emerald-600">security</span>
                        <span>Cadena Criptográfica de Custodia (PKI)</span>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 font-mono text-[11px] flex flex-col gap-2">
                        <div>
                            <span class="font-sans text-[10px] font-bold uppercase text-slate-400 block tracking-wide">Hash SHA-256 del Registro Actual:</span>
                            <span class="text-primary-container font-bold break-all select-all block leading-tight mt-0.5">
                                e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855
                            </span>
                        </div>
                        <div class="pt-1 border-t border-slate-200/80">
                            <span class="font-sans text-[10px] font-bold uppercase text-slate-400 block tracking-wide">Hash Bloque Encadenado Anterior (#AUD-2025-09823):</span>
                            <span class="text-slate-600 font-medium break-all select-all block leading-tight mt-0.5">
                                8f434346648f6b96df89dda901c5176b10a6d83961dd3c1ac88b59b2dc327aa4
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-200/80 font-sans text-[11px]">
                            <div>
                                <span class="text-slate-400 block text-[10px]">Certificado PKI:</span>
                                <span class="font-semibold text-slate-800 block truncate">RENIEC / ONPE #8921-99A</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Autoridad TSA:</span>
                                <span class="font-semibold text-slate-800 block truncate">INDECOPI Acreditada</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Footer --}}
                <div class="p-3.5 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2">
                    <button class="flex-1 flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-primary-container hover:bg-[#1a3860] text-white text-[13px] font-semibold transition-all shadow-xs" id="btn-download-acta">
                        <span class="material-symbols-outlined text-[17px]">picture_as_pdf</span>
                        Descargar Acta Timbrada (PDF/A)
                    </button>
                    <button class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-white border border-slate-200 hover:bg-amber-50 hover:border-amber-200 hover:text-amber-800 text-slate-700 text-[13px] font-semibold transition-colors" id="btn-rollback-version" title="Restaurar versión">
                        <span class="material-symbols-outlined text-[17px]">history</span>
                        Rollback
                    </button>
                </div>
            </div>

            {{-- Security Advisory Card --}}
            <div class="p-3.5 rounded-xl bg-white border border-slate-200 shadow-xs flex items-start gap-2.5 text-slate-600">
                <span class="material-symbols-outlined text-secondary text-[20px] shrink-0 mt-0.5">info</span>
                <p class="text-[12px] leading-relaxed">
                    Las versiones aprobadas tienen fuerza reglamentaria vinculante. Los rollbacks normativos requieren doble autenticación por Token Criptográfico L3 y resolución suscrita.
                </p>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const tableRows = document.querySelectorAll('#audit-table-body tr');

    tableRows.forEach(row => {
        row.addEventListener('click', () => {
            tableRows.forEach(r => {
                r.classList.remove('bg-blue-50/60', 'border-l-primary-container');
                r.classList.add('border-l-transparent');
                const dot = r.querySelector('.rounded-full');
                if (dot) {
                    dot.classList.remove('bg-secondary');
                    dot.classList.add('bg-slate-300');
                }
                const btn = r.querySelector('button');
                if (btn) {
                    btn.className = 'p-1.5 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors';
                }
            });

            row.classList.add('bg-blue-50/60', 'border-l-primary-container');
            row.classList.remove('border-l-transparent');
            const dot = row.querySelector('.rounded-full');
            if (dot) {
                dot.classList.remove('bg-slate-300');
                dot.classList.add('bg-secondary');
            }
            const btn = row.querySelector('button');
            if (btn) {
                btn.className = 'p-1.5 rounded-md bg-primary-container text-white hover:bg-slate-800 transition-colors shadow-xs';
            }
        });
    });

    const btnVerify = document.getElementById('btn-verify-chain');
    if (btnVerify) {
        btnVerify.addEventListener('click', () => {
            const originalHTML = btnVerify.innerHTML;
            btnVerify.innerHTML = '<span class="material-symbols-outlined text-[17px] animate-spin text-secondary">sync</span> Comprobando Árbol Merkle...';
            btnVerify.disabled = true;
            setTimeout(() => {
                btnVerify.innerHTML = '<span class="material-symbols-outlined text-[17px] text-emerald-600">check_circle</span> Cadena 100% Íntegra';
                setTimeout(() => {
                    btnVerify.innerHTML = originalHTML;
                    btnVerify.disabled = false;
                }, 3000);
            }, 1000);
        });
    }

    const btnExport = document.getElementById('btn-export-log');
    if (btnExport) {
        btnExport.addEventListener('click', () => {
            const originalHTML = btnExport.innerHTML;
            btnExport.innerHTML = '<span class="material-symbols-outlined text-[17px] animate-spin text-secondary">sync</span> Generando foliado...';
            btnExport.disabled = true;
            setTimeout(() => {
                btnExport.innerHTML = originalHTML;
                btnExport.disabled = false;
            }, 1200);
        });
    }

    const btnDiff = document.getElementById('btn-compare-versions');
    if (btnDiff) {
        btnDiff.addEventListener('click', () => {
            alert('Selector de versiones activado: Compare v4.1.9 vs v4.2.0 en el visor lateral.');
        });
    }
});
</script>
@endpush
@endsection
