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
    <div class="bg-white dark:bg-[#0e1c2e] rounded-2xl border border-slate-200/80 dark:border-slate-800 p-8 shadow-xs relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-[#eff4ff] dark:bg-blue-950/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-start justify-between gap-6">
            <div class="space-y-4 max-w-3xl">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 rounded-full bg-[#eff4ff] dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300 text-xs font-semibold">
                        Trámite {{ $tramite->Estado_Tipo_Tr }}
                    </span>
                    @if ($versionVigente)
                        <span class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-[#44474d] dark:text-slate-300 text-xs font-semibold font-mono">
                            Versión {{ $versionVigente->No_Version }} (Vigente)
                        </span>
                    @endif
                </div>

                <h1 class="text-3xl font-bold tracking-tight text-[#001229] dark:text-white" style="font-family: var(--font-display);">
                    {{ $tramite->Nombre }}
                </h1>

                <p class="text-base text-[#44474d] dark:text-slate-300 leading-relaxed">
                    {{ $tramite->Descripcion ?? 'Este trámite telemático permite la emisión de dictamen y resolución oficial sin necesidad de acudir a ventanillas físicas salvo para inspección técnica.' }}
                </p>

                <div class="flex flex-wrap items-center gap-6 pt-2 text-xs text-[#44474d] dark:text-slate-400">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#0051d5] dark:text-blue-400">timer</span>
                        <span>Plazo de resolución: <strong class="text-[#001229] dark:text-white">3 a 5 días hábiles</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#059669]">verified_user</span>
                        <span>Firma y validación: <strong class="text-[#001229] dark:text-white">Digital Certificada</strong></span>
                    </div>
                </div>
            </div>

            {{-- Botón CTA --}}
            <div class="shrink-0 flex flex-col gap-3">
                @auth
                    <a href="{{ route('solicitudes.create', $tramite->ID_Tipo_Tramite) }}" class="h-12 px-6 rounded-xl bg-[#001229] dark:bg-blue-600 hover:bg-[#0f2744] dark:hover:bg-blue-700 text-white text-sm font-semibold transition-all flex items-center justify-center gap-2 shadow-sm cursor-pointer">
                        <span class="material-symbols-outlined text-lg">play_arrow</span>
                        <span>Iniciar Trámite Ahora</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="h-12 px-6 rounded-xl bg-[#001229] dark:bg-blue-600 hover:bg-[#0f2744] dark:hover:bg-blue-700 text-white text-sm font-semibold transition-all flex items-center justify-center gap-2 shadow-sm cursor-pointer">
                        <span class="material-symbols-outlined text-lg">login</span>
                        <span>Iniciar Sesión para Tramitar</span>
                    </a>
                @endauth
                <a href="{{ route('citas.create') }}" class="h-11 px-6 rounded-xl bg-[#eff4ff] dark:bg-slate-800 hover:bg-[#dce9ff] dark:hover:bg-slate-700 text-[#0051d5] dark:text-blue-300 text-xs font-semibold transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-base">calendar_month</span>
                    <span>Agendar orientación previa</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Requisitos Normativos --}}
    <div class="bg-white dark:bg-[#0e1c2e] rounded-2xl border border-slate-200/80 dark:border-slate-800 p-8 shadow-xs space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h2 class="text-xl font-bold text-[#001229] dark:text-white" style="font-family: var(--font-display);">
                    Requisitos y Documentación Obligatoria
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Documentos indispensables requeridos por la Mesa de Calificación Documental.
                </p>
            </div>
            @if ($versionVigente)
                <span class="text-xs font-semibold text-[#0051d5] dark:text-blue-300 bg-[#eff4ff] dark:bg-blue-950/60 px-3 py-1 rounded-full">
                    {{ $versionVigente->requisitos->count() }} {{ Str::plural('requisito', $versionVigente->requisitos->count()) }}
                </span>
            @endif
        </div>

        @if ($versionVigente && $versionVigente->requisitos->isNotEmpty())
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach ($versionVigente->requisitos as $index => $req)
                    <div class="py-4 flex items-start justify-between gap-4 first:pt-0 last:pb-0">
                        <div class="flex items-start gap-4">
                            <span class="w-7 h-7 rounded-full bg-[#001229] dark:bg-blue-600 text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                                {{ $index + 1 }}
                            </span>
                            <div class="space-y-1">
                                <h3 class="text-sm font-bold text-[#001229] dark:text-white">
                                    {{ $req->Nombre }}
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $req->Descripcion ?? 'Presentación telemática en formato digital certificado.' }}
                                </p>
                                <div class="flex items-center gap-2 pt-1 text-[11px] text-slate-400 dark:text-slate-500 font-mono">
                                    <span>Formato: {{ $req->Formato ?? 'PDF' }}</span>
                                    <span>•</span>
                                    <span>Obligatoriedad: {{ $req->Prioridad ?? 'Requerido' }}</span>
                                </div>
                            </div>
                        </div>

                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold shrink-0 {{ ($req->Prioridad ?? 'Obligatorio') === 'Obligatorio' ? 'bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                            {{ $req->Prioridad ?? 'Obligatorio' }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-500 dark:text-slate-400 py-4">
                No hay requisitos registrados para esta versión de trámite.
            </p>
        @endif
    </div>
</div>
@endsection
