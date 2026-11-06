@extends('layouts.public')

@section('title', 'Ventanilla Única Digital')
@section('meta_description', 'Portal ciudadano de SETC: inicia solicitudes telemáticas, consulta tus expedientes y agenda citas presenciales o virtuales.')

@section('breadcrumb')
    <span class="hover:text-on-surface cursor-pointer">SETC</span>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="text-on-surface font-semibold">Ventanilla Digital</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-12 py-8 w-full space-y-12">

    {{-- ══════════════════════════════════════════ HERO DE BIENVENIDA & BUSCADOR ══ --}}
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#eff4ff] via-[#f8f9ff] to-[#e5eeff] border border-[#dce9ff] shadow-xs p-8 lg:p-12">
        <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-[#0051d5]/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-4xl space-y-5">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white text-[#0051d5] text-xs font-semibold shadow-xs border border-blue-100">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Ventanilla Única Digital Ciudadana</span>
            </div>

            <div class="space-y-2">
                <h1 class="text-3xl lg:text-4xl font-extrabold text-[#001229] tracking-tight" style="font-family: var(--font-display);">
                    @auth
                        Bienvenido, {{ auth()->user()->Nombre ?? auth()->user()->name }}.
                    @else
                        Bienvenido al Sistema Estatal de Trámites y Citas (SETC).
                    @endauth
                    <span class="text-[#0051d5] block font-semibold text-2xl lg:text-3xl mt-1">
                        ¿Qué gestión o trámite deseas realizar hoy?
                    </span>
                </h1>
                <p class="text-slate-600 text-sm lg:text-base leading-relaxed max-w-2xl">
                    Realiza tus trámites institucionales 100% en línea, sin intermediarios, con trazabilidad garantizada por firma digital y sello notarial.
                </p>
            </div>

            {{-- Buscador Principal --}}
            <div class="relative max-w-2xl pt-2">
                <form action="{{ route('tramites.index') }}" method="GET" class="relative flex items-center shadow-sm rounded-xl bg-white border border-slate-300 focus-within:border-[#0051d5] focus-within:ring-2 focus-within:ring-[#0051d5]/20 overflow-hidden transition-all">
                    <span class="material-symbols-outlined text-[#0051d5] ml-4 mr-2 text-xl select-none">search</span>
                    <input type="text"
                           name="buscar"
                           value="{{ request('buscar') }}"
                           class="w-full py-3.5 pr-4 bg-transparent text-sm text-[#0b1c30] placeholder:text-slate-400 focus:outline-none"
                           placeholder="Buscar por trámite, norma o palabra clave (ej. Licencia, ITSE, Catastro)..." />
                    <button type="submit" class="m-1.5 px-5 py-2.5 bg-[#001229] hover:bg-[#0f2744] text-white text-xs font-semibold rounded-lg transition-colors shrink-0 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm">search</span>
                        <span>Buscar</span>
                    </button>
                </form>

                {{-- Búsquedas Frecuentes / Chips --}}
                <div class="flex items-center gap-2 mt-3 flex-wrap text-xs">
                    <span class="text-slate-500 font-medium">Sugerencias:</span>
                    <a href="{{ route('tramites.index', ['buscar' => 'Licencia']) }}" class="px-2.5 py-1 rounded-full bg-white text-slate-700 hover:bg-blue-50 hover:text-[#0051d5] border border-slate-200 transition-colors shadow-2xs font-medium">
                        Licencia de Funcionamiento
                    </a>
                    <a href="{{ route('tramites.index', ['buscar' => 'ITSE']) }}" class="px-2.5 py-1 rounded-full bg-white text-slate-700 hover:bg-blue-50 hover:text-[#0051d5] border border-slate-200 transition-colors shadow-2xs font-medium">
                        Inspección Técnica ITSE
                    </a>
                    <a href="{{ route('tramites.index', ['buscar' => 'Ambiental']) }}" class="px-2.5 py-1 rounded-full bg-white text-slate-700 hover:bg-blue-50 hover:text-[#0051d5] border border-slate-200 transition-colors shadow-2xs font-medium">
                        Certificación Ambiental
                    </a>
                    <a href="{{ route('tramites.index', ['buscar' => 'Copia']) }}" class="px-2.5 py-1 rounded-full bg-white text-slate-700 hover:bg-blue-50 hover:text-[#0051d5] border border-slate-200 transition-colors shadow-2xs font-medium">
                        Copia Certificada
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════ CATÁLOGO DE TRÁMITES DISPONIBLES ══ --}}
    <section class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-bold text-[#001229] tracking-tight" style="font-family: var(--font-display);">
                    Catálogo Oficial de Trámites y Procedimientos
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Selecciona un trámite para consultar sus requisitos obligatorios o iniciar tu solicitud telemática.
                </p>
            </div>
            <a href="{{ route('tramites.index') }}" class="inline-flex items-center gap-1.5 text-xs text-[#0051d5] font-semibold hover:underline">
                <span>Ver todos los trámites</span>
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $tramitesCatalogo = $tramites ?? \App\Models\TipoTramite::where('Estado_Tipo_Tr', 'Activo')->with(['versiones' => fn($q) => $q->where('Estado_Ver_Tr', 'Vigente')->with('requisitos')])->get();
            @endphp
            @forelse ($tramitesCatalogo as $tramite)
                @php
                    $version = $tramite->versiones->where('Estado_Ver_Tr', 'Vigente')->first() ?? $tramite->versiones->first();
                    $reqsCount = $version ? $version->requisitos->count() : 0;
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-xs hover:border-[#0051d5]/40 hover:shadow-md transition-all flex flex-col justify-between group">
                    <div class="space-y-4">
                        <div class="flex items-start justify-between">
                            <div class="w-11 h-11 rounded-xl bg-[#eff4ff] text-[#001229] flex items-center justify-center group-hover:bg-[#001229] group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-2xl">description</span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                100% Digital
                            </span>
                        </div>

                        <div>
                            <h3 class="text-base font-bold text-[#001229] group-hover:text-[#0051d5] transition-colors leading-snug">
                                {{ $tramite->Nombre }}
                            </h3>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                                {{ $tramite->Descripcion ?? 'Procedimiento administrativo oficial conforme a la normativa municipal vigente.' }}
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 text-[11px] text-slate-500">
                            @if ($version)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 font-mono">
                                    v{{ $version->No_Version }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-blue-50 text-[#0051d5] font-semibold">
                                    <span class="material-symbols-outlined text-xs">checklist</span>
                                    {{ $reqsCount }} {{ Str::plural('requisito', $reqsCount) }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 text-slate-600">
                                <span class="material-symbols-outlined text-xs">schedule</span>
                                48h - 72h SLA
                            </span>
                        </div>
                    </div>

                    <div class="pt-5 mt-5 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('tramites.show', $tramite->ID_Tipo_Tramite) }}" class="text-xs font-semibold text-[#0051d5] hover:underline flex items-center gap-1">
                            <span>Requisitos</span>
                            <span class="material-symbols-outlined text-sm">chevron_right</span>
                        </a>

                        @auth
                            <a href="{{ route('solicitudes.create', $tramite->ID_Tipo_Tramite) }}" class="px-3.5 py-2 rounded-lg bg-[#001229] hover:bg-[#0f2744] text-white text-xs font-semibold transition-colors shadow-xs flex items-center gap-1.5">
                                <span>Iniciar Trámite</span>
                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-3.5 py-2 rounded-lg bg-[#001229] hover:bg-[#0f2744] text-white text-xs font-semibold transition-colors shadow-xs flex items-center gap-1.5">
                                <span>Iniciar Trámite</span>
                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        @endauth
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center space-y-3 bg-white rounded-2xl border border-slate-200">
                    <span class="material-symbols-outlined text-4xl text-slate-400">search_off</span>
                    <p class="text-base font-semibold text-[#001229]">No se encontraron trámites registrados.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ══════════════════════════════════════════ ACCESOS RÁPIDOS COMPLEMENTARIOS ══ --}}
    <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Tarjeta: Consultar Mis Expedientes --}}
        <div class="p-6 rounded-2xl bg-gradient-to-br from-white to-[#eff4ff] border border-slate-200 shadow-xs flex items-center justify-between gap-4">
            <div class="space-y-2">
                <div class="w-10 h-10 rounded-xl bg-[#0051d5] text-white flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">folder_shared</span>
                </div>
                <h3 class="text-base font-bold text-[#001229]">¿Ya iniciaste un trámite?</h3>
                <p class="text-xs text-slate-500">Consulta el estado, observaciones y dictámenes de tus expedientes ingresando con tu cuenta.</p>
                <div class="pt-2">
                    <a href="{{ route('expedientes.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#001229] hover:bg-[#0f2744] text-white text-xs font-semibold rounded-lg transition-colors">
                        <span>Ver Mis Expedientes</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Tarjeta: Agenda de Citas --}}
        <div class="p-6 rounded-2xl bg-gradient-to-br from-white to-emerald-50/50 border border-slate-200 shadow-xs flex items-center justify-between gap-4">
            <div class="space-y-2">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">calendar_month</span>
                </div>
                <h3 class="text-base font-bold text-[#001229]">Atención Presencial y Virtual</h3>
                <p class="text-xs text-slate-500">Agenda tu turno telemático o presencial en ventanilla metropolitana sin hacer filas.</p>
                <div class="pt-2">
                    <a href="{{ route('citas.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-lg transition-colors">
                        <span>Agendar Cita</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
