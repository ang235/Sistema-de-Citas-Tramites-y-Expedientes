@extends('layouts.portal')

@section('title', 'Constructor TUPA — Configuración de Trámites y Requisitos')

@section('breadcrumb')
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span>Administración Central</span>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span>Parámetros y Requisitos</span>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="current">Constructor TUPA</span>
@endsection

@section('content')

{{-- ═══════════════════════════════════ HEADER / TITULAR ══ --}}
<div class="mb-6 bg-white rounded-xl border border-slate-200/90 shadow-xs p-6 relative overflow-hidden">
    <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-blue-500/5 pointer-events-none blur-3xl"></div>
    <nav class="flex items-center gap-1.5 text-xs text-slate-500 mb-4">
        <a class="hover:text-blue-700 transition-colors" href="{{ route('portal.dashboard') }}">Inicio</a>
        <span class="material-symbols-outlined text-sm text-slate-400">chevron_right</span>
        <a class="hover:text-blue-700 transition-colors" href="#">Administración Central</a>
        <span class="material-symbols-outlined text-sm text-slate-400">chevron_right</span>
        <span class="text-blue-900 font-semibold">Constructor TUPA</span>
    </nav>
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="max-w-3xl">
            <div class="flex items-start gap-3.5">
                <div class="w-11 h-11 rounded-lg bg-[#0f2744] text-white flex items-center justify-center shadow-sm shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-xl">account_tree</span>
                </div>
                <div>
                    <h1 class="text-2xl lg:text-[26px] font-bold text-slate-900 tracking-tight leading-snug" style="font-family: var(--font-display);">
                        Configuración de Procedimientos y Requisitos (Constructor TUPA)
                    </h1>
                    <p class="text-xs lg:text-sm text-slate-600 mt-1 leading-relaxed">
                        Gestión del catálogo normativo, parametrización de recaudos admisibles, tiempos de SLA, aranceles y flujos de inspección técnica según el Texto Único de Procedimientos Administrativos vigente.
                    </p>
                </div>
            </div>
        </div>
        <div class="flex items-center flex-wrap gap-2.5 self-start lg:self-center">
            <button class="px-3.5 py-2 rounded-lg bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 font-medium text-xs flex items-center gap-1.5 shadow-xs transition-colors">
                <span class="material-symbols-outlined text-base text-blue-600">visibility</span>
                <span>Previsualizar Vista Ciudadana</span>
            </button>
            <button class="px-3.5 py-2 rounded-lg bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 font-medium text-xs flex items-center gap-1.5 shadow-xs transition-colors">
                <span class="material-symbols-outlined text-base text-slate-600">upload_file</span>
                <span>Importar Matriz</span>
            </button>
            <a href="{{ route('portal.parametros.create') }}"
               class="px-4 py-2 rounded-lg bg-[#0f2744] hover:bg-[#16365c] text-white font-semibold text-xs flex items-center gap-1.5 shadow-sm hover:shadow transition-all">
                <span class="material-symbols-outlined text-base">add_circle</span>
                <span>+ Nuevo Trámite TUPA</span>
            </a>
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
                <span class="text-xs text-slate-400">/ 50 límite TUPA</span>
            </div>
        </div>
        <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-lg">list_alt</span>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-xs flex items-center justify-between hover:border-slate-300 transition-all">
        <div class="flex flex-col">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Versiones Activas</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-bold text-emerald-700">{{ $kpis['versiones_activas'] ?? 12 }}</span>
                <span class="text-xs text-emerald-600">vigentes</span>
            </div>
        </div>
        <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-lg">verified</span>
        </div>
    </div>

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

