@extends('layouts.public')

@section('title', 'Catálogo de Trámites y Servicios')
@section('meta_description', 'Explora todos los trámites normativos disponibles para gestión telemática en el SETC.')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="breadcrumb-item">SETC</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <span class="breadcrumb-item current">Trámites y Servicios</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-12 py-8 w-full space-y-8">
    {{-- Cabecera de Página --}}
    <div class="space-y-3">
        <span class="px-3 py-1 rounded-full bg-[#eff4ff] dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300 text-xs font-semibold">Catálogo Normativo TUPA</span>
        <h1 class="text-3xl font-bold tracking-tight text-[#001229] dark:text-white" style="font-family: var(--font-display);">
            Trámites y Servicios al Ciudadano
        </h1>
        <p class="text-sm text-[#44474d] dark:text-slate-400 max-w-3xl">
            Inicia tus gestiones administrativas con requisitos digitalizados, validaciones en línea y seguimiento por folio único en tiempo real.
        </p>
    </div>

    {{-- Barra de Filtros y Búsqueda --}}
    <div class="p-4 rounded-xl bg-white dark:bg-[#0e1c2e] border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col md:flex-row items-center gap-4">
        <form action="{{ route('tramites.index') }}" method="GET" class="relative flex-1 w-full">
            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-lg pointer-events-none">search</span>
            <input type="text"
                   name="buscar"
                   value="{{ $busqueda ?? request('buscar') }}"
                   placeholder="Filtrar por nombre del trámite o descripción..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0b1624] text-sm text-[#0b1c30] dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
        </form>
        @if (!empty($busqueda ?? request('buscar')))
            <a href="{{ route('tramites.index') }}" class="text-xs text-[#ba1a1a] dark:text-rose-400 font-semibold hover:underline flex items-center gap-1 shrink-0">
                <span class="material-symbols-outlined text-sm">close</span>
                Limpiar búsqueda
            </a>
        @endif
    </div>

    {{-- Grid de Trámites --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($tramites as $tramite)
            @php
                $versionVigente = $tramite->versiones->where('Estado_Ver_Tr', 'Vigente')->first() ?? $tramite->versiones->first();
                $requisitosCount = $versionVigente ? $versionVigente->requisitos->count() : 0;
            @endphp
            <div class="bg-white dark:bg-[#0e1c2e] rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs hover:shadow-md transition-all flex flex-col justify-between group">
                <div class="space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="w-11 h-11 rounded-xl bg-[#eff4ff] dark:bg-blue-950/60 flex items-center justify-center text-[#001229] dark:text-blue-400 group-hover:bg-[#0051d5] group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-2xl">description</span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full bg-[#ecfdf5] dark:bg-emerald-950/60 text-[#065f46] dark:text-emerald-300 text-xs font-semibold">
                            Activo
                        </span>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-[#001229] dark:text-white group-hover:text-[#0051d5] dark:group-hover:text-blue-400 transition-colors leading-snug">
                            {{ $tramite->Nombre }}
                        </h2>
                        <p class="text-xs text-[#44474d] dark:text-slate-400 mt-2 line-clamp-3 leading-relaxed">
                            {{ $tramite->Descripcion ?? 'Trámite digital normado conforme a los estándares de ventanilla única SETC.' }}
                        </p>
                    </div>

                    {{-- Badges de Metadatos --}}
                    <div class="flex flex-wrap items-center gap-2 pt-2 text-xs text-[#44474d] dark:text-slate-400">
                        @if ($versionVigente)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-[#eff4ff] dark:bg-blue-950/60 text-[#0051d5] dark:text-blue-300 font-medium">
                                <span class="material-symbols-outlined text-xs">tune</span>
                                Ver. {{ $versionVigente->No_Version }}
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-[#44474d] dark:text-slate-300">
                                <span class="material-symbols-outlined text-xs">checklist</span>
                                {{ $requisitosCount }} {{ Str::plural('requisito', $requisitosCount) }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="pt-6 mt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <a href="{{ route('tramites.show', $tramite->ID_Tipo_Tramite) }}" class="text-xs font-semibold text-[#0051d5] dark:text-blue-400 hover:underline flex items-center gap-1">
                        <span>Ver requisitos</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                    @auth
                        <a href="{{ route('solicitudes.create', $tramite->ID_Tipo_Tramite) }}" class="px-3.5 py-1.5 rounded-lg bg-[#001229] dark:bg-blue-600 hover:bg-[#0f2744] dark:hover:bg-blue-700 text-white text-xs font-semibold transition-colors shadow-xs cursor-pointer">
                            Iniciar Trámite
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-3.5 py-1.5 rounded-lg bg-[#001229] dark:bg-blue-600 hover:bg-[#0f2744] dark:hover:bg-blue-700 text-white text-xs font-semibold transition-colors shadow-xs cursor-pointer">
                            Iniciar Trámite
                        </a>
                    @endauth
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center space-y-3 bg-white dark:bg-[#0e1c2e] rounded-2xl border border-slate-200/80 dark:border-slate-800">
                <span class="material-symbols-outlined text-4xl text-slate-400">search_off</span>
                <p class="text-base font-semibold text-[#001229] dark:text-white">No se encontraron trámites con esos criterios.</p>
                <a href="{{ route('tramites.index') }}" class="inline-block text-sm text-[#0051d5] dark:text-blue-400 hover:underline">
                    Ver todos los trámites
                </a>
            </div>
        @endforelse
    </div>

    {{-- Paginación si existe --}}
    @if($tramites instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="pt-4">
            {{ $tramites->links() }}
        </div>
    @endif
</div>
@endsection
