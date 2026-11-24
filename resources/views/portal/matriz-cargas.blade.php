@extends('layouts.portal')

@section('title', 'Matriz de Distribución de Carga y Asignación')

@section('breadcrumb')
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span>Supervisión Operativa</span>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="current">Matriz de Cargas</span>
@endsection

@section('content')

<div x-data="{
    showAssignModal: false,
    selectedRevisorId: null,
    selectedRevisorName: ''
}">

{{-- ═══════════════════════════════════ HEADER / TÍTULO ══ --}}
<div class="mb-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white rounded-xl border border-slate-200/80 shadow-xs p-6">
    <div>
        <div class="flex flex-wrap items-center gap-2 mb-2">
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                Algoritmo Heurístico L2 Activo
            </span>
            <span class="text-slate-400 text-xs">•</span>
            <span class="text-slate-500 text-xs font-medium">Turno Ordinario (08:00 – 17:00)</span>
        </div>
        <h1 class="text-2xl lg:text-3xl font-bold text-[#0b1c30] tracking-tight" style="font-family: var(--font-display);">
            Matriz de Distribución de Carga y Asignación
        </h1>
        <p class="text-sm text-slate-500 mt-1 max-w-3xl">
            Balanceo de carga técnica, gestión de expedientes en cola y asignación pericial para Revisores Jurídicos L1/L2.
        </p>
    </div>
    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
        <form method="POST" action="{{ route('portal.asignaciones.auto') }}" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-all shadow-xs cursor-pointer">
                <span class="material-symbols-outlined text-base text-slate-500">sync_alt</span>
                <span>Rebalancear Cola Activa</span>
            </button>
        </form>
        <form method="POST" action="{{ route('portal.asignaciones.auto') }}" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-[#001229] hover:bg-[#0f2744] rounded-lg transition-all shadow-sm cursor-pointer">
                <span class="material-symbols-outlined text-base">bolt</span>
                <span>Asignación Inteligente</span>
                <span class="ml-1 px-2 py-0.5 rounded bg-blue-500/40 text-[10px] font-bold">Autobalance</span>
            </button>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════ BANNER CAPACIDAD ══ --}}
<div class="mb-6 bg-white dark:bg-[#111f33] border border-slate-200/90 dark:border-slate-800 rounded-xl p-4 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900 flex items-center justify-center text-blue-700 dark:text-blue-400 shrink-0">
            <span class="material-symbols-outlined text-2xl">speed</span>
        </div>
        <div>
            <div class="flex items-center gap-2">
                <span class="font-semibold text-sm text-slate-900 dark:text-white">Capacidad Global del Equipo al {{ $capacidadPorcentaje ?? 0 }}%</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ ($capacidadPorcentaje ?? 0) >= 90 ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200 dark:border-rose-800' : (($capacidadPorcentaje ?? 0) >= 70 ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800') }}">
                    {{ ($capacidadPorcentaje ?? 0) >= 90 ? 'Sobrecarga' : (($capacidadPorcentaje ?? 0) >= 70 ? 'Alerta Media' : 'Umbral Seguro') }}
                </span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Capacidad nominal: {{ $capacidadNominal ?? 0 }} expedientes • <strong class="text-slate-700 dark:text-slate-300">{{ $enCurso ?? 0 }} en curso</strong> • {{ $cuposDisponibles ?? 0 }} cupos disponibles
            </p>
        </div>
    </div>
    <div class="flex items-center gap-3 min-w-[260px]">
        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden">
            <div class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width: {{ $capacidadPorcentaje ?? 0 }}%"></div>
        </div>
        <span class="font-bold text-xs text-slate-700 dark:text-slate-300 shrink-0">{{ $enCurso ?? 0 }} / {{ $capacidadNominal ?? 0 }}</span>
    </div>
</div>

