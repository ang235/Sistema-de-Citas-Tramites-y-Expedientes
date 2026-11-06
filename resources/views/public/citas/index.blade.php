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
            <span class="px-3 py-1 rounded-full bg-[#eff4ff] text-[#0051d5] text-xs font-semibold">Gestión de Turnos</span>
            <h1 class="text-3xl font-bold tracking-tight text-[#001229] mt-2" style="font-family: var(--font-display);">
                Mis Citas Programadas
            </h1>
            <p class="text-sm text-[#44474d] mt-1">
                Visualiza tus horarios reservados, descarga tu pase con código QR o cancela con anticipación.
            </p>
        </div>
        <a href="{{ route('citas.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#001229] hover:bg-[#0f2744] text-white rounded-xl text-xs font-semibold transition-all shadow-sm shrink-0">
            <span class="material-symbols-outlined text-base">add_circle</span>
            <span>Agendar Nueva Cita</span>
        </a>
    </div>

    {{-- Lista de Citas --}}
    <div class="space-y-4">
        @forelse ($citas as $cita)
            @php
                $esCancelada = $cita->Estado_Citas === 'Cancelada';
                $esAtendida = $cita->Estado_Citas === 'Atendida';
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs hover:shadow-md transition-all flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 {{ $esCancelada ? 'bg-slate-100 text-slate-400' : ($esAtendida ? 'bg-[#ecfdf5] text-[#059669]' : 'bg-[#eff4ff] text-[#0051d5]') }}">
                        <span class="material-symbols-outlined text-2xl">calendar_today</span>
                    </div>

                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $esCancelada ? 'bg-slate-100 text-slate-600' : ($esAtendida ? 'bg-[#ecfdf5] text-[#065f46]' : 'bg-[#eff4ff] text-[#0051d5]') }}">
                                {{ $cita->Estado_Citas }}
                            </span>
                            @if ($cita->solicitud)
                                <span class="font-mono text-xs font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                                    {{ $cita->solicitud->folio?->Codigo_Folio ?? 'Exp. #' . $cita->solicitud->ID_Solicitud }}
                                </span>
                            @endif
                        </div>

                        <div>
                            <h2 class="text-base font-bold text-[#0b1c30]">
                                Cita para {{ $cita->Fecha ? $cita->Fecha->format('l, d F Y') : 'Fecha' }} a las {{ $cita->Hora }} hrs
                            </h2>
                            <p class="text-xs text-[#44474d] mt-0.5">
                                {{ $cita->solicitud?->versionTramite?->tipoTramite?->Nombre ?? 'Orientación y Asesoría Técnica General' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="flex items-center gap-3 shrink-0">
                    @if (! $esCancelada)
                        <a href="{{ route('citas.pase', $cita->ID_Citas) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[#eff4ff] hover:bg-[#dce9ff] text-[#0051d5] text-xs font-semibold transition-colors">
                            <span class="material-symbols-outlined text-base">qr_code</span>
                            <span>Ver Pase Digital</span>
                        </a>

                        <form action="{{ route('citas.cancelar', $cita->ID_Citas) }}" method="POST" onsubmit="return confirm('¿Confirmas que deseas cancelar esta cita?');">
                            @csrf
                            <button type="submit" class="px-3 py-2 rounded-lg text-xs font-semibold text-[#ba1a1a] hover:bg-rose-50 transition-colors">
                                Cancelar
                            </button>
                        </form>
                    @else
                        <span class="text-xs text-slate-400 italic">Cita cancelada</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="py-16 text-center space-y-4 bg-white rounded-2xl border border-slate-200/80">
                <span class="material-symbols-outlined text-4xl text-slate-400">event_busy</span>
                <p class="text-base font-semibold text-[#001229]">No tienes citas programadas actualmente.</p>
                <a href="{{ route('citas.create') }}" class="inline-flex items-center gap-1 text-xs text-[#0051d5] font-semibold hover:underline">
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

