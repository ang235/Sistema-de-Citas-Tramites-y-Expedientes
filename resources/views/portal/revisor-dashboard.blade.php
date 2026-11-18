@extends('layouts.portal')

@section('title', 'Mando de Revisión Técnica')

@section('breadcrumb')
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span>Revisión Técnica</span>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="current">Mando de Revisión Técnica</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ═══════════════════════════════════ HEADER REVISOR TÉCNICO ══ --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-2 border-b border-slate-200/80 dark:border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400 mb-1">
                <span class="material-symbols-outlined text-base">verified</span>
                <span>Revisor Técnico Oficial • Calificación de Recaudos</span>
            </div>
            <h1 class="text-2xl lg:text-3xl font-bold text-[#001229] dark:text-white tracking-tight" style="font-family: var(--font-display);">
                Mando de Revisión Técnica
            </h1>
            <p class="text-xs lg:text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">
                Panel especializado para la calificación documental de expedientes asignados a su mesa por el supervisor.
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('portal.mesa-dictamen') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#001229] hover:bg-[#0f2744] dark:bg-blue-600 dark:hover:bg-blue-700 text-white text-xs font-semibold shadow-xs transition-all">
                <span class="material-symbols-outlined text-sm">fact_check</span>
                <span>Mesa de Calificación Documental</span>
            </a>
        </div>
    </div>

    {{-- KPIS DEL REVISOR TÉCNICO --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        {{-- Asignados por Supervisor --}}
        <div class="p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Asignados Activos</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">assignment_ind</span>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-[#001229] dark:text-white">{{ $metricas['asignados'] ?? 0 }}</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Expedientes en su carga</div>
            </div>
        </div>

        {{-- En Revisión Activa --}}
        <div class="p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">En Revisión</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">pending_actions</span>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-sky-700 dark:text-sky-400">{{ $metricas['en_revision'] ?? 0 }}</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">En dictamen activo</div>
            </div>
        </div>

        {{-- Con Observaciones --}}
        <div class="p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Observados</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">warning</span>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-amber-800 dark:text-amber-300">{{ $metricas['con_observaciones'] ?? 0 }}</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Esperando subsanación</div>
            </div>
        </div>

        {{-- Dictaminados --}}
        <div class="p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Dictaminados</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">check_circle</span>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-emerald-700 dark:text-emerald-400">{{ $metricas['dictaminados'] ?? 0 }}</div>
                <div class="mt-1 text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">Revisión concluida</div>
            </div>
        </div>

        {{-- Documentos Pendientes --}}
        <div class="p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Recaudos Pendientes</span>
                <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">description</span>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-purple-700 dark:text-purple-400">{{ $metricas['documentos_pendientes'] ?? 0 }}</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Por calificar</div>
            </div>
        </div>

        {{-- Citas Técnicas Pendientes --}}
        <div class="p-4 bg-white dark:bg-[#111f33] rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Citas Asignadas</span>
                <div class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">calendar_month</span>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-teal-700 dark:text-teal-300">{{ $metricas['citas_pendientes'] ?? 0 }}</div>
                <div class="mt-1 text-[11px] text-teal-600 dark:text-teal-400 font-medium">
                    {{ ($metricas['citas_hoy'] ?? 0) > 0 ? ($metricas['citas_hoy'] . ' agendada(s) hoy') : 'Sin citas para hoy' }}
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════ CITAS TÉCNICAS ASIGNADAS ══ --}}
    <div x-data="{
            modalDetalle: false,
            citaSeleccionada: null,
            verCita(data) {
                this.citaSeleccionada = data;
                this.modalDetalle = true;
            },
            cerrarModal() {
                this.modalDetalle = false;
                this.citaSeleccionada = null;
            }
         }"
         class="bg-white dark:bg-[#111f33] rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">

        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gradient-to-r from-teal-500/5 via-transparent to-transparent">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200/60 dark:border-teal-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>
                        Atención Personalizada
                    </span>
                    <span class="text-xs text-slate-400 dark:text-slate-500">•</span>
                    <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Turnos asignados según recaudos calificados</span>
                </div>
                <h2 class="text-base font-bold text-[#001229] dark:text-white mt-1" style="font-family: var(--font-display);">
                    Citas Técnicas Pendientes de Atención
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Comparecencias presenciales y virtuales de ciudadanos cuyos expedientes técnicos fueron revisados por usted.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('portal.citas') }}" class="text-xs font-semibold text-teal-600 dark:text-teal-400 hover:underline inline-flex items-center gap-1">
                    <span>Ver agenda completa</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800 uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4">Fecha y Horario</th>
                        <th class="py-3 px-4">Ciudadano Solicitante</th>
                        <th class="py-3 px-4">Expediente y Trámite</th>
                        <th class="py-3 px-4 text-center">Estado de Recaudos</th>
                        <th class="py-3 px-4 text-center">Estado Cita</th>
                        <th class="py-3 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($citasPendientes as $cita)
                        @php
                            $isHoy = $cita->Fecha && \Carbon\Carbon::parse($cita->Fecha)->isToday();
                            $docsTotal = $cita->solicitud?->documentos?->count() ?? 0;
                            $docsAprobados = $cita->solicitud?->documentos?->where('Estado_Doc', 'Aprobado')->count() ?? 0;

                            $citaJson = [
                                'id' => $cita->ID_Citas,
                                'fecha' => \Carbon\Carbon::parse($cita->Fecha)->translatedFormat('l, d \d\e F \d\e Y'),
                                'fecha_corta' => \Carbon\Carbon::parse($cita->Fecha)->format('d/m/Y'),
                                'hora' => substr((string)$cita->Hora, 0, 5) . ' hrs',
                                'es_hoy' => $isHoy,
                                'estado' => $cita->Estado_Citas,
                                'ciudadano' => [
                                    'nombre' => $cita->usuario?->Nombre ?? 'Ciudadano Particular',
                                    'email' => $cita->usuario?->Email ?? 'Sin correo registrado',
                                ],
                                'expediente' => [
                                    'id' => $cita->solicitud?->ID_Solicitud,
                                    'folio' => $cita->solicitud?->folio?->Codigo_Folio ?? ('EXP-' . $cita->solicitud?->ID_Solicitud),
                                    'tramite' => $cita->solicitud?->versionTramite?->tipoTramite?->Nombre ?? 'Trámite Vinculado',
                                    'version' => $cita->solicitud?->versionTramite?->Numero_Version ?? '1.0',
                                    'estado' => $cita->solicitud?->Estado_Solicitud ?? 'N/D',
                                    'recaudos_calificados' => "{$docsAprobados}/{$docsTotal}",
                                ],
                                'pase_url' => route('citas.pase', $cita->ID_Citas),
                                'evaluar_url' => $cita->solicitud ? route('portal.expedientes.evaluar', $cita->solicitud->ID_Solicitud) : null,
                                'estado_url' => route('portal.citas.estado', $cita->ID_Citas),
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors {{ $isHoy ? 'bg-teal-50/20 dark:bg-teal-950/20' : '' }}">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    @if($isHoy)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-teal-500 text-white tracking-wide uppercase">HOY</span>
                                    @endif
                                    <span class="font-bold text-[#001229] dark:text-white capitalize">
                                        {{ \Carbon\Carbon::parse($cita->Fecha)->translatedFormat('d \d\e M') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400 font-mono text-[11px] mt-0.5">
                                    <span class="material-symbols-outlined text-xs text-teal-600 dark:text-teal-400">schedule</span>
                                    <span>{{ substr((string)$cita->Hora, 0, 5) }} hrs</span>
                                    <span>•</span>
                                    <span>Ventanilla 04</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-900 dark:text-white block">{{ $cita->usuario?->Nombre ?? 'Ciudadano Particular' }}</span>
                                <span class="text-[11px] text-slate-400">{{ $cita->usuario?->Email ?? '' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($cita->solicitud)
                                    <a href="{{ route('portal.mesa-dictamen', ['expediente' => $cita->solicitud->ID_Solicitud]) }}"
                                       class="font-mono font-bold text-[#0051d5] dark:text-blue-400 hover:underline block">
                                        {{ $cita->solicitud->folio?->Codigo_Folio ?? 'EXP-'.$cita->solicitud->ID_Solicitud }}
                                    </a>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 block truncate max-w-xs">
                                        {{ $cita->solicitud->versionTramite?->tipoTramite?->Nombre ?? 'Trámite Vinculado' }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Sin expediente vinculado</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center gap-1 font-mono font-bold px-2 py-0.5 rounded text-[11px] bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                                    <span class="material-symbols-outlined text-xs">task_alt</span>
                                    {{ $docsAprobados }}/{{ $docsTotal }} Aprobados
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                    {{ $cita->Estado_Citas }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button"
                                            @click="verCita(@js($citaJson))"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/60 hover:bg-teal-100 dark:hover:bg-teal-900/60 text-teal-700 dark:text-teal-300 border border-teal-200/80 dark:border-teal-800 text-xs font-semibold transition-colors cursor-pointer"
                                            title="Ver detalles completos de la cita y del ciudadano">
                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                        <span>Detalles</span>
                                    </button>

                                    <form action="{{ route('portal.citas.estado', $cita->ID_Citas) }}" method="POST" class="inline" onsubmit="return confirm('¿Confirmar que la cita ha sido Atendida?');">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="estado_citas" value="Atendida">
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-2xs transition-colors cursor-pointer"
                                                title="Marcar comparecencia como Atendida">
                                            <span class="material-symbols-outlined text-sm">check</span>
                                            <span>Atendida</span>
                                        </button>
                                    </form>

                                    <a href="{{ route('citas.pase', $cita->ID_Citas) }}"
                                       target="_blank"
                                       class="inline-flex items-center justify-center p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 transition-colors"
                                       title="Ver Pase Digital con QR">
                                        <span class="material-symbols-outlined text-sm">qr_code_2</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-full bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 mx-auto flex items-center justify-center mb-2">
                                    <span class="material-symbols-outlined text-2xl">event_available</span>
                                </div>
                                <p class="font-medium text-slate-700 dark:text-slate-300">No tienes citas técnicas pendientes en este momento.</p>
                                <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                                    Cuando un ciudadano agende una cita vinculada a un expediente cuyos recaudos usted evaluó, aparecerá automáticamente aquí.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MODAL DE DETALLES DE LA CITA --}}
        <div x-show="modalDetalle"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">

            <div @click.away="cerrarModal()"
                 class="bg-white dark:bg-[#0e1c2e] rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl max-w-lg w-full overflow-hidden">

                {{-- Cabecera Modal --}}
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/60 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                            <span class="material-symbols-outlined text-xl">event_note</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-base">
                                Detalle de Cita Técnica #<span x-text="citaSeleccionada?.id"></span>
                            </h3>
                            <span class="text-xs text-slate-400">Asignada a su mesa por revisión de recaudos</span>
                        </div>
                    </div>
                    <button type="button" @click="cerrarModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg">
                        <span class="material-symbols-outlined text-xl">close</span>
                    </button>
                </div>

                {{-- Cuerpo Modal --}}
                <div class="p-5 space-y-4 text-xs" x-show="citaSeleccionada">
                    {{-- Tarjeta Fecha y Hora --}}
                    <div class="p-3.5 rounded-xl bg-teal-50/60 dark:bg-teal-950/30 border border-teal-200/80 dark:border-teal-800/60 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-teal-700 dark:text-teal-300 block">Horario Agendado</span>
                            <div class="font-bold text-slate-900 dark:text-white text-sm mt-0.5" x-text="citaSeleccionada?.fecha"></div>
                            <div class="text-slate-500 dark:text-slate-400 mt-0.5">
                                Hora: <strong class="text-teal-700 dark:text-teal-300" x-text="citaSeleccionada?.hora"></strong> • Ventanilla 04
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full font-bold text-[11px] bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300" x-text="citaSeleccionada?.estado"></span>
                    </div>

                    {{-- Solicitante --}}
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Ciudadano Solicitante</span>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-800">
                            <div class="font-bold text-slate-900 dark:text-white text-sm" x-text="citaSeleccionada?.ciudadano?.nombre"></div>
                            <div class="text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-xs">email</span>
                                <span x-text="citaSeleccionada?.ciudadano?.email"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Expediente --}}
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Expediente Asociado</span>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-bold text-blue-600 dark:text-blue-400 text-sm" x-text="citaSeleccionada?.expediente?.folio"></span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300" x-text="citaSeleccionada?.expediente?.estado"></span>
                            </div>
                            <div class="text-slate-700 dark:text-slate-300 font-medium" x-text="citaSeleccionada?.expediente?.tramite"></div>
                            <div class="text-slate-500 dark:text-slate-400 text-[11px]">
                                Recaudos calificados: <strong class="text-emerald-600 dark:text-emerald-400" x-text="citaSeleccionada?.expediente?.recaudos_calificados"></strong>
                            </div>
                        </div>
                    </div>

                    {{-- Acciones de Cambio de Estado en Modal --}}
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Actualizar Estado Operativo</span>
                        <div class="grid grid-cols-2 gap-2">
                            <form :action="citaSeleccionada?.estado_url" method="POST" onsubmit="return confirm('¿Confirmar que la cita fue Atendida?');">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estado_citas" value="Atendida">
                                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs flex items-center justify-center gap-1.5 transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-sm">check_circle</span>
                                    <span>Marcar Atendida</span>
                                </button>
                            </form>
                            <form :action="citaSeleccionada?.estado_url" method="POST" onsubmit="return confirm('¿Marcar que el ciudadano No Asistió?');">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estado_citas" value="No asistió">
                                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs flex items-center justify-center gap-1.5 transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-sm">person_off</span>
                                    <span>No Asistió</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Pie Modal --}}
                <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/60 flex items-center justify-between">
                    <a :href="citaSeleccionada?.pase_url" target="_blank" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                        <span class="material-symbols-outlined text-sm">qr_code_2</span>
                        <span>Abrir Pase Digital</span>
                    </a>
                    <button type="button" @click="cerrarModal()" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 text-xs font-semibold transition-colors cursor-pointer">
                        Cerrar
                    </button>
                </div>

            </div>
        </div>

    </div>

    {{-- TABLA DE EXPEDIENTES ASIGNADOS AL REVISOR --}}
    <div class="bg-white dark:bg-[#111f33] rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-[#001229] dark:text-white" style="font-family: var(--font-display);">
                    Expedientes en su Carga de Trabajo
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Expedientes formalmente asignados por el supervisor para evaluación de documentos y emisión de dictamen técnico.
                </p>
            </div>
            <a href="{{ route('portal.mesa-dictamen') }}" class="text-xs font-semibold text-[#0051d5] dark:text-blue-400 hover:underline inline-flex items-center gap-1">
                <span>Ir a consola completa</span>
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800 uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4">Folio / Fecha</th>
                        <th class="py-3 px-4">Solicitante</th>
                        <th class="py-3 px-4">Trámite Institucional</th>
                        <th class="py-3 px-4 text-center">Recaudos</th>
                        <th class="py-3 px-4">Estado</th>
                        <th class="py-3 px-4 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($expedientesAsignados as $sol)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-4">
                                <span class="font-mono font-bold text-[#0051d5] dark:text-blue-400 block">
                                    {{ $sol->folio->Codigo_Folio ?? 'EXP-'.$sol->ID_Solicitud }}
                                </span>
                                <span class="text-[10px] text-slate-400">
                                    {{ $sol->Fecha_Registro ? \Carbon\Carbon::parse($sol->Fecha_Registro)->format('d/m/Y H:i') : 'N/D' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-900 dark:text-white block">{{ $sol->solicitante->Nombre ?? 'N/D' }}</span>
                                <span class="text-[11px] text-slate-400">{{ $sol->solicitante->Email ?? '' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-slate-800 dark:text-slate-200 block truncate max-w-xs">
                                    {{ $sol->versionTramite->tipoTramite->Nombre ?? 'Trámite General' }}
                                </span>
                                <span class="text-[10px] text-slate-400">Versión {{ $sol->versionTramite->Numero_Version ?? '1.0' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @php
                                    $docsTotal = $sol->documentos->count();
                                    $docsAprobados = $sol->documentos->where('Estado_Doc', 'Aprobado')->count();
                                    $docsPendientes = $sol->documentos->where('Estado_Doc', 'Pendiente')->count();
                                @endphp
                                <span class="inline-flex items-center gap-1 font-mono font-bold px-2 py-0.5 rounded {{ $docsPendientes > 0 ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' }}">
                                    {{ $docsAprobados }}/{{ $docsTotal }} calificados
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $estadoStyles = [
                                        'Registrado' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                        'En_Revision' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300',
                                        'Observado' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300',
                                        'Aprobado' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
                                        'Rechazado' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $estadoStyles[$sol->Estado_Solicitud] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ str_replace('_', ' ', $sol->Estado_Solicitud) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('portal.mesa-dictamen', ['expediente' => $sol->ID_Solicitud]) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-[#001229] hover:bg-[#0f2744] dark:bg-blue-600 dark:hover:bg-blue-700 text-white text-xs font-semibold shadow-xs transition-colors">
                                    <span class="material-symbols-outlined text-sm">fact_check</span>
                                    <span>Calificar Recaudos</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl block mb-2 text-slate-300 dark:text-slate-600">assignment_late</span>
                                <p class="font-medium text-slate-700 dark:text-slate-300">No tienes expedientes asignados en este momento.</p>
                                <p class="text-xs text-slate-400 mt-1">El supervisor le asignará expedientes desde la Matriz de Cargas para que pueda examinarlos y dictaminarlos.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expedientesAsignados->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $expedientesAsignados->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