{{-- ═══════════════════════════════════ KPIs ══ --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 p-4 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-all">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Revisores Activos</span>
            <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 text-blue-700 dark:text-blue-400 flex items-center justify-center">
                <span class="material-symbols-outlined text-lg">badge</span>
            </div>
        </div>
        <div class="mt-3 flex items-baseline gap-2">
            <span class="font-bold text-2xl text-slate-900 dark:text-white">{{ $kpis['revisores_con_carga'] ?? 0 }}</span>
            <span class="text-xs text-slate-500 dark:text-slate-400">/ {{ $kpis['revisores_totales'] ?? 0 }} registrados</span>
        </div>
        <div class="flex items-center gap-2 text-[11px] text-emerald-600 dark:text-emerald-400 mt-2">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>{{ $kpis['supervisores_totales'] ?? 0 }} en supervisión</span>
        </div>
    </div>

    <div class="bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 p-4 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-all">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Expedientes en Cola</span>
            <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 flex items-center justify-center">
                <span class="material-symbols-outlined text-lg">inbox</span>
            </div>
        </div>
        <div class="mt-3 flex items-baseline gap-2">
            <span class="font-bold text-2xl text-amber-700 dark:text-amber-400">{{ $kpis['en_cola'] ?? 0 }}</span>
        </div>
        <div class="flex items-center gap-2 text-[11px] {{ ($kpis['criticos'] ?? 0) > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400' }} mt-2">
            <span class="w-1.5 h-1.5 rounded-full {{ ($kpis['criticos'] ?? 0) > 0 ? 'bg-rose-500' : 'bg-slate-400' }}"></span>
            <span>{{ $kpis['criticos'] ?? 0 }} críticos (&gt;3 días sin asignar)</span>
        </div>
    </div>

    <div class="bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 p-4 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-all">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tiempo Promedio Dictamen</span>
            <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 flex items-center justify-center">
                <span class="material-symbols-outlined text-lg">timer</span>
            </div>
        </div>
        <div class="mt-3 flex items-baseline gap-2">
            <span class="font-bold text-2xl text-slate-900 dark:text-white">{{ $kpis['tiempo_promedio'] ?? 'Sin registros' }}</span>
        </div>
        <div class="flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400 mt-2">
            <span class="material-symbols-outlined text-xs">history_edu</span>
            <span>{{ $kpis['total_dictaminados'] ?? 0 }} dictámenes formalizados</span>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════ TABLA DE REVISORES ══ --}}
