@extends('layouts.portal')

@section('title', 'Agenda Operativa de Citas')

@section('breadcrumb')
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <a href="{{ route('portal.dashboard') }}">Operación y Dictamen</a>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="current">Agenda de Citas</span>
@endsection

@section('content')
<div class="flex flex-col w-full gap-space-lg">

    {{-- ══ CABECERA DE SALA DE CITAS ══ --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 lg:p-6 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-5">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-primary-container text-white flex items-center justify-center font-bold text-lg shadow-sm shrink-0">
                <span class="material-symbols-outlined text-2xl text-blue-200">calendar_month</span>
            </div>
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-mono font-bold text-xs px-2.5 py-1 rounded-md bg-[#eff4ff] text-[#0051d5]">
                        Módulo de Atención Presencial & Telemática
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Ventanilla Activa
                    </span>
                </div>
                <h1 class="text-xl lg:text-2xl font-bold tracking-tight text-[#001229]" style="font-family: var(--font-display);">
                    Control y Despacho de Citas Ciudadanas
                </h1>
                <p class="text-xs text-slate-500">
                    Gestión de aforo, validación de pases QR y registro de comparecencia en ventanilla metropolitana.
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <a href="{{ route('citas.create') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-primary-container hover:bg-[#1a3860] text-white text-xs font-semibold shadow-xs transition-all">
                <span class="material-symbols-outlined text-base">add_circle</span>
                <span>Agendar Nueva Cita</span>
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs transition-colors">
                <span class="material-symbols-outlined text-base text-slate-500">print</span>
                <span>Padrón Diario</span>
            </button>
        </div>
    </div>

    {{-- ══ KPI SUMMARY CARDS ══ --}}
    @php
        $totalCitas = $citas->total();
        $programadasCount = $citas->where('Estado_Citas', 'Programada')->count();
        $atendidasCount = $citas->where('Estado_Citas', 'Atendida')->count();
        $canceladasCount = $citas->whereIn('Estado_Citas', ['Cancelada', 'No asistió'])->count();
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Total Programadas</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-secondary flex items-center justify-center">
                    <span class="material-symbols-outlined text-base">event</span>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-2xl font-bold text-slate-900">{{ $totalCitas }}</span>
                <span class="text-[11px] text-slate-400 block mt-0.5">Fecha: {{ \Carbon\Carbon::parse($fechaFiltro)->format('d/m/Y') }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">En Espera / Pendientes</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-base">hourglass_top</span>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-2xl font-bold text-amber-700">{{ $programadasCount }}</span>
                <span class="text-[11px] text-slate-400 block mt-0.5">Turnos por atender</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Atendidas</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-base">check_circle</span>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-2xl font-bold text-emerald-700">{{ $atendidasCount }}</span>
                <span class="text-[11px] text-slate-400 block mt-0.5">Comparecencias válidas</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Canceladas / No Asistió</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-base">cancel</span>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-2xl font-bold text-rose-700">{{ $canceladasCount }}</span>
                <span class="text-[11px] text-slate-400 block mt-0.5">Incomparecencias</span>
            </div>
        </div>
    </div>

    {{-- ══ FILTROS DE AGENDA ══ --}}
    <div class="p-4 bg-white rounded-xl border border-slate-200/90 shadow-xs">
        <form action="{{ route('portal.citas') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3 items-end">
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700">Fecha de Atención</label>
                <input type="date"
                       name="fecha"
                       value="{{ $fechaFiltro }}"
                       class="w-full px-3 py-2 bg-slate-50 hover:bg-slate-100/60 rounded-lg text-xs text-slate-800 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary focus:bg-white transition-all">
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700">Estado de la Cita</label>
                <select name="estado" class="w-full px-3 py-2 bg-slate-50 hover:bg-slate-100/60 rounded-lg text-xs text-slate-800 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary focus:bg-white cursor-pointer">
                    <option value="">Todos los estados</option>
                    <option value="Programada" @selected($estadoFiltro === 'Programada')>Programada</option>
                    <option value="Atendida" @selected($estadoFiltro === 'Atendida')>Atendida</option>
                    <option value="Cancelada" @selected($estadoFiltro === 'Cancelada')>Cancelada</option>
                    <option value="No asistió" @selected($estadoFiltro === 'No asistió')>No asistió</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-primary-container hover:bg-[#1a3860] text-white rounded-lg text-xs font-semibold transition-all">
                    Filtrar Agenda
                </button>
                @if ($estadoFiltro || $fechaFiltro !== today()->toDateString())
                    <a href="{{ route('portal.citas') }}" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg border border-slate-200" title="Restablecer hoy">
                        <span class="material-symbols-outlined text-base">today</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ══ TABLA DE CITAS DEL DÍA ══ --}}
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col">
        <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary-container text-lg">schedule</span>
                <span class="font-headline-sm text-sm font-bold text-slate-900">
                    Turnos Asignados — {{ \Carbon\Carbon::parse($fechaFiltro)->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                </span>
            </div>
            <span class="text-xs text-slate-500 font-medium">Mostrando {{ $citas->count() }} de {{ $citas->total() }} turnos</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Hora / Turno</th>
                        <th class="py-3 px-4">Ciudadano Solicitante</th>
                        <th class="py-3 px-4">Expediente / Trámite Asociado</th>
                        <th class="py-3 px-4 text-center">Estado del Turno</th>
                        <th class="py-3 px-4 text-right">Actualizar Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($citas as $cita)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-sm text-primary-container">
                                        {{ substr($cita->Hora, 0, 5) }}
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-secondary border border-blue-200/60">
                                        Ventanilla 04
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-mono mt-0.5 block">#CITA-{{ $cita->ID_Citas }}</span>
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-900">{{ $cita->usuario?->Nombre ?? 'Ciudadano Particular' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $cita->usuario?->Email }}</div>
                            </td>

                            <td class="py-3.5 px-4 max-w-xs">
                                @if ($cita->solicitud)
                                    <a href="{{ route('portal.expedientes.evaluar', $cita->solicitud->ID_Solicitud) }}" class="font-mono font-bold text-[#0051d5] hover:underline block">
                                        {{ $cita->solicitud->folio?->Codigo_Folio ?? 'Exp. #' . $cita->solicitud->ID_Solicitud }}
                                    </a>
                                    <div class="text-[11px] text-slate-500 truncate">
                                        {{ $cita->solicitud->versionTramite?->tipoTramite?->Nombre ?? 'Trámite Vinculado' }}
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Cita informativa general</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $cita->Estado_Citas === 'Atendida' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($cita->Estado_Citas === 'Programada' ? 'bg-blue-50 text-blue-700 border-blue-200' : ($cita->Estado_Citas === 'Cancelada' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-slate-100 text-slate-700 border-slate-200')) }}">
                                    @if ($cita->Estado_Citas === 'Programada')
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                    @elseif ($cita->Estado_Citas === 'Atendida')
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    @endif
                                    {{ $cita->Estado_Citas }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <form action="{{ route('portal.citas.estado', $cita->ID_Citas) }}" method="POST" class="inline-flex items-center gap-1.5">
                                    @csrf
                                    @method('PATCH')
                                    <select name="estado_citas" onchange="this.form.submit()" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:border-slate-300 focus:outline-none focus:ring-1 focus:ring-secondary cursor-pointer shadow-xs">
                                        <option value="Programada" @selected($cita->Estado_Citas === 'Programada')>Programada</option>
                                        <option value="Atendida" @selected($cita->Estado_Citas === 'Atendida')>Atendida</option>
                                        <option value="No asistió" @selected($cita->Estado_Citas === 'No asistió')>No asistió</option>
                                        <option value="Cancelada" @selected($cita->Estado_Citas === 'Cancelada')>Cancelada</option>
                                    </select>
                                    <a href="{{ route('citas.pase', $cita->ID_Citas) }}" target="_blank" class="p-1 rounded-md text-slate-400 hover:text-[#0051d5] hover:bg-slate-100 transition-colors" title="Ver Pase Digital QR">
                                        <span class="material-symbols-outlined text-base">qr_code_2</span>
                                    </a>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-3xl mb-1 block text-slate-300">event_busy</span>
                                No hay citas registradas para la fecha seleccionada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($citas instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="p-4 border-t border-slate-200 bg-slate-50/60">
                {{ $citas->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

