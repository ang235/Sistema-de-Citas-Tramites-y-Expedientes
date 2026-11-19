@extends('layouts.public')

@section('title', 'Mis Citas')
@section('meta_description', 'Consulta y administra tus citas de atención ciudadana e inspecciones técnicas en el SETC.')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="breadcrumb-item">SETC</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <span class="breadcrumb-item current">Mis Citas</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-12 py-8 w-full space-y-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 rounded-full bg-[#eff4ff] dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300 text-xs font-semibold">Gestión de Turnos</span>
            <h1 class="text-3xl font-bold tracking-tight text-[#001229] dark:text-white mt-2" style="font-family: var(--font-display);">
                Mis Citas Programadas
            </h1>
            <p class="text-sm text-[#44474d] dark:text-slate-400 mt-1">
                Visualiza tus horarios reservados, descarga tu pase con código QR o cancela con anticipación.
            </p>
        </div>
        <a href="{{ route('citas.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#001229] hover:bg-[#0f2744] dark:bg-blue-600 dark:hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition-all shadow-sm shrink-0">
            <span class="material-symbols-outlined text-base">add_circle</span>
            <span>Agendar Nueva Cita</span>
        </a>
    </div>

    {{-- Aclaración sobre anticipación y reglas --}}
    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/90 dark:border-amber-800 text-amber-950 dark:text-amber-200 flex items-center gap-3 text-xs">
        <span class="material-symbols-outlined text-amber-700 dark:text-amber-400 text-xl shrink-0">info</span>
        <p>
            <strong>Aclaración:</strong> Las citas solo pueden agendarse tras la aprobación documental del revisor técnico (plazo de 15 días posteriores) y se permite una sola cita activa por usuario.
        </p>
    </div>

    {{-- Lista de Citas --}}
    <div class="space-y-4">
        @forelse ($citas as $cita)
            @php
                $esCancelada = $cita->Estado_Citas === 'Cancelada';
                $esAtendida = $cita->Estado_Citas === 'Atendida';
            @endphp
            <div class="bg-white dark:bg-[#111f33] rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs hover:shadow-md transition-all flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 {{ $esCancelada ? 'bg-slate-100 dark:bg-slate-800 text-slate-400' : ($esAtendida ? 'bg-[#ecfdf5] dark:bg-emerald-950/60 text-[#059669] dark:text-emerald-400' : 'bg-[#eff4ff] dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300') }}">
                        <span class="material-symbols-outlined text-2xl">calendar_today</span>
                    </div>

                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $esCancelada ? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' : ($esAtendida ? 'bg-[#ecfdf5] dark:bg-emerald-950/60 text-[#065f46] dark:text-emerald-300' : 'bg-[#eff4ff] dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300') }}">
                                {{ $cita->Estado_Citas }}
                            </span>
                            @if ($cita->solicitud)
                                <span class="font-mono text-xs font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-700">
                                    {{ $cita->solicitud->folio?->Codigo_Folio ?? 'Exp. #' . $cita->solicitud->ID_Solicitud }}
                                </span>
                            @endif
                        </div>

                        <div>
                            <h2 class="text-base font-bold text-[#0b1c30] dark:text-white capitalize">
                                Cita para {{ $cita->Fecha ? \Carbon\Carbon::parse($cita->Fecha)->translatedFormat('l, d \d\e F \d\e Y') : 'Fecha' }} a las {{ substr((string)$cita->Hora, 0, 5) }} hrs
                            </h2>
                            <p class="text-xs text-[#44474d] dark:text-slate-400 mt-0.5">
                                {{ $cita->solicitud?->versionTramite?->tipoTramite?->Nombre ?? 'Orientación y Asesoría Técnica General' }}
                            </p>
                            @if ($cita->revisor)
                                <p class="text-xs text-[#0051d5] dark:text-blue-400 font-medium mt-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">badge</span>
                                    <span>Especialista asignado: {{ $cita->revisor->Nombre }}</span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="flex items-center gap-3 shrink-0">
                    @if (! $esCancelada)
                        <a href="{{ route('citas.pase', $cita->ID_Citas) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#eff4ff] hover:bg-[#dce9ff] dark:bg-blue-950/60 dark:hover:bg-blue-900/60 text-[#0051d5] dark:text-blue-300 text-xs font-semibold transition-colors">
                            <span class="material-symbols-outlined text-base">qr_code</span>
                            <span>Ver Pase</span>
                        </a>

                        <a href="{{ route('citas.pase.pdf', $cita->ID_Citas) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#001229] hover:bg-[#00224d] text-white text-xs font-semibold transition-colors shadow-xs" title="Descargar Pase en PDF oficial con QR">
                            <span class="material-symbols-outlined text-base text-rose-400">picture_as_pdf</span>
                            <span>PDF</span>
                        </a>

                        <form action="{{ route('citas.cancelar', $cita->ID_Citas) }}" method="POST" onsubmit="return confirm('¿Confirmas que deseas cancelar esta cita?');">
                            @csrf
                            <button type="submit" class="px-3 py-2 rounded-lg text-xs font-semibold text-[#ba1a1a] dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition-colors">
                                Cancelar
                            </button>
                        </form>
                    @else
                        <span class="text-xs text-slate-400 dark:text-slate-500 italic">Cita cancelada</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="py-16 text-center space-y-4 bg-white dark:bg-[#111f33] rounded-2xl border border-slate-200/80 dark:border-slate-800">
                <span class="material-symbols-outlined text-4xl text-slate-400">event_busy</span>
                <p class="text-base font-semibold text-[#001229] dark:text-white">No tienes citas programadas actualmente.</p>
                <a href="{{ route('citas.create') }}" class="inline-flex items-center gap-1 text-xs text-[#0051d5] dark:text-blue-400 font-semibold hover:underline">
                    <span>Agendar tu primer turno</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        @endforelse
    </div>

    @if($citas instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="pt-4">
            {{ $citas->links() }}
        </div>
    @endif
</div>
@endsection

