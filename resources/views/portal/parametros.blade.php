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

<div x-data="{
    showNewTramiteModal: false,
    showEditTramiteModal: false,
    showNewVersionModal: false,
    showNewReqModal: false,
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
            <a href="{{ route('tramites.index') }}" target="_blank"
               class="px-3.5 py-2 rounded-lg bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 font-medium text-xs flex items-center gap-1.5 shadow-xs transition-colors">
                <span class="material-symbols-outlined text-base text-blue-600">visibility</span>
                <span>Previsualizar Catálogo Ciudadano</span>
            </a>
            <button type="button"
                    @click="showNewTramiteModal = true"
                    class="px-4 py-2 rounded-lg bg-[#001229] hover:bg-[#0f2744] text-white font-semibold text-xs flex items-center gap-1.5 shadow-sm hover:shadow transition-all cursor-pointer">
                <span class="material-symbols-outlined text-base">add_circle</span>
                <span>+ Nuevo Trámite TUPA</span>
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
                            <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                <button type="button"
                                        @click="showEditTramiteModal = true; currentTramiteId = {{ $tipo->ID_Tipo_Tramite }}; currentTramiteName = '{{ addslashes($tipo->Nombre) }}'; currentTramiteDesc = '{{ addslashes($tipo->Descripcion ?? '') }}'; currentTramiteEstado = '{{ $tipo->Estado_Tipo_Tr ?? 'Activo' }}';"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-600 text-[11px] font-semibold transition-colors cursor-pointer"
                                        title="Editar procedimiento">
                                    <span class="material-symbols-outlined text-xs">edit</span>
                                    <span>Editar</span>
                                </button>
                                <button type="button"
                                        @click="showNewVersionModal = true; currentTramiteId = {{ $tipo->ID_Tipo_Tramite }}; currentTramiteName = '{{ addslashes($tipo->Nombre) }}';"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-[11px] font-semibold transition-colors cursor-pointer"
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
                                <form method="POST" action="{{ route('portal.tipo-tramites.destroy', $tipo->ID_Tipo_Tramite) }}" onsubmit="return confirm('¿Está seguro de eliminar este trámite del catálogo?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            title="Eliminar trámite"
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-rose-700 text-slate-500 text-[11px] font-semibold transition-colors cursor-pointer">
                                        <span class="material-symbols-outlined text-xs">delete</span>
                                    </button>
                                </form>
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
                <h3 class="font-bold text-base text-[#0b1c30]">Nuevo Procedimiento TUPA</h3>
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
