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
        <span class="px-3.5 py-1.5 rounded-full bg-[#ecfdf5] dark:bg-emerald-950/60 text-[#065f46] dark:text-emerald-300 text-xs font-semibold">
            Centro de Asistencia Ciudadana
        </span>
        <h1 class="text-3xl lg:text-4xl font-bold tracking-tight text-[#001229] dark:text-white" style="font-family: var(--font-display);">
            ¿Cómo podemos orientarte hoy?
        </h1>
        <p class="text-sm text-[#44474d] dark:text-slate-400">
            Encuentra respuestas a preguntas frecuentes sobre trámites, citas, formatos y requisitos del sistema SETC.
        </p>
    </div>

    {{-- Canales de Soporte --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-[#0e1c2e] p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3 text-center">
            <div class="w-12 h-12 rounded-xl bg-[#eff4ff] dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-400 flex items-center justify-center mx-auto">
                <span class="material-symbols-outlined text-2xl">call</span>
            </div>
            <h3 class="text-base font-bold text-[#001229] dark:text-white">Atención Telefónica</h3>
            <p class="text-xs text-[#44474d] dark:text-slate-400">Línea directa para asesoría normativa.</p>
            <span class="block text-sm font-bold text-[#0051d5] dark:text-blue-400 font-mono">01-800-SETC-GOB</span>
            <span class="text-[11px] text-slate-400 dark:text-slate-500 block">Lun a Vie • 08:00 a 17:00 hrs</span>
        </div>

        <div class="bg-white dark:bg-[#0e1c2e] p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3 text-center">
            <div class="w-12 h-12 rounded-xl bg-[#ecfdf5] dark:bg-emerald-950/60 text-[#059669] dark:text-emerald-400 flex items-center justify-center mx-auto">
                <span class="material-symbols-outlined text-2xl">mail</span>
            </div>
            <h3 class="text-base font-bold text-[#001229] dark:text-white">Mesa de Partes Virtual</h3>
            <p class="text-xs text-[#44474d] dark:text-slate-400">Envío de consultas documentales y reclamos.</p>
            <span class="block text-sm font-bold text-[#059669] dark:text-emerald-400">soporte@setc.gob.mx</span>
            <span class="text-[11px] text-slate-400 dark:text-slate-500 block">Respuesta en menos de 24h</span>
        </div>

        <div class="bg-white dark:bg-[#0e1c2e] p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3 text-center">
            <div class="w-12 h-12 rounded-xl bg-[#eff4ff] dark:bg-blue-950/60 text-[#001229] dark:text-blue-400 flex items-center justify-center mx-auto">
                <span class="material-symbols-outlined text-2xl">apartment</span>
            </div>
            <h3 class="text-base font-bold text-[#001229] dark:text-white">Ventanilla Presencial</h3>
            <p class="text-xs text-[#44474d] dark:text-slate-400">Módulo central de atención ciudadana.</p>
            <span class="block text-xs font-semibold text-[#001229] dark:text-slate-300">Palacio de Gobierno • Sede Central</span>
            <a href="{{ route('citas.create') }}" class="inline-block text-xs text-[#0051d5] dark:text-blue-400 font-semibold hover:underline">
                Agendar cita previa
            </a>
        </div>
    </div>

    {{-- Preguntas Frecuentes (FAQ) --}}
    <div class="space-y-4" x-data="{ openFaq: null }">
        <h2 class="text-xl font-bold text-[#001229] dark:text-white" style="font-family: var(--font-display);">
            Preguntas Frecuentes
        </h2>

        @php
            $faqs = [
                ['q' => '¿Cómo doy seguimiento a un trámite que ya inicié?', 'a' => 'Puedes consultar en tiempo real el avance de tu expediente ingresando a la sección "Mis Expedientes" con tu correo y contraseña registrados, o bien ingresando el folio oficial en el buscador.'],
                ['q' => '¿Qué formato deben tener los requisitos que adjunto?', 'a' => 'Todos los documentos obligatorios deben ser adjuntados exclusivamente en formato PDF digitalizado o con firma electrónica válida. El tamaño máximo admitido es de 10 MB por archivo.'],
                ['q' => '¿Es necesario agendar cita para todos los trámites?', 'a' => 'No. La mayoría de los trámites son 100% telemáticos y su dictamen se emite en línea. Las citas presenciales o virtuales están reservadas para inspecciones técnicas reglamentarias (como ITSE) o aclaraciones personalizadas una vez aprobados los requisitos.'],
                ['q' => '¿Dónde descargo mi resolución oficial?', 'a' => 'Una vez emitido el dictamen favorable y la resolución firmada digitalmente por la autoridad municipal, encontrarás el archivo descargable con código QR en la vista de trazabilidad de tu expediente.'],
            ];
        @endphp

        <div class="space-y-3">
            @foreach ($faqs as $i => $faq)
                <div class="bg-white dark:bg-[#0e1c2e] rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
                    <button type="button"
                            @click="openFaq === {{ $i }} ? openFaq = null : openFaq = {{ $i }}"
                            class="w-full p-4 text-left flex items-center justify-between gap-4 font-semibold text-sm text-[#001229] dark:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                        <span>{{ $faq['q'] }}</span>
                        <span class="material-symbols-outlined text-slate-400 transition-transform duration-200"
                              :class="{ 'rotate-180': openFaq === {{ $i }} }">
                            expand_more
                        </span>
                    </button>
                    <div x-show="openFaq === {{ $i }}" x-collapse class="px-4 pb-4 pt-1 text-xs text-slate-600 dark:text-slate-300 leading-relaxed border-t border-slate-100 dark:border-slate-800">
                        {{ $faq['a'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
