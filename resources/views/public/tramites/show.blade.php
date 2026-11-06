@extends('layouts.public')

@section('title', $tramite->Nombre)
@section('meta_description', 'Consulta los requisitos y formatos requeridos para iniciar ' . $tramite->Nombre)

@section('breadcrumb')
    <a href="{{ route('home') }}" class="breadcrumb-item">SETC</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <a href="{{ route('tramites.index') }}" class="breadcrumb-item">Trámites y Servicios</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <span class="breadcrumb-item current">{{ Str::limit($tramite->Nombre, 25) }}</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-12 py-8 w-full space-y-8">
    {{-- Banner Principal --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-8 shadow-xs relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-[#eff4ff] blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-start justify-between gap-6">
            <div class="space-y-4 max-w-3xl">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 rounded-full bg-[#eff4ff] text-[#0051d5] text-xs font-semibold">
                        Trámite {{ $tramite->Estado_Tipo_Tr }}
                    </span>
                    @if ($versionVigente)
                        <span class="px-3 py-1 rounded-full bg-slate-100 text-[#44474d] text-xs font-semibold font-mono">
                            Versión {{ $versionVigente->No_Version }} (Vigente)
                        </span>
                    @endif
                </div>

                <h1 class="text-3xl font-bold tracking-tight text-[#001229]" style="font-family: var(--font-display);">
                    {{ $tramite->Nombre }}
                </h1>

                <p class="text-base text-[#44474d] leading-relaxed">
                    {{ $tramite->Descripcion ?? 'Este trámite telemático permite la emisión de dictamen y resolución oficial sin necesidad de acudir a ventanillas físicas salvo para inspección técnica.' }}
                </p>

                <div class="flex flex-wrap items-center gap-6 pt-2 text-xs text-[#44474d]">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#0051d5]">timer</span>
                        <span>Plazo de resolución: <strong>3 a 5 días hábiles</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#059669]">verified_user</span>
                        <span>Firma y validación: <strong>Digital Certificada</strong></span>
                    </div>
                </div>
            </div>

            {{-- Botón CTA --}}
            <div class="shrink-0 flex flex-col gap-3">
                @auth
                    <a href="{{ route('solicitudes.create', $tramite->ID_Tipo_Tramite) }}" class="h-12 px-6 rounded-xl bg-[#001229] hover:bg-[#0f2744] text-white text-sm font-semibold transition-all flex items-center justify-center gap-2 shadow-sm">
                        <span class="material-symbols-outlined text-lg">play_arrow</span>
                        <span>Iniciar Trámite Ahora</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="h-12 px-6 rounded-xl bg-[#001229] hover:bg-[#0f2744] text-white text-sm font-semibold transition-all flex items-center justify-center gap-2 shadow-sm">
                        <span class="material-symbols-outlined text-lg">login</span>
                        <span>Iniciar Sesión para Tramitar</span>
                    </a>
                @endauth
                <a href="{{ route('citas.create') }}" class="h-11 px-6 rounded-xl bg-[#eff4ff] hover:bg-[#dce9ff] text-[#0051d5] text-xs font-semibold transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-base">calendar_month</span>
                    <span>Agendar orientación previa</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Requisitos Normativos --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-8 shadow-xs space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-xl font-bold text-[#001229]" style="font-family: var(--font-display);">
                    Requisitos y Documentación Obligatoria
                </h2>
                <p class="text-xs text-[#44474d] mt-1">
                    Prepara estos documentos en formato digital para adjuntarlos al iniciar tu expediente.
                </p>
            </div>
            <span class="material-symbols-outlined text-slate-400">task_alt</span>
        </div>

        @if ($versionVigente && $versionVigente->requisitos->isNotEmpty())
            <div class="divide-y divide-slate-100">
                @foreach ($versionVigente->requisitos as $index => $req)
                    <div class="py-4 flex items-start justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-lg bg-[#eff4ff] text-[#0051d5] font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                {{ $index + 1 }}
                            </div>
                            <div class="space-y-1">
                                <h3 class="text-sm font-semibold text-[#0b1c30]">
                                    {{ $req->Nombre }}
                                </h3>
                                <p class="text-xs text-slate-500">
                                    Formato aceptado: <span class="font-mono font-medium text-[#0051d5] uppercase">{{ $req->Formato }}</span> (Máx. 10MB)
                                </p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold shrink-0 {{ $req->Prioridad === 'Obligatorio' ? 'bg-amber-50 text-amber-900 border border-amber-200/60' : 'bg-slate-100 text-slate-600' }}">
                            {{ $req->Prioridad }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center bg-slate-50 rounded-xl text-slate-500 text-sm">
                No hay requisitos normativos registrados aún para esta versión del trámite.
            </div>
        @endif
    </div>
</div>
@endsection

