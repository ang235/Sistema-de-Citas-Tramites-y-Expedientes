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
<div class="max-w-4xl mx-auto px-6 lg:px-8 py-8 w-full space-y-8" x-data="{
    step: 1,
    modalidad: 'presencial',
    fecha: '{{ $minFecha ?? date('Y-m-d') }}',
    hora: '09:30',
    solicitudId: '{{ $idSolicitudSeleccionada ?? ($solicitudes->first()?->ID_Solicitud ?? '') }}',
    revisorId: '{{ $idRevisorDefault ?? ($revisores->first()?->ID_Usuarios ?? '') }}',
    revisoresPorSolicitud: {{ json_encode($revisoresPorSolicitud ?? []) }},
    citasOcupadasPorRevisor: {{ json_encode($citasOcupadasPorRevisor ?? []) }},
    slots: ['08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30'],
    onSolicitudChange() {
        let rev = this.revisoresPorSolicitud[this.solicitudId];
        if (rev && rev.id) {
            this.revisorId = String(rev.id);
        }
        this.checkSlotAvailable();
    },
    getRevisorNombre() {
        let rev = this.revisoresPorSolicitud[this.solicitudId];
        return rev ? rev.nombre : 'Revisor Técnico Asignado';
    },
    getRevisorEmail() {
        let rev = this.revisoresPorSolicitud[this.solicitudId];
        return rev ? rev.email : '';
    },
    getRevisorRol() {
        let rev = this.revisoresPorSolicitud[this.solicitudId];
        return rev ? rev.rol : 'Revisor Técnico';
    },
    isSlotOccupied(slot) {
        if (!this.revisorId) return false;
        let ocupadosRevisor = (this.citasOcupadasPorRevisor[this.revisorId] || {})[this.fecha] || [];
        return ocupadosRevisor.includes(slot);
    },
    selectSlot(slot) {
        if (!this.isSlotOccupied(slot)) { this.hora = slot; }
    },
    checkSlotAvailable() {
        if (this.isSlotOccupied(this.hora)) {
            let available = this.slots.find(s => !this.isSlotOccupied(s));
            this.hora = available || '';
        }
    },
    formatDateEs(dateStr) {
        if (!dateStr) return '';
        const parts = dateStr.split('-');
        if (parts.length !== 3) return dateStr;
        const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        return `${parseInt(parts[2], 10)} de ${meses[parseInt(parts[1], 10) - 1]} de ${parts[0]}`;
    },
    getSolicitudLabel() {
        const sel = document.getElementById('id_solicitud_select');
        return sel ? sel.options[sel.selectedIndex]?.text : '';
    },
    getRevisorLabel() {
        const sel = document.getElementById('id_revisor_select');
        return sel ? sel.options[sel.selectedIndex]?.text : '';
    },
    canStep1() { return this.solicitudId !== '' && this.solicitudId !== null && this.solicitudId !== undefined && this.revisorId !== ''; },
    canStep2() { return this.fecha !== '' && this.hora !== ''; },
    goTo(n) {
        this.step = n;
        this.$nextTick(() => window.scrollTo({ top: 0, behavior: 'smooth' }));
    }
}" x-init="checkSlotAvailable()">

    {{-- ══════════════════════════ CASO 1: CITA ACTIVA ══ --}}
    @if ($citaActiva)
        <div class="p-6 rounded-2xl bg-blue-50 dark:bg-[#111f33] border-2 border-[#0051d5] text-slate-900 dark:text-white shadow-md space-y-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-[#0051d5] text-white flex items-center justify-center shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-2xl">event_available</span>
                </div>
                <div class="flex-1 min-w-0 space-y-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-950 text-[#0051d5] dark:text-blue-300 text-xs font-bold uppercase tracking-wider">
                        Cita Activa en Curso
                    </span>
                    <h2 class="text-lg font-bold text-[#001229] dark:text-white" style="font-family: var(--font-display);">
                        Ya cuentas con una cita programada
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                        Por normativa institucional, <strong>solo puedes tener una cita activa a la vez</strong>. Tienes un turno reservado para el
                        <strong class="text-[#0051d5] dark:text-blue-400 capitalize">{{ \Carbon\Carbon::parse($citaActiva->Fecha)->translatedFormat('l, d \d\e F \d\e Y') }}</strong>
                        a las <strong class="text-[#0051d5] dark:text-blue-400">{{ substr((string)$citaActiva->Hora, 0, 5) }} hrs</strong>
                        @if($citaActiva->solicitud)
                            (Expediente: <span class="font-mono font-bold">{{ $citaActiva->solicitud->folio?->Codigo_Folio ?? 'EXP-'.$citaActiva->solicitud->ID_Solicitud }}</span>).
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-blue-200 dark:border-slate-800">
                <a href="{{ route('citas.pase', $citaActiva->ID_Citas) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#0051d5] hover:bg-[#003ea8] text-white text-xs font-semibold shadow-xs transition-colors">
                    <span class="material-symbols-outlined text-base">qr_code</span>
                    <span>Ver Mi Pase Digital Actual</span>
                </a>
                <form action="{{ route('citas.cancelar', $citaActiva->ID_Citas) }}" method="POST"
                      onsubmit="return confirm('¿Confirmas que deseas cancelar esta cita para poder agendar una nueva?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl border border-rose-300 dark:border-rose-800 text-rose-700 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-semibold transition-colors">
                        Cancelar Turno y Reagendar
                    </button>
                </form>
                <a href="{{ route('citas.index') }}" class="px-4 py-2 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-medium transition-colors">
                    Volver a Mis Citas
                </a>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════ CASO 2: SIN EXPEDIENTES APROBADOS ══ --}}
    @if (! $citaActiva && $solicitudes->isEmpty())
        <div class="p-8 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-950 dark:text-amber-200 shadow-sm space-y-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-2xl">verified_user</span>
                </div>
                <div class="space-y-1">
                    <h2 class="text-base font-bold text-amber-950 dark:text-amber-100" style="font-family: var(--font-display);">
                        Requisito de Aprobación Documental Previa
                    </h2>
                    <p class="text-xs text-amber-900 dark:text-amber-300 leading-relaxed max-w-3xl">
                        Por disposición del procedimiento administrativo del SETC, <strong>las citas solo pueden agendarse después de que el revisor técnico haya calificado y aprobado todos los documentos</strong> de su expediente.
                    </p>
                    <p class="text-xs text-amber-800 dark:text-amber-400 mt-2">
                        Una vez que tu revisor emita la calificación favorable, tendrás un plazo de <strong>15 días naturales</strong> para agendar tu cita.
                    </p>
                </div>
            </div>
            <div class="pt-3 border-t border-amber-200/80 dark:border-amber-800/80 flex items-center gap-3">
                <a href="{{ route('expedientes.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#001229] dark:bg-blue-600 text-white text-xs font-semibold hover:bg-slate-800 dark:hover:bg-blue-700 transition-colors">
                    <span class="material-symbols-outlined text-sm">folder_open</span>
                    <span>Consultar Estado de Mis Expedientes</span>
                </a>
                <a href="{{ route('home') }}" class="px-4 py-2 text-xs text-slate-600 dark:text-slate-400 hover:underline">Ir al Inicio</a>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════ CASO 3: FORMULARIO MULTI-PASO ══ --}}
    @if (! $citaActiva && $solicitudes->isNotEmpty())

        {{-- Banner de plazo --}}
        <div class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 flex items-start gap-3.5 shadow-xs">
            <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/60 text-[#0051d5] dark:text-blue-300 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">verified</span>
            </div>
            <div class="space-y-0.5">
                <h3 class="text-sm font-bold text-[#001229] dark:text-white">Documentación Aprobada • Ventana de Agendamiento Activa</h3>
                <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                    Tienes hasta el <strong class="text-[#0051d5] dark:text-blue-400">{{ \Carbon\Carbon::parse($maxFecha)->translatedFormat('d \d\e F \d\e Y') }}</strong> para agendar tu cita (15 días desde la aprobación del revisor).
                </p>
            </div>
        </div>

        {{-- ══════ HEADER + STEPPER ══ --}}
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-[#111f33] border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs">
            <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-[#eff4ff] dark:bg-blue-950/20 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 space-y-2 mb-7">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#eff4ff] dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-[#10b981]"></span>
                    <span>Módulo de Gestión de Turnos Ciudadanos</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-[#001229] dark:text-white" style="font-family: var(--font-display);">
                    Agenda tu turno presencial
                </h1>
                <p class="text-sm text-[#44474d] dark:text-slate-400">
                    Solo se permite una cita activa por usuario. Completa los 3 pasos para generar tu Pase Digital.
                </p>
            </div>

            {{-- Stepper de 3 pasos con línea de tiempo --}}
            <div class="relative z-10 flex items-start justify-between gap-0">

                {{-- ── PASO 1 ── --}}
                <div class="flex flex-col items-center flex-1">
                    {{-- Círculo --}}
                    <div :class="step > 1 ? 'bg-emerald-500 border-emerald-500 text-white shadow-emerald-200 dark:shadow-emerald-900' :
                                 (step === 1 ? 'bg-[#0051d5] border-[#0051d5] text-white shadow-blue-200 dark:shadow-blue-900 ring-4 ring-[#0051d5]/20' :
                                 'bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 text-slate-400 dark:text-slate-500')"
                         class="w-10 h-10 rounded-full border-2 flex items-center justify-center font-bold text-sm shrink-0 transition-all duration-300 shadow-md z-10">
                        <span class="material-symbols-outlined text-lg" x-text="step > 1 ? 'check' : 'looks_one'">looks_one</span>
                    </div>
                    {{-- Etiqueta --}}
                    <div class="mt-2 text-center px-1">
                        <span class="block text-[10px] font-bold uppercase tracking-wider transition-colors"
                              :class="step > 1 ? 'text-emerald-600 dark:text-emerald-400' : (step === 1 ? 'text-[#0051d5] dark:text-blue-400' : 'text-slate-400')"
                              x-text="step > 1 ? '✓ Completado' : 'Paso 1'"></span>
                        <span class="block text-xs font-semibold leading-tight mt-0.5 transition-colors"
                              :class="step >= 1 ? 'text-[#001229] dark:text-white' : 'text-slate-400 dark:text-slate-500'">Modalidad y<br>Expediente</span>
                    </div>
                </div>

                {{-- Línea 1→2 --}}
                <div class="flex-1 mt-5 px-1">
                    <div class="h-0.5 w-full relative overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
                        <div :class="step > 1 ? 'w-full bg-emerald-500' : 'w-0 bg-[#0051d5]'"
                             class="absolute inset-0 h-full transition-all duration-500 rounded-full"></div>
                    </div>
                </div>

                {{-- ── PASO 2 ── --}}
                <div class="flex flex-col items-center flex-1">
                    <div :class="step > 2 ? 'bg-emerald-500 border-emerald-500 text-white shadow-emerald-200 dark:shadow-emerald-900' :
                                 (step === 2 ? 'bg-[#0051d5] border-[#0051d5] text-white shadow-blue-200 dark:shadow-blue-900 ring-4 ring-[#0051d5]/20' :
                                 'bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 text-slate-400 dark:text-slate-500')"
                         class="w-10 h-10 rounded-full border-2 flex items-center justify-center font-bold text-sm shrink-0 transition-all duration-300 shadow-md z-10">
                        <span class="material-symbols-outlined text-lg" x-text="step > 2 ? 'check' : 'looks_two'">looks_two</span>
                    </div>
                    <div class="mt-2 text-center px-1">
                        <span class="block text-[10px] font-bold uppercase tracking-wider transition-colors"
                              :class="step > 2 ? 'text-emerald-600 dark:text-emerald-400' : (step === 2 ? 'text-[#0051d5] dark:text-blue-400' : 'text-slate-400')"
                              x-text="step > 2 ? '✓ Completado' : 'Paso 2'"></span>
                        <span class="block text-xs font-semibold leading-tight mt-0.5 transition-colors"
                              :class="step >= 2 ? 'text-[#001229] dark:text-white' : 'text-slate-400 dark:text-slate-500'">Fecha y<br>Horario</span>
                    </div>
                </div>

                {{-- Línea 2→3 --}}
                <div class="flex-1 mt-5 px-1">
                    <div class="h-0.5 w-full relative overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
                        <div :class="step > 2 ? 'w-full bg-emerald-500' : 'w-0 bg-[#0051d5]'"
                             class="absolute inset-0 h-full transition-all duration-500 rounded-full"></div>
                    </div>
                </div>

                {{-- ── PASO 3 ── --}}
                <div class="flex flex-col items-center flex-1">
                    <div :class="step === 3 ? 'bg-[#0051d5] border-[#0051d5] text-white shadow-blue-200 dark:shadow-blue-900 ring-4 ring-[#0051d5]/20' :
                                 'bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 text-slate-400 dark:text-slate-500'"
                         class="w-10 h-10 rounded-full border-2 flex items-center justify-center font-bold text-sm shrink-0 transition-all duration-300 shadow-md z-10">
                        <span class="material-symbols-outlined text-lg">looks_3</span>
                    </div>
                    <div class="mt-2 text-center px-1">
                        <span class="block text-[10px] font-bold uppercase tracking-wider transition-colors"
                              :class="step === 3 ? 'text-[#0051d5] dark:text-blue-400' : 'text-slate-400'">Paso 3</span>
                        <span class="block text-xs font-semibold leading-tight mt-0.5 transition-colors"
                              :class="step === 3 ? 'text-[#001229] dark:text-white' : 'text-slate-400 dark:text-slate-500'">Pase Digital<br>y QR</span>
                    </div>
                </div>

            </div>
        </div>

        {{-- ══════ FORMULARIO ══ --}}
        <form action="{{ route('citas.store') }}" method="POST" id="cita-form">
            @csrf
            <input type="hidden" name="modalidad" :value="modalidad">
            <input type="hidden" name="id_solicitud" :value="solicitudId">
            <input type="hidden" name="id_revisor" :value="revisorId">
            <input type="hidden" name="fecha" :value="fecha">
            <input type="hidden" name="hora" :value="hora">

            {{-- ───── PASO 1: Modalidad + Expediente + Revisor ───── --}}
            <div x-show="step === 1"
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 translate-y-3"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-6">

                {{-- 1. Modalidad --}}
                <div class="bg-white dark:bg-[#111f33] p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-[#001229] dark:text-white flex items-center gap-2" style="font-family: var(--font-display);">
                            <span class="material-symbols-outlined text-[#0051d5] dark:text-blue-400">domain</span>
                            1. Modalidad de Atención
                        </h2>
                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-[#eff4ff] dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300 font-semibold">Requerido</span>
                    </div>
                    <div>
                        <div class="p-4 rounded-xl border border-[#0051d5] bg-[#eff4ff]/40 dark:bg-blue-950/30 ring-2 ring-[#0051d5] transition-all space-y-2">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-white dark:bg-slate-800 shadow-xs flex items-center justify-center text-[#0051d5] dark:text-blue-400 shrink-0">
                                    <span class="material-symbols-outlined text-xl">apartment</span>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-[#0b1c30] dark:text-white">Presencial en Sede Central</h3>
                                    <p class="text-xs text-[#44474d] dark:text-slate-400 leading-relaxed">
                                        Ventanilla 08 • Mesa Técnica de Obras e ITSE. Atención presencial con el especialista.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Expediente Aprobado --}}
                <div class="bg-white dark:bg-[#111f33] p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-[#001229] dark:text-white flex items-center gap-2" style="font-family: var(--font-display);">
                            <span class="material-symbols-outlined text-[#0051d5] dark:text-blue-400">folder_open</span>
                            2. Expediente Aprobado para el Turno
                        </h2>
                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            Documentos Validados
                        </span>
                    </div>
                    <div class="space-y-1.5">
                        <label for="id_solicitud_select" class="block text-xs font-semibold text-[#0b1c30] dark:text-slate-200">
                            Selecciona el expediente calificado por el revisor:
                        </label>
                        <select id="id_solicitud_select"
                                x-model="solicitudId"
                                @change="onSolicitudChange()"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                            @foreach ($solicitudes as $sol)
                                <option value="{{ $sol->ID_Solicitud }}" {{ (isset($idSolicitudSeleccionada) && $idSolicitudSeleccionada == $sol->ID_Solicitud) ? 'selected' : '' }}>
                                    {{ $sol->folio?->Codigo_Folio ?? "Expediente #{$sol->ID_Solicitud}" }} — {{ $sol->versionTramite?->tipoTramite?->Nombre ?? 'Trámite' }} (Aprobado)
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                            Solo se muestran expedientes cuyos recaudos han sido calificados favorablemente por el revisor técnico.
                        </p>
                    </div>
                </div>

                {{-- 3. Especialista Técnico Asignado Automáticamente --}}
                <div class="bg-white dark:bg-[#111f33] p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-[#001229] dark:text-white flex items-center gap-2" style="font-family: var(--font-display);">
                            <span class="material-symbols-outlined text-[#0051d5] dark:text-blue-400">badge</span>
                            3. Especialista Asignado para la Cita
                        </h2>
                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-semibold border border-emerald-200 dark:border-emerald-800">
                            Asignación Automática
                        </span>
                    </div>

                    <div class="p-4 rounded-xl bg-[#eff4ff]/60 dark:bg-slate-800/60 border border-blue-100 dark:border-slate-700 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-11 h-11 rounded-xl bg-[#0051d5] text-white flex items-center justify-center font-bold text-base shadow-xs shrink-0">
                                <span class="material-symbols-outlined text-2xl">support_agent</span>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block">
                                    Revisor Técnico que calificó tus documentos
                                </span>
                                <h3 class="text-sm font-bold text-[#0b1c30] dark:text-white truncate" x-text="getRevisorNombre()">
                                    {{ $revisorDefault['nombre'] ?? 'Revisor Técnico Institucional' }}
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate" x-text="getRevisorEmail()">
                                    {{ $revisorDefault['email'] ?? '' }}
                                </p>
                            </div>
                        </div>

                        <div class="text-right shrink-0 hidden sm:block">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-white dark:bg-slate-900 text-[#0051d5] dark:text-blue-400 border border-slate-200 dark:border-slate-700 shadow-2xs" x-text="getRevisorRol()">
                                {{ $revisorDefault['rol'] ?? 'Revisor Técnico' }}
                            </span>
                            <span class="block text-[10px] text-slate-400 dark:text-slate-500 mt-1">Ventanilla de Atención</span>
                        </div>
                    </div>

                    <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                        Para garantizar la trazabilidad de tu trámite, la cita presencial o virtual es atendida por el mismo especialista técnico que evaluó y dictaminó tus recaudos. La disponibilidad de turnos se sincroniza directamente con su agenda.
                    </p>
                </div>

                {{-- Botón continuar al Paso 2 --}}
                <button type="button"
                        @click="canStep1() && goTo(2)"
                        :disabled="!canStep1()"
                        :class="canStep1() ? 'bg-[#0051d5] hover:bg-[#003ea8] cursor-pointer shadow-sm' : 'bg-slate-300 dark:bg-slate-700 cursor-not-allowed opacity-60'"
                        class="w-full h-12 text-white rounded-xl font-semibold text-sm transition-all flex items-center justify-center gap-2">
                    <span>Continuar al Paso 2 — Fecha y Horario</span>
                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                </button>
                <p x-show="!canStep1()" class="text-xs text-center text-slate-400 -mt-4">
                    Selecciona un expediente aprobado para continuar.
                </p>
            </div>

            {{-- ───── PASO 2: Fecha + Hora ───── --}}
            <div x-show="step === 2"
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 translate-y-3"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-6">

                {{-- Resumen Paso 1 --}}
                <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-xl">check_circle</span>
                        <div>
                            <span class="block text-xs font-bold text-emerald-800 dark:text-emerald-300">Paso 1 completado</span>
                            <span class="text-xs text-emerald-700 dark:text-emerald-400 capitalize" x-text="'Modalidad: ' + modalidad + ' • ' + getSolicitudLabel()"></span>
                        </div>
                    </div>
                    <button type="button" @click="goTo(1)"
                            class="text-xs text-[#0051d5] dark:text-blue-400 hover:underline flex items-center gap-1 shrink-0">
                        <span class="material-symbols-outlined text-sm">edit</span> Editar
                    </button>
                </div>

                {{-- Selector fecha + hora --}}
                <div class="bg-white dark:bg-[#111f33] p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-[#001229] dark:text-white flex items-center gap-2" style="font-family: var(--font-display);">
                            <span class="material-symbols-outlined text-[#0051d5] dark:text-blue-400">schedule</span>
                            Fecha y Horario de Turno
                        </h2>
                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-semibold">
                            Próximos 15 días
                        </span>
                    </div>

                    {{-- Fecha --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="fecha_input" class="block text-xs font-semibold text-[#0b1c30] dark:text-slate-200">
                                Fecha de la Cita
                            </label>
                            <span class="text-[11px] text-[#0051d5] dark:text-blue-400 font-medium">
                                Hasta el {{ \Carbon\Carbon::parse($maxFecha)->translatedFormat('d/m/Y') }}
                            </span>
                        </div>
                        <input type="date"
                               id="fecha_input"
                               x-model="fecha"
                               @change="checkSlotAvailable()"
                               min="{{ $minFecha }}"
                               max="{{ $maxFecha }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs text-[#0051d5] dark:text-blue-400">info</span>
                            Del {{ \Carbon\Carbon::parse($minFecha)->translatedFormat('d \d\e F') }} al {{ \Carbon\Carbon::parse($maxFecha)->translatedFormat('d \d\e F \d\e Y') }}.
                        </p>
                    </div>

                    {{-- Slots --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="block text-xs font-semibold text-[#0b1c30] dark:text-slate-200">Horarios Disponibles</span>
                            <span class="text-[11px] text-slate-400">Los turnos tomados se bloquean automáticamente</span>
                        </div>
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2.5">
                            <template x-for="slot in slots" :key="slot">
                                <button type="button"
                                        @click="selectSlot(slot)"
                                        :disabled="isSlotOccupied(slot)"
                                        :class="{
                                            'bg-[#001229] dark:bg-blue-600 text-white shadow-xs font-bold ring-2 ring-blue-500 cursor-pointer': hora === slot && !isSlotOccupied(slot),
                                            'bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 cursor-pointer': hora !== slot && !isSlotOccupied(slot),
                                            'bg-slate-100 dark:bg-slate-800/40 text-slate-400 dark:text-slate-600 cursor-not-allowed line-through opacity-60': isSlotOccupied(slot)
                                        }"
                                        class="py-2.5 px-1.5 rounded-lg text-xs transition-all text-center flex flex-col items-center justify-center gap-0.5 min-h-[46px]">
                                    <span x-text="slot"></span>
                                    <span x-show="isSlotOccupied(slot)" class="text-[9px] font-bold text-rose-600 dark:text-rose-400">Ocupado</span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Botones Paso 2 --}}
                <div class="flex gap-3">
                    <button type="button" @click="goTo(1)"
                            class="flex-1 h-12 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-xl font-semibold text-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <span class="material-symbols-outlined text-lg">arrow_back</span>
                        <span>Volver al Paso 1</span>
                    </button>
                    <button type="button"
                            @click="canStep2() && goTo(3)"
                            :disabled="!canStep2()"
                            :class="canStep2() ? 'bg-[#0051d5] hover:bg-[#003ea8] cursor-pointer shadow-sm' : 'bg-slate-300 dark:bg-slate-700 cursor-not-allowed opacity-60'"
                            class="flex-[2] h-12 text-white rounded-xl font-semibold text-sm transition-all flex items-center justify-center gap-2">
                        <span>Ver Resumen y Pase Digital</span>
                        <span class="material-symbols-outlined text-lg">arrow_forward</span>
                    </button>
                </div>
                <p x-show="!canStep2()" class="text-xs text-center text-slate-400 -mt-4">
                    Selecciona fecha y horario para continuar.
                </p>
            </div>

            {{-- ───── PASO 3: Confirmación — Pase Digital ───── --}}
            <div x-show="step === 3"
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 translate-y-3"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-6">

                {{-- Resumen Pasos 1 y 2 --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-xl">check_circle</span>
                            <div>
                                <span class="block text-xs font-bold text-emerald-800 dark:text-emerald-300">Modalidad y Expediente</span>
                                <span class="text-xs text-emerald-700 dark:text-emerald-400 capitalize" x-text="modalidad + ' • ' + getSolicitudLabel()"></span>
                            </div>
                        </div>
                        <button type="button" @click="goTo(1)" class="text-xs text-[#0051d5] dark:text-blue-400 hover:underline flex items-center gap-1 shrink-0">
                            <span class="material-symbols-outlined text-sm">edit</span>
                        </button>
                    </div>
                    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-xl">check_circle</span>
                            <div>
                                <span class="block text-xs font-bold text-emerald-800 dark:text-emerald-300">Fecha y Horario</span>
                                <span class="text-xs text-emerald-700 dark:text-emerald-400 capitalize" x-text="formatDateEs(fecha) + ' • ' + hora + ' hrs'"></span>
                            </div>
                        </div>
                        <button type="button" @click="goTo(2)" class="text-xs text-[#0051d5] dark:text-blue-400 hover:underline flex items-center gap-1 shrink-0">
                            <span class="material-symbols-outlined text-sm">edit</span>
                        </button>
                    </div>
                </div>

                {{-- Pase Digital Preview --}}
                <div class="bg-white dark:bg-[#111f33] rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-md relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-[#001229] via-[#0051d5] to-[#85f8c4]"></div>

                    <div class="p-6 space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#0051d5] dark:text-blue-400 text-xl">qr_code_2</span>
                                <span class="text-sm font-bold uppercase tracking-wider text-[#001229] dark:text-white">Pase Digital de Turno — Vista Previa</span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full bg-[#eff4ff] dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300 font-mono text-[11px] font-bold">PREVIEW</span>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-6 items-center sm:items-start">
                            {{-- QR --}}
                            <div class="p-6 bg-[#eff4ff]/60 dark:bg-slate-800/60 border border-[#dce9ff] dark:border-slate-700 rounded-xl text-center space-y-3 shrink-0">
                                <div class="inline-block p-4 bg-white dark:bg-slate-900 rounded-xl shadow-xs border border-slate-200 dark:border-slate-700">
                                    <span class="material-symbols-outlined text-7xl text-[#001229] dark:text-white">qr_code_2</span>
                                </div>
                                <div>
                                    <span class="block text-xs text-slate-400 font-mono">CÓDIGO DE TURNO</span>
                                    <span class="block text-xl font-bold font-mono text-[#001229] dark:text-white" x-text="'TURNO-' + hora.replace(':', '')">TURNO-0930</span>
                                </div>
                            </div>

                            {{-- Datos --}}
                            <div class="flex-1 w-full space-y-0 divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                                <div class="flex items-center justify-between py-3">
                                    <span class="text-slate-500 dark:text-slate-400 text-xs">Ciudadano</span>
                                    <span class="font-bold text-[#001229] dark:text-white">{{ auth()->user()->Nombre ?? auth()->user()->name }}</span>
                                </div>
                                <div class="flex items-center justify-between py-3">
                                    <span class="text-slate-500 dark:text-slate-400 text-xs">Modalidad</span>
                                    <span class="font-bold text-[#0051d5] dark:text-blue-400">Presencial en Sede Central</span>
                                </div>
                                <div class="flex items-center justify-between py-3">
                                    <span class="text-slate-500 dark:text-slate-400 text-xs">Expediente</span>
                                    <span class="font-bold text-[#001229] dark:text-white text-right max-w-xs truncate" x-text="getSolicitudLabel()"></span>
                                </div>
                                <div class="flex items-center justify-between py-3">
                                    <span class="text-slate-500 dark:text-slate-400 text-xs">Especialista Asignado</span>
                                    <span class="font-bold text-[#0051d5] dark:text-blue-400 text-right max-w-xs truncate" x-text="getRevisorNombre()"></span>
                                </div>
                                <div class="flex items-center justify-between py-3">
                                    <span class="text-slate-500 dark:text-slate-400 text-xs">Fecha</span>
                                    <span class="font-bold text-[#001229] dark:text-white capitalize" x-text="formatDateEs(fecha)"></span>
                                </div>
                                <div class="flex items-center justify-between py-3">
                                    <span class="text-slate-500 dark:text-slate-400 text-xs">Horario Asignado</span>
                                    <span class="font-bold text-[#001229] dark:text-white" x-text="hora + ' hrs'"></span>
                                </div>
                                <div class="flex items-center justify-between py-3">
                                    <span class="text-slate-500 dark:text-slate-400 text-xs">Estado</span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 text-xs font-bold border border-amber-200 dark:border-amber-700">Pendiente de Confirmación</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900 rounded-lg text-[11px] text-slate-600 dark:text-slate-400 flex items-start gap-2">
                            <span class="material-symbols-outlined text-xs mt-0.5 text-[#0051d5] dark:text-blue-400">info</span>
                            <span>Al confirmar, se emitirá tu pase oficial con firma telemática descargable en PDF. Esta acción no se puede deshacer; para cambios, cancela la cita desde tu perfil.</span>
                        </div>
                    </div>
                </div>

                {{-- Botones Paso 3 --}}
                <div class="flex gap-3">
                    <button type="button" @click="goTo(2)"
                            class="flex-1 h-12 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-xl font-semibold text-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <span class="material-symbols-outlined text-lg">arrow_back</span>
                        <span>Volver al Paso 2</span>
                    </button>
                    <button type="submit"
                            class="flex-[2] h-12 bg-[#059669] hover:bg-[#047857] text-white rounded-xl font-semibold text-sm transition-all duration-200 flex items-center justify-center gap-2 shadow-sm hover:shadow active:scale-[0.99] cursor-pointer">
                        <span class="material-symbols-outlined text-lg">confirmation_number</span>
                        <span>Confirmar Cita y Generar Pase Digital</span>
                    </button>
                </div>
            </div>

        </form>
    @endif
</div>
@endsection
