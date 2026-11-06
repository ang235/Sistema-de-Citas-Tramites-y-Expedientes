@extends('layouts.public')

@section('title', 'Agendar Cita y Pase Digital')
@section('meta_description', 'Agenda tu cita presencial o virtual para inspección técnica o revisión de expediente en el SETC.')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="breadcrumb-item">SETC</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <a href="{{ route('citas.index') }}" class="breadcrumb-item">Citas</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <span class="breadcrumb-item current">Agendar Cita</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-12 py-8 w-full space-y-10" x-data="{
    modalidad: 'presencial',
    fecha: '{{ date('Y-m-d', strtotime('+1 day')) }}',
    hora: '09:30',
    solicitudId: '{{ $idSolicitudSeleccionada ?? ($solicitudes->first()?->ID_Solicitud ?? '') }}'
}">

    {{-- ══════════════════════════════════════════ HEADER & STEPPER ══ --}}
    <div class="relative overflow-hidden rounded-2xl bg-white border border-slate-200/80 p-8 shadow-xs">
        <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-[#eff4ff] blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-3 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#eff4ff] text-[#0051d5] text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-[#10b981]"></span>
                    <span>Módulo de Gestión de Turnos Ciudadanos</span>
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-[#001229]" style="font-family: var(--font-display);">
                    Agenda tu turno presencial o virtual
                </h1>
                <p class="text-sm text-[#44474d] leading-relaxed">
                    Elige la fecha y horario que mejor te convenga para la revisión presencial o inspección técnica ITSE de tu expediente. ¡Solo te tomará 2 minutos!
                </p>
            </div>

            <div class="flex items-center gap-4 p-4 rounded-xl bg-[#eff4ff]/60 border border-[#dce9ff] shrink-0">
                <span class="material-symbols-outlined text-[#0051d5] text-2xl">timer</span>
                <div>
                    <span class="text-xs text-slate-500 block">Tiempo promedio</span>
                    <span class="text-sm font-bold text-[#001229]">~2 min de trámite</span>
                    <span class="text-[11px] text-[#059669] font-medium flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">verified</span> Validación instantánea
                    </span>
                </div>
            </div>
        </div>

        {{-- Stepper Dinámico --}}
        <div class="mt-8 pt-6 border-t border-slate-100">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Paso 1 --}}
                <div :class="solicitudId ? 'bg-emerald-50 border-emerald-200' : 'bg-slate-50 border-slate-200'"
                     class="flex items-center gap-3.5 p-3.5 rounded-xl border transition-all">
                    <div :class="solicitudId ? 'bg-emerald-600 text-white' : 'bg-[#001229] text-white'"
                         class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0">
                        <span class="material-symbols-outlined text-base" x-text="solicitudId ? 'check' : 'looks_one'">looks_one</span>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[11px] font-semibold uppercase tracking-wider"
                              :class="solicitudId ? 'text-emerald-700' : 'text-slate-400'"
                              x-text="solicitudId ? 'Completado' : 'Paso 1'">Paso 1</span>
                        <span class="block text-xs font-bold text-[#0b1c30] truncate">Modalidad y Expediente</span>
                    </div>
                </div>

                {{-- Paso 2 (Activo) --}}
                <div :class="fecha && hora ? 'bg-emerald-50 border-emerald-200' : 'bg-[#001229] text-white'"
                     class="flex items-center gap-3.5 p-3.5 rounded-xl shadow-xs transition-all">
                    <div :class="fecha && hora ? 'bg-emerald-600 text-white' : 'bg-[#0051d5] text-white'"
                         class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0">
                        <span class="material-symbols-outlined text-base" x-text="fecha && hora ? 'check' : 'looks_two'">looks_two</span>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[11px] font-semibold uppercase tracking-wider"
                              :class="fecha && hora ? 'text-emerald-700' : 'text-[#d4e3ff]'">Paso 2</span>
                        <span class="block text-xs font-bold truncate"
                              :class="fecha && hora ? 'text-[#0b1c30]' : 'text-white'">Fecha y Horario</span>
                    </div>
                </div>

                {{-- Paso 3 --}}
                <div class="flex items-center gap-3.5 bg-slate-50 p-3.5 rounded-xl border border-slate-200/60 opacity-80">
                    <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-xs shrink-0">
                        3
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Paso 3</span>
                        <span class="block text-xs font-medium text-slate-600 truncate">Pase Digital y QR</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════ GRID DE CONFIGURACIÓN & PREVIEW ══ --}}
    <form action="{{ route('citas.store') }}" method="POST">
        @csrf
        <input type="hidden" name="modalidad" :value="modalidad">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- Columna Izquierda: Formulario (7 cols) --}}
            <div class="lg:col-span-7 space-y-8">

                {{-- 1. Modalidad de Atención --}}
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-[#001229] flex items-center gap-2" style="font-family: var(--font-display);">
                            <span class="material-symbols-outlined text-[#0051d5]">domain</span>
                            <span>1. Modalidad de Atención</span>
                        </h2>
                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-[#eff4ff] text-[#0051d5] font-semibold">Requerido</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Presencial --}}
                        <div @click="modalidad = 'presencial'"
                             :class="modalidad === 'presencial' ? 'border-[#0051d5] bg-[#eff4ff]/40 ring-2 ring-[#0051d5]' : 'border-slate-200 hover:border-slate-300'"
                             class="p-4 rounded-xl border cursor-pointer transition-all relative space-y-2">
                            <div class="w-9 h-9 rounded-lg bg-white shadow-xs flex items-center justify-center text-[#0051d5]">
                                <span class="material-symbols-outlined text-xl">apartment</span>
                            </div>
                            <h3 class="text-sm font-bold text-[#0b1c30]">Presencial en Sede Central</h3>
                            <p class="text-xs text-[#44474d] leading-relaxed">
                                Ventanilla 08 • Mesa Técnica de Obras e ITSE. Atención cara a cara.
                            </p>
                        </div>

                        {{-- Virtual --}}
                        <div @click="modalidad = 'virtual'"
                             :class="modalidad === 'virtual' ? 'border-[#0051d5] bg-[#eff4ff]/40 ring-2 ring-[#0051d5]' : 'border-slate-200 hover:border-slate-300'"
                             class="p-4 rounded-xl border cursor-pointer transition-all relative space-y-2">
                            <div class="w-9 h-9 rounded-lg bg-white shadow-xs flex items-center justify-center text-[#0051d5]">
                                <span class="material-symbols-outlined text-xl">videocam</span>
                            </div>
                            <h3 class="text-sm font-bold text-[#0b1c30]">Virtual (ITSE GovMeet)</h3>
                            <p class="text-xs text-[#44474d] leading-relaxed">
                                Videollamada cifrada y revisión de planos en tiempo real con el especialista.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- 2. Vincular con Expediente --}}
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-[#001229] flex items-center gap-2" style="font-family: var(--font-display);">
                            <span class="material-symbols-outlined text-[#0051d5]">folder_open</span>
                            <span>2. Expediente o Trámite a Tratar</span>
                        </h2>
                    </div>

                    @if ($solicitudes->isNotEmpty())
                        <div class="space-y-1.5">
                            <label for="id_solicitud" class="block text-xs font-semibold text-[#0b1c30]">
                                Selecciona el expediente para el turno
                            </label>
                            <select name="id_solicitud"
                                    id="id_solicitud"
                                    x-model="solicitudId"
                                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                                @foreach ($solicitudes as $sol)
                                    <option value="{{ $sol->ID_Solicitud }}" {{ (isset($idSolicitudSeleccionada) && $idSolicitudSeleccionada == $sol->ID_Solicitud) ? 'selected' : '' }}>
                                        {{ $sol->folio?->Codigo_Folio ?? "Expediente #{$sol->ID_Solicitud}" }} — {{ $sol->versionTramite?->tipoTramite?->Nombre ?? 'Trámite' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <p class="text-xs text-slate-500">
                            Cita de orientación general ciudadana (sin expediente previo asignado).
                        </p>
                    @endif
                </div>

                {{-- 3. Selector de Fecha y Hora --}}
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-[#001229] flex items-center gap-2" style="font-family: var(--font-display);">
                            <span class="material-symbols-outlined text-[#0051d5]">schedule</span>
                            <span>3. Fecha y Horario de Turno</span>
                        </h2>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="fecha" class="block text-xs font-semibold text-[#0b1c30] mb-1">
                                Fecha de la Cita
                            </label>
                            <input type="date"
                                   name="fecha"
                                   id="fecha"
                                   x-model="fecha"
                                   min="{{ date('Y-m-d') }}"
                                   required
                                   class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                        </div>

                        <div>
                            <span class="block text-xs font-semibold text-[#0b1c30] mb-2">Horarios Disponibles</span>
                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-2.5">
                                @foreach (['08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30'] as $slot)
                                    <button type="button"
                                            @click="hora = '{{ $slot }}'"
                                            :class="hora === '{{ $slot }}' ? 'bg-[#001229] text-white shadow-xs font-bold' : 'bg-slate-50 hover:bg-slate-100 text-slate-700'"
                                            class="py-2.5 rounded-lg text-xs transition-all text-center">
                                        {{ $slot }}
                                    </button>
                                @endforeach
                            </div>
                            <input type="hidden" name="hora" :value="hora">
                        </div>
                    </div>
                </div>

                {{-- Botón Submit --}}
                <button type="submit"
                        class="w-full h-12 bg-[#0051d5] hover:bg-[#003ea8] text-white rounded-xl font-semibold text-sm transition-all duration-200 flex items-center justify-center gap-2 shadow-sm hover:shadow active:scale-[0.99]">
                    <span class="material-symbols-outlined text-lg">confirmation_number</span>
                    <span>Confirmar Cita y Generar Pase Digital</span>
                </button>
            </div>

            {{-- Columna Derecha: Pase Digital Preview (5 cols) --}}
            <div class="lg:col-span-5 sticky top-28">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-md p-6 relative overflow-hidden space-y-6">
                    <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-[#001229] via-[#0051d5] to-[#85f8c4]"></div>

                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#0051d5] text-xl">qr_code_2</span>
                            <span class="text-xs font-bold uppercase tracking-wider text-[#001229]">Pase Digital de Turno</span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full bg-[#eff4ff] text-[#0051d5] font-mono text-[11px] font-bold">
                            PREVIEW
                        </span>
                    </div>

                    {{-- QR Mockup / Badge --}}
                    <div class="p-6 bg-[#eff4ff]/60 border border-[#dce9ff] rounded-xl text-center space-y-3">
                        <div class="inline-block p-4 bg-white rounded-xl shadow-xs border border-slate-200">
                            <span class="material-symbols-outlined text-6xl text-[#001229]">qr_code_2</span>
                        </div>
                        <div class="space-y-0.5">
                            <span class="block text-xs text-slate-400 font-mono">CÓDIGO DE TURNO</span>
                            <span class="block text-lg font-bold font-mono text-[#001229]" x-text="'TURNO-' + hora.replace(':', '')">TURNO-0930</span>
                        </div>
                    </div>

                    {{-- Detalles de la cita --}}
                    <div class="space-y-3 text-xs divide-y divide-slate-100">
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-slate-500">Ciudadano</span>
                            <span class="font-semibold text-[#0b1c30]">{{ auth()->user()->Nombre ?? auth()->user()->name }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-slate-500">Modalidad</span>
                            <span class="font-semibold text-[#0051d5] capitalize" x-text="modalidad">Presencial</span>
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-slate-500">Fecha</span>
                            <span class="font-semibold text-[#0b1c30]" x-text="fecha">2026-11-06</span>
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-slate-500">Horario Asignado</span>
                            <span class="font-bold text-[#001229]" x-text="hora + ' hrs'">09:30 hrs</span>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-lg text-[11px] text-slate-500 flex items-start gap-2">
                        <span class="material-symbols-outlined text-xs mt-0.5 text-[#0051d5]">info</span>
                        <span>Al confirmar, podrás descargar el pase en PDF o presentarlo directamente desde tu dispositivo móvil.</span>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

