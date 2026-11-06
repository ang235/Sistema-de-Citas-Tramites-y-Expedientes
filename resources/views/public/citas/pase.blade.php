@extends('layouts.public')

@section('title', 'Pase Digital de Turno #' . $cita->ID_Citas)
@section('meta_description', 'Comprobante y pase oficial con código QR para turno de atención en SETC.')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="breadcrumb-item">SETC</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <a href="{{ route('citas.index') }}" class="breadcrumb-item">Citas</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <span class="breadcrumb-item current">Pase #{{ $cita->ID_Citas }}</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto px-6 lg:px-12 py-10 w-full space-y-8">
    {{-- Notificación de éxito --}}
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <span class="material-symbols-outlined text-emerald-600 text-xl">check_circle</span>
            <span class="font-semibold">¡Cita confirmada exitosamente! Tu turno ha sido reservado.</span>
        </div>
        <button onclick="window.print();" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition-colors flex items-center gap-1 shrink-0">
            <span class="material-symbols-outlined text-sm">print</span>
            <span>Imprimir Pase</span>
        </button>
    </div>

    {{-- Ticket / Pase Digital --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-md overflow-hidden relative" id="pase-imprimible">
        {{-- Cabecera Institucional del Pase --}}
        <div class="bg-[#001229] text-white p-6 sm:p-8 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#0051d5] flex items-center justify-center text-white">
                    <span class="material-symbols-outlined text-2xl">account_balance</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight" style="font-family: var(--font-display);">SETC • Pase Digital Oficial</h1>
                    <span class="text-xs text-slate-300">Gobierno Digital e Innovación Telemática</span>
                </div>
            </div>
            <div class="text-right">
                <span class="block text-[11px] text-[#85f8c4] font-semibold uppercase tracking-wider">Estado</span>
                <span class="inline-flex items-center gap-1 text-sm font-bold text-white">
                    <span class="w-2 h-2 rounded-full bg-[#10b981]"></span>
                    {{ $cita->Estado_Citas }}
                </span>
            </div>
        </div>

        {{-- Cuerpo del Pase --}}
        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 items-center border-b border-slate-100 pb-8">
                {{-- QR --}}
                <div class="flex flex-col items-center justify-center p-6 bg-[#eff4ff]/60 border border-[#dce9ff] rounded-2xl text-center space-y-3">
                    <div class="p-4 bg-white rounded-xl shadow-xs border border-slate-200 inline-block">
                        <span class="material-symbols-outlined text-7xl text-[#001229]">qr_code_2</span>
                    </div>
                    <div class="space-y-0.5">
                        <span class="text-[11px] text-slate-400 font-mono block">CÓDIGO DE VALIDACIÓN</span>
                        <span class="text-base font-bold font-mono text-[#001229]">TURNO-{{ str_pad((string)$cita->ID_Citas, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>

                {{-- Datos Principales --}}
                <div class="space-y-4 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Ciudadano Solicitante</span>
                        <span class="text-sm font-bold text-[#0b1c30]">{{ $cita->usuario?->Nombre ?? auth()->user()->Nombre }}</span>
                        <span class="text-slate-500 block">{{ $cita->usuario?->Email ?? auth()->user()->Email }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-2">
                        <div>
                            <span class="text-slate-400 block text-[11px]">Fecha Programada</span>
                            <span class="text-sm font-bold text-[#0051d5]">{{ $cita->Fecha ? $cita->Fecha->format('d/m/Y') : '' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Horario de Turno</span>
                            <span class="text-sm font-bold text-[#001229]">{{ $cita->Hora }} hrs</span>
                        </div>
                    </div>

                    @if ($cita->solicitud)
                        <div class="pt-2">
                            <span class="text-slate-400 block text-[11px]">Expediente Vinculado</span>
                            <span class="text-xs font-mono font-bold text-[#0051d5]">{{ $cita->solicitud->folio?->Codigo_Folio ?? '#' . $cita->solicitud->ID_Solicitud }}</span>
                            <span class="text-[11px] text-slate-500 block">{{ $cita->solicitud->versionTramite?->tipoTramite?->Nombre }}</span>
                        </div>
                    @endif

                    <div class="pt-2">
                        <span class="text-slate-400 block text-[11px]">Sede de Atención</span>
                        <span class="text-xs font-semibold text-[#0b1c30]">Sede Central • Mesa de Control y Dictamen</span>
                        <span class="text-[11px] text-slate-500 block">Ventanilla 08 • Atención Ciudadana</span>
                    </div>
                </div>
            </div>

            {{-- Instrucciones --}}
            <div class="space-y-2 text-xs text-[#44474d] bg-slate-50 p-4 rounded-xl border border-slate-200">
                <span class="font-bold text-[#001229] block flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base text-[#0051d5]">info</span>
                    Recomendaciones para el día de tu turno
                </span>
                <ul class="list-disc list-inside space-y-1 text-slate-600 pl-1">
                    <li>Presenta este código QR impreso o en la pantalla de tu teléfono 10 minutos antes de tu horario.</li>
                    <li>Porta tu identificación oficial vigente con fotografía.</li>
                    <li>Si tu cita es para revisión de expediente, asegúrate de haber cargado tus recaudos en el sistema.</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Botones de Navegación --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('citas.index') }}" class="text-xs text-[#0051d5] font-semibold hover:underline flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span>Volver a mis citas</span>
        </a>
        <a href="{{ route('expedientes.index') }}" class="text-xs text-slate-600 hover:text-[#001229] font-medium">
            Ir a mis expedientes
        </a>
    </div>
</div>
@endsection