{{-- ═══════════════════════════════════ TABLA CATÁLOGO TUPA ══ --}}
<div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-[#0b1c30]" style="font-family: var(--font-display);">Catálogo de Tipos de Trámite TUPA</h2>
            <p class="text-xs text-slate-500 mt-0.5">Gestión de procedimientos administrativos según normativa vigente.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none">search</span>
                <input class="pl-9 pr-4 py-1.5 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 w-56" placeholder="Buscar trámite, código TUPA..." type="text">
            </div>
            <select class="px-3 py-1.5 border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20 cursor-pointer bg-white">
                <option>Todos los estados</option>
                <option>Vigente</option>
                <option>Borrador</option>
                <option>Obsoleto</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50 text-slate-400 uppercase tracking-wider font-semibold text-[11px]">
                    <th class="py-3 px-6">Código TUPA</th>
                    <th class="py-3 px-4">Nombre del Procedimiento</th>
                    <th class="py-3 px-4">Versión Activa</th>
                    <th class="py-3 px-4">Requisitos</th>
                    <th class="py-3 px-4">SLA Legal</th>
                    <th class="py-3 px-4">Estado</th>
                    <th class="py-3 px-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @forelse ($tiposTramite ?? [] as $tipo)
                    @php
                        $version = $tipo->versionesTramite->where('Vigente', true)->first() ?? $tipo->versionesTramite->last();
                        $esVigente = $version?->Vigente ?? false;
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3.5 px-6">
                            <span class="font-mono font-bold text-blue-700 text-[11px]">TUPA-{{ str_pad($tipo->ID_Tipo_Tramite, 3, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-semibold text-[#0b1c30]">{{ $tipo->Nombre }}</span>
                            <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">{{ $tipo->Descripcion ?? '' }}</p>
                        </td>
                        <td class="py-3.5 px-4">
                            @if ($version)
                                <span class="font-mono text-slate-600">v{{ $version->No_Version }}</span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-[#0b1c30]">{{ $version?->requisitos->count() ?? '—' }}</td>
                        <td class="py-3.5 px-4 text-slate-600">{{ $tipo->SLA_dias ?? '15' }}d</td>
                        <td class="py-3.5 px-4">
                            @if ($esVigente)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Vigente
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Borrador
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('portal.parametros.show', $tipo->ID_Tipo_Tramite) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-600 text-[11px] font-semibold transition-colors">
                                    <span class="material-symbols-outlined text-xs">visibility</span>
                                    Ver
                                </a>
                                <a href="{{ route('portal.parametros.edit', $tipo->ID_Tipo_Tramite) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#0f2744] hover:bg-[#16365c] text-white text-[11px] font-semibold transition-colors">
                                    <span class="material-symbols-outlined text-xs">edit</span>
                                    Editar
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    {{-- Filas de demostración --}}
                    @foreach([
                        ['001','Licencia de Funcionamiento Comercial Tipo A','v3.2','8','15d','Vigente'],
                        ['002','Habilitación Urbana y Conformidad de Obra','v2.1','12','30d','Vigente'],
                        ['003','Inspección Técnica de Seguridad ITSE','v4.0','6','7d','Vigente'],
                        ['004','Permiso de Operación de Almacén','v1.3','5','20d','Borrador'],
                        ['005','Copia Certificada de Documentos Registrales','v2.0','3','5d','Vigente'],
                    ] as $t)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3.5 px-6"><span class="font-mono font-bold text-blue-700 text-[11px]">TUPA-{{ $t[0] }}</span></td>
                            <td class="py-3.5 px-4"><span class="font-semibold text-[#0b1c30]">{{ $t[1] }}</span></td>
                            <td class="py-3.5 px-4"><span class="font-mono text-slate-600">{{ $t[2] }}</span></td>
                            <td class="py-3.5 px-4 font-semibold text-[#0b1c30]">{{ $t[3] }}</td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $t[4] }}</td>
                            <td class="py-3.5 px-4">
                                @if($t[5] === 'Vigente')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Vigente
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Borrador
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-600 text-[11px] font-semibold transition-colors">
                                        <span class="material-symbols-outlined text-xs">visibility</span> Ver
                                    </button>
                                    <button class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#0f2744] hover:bg-[#16365c] text-white text-[11px] font-semibold transition-colors">
                                        <span class="material-symbols-outlined text-xs">edit</span> Editar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
        @php
            $tramitesCol = $tiposTramite ?? $tramites ?? null;
        @endphp
        <span class="text-xs text-slate-500">{{ $tramitesCol ? (method_exists($tramitesCol, 'total') ? $tramitesCol->total() : $tramitesCol->count()) : 5 }} procedimientos administrativos registrados</span>
        @if(isset($tramitesCol) && $tramitesCol instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $tramitesCol->links() }}
        @endif
    </div>
</div>

@endsection
