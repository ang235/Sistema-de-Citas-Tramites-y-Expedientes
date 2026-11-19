@extends('layouts.portal')

@section('title', !empty($esSoloRevisor) ? 'Mis Citas Asignadas' : 'Agenda y Supervisión de Citas')

@section('breadcrumb')
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span>Operación</span>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="current">{{ !empty($esSoloRevisor) ? 'Mis Citas Asignadas' : 'Supervisión de Citas' }}</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ══ CABECERA DE LA VENTANA ══ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#111f33] p-5 rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#eff4ff] dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300">
                    {{ !empty($esSoloRevisor) ? 'Mesa Técnica Revisor' : 'Control Operativo' }}
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500">•</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">Atención Presencial y Virtual ITSE</span>
            </div>
            <h1 class="font-headline-sm text-xl font-bold text-slate-900 dark:text-white mt-1">
                {{ !empty($esSoloRevisor) ? 'Mis Citas Asignadas en Ventanilla' : 'Agenda de Citas y Turnos de Ventanilla' }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                {{ !empty($esSoloRevisor) ? 'Turnos y comparecencias de ciudadanos cuyos expedientes técnicos fueron revisados y calificados por usted.' : 'Supervisa el calendario de atención ciudadana, confirma comparecencias y valida pases digitales.' }}
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('portal.citas', ['fecha' => today()->toDateString()]) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200 transition-colors">
                <span class="material-symbols-outlined text-sm text-[#0051d5] dark:text-blue-400">today</span>
                <span>Hoy: {{ today()->translatedFormat('d \d\e M') }}</span>
            </a>
            @if(! (auth()->user()->esSupervisor() || auth()->user()->esAdministrador()))
                <a href="{{ route('citas.create') }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-primary-container hover:bg-[#1a3860] dark:bg-blue-600 dark:hover:bg-blue-700 text-white text-xs font-semibold transition-colors shadow-2xs">
                    <span class="material-symbols-outlined text-sm">add</span>
                    <span>Agendar Turno</span>
                </a>
            @endif
        </div>
    </div>

    {{-- Aviso exclusivo para el rol de Supervisor y Administrador (Solo Lectura) --}}
    @if(auth()->user()->esSupervisor() || auth()->user()->esAdministrador())
        <div class="p-4 rounded-xl bg-slate-100 dark:bg-slate-800/70 border border-slate-200/90 dark:border-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-between gap-3 text-xs shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-lg">visibility</span>
                </div>
                <div>
                    <span class="font-bold text-slate-900 dark:text-white block">Supervisión Institucional de Citas (Solo Lectura)</span>
                    <span class="text-slate-500 dark:text-slate-400">Como supervisor o administrador, puede auditar el calendario de comparecencias y pases digitales. La gestión y marcado de comparecencia en ventanilla es exclusiva del Revisor Técnico asignado.</span>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 shrink-0">
                Solo Lectura
            </span>
        </div>
    @endif

    {{-- Aclaración sobre anticipación y 15 días posteriores a aprobación --}}
    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/90 dark:border-amber-800 text-amber-950 dark:text-amber-200 flex items-center gap-3 text-xs">
        <span class="material-symbols-outlined text-amber-700 dark:text-amber-400 text-xl shrink-0">info</span>
        <p>
            <strong>Regla Operativa:</strong> Las citas solo pueden ser agendadas tras la aprobación de los recaudos por el revisor técnico, con una ventana de <strong>15 días posteriores</strong> y un límite estricto de <strong>1 cita activa por ciudadano</strong>.
        </p>
    </div>

    {{-- ══ KPIS RÁPIDOS ══ --}}
    @php
        $totalCitas = $kpiTotal ?? $citas->total();
        $programadasCount = $kpiProgramadas ?? $citas->where('Estado_Citas', 'Programada')->count();
        $atendidasCount = $kpiAtendidas ?? $citas->where('Estado_Citas', 'Atendida')->count();
        $canceladasCount = $kpiCanceladas ?? $citas->whereIn('Estado_Citas', ['Cancelada', 'No asistió'])->count();
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-[#111f33] p-4 rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Citas</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300 flex items-center justify-center">
                    <span class="material-symbols-outlined text-base">event</span>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ $totalCitas }}</span>
                <span class="text-[11px] text-slate-400 dark:text-slate-500 block mt-0.5 capitalize">
                    {{ $fechaFiltro ? 'Fecha: ' . \Carbon\Carbon::parse($fechaFiltro)->translatedFormat('d \d\e F \d\e Y') : 'Todas las fechas' }}
                </span>
            </div>
        </div>

        <div class="bg-white dark:bg-[#111f33] p-4 rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Programadas</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 flex items-center justify-center">
                    <span class="material-symbols-outlined text-base">hourglass_top</span>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-2xl font-bold text-amber-700 dark:text-amber-400">{{ $programadasCount }}</span>
                <span class="text-[11px] text-slate-400 dark:text-slate-500 block mt-0.5">Turnos por atender</span>
            </div>
        </div>

        <div class="bg-white dark:bg-[#111f33] p-4 rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Realizadas / Atendidas</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center">
                    <span class="material-symbols-outlined text-base">check_circle</span>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-2xl font-bold text-emerald-700 dark:text-emerald-400">{{ $atendidasCount }}</span>
                <span class="text-[11px] text-slate-400 dark:text-slate-500 block mt-0.5">Comparecencias válidas</span>
            </div>
        </div>

        <div class="bg-white dark:bg-[#111f33] p-4 rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Canceladas / Vencidas</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-base">cancel</span>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-2xl font-bold text-rose-700 dark:text-rose-400">{{ $canceladasCount }}</span>
                <span class="text-[11px] text-slate-400 dark:text-slate-500 block mt-0.5">Incomparecencias</span>
            </div>
        </div>
    </div>

    {{-- ══ FILTROS DE AGENDA ══ --}}
    <div class="p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs">
        <form action="{{ route('portal.citas') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Fecha de Atención</label>
                <input type="date"
                       name="fecha"
                       value="{{ $fechaFiltro }}"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100/60 rounded-lg text-xs text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary focus:bg-white transition-all">
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Estado de la Cita</label>
                <select name="estado" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100/60 rounded-lg text-xs text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary focus:bg-white cursor-pointer">
                    <option value="">Todos los estados</option>
                    <option value="Programada" @selected($estadoFiltro === 'Programada')>Programada</option>
                    <option value="Atendida" @selected($estadoFiltro === 'Atendida')>Atendida (Realizada)</option>
                    <option value="Cancelada" @selected($estadoFiltro === 'Cancelada')>Cancelada</option>
                    <option value="No asistió" @selected($estadoFiltro === 'No asistió')>No asistió</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Revisor Asignado</label>
                @if(!empty($esSoloRevisor))
                    <div class="px-3 py-2 bg-slate-100 dark:bg-slate-800/80 rounded-lg text-xs text-slate-600 dark:text-slate-300 font-semibold border border-slate-200 dark:border-slate-700 truncate flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                        <span class="truncate">{{ auth()->user()->Nombre }} (Tú)</span>
                    </div>
                @else
                    <select name="revisor" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100/60 rounded-lg text-xs text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary focus:bg-white cursor-pointer">
                        <option value="">Todos los revisores</option>
                        @foreach($revisores as $rev)
                            <option value="{{ $rev->ID_Usuarios }}" @selected($revisorFiltro == $rev->ID_Usuarios)>{{ $rev->Nombre }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-primary-container hover:bg-[#1a3860] dark:bg-blue-600 dark:hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition-all cursor-pointer">
                    Filtrar Agenda
                </button>
                @if ($estadoFiltro || $fechaFiltro || (!empty($revisorFiltro) && empty($esSoloRevisor)))
                    <a href="{{ route('portal.citas') }}" class="py-2 px-3 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 rounded-lg border border-slate-200 dark:border-slate-700 transition-colors" title="Limpiar filtros">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ══ TABLA DE CITAS DEL DÍA ══ --}}
    <div class="bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden flex flex-col">
        <div class="px-5 py-3.5 bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary-container dark:text-blue-400 text-lg">calendar_month</span>
                <span class="font-headline-sm text-sm font-bold text-slate-900 dark:text-white capitalize">
                    @if ($fechaFiltro)
                        Citas del {{ \Carbon\Carbon::parse($fechaFiltro)->translatedFormat('l, d \d\e F \d\e Y') }}
                    @else
                        Todas las Citas Registradas (Histórico Completo)
                    @endif
                </span>
            </div>
            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Mostrando {{ $citas->count() }} de {{ $citas->total() }} turnos</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Fecha y Turno</th>
                        <th class="py-3 px-4">Ciudadano Solicitante</th>
                        <th class="py-3 px-4">Expediente / Trámite</th>
                        <th class="py-3 px-4">Revisor Especialista</th>
                        <th class="py-3 px-4 text-center">Estado del Turno</th>
                        @if(! (auth()->user()->esSupervisor() || auth()->user()->esAdministrador()))
                            <th class="py-3 px-4 text-right">Acciones Operativas</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($citas as $cita)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#001229] dark:text-white flex items-center gap-1.5 capitalize">
                                    <span class="material-symbols-outlined text-xs text-[#0051d5] dark:text-blue-400">calendar_today</span>
                                    <span>{{ \Carbon\Carbon::parse($cita->Fecha)->translatedFormat('d \d\e M, Y') }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="font-mono font-bold text-xs text-primary-container dark:text-blue-300">
                                        {{ substr((string)$cita->Hora, 0, 5) }} hrs
                                    </span>
                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-950/60 text-secondary dark:text-blue-300 border border-blue-200/60 dark:border-blue-800">
                                        Ventanilla 04
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-0.5 block">#CITA-{{ $cita->ID_Citas }}</span>
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $cita->usuario?->Nombre ?? 'Ciudadano Particular' }}</div>
                                <div class="text-[11px] text-slate-400 dark:text-slate-500">{{ $cita->usuario?->Email }}</div>
                            </td>

                            <td class="py-3.5 px-4 max-w-xs">
                                @if ($cita->solicitud)
                                    <a href="{{ route('portal.expedientes.evaluar', $cita->solicitud->ID_Solicitud) }}" class="font-mono font-bold text-[#0051d5] dark:text-blue-400 hover:underline block">
                                        {{ $cita->solicitud->folio?->Codigo_Folio ?? 'Exp. #' . $cita->solicitud->ID_Solicitud }}
                                    </a>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                        {{ $cita->solicitud->versionTramite?->tipoTramite?->Nombre ?? 'Trámite Vinculado' }}
                                    </div>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500 italic">Cita informativa general</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                @if ($cita->revisor)
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-6 h-6 rounded-full bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 flex items-center justify-center font-bold text-[10px]">
                                            {{ strtoupper(substr($cita->revisor->Nombre, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-semibold text-slate-900 dark:text-white block text-xs">{{ $cita->revisor->Nombre }}</span>
                                            <span class="text-[10px] text-teal-600 dark:text-teal-400">Revisor Calificador</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500">
                                        Por asignar
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $cita->Estado_Citas === 'Atendida' ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : ($cita->Estado_Citas === 'Programada' ? 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800' : ($cita->Estado_Citas === 'Cancelada' ? 'bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700')) }}">
                                    @if ($cita->Estado_Citas === 'Programada')
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                    @elseif ($cita->Estado_Citas === 'Atendida')
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    @endif
                                    {{ $cita->Estado_Citas }}
                                </span>
                            </td>

                            @php
                                $esSoloLecturaCitas = auth()->user()->esSupervisor() || auth()->user()->esAdministrador();
                                $esRevisorAsignado = $cita->ID_Revisor === auth()->id() || ($cita->ID_Revisor === null && $cita->solicitud?->asignaciones()->where('ID_Revisor', auth()->id())->exists());
                                $puedeModificar = ! $esSoloLecturaCitas && (auth()->user()->esRevisor() && $esRevisorAsignado);
                            @endphp
                            @if(! $esSoloLecturaCitas)
                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        @if($cita->Estado_Citas === 'Programada' && $puedeModificar)
                                            <form action="{{ route('portal.citas.estado', $cita->ID_Citas) }}" method="POST" class="inline" onsubmit="return confirm('¿Marcar como Atendida?');">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="estado_citas" value="Atendida">
                                                <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 transition-colors cursor-pointer" title="Marcar Atendida">
                                                    <span class="material-symbols-outlined text-sm align-middle">check</span>
                                                </button>
                                            </form>

                                            <form action="{{ route('portal.citas.estado', $cita->ID_Citas) }}" method="POST" class="inline" onsubmit="return confirm('¿Marcar como No Asistió?');">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="estado_citas" value="No asistió">
                                                <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 hover:bg-rose-100 transition-colors cursor-pointer" title="Marcar No Asistió">
                                                    <span class="material-symbols-outlined text-sm align-middle">close</span>
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('citas.pase', $cita->ID_Citas) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-[#0051d5] dark:text-blue-300 bg-[#eff4ff] dark:bg-blue-950/60 hover:bg-[#dce9ff] dark:hover:bg-blue-900/60 transition-colors" title="Ver Pase Digital con código QR">
                                            <span class="material-symbols-outlined text-sm">qr_code_2</span>
                                            <span>Pase</span>
                                        </a>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ (auth()->user()->esSupervisor() || auth()->user()->esAdministrador()) ? 5 : 6 }}" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                <span class="material-symbols-outlined text-3xl mb-1 block text-slate-300 dark:text-slate-600">event_busy</span>
                                No hay citas registradas para la consulta realizada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($citas instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                {{ $citas->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
