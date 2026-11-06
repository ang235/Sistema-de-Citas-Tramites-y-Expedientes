@extends('layouts.public')

@section('title', 'Mesa de Ayuda y Soporte')
@section('meta_description', 'Centro de asistencia ciudadana, preguntas frecuentes y canales de soporte de SETC.')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="breadcrumb-item">SETC</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <span class="breadcrumb-item current">Mesa de Ayuda</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto px-6 lg:px-12 py-10 w-full space-y-10">
    {{-- Header --}}
    <div class="text-center space-y-3 max-w-2xl mx-auto">
        <span class="px-3.5 py-1.5 rounded-full bg-[#ecfdf5] text-[#065f46] text-xs font-semibold">
            Centro de Asistencia Ciudadana
        </span>
        <h1 class="text-3xl lg:text-4xl font-bold tracking-tight text-[#001229]" style="font-family: var(--font-display);">
            ¿Cómo podemos orientarte hoy?
        </h1>
        <p class="text-sm text-[#44474d]">
            Encuentra respuestas a preguntas frecuentes sobre trámites, citas, formatos y requisitos del sistema SETC.
        </p>
    </div>

    {{-- Canales de Soporte --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-3 text-center">
            <div class="w-12 h-12 rounded-xl bg-[#eff4ff] text-[#0051d5] flex items-center justify-center mx-auto">
                <span class="material-symbols-outlined text-2xl">call</span>
            </div>
            <h3 class="text-base font-bold text-[#001229]">Atención Telefónica</h3>
            <p class="text-xs text-[#44474d]">Línea directa para asesoría normativa.</p>
            <span class="block text-sm font-bold text-[#0051d5] font-mono">01-800-SETC-GOB</span>
            <span class="text-[11px] text-slate-400 block">Lun a Vie • 08:00 a 17:00 hrs</span>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-3 text-center">
            <div class="w-12 h-12 rounded-xl bg-[#ecfdf5] text-[#059669] flex items-center justify-center mx-auto">
                <span class="material-symbols-outlined text-2xl">mail</span>
            </div>
            <h3 class="text-base font-bold text-[#001229]">Mesa de Partes Virtual</h3>
            <p class="text-xs text-[#44474d]">Envío de consultas documentales y reclamos.</p>
            <span class="block text-sm font-bold text-[#059669]">soporte@setc.gob.mx</span>
            <span class="text-[11px] text-slate-400 block">Respuesta en menos de 24h</span>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-3 text-center">
            <div class="w-12 h-12 rounded-xl bg-[#eff4ff] text-[#001229] flex items-center justify-center mx-auto">
                <span class="material-symbols-outlined text-2xl">apartment</span>
            </div>
            <h3 class="text-base font-bold text-[#001229]">Ventanilla Presencial</h3>
            <p class="text-xs text-[#44474d]">Módulo central de atención ciudadana.</p>
            <span class="block text-xs font-semibold text-[#001229]">Palacio de Gobierno • Sede Central</span>
            <a href="{{ route('citas.create') }}" class="inline-block text-xs text-[#0051d5] font-semibold hover:underline">
                Agendar cita previa
            </a>
        </div>
    </div>

    {{-- Preguntas Frecuentes (FAQ) --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-8 shadow-xs space-y-6">
        <h2 class="text-xl font-bold text-[#001229] border-b border-slate-100 pb-4" style="font-family: var(--font-display);">
            Preguntas Frecuentes
        </h2>

        <div class="space-y-4 text-sm" x-data="{ active: null }">
            @foreach ([
                ['¿Cómo obtengo mi código de folio para seguimiento?', 'Al iniciar y enviar tu trámite en la ventanilla única digital, el sistema genera de forma automática un código oficial único con formato SETC-YYYY-NNNNN, el cual puedes consultar en tu carpeta ciudadana o en el comprobante emitido.'],
                ['¿Qué formato deben tener los documentos que adjunto?', 'Los formatos aceptados por defecto son PDF, JPG y PNG con un peso máximo de 10 MB por archivo. Asegúrate de que las firmas y sellos sean legibles.'],
                ['¿Qué sucede si un revisor levanta una observación a mi expediente?', 'Recibirás una notificación en el portal. Podrás ingresar al detalle de tu expediente en la sección "Mis Expedientes", leer el detalle del dictamen y enviar una respuesta adjuntando el documento corregido sin iniciar un nuevo trámite.'],
                ['¿Cómo cancelo o reprogramo una cita?', 'Ingresa a la sección "Mis Citas" con tu cuenta ciudadana y presiona el botón "Cancelar". Luego podrás seleccionar un nuevo horario disponible.']
            ] as $i => $faq)
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button type="button"
                            @click="active = (active === {{ $i }} ? null : {{ $i }})"
                            class="w-full p-4 text-left font-semibold text-[#0b1c30] flex items-center justify-between gap-4 hover:bg-slate-50 transition-colors">
                        <span>{{ $faq[0] }}</span>
                        <span class="material-symbols-outlined text-slate-400 text-lg transition-transform" :class="active === {{ $i }} ? 'rotate-180' : ''">
                            expand_more
                        </span>
                    </button>
                    <div x-show="active === {{ $i }}" x-collapse class="px-4 pb-4 pt-1 text-xs text-[#44474d] leading-relaxed border-t border-slate-100 bg-slate-50/50">
                        {{ $faq[1] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