<div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-[#0b1c30]" style="font-family: var(--font-display);">Panel de Revisores y Carga Activa</h2>
            <p class="text-xs text-slate-500 mt-0.5">Distribución en tiempo real de expedientes por revisor técnico L1/L2.</p>
        </div>
        <form method="GET" action="{{ route('portal.matriz-cargas') }}" class="flex items-center gap-3">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none">search</span>
                <input name="buscar" value="{{ request('buscar') }}"
                       class="pl-9 pr-4 py-1.5 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 w-56"
                       placeholder="Buscar revisor..." type="text">
            </div>
            <select name="nivel" onchange="this.form.submit()"
                    class="px-3 py-1.5 border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20 cursor-pointer bg-white">
                <option value="Todos los niveles" {{ request('nivel') === 'Todos los niveles' ? 'selected' : '' }}>Todos los niveles</option>
                <option value="Revisor" {{ request('nivel') === 'Revisor' ? 'selected' : '' }}>Revisor Técnico</option>
                <option value="Supervisor" {{ request('nivel') === 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
            </select>
            @if(request('buscar') || request('nivel'))
                <a href="{{ route('portal.matriz-cargas') }}" class="text-xs text-slate-400 hover:text-slate-600">Limpiar</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50 text-slate-400 uppercase tracking-wider font-semibold text-[11px]">
                    <th class="py-3 px-6">Revisor Técnico</th>
                    <th class="py-3 px-4">Nivel / Rol</th>
                    <th class="py-3 px-4">Expedientes Asignados</th>
                    <th class="py-3 px-4">Expedientes Resueltos</th>
                    <th class="py-3 px-4">Estado</th>
                    @if(auth()->check() && auth()->user()->esSupervisor())
                        <th class="py-3 px-4 text-right">Acción</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @forelse ($revisores ?? [] as $revisor)
                    @php
                        $asignadosActivos = $revisor->asignaciones_activas_count ?? 0;
                        $asignadosResueltos = $revisor->asignaciones_resueltas_count ?? 0;
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3.5 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#0f2744] text-white flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ strtoupper(substr($revisor->Nombre ?? 'U', 0, 2)) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-semibold text-[#0b1c30] text-xs">{{ $revisor->Nombre ?? 'Revisor' }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $revisor->Email ?? '' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $revisor->role_label }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white text-sm">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold {{ $asignadosActivos > 0 ? 'bg-blue-50 dark:bg-blue-950/40 text-[#0051d5] dark:text-blue-400 border border-blue-200 dark:border-blue-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}" title="Expedientes en curso / pendientes de dictamen">
                                {{ $asignadosActivos }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white text-sm">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold {{ $asignadosResueltos > 0 ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}" title="Expedientes dictaminados y concluidos">
                                @if($asignadosResueltos > 0)
                                    <span class="material-symbols-outlined text-xs text-emerald-600">check</span>
                                @endif
                                {{ $asignadosResueltos }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Activo
                            </span>
                        </td>
                        @if(auth()->check() && auth()->user()->esSupervisor())
                            <td class="py-3.5 px-4 text-right">
                                <button type="button"
                                        @click="showAssignModal = true; selectedRevisorId = {{ $revisor->ID_Usuarios }}; selectedRevisorName = '{{ addslashes($revisor->Nombre) }}';"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-700 text-[11px] font-semibold transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-xs">add_task</span>
                                    Asignar
                                </button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ (auth()->check() && auth()->user()->esSupervisor()) ? 6 : 5 }}" class="py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl block mb-2 text-slate-300">group_off</span>
                            <p class="font-semibold text-slate-700 text-sm">No hay revisores técnicos registrados aún</p>
                            <p class="text-xs text-slate-400 mt-1">Cree usuarios con rol de Revisor Técnico desde la Gestión de Usuarios para comenzar la asignación.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
        <span class="text-xs text-slate-500">Mostrando {{ isset($revisores) ? $revisores->count() : 0 }} revisores</span>
        <div class="flex items-center gap-2">
            @if(isset($revisores) && $revisores instanceof \Illuminate\Pagination\LengthAwarePaginator)
                {{ $revisores->links() }}
            @endif
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════ MODAL ASIGNAR EXPEDIENTE ══ --}}
<div x-show="showAssignModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
     style="display: none;">
    <div @click.outside="showAssignModal = false"
         class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0051d5]">assignment_ind</span>
                <h3 class="font-bold text-base text-[#0b1c30]">Asignar Expediente a Revisor</h3>
            </div>
            <button @click="showAssignModal = false" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <p class="text-xs text-slate-600">
            Revisor seleccionado: <strong class="text-[#0b1c30]" x-text="selectedRevisorName"></strong>
        </p>

        <form action="{{ route('portal.asignaciones.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="id_revisor" :value="selectedRevisorId">

            <div>
                <label for="id_solicitud" class="block text-xs font-semibold text-[#0b1c30] mb-1">
                    Seleccione un Expediente Pendiente en Cola
                </label>
                @if(isset($pendientes) && $pendientes->isNotEmpty())
                    <select name="id_solicitud" id="id_solicitud" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                        <option value="">-- Seleccionar expediente --</option>
                        @foreach($pendientes as $p)
                            <option value="{{ $p->ID_Solicitud }}">
                                {{ $p->folio?->Codigo_Folio ?? "Expediente #{$p->ID_Solicitud}" }} — {{ $p->versionTramite?->tipoTramite?->Nombre ?? 'Trámite' }} ({{ $p->solicitante?->Nombre ?? 'Ciudadano' }})
                            </option>
                        @endforeach
                    </select>
                @else
                    <div class="p-3 bg-amber-50 rounded-lg border border-amber-200 text-xs text-amber-800">
                        No hay expedientes pendientes de asignación en este momento.
                    </div>
                @endif
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" @click="showAssignModal = false"
                        class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition-colors">
                    Cancelar
                </button>
                @if(isset($pendientes) && $pendientes->isNotEmpty())
                    <button type="submit"
                            class="px-5 py-2 rounded-lg bg-[#001229] hover:bg-[#0f2744] text-white text-xs font-semibold transition-colors">
                        Confirmar Asignación
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>

</div> {{-- Cierre de x-data --}}
@endsection
