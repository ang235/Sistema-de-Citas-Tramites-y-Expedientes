@extends('layouts.portal')

@section('title', 'Mesa Técnica de Calificación y Dictamen')

@section('breadcrumb')
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span>Operaciones</span>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span>Mesa de Dictamen</span>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="current">Expediente Activo</span>
@endsection

@section('content')

{{-- ═══════════════════════════════════ HEADER / CONTEXTO SALA ══ --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4 bg-white rounded-xl shadow-sm border border-slate-200/80 p-5">
    <div class="flex flex-col gap-1">
        <h1 class="font-bold text-xl text-[#0b1c30] tracking-tight" style="font-family: var(--font-display);">
            Mesa Técnica de Calificación y Dictamen
        </h1>
        <div class="flex items-center gap-3 mt-1">
            <span class="px-2.5 py-0.5 rounded-full bg-[#e5eeff] text-[#0b1c30] text-xs font-semibold">Sala V-04</span>
            <div class="flex items-center gap-2 bg-[#eff4ff] px-3 py-1.5 rounded-lg">
                <span class="relative flex items-center justify-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#0051d5]"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-[#0051d5] animate-ping absolute opacity-75"></span>
                </span>
                <div class="flex flex-col">
                    <span class="text-xs font-semibold text-[#0b1c30]">Revisor Jurídico L2: {{ auth()->user()->name ?? 'Ing. Carlos Ruiz' }}</span>
                    <span class="text-[11px] text-[#44474d] font-mono">Turno Ordinario • Ventanilla Digital Metropolitana</span>
                </div>
            </div>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('portal.dashboard') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Volver al Panel
        </a>
    </div>
</div>

{{-- ═══════════════════════════════════ KPI RIBBON (4 métricas) ══ --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
    <div class="flex items-center gap-3 p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-xs">
        <div class="p-2 rounded-lg bg-[#eff4ff] text-[#0051d5] flex items-center justify-center">
            <span class="material-symbols-outlined text-base">assignment</span>
        </div>
        <div class="flex flex-col">
            <span class="text-xl font-bold text-[#0b1c30] leading-none">{{ $metricas['expedientes_asignados'] ?? 12 }}</span>
            <span class="text-[11px] text-[#44474d]">Expedientes asignados</span>
        </div>
    </div>
    <div class="flex items-center gap-3 p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-xs">
        <div class="p-2 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
            <span class="material-symbols-outlined text-base">check_circle</span>
        </div>
        <div class="flex flex-col">
            <span class="text-xl font-bold text-emerald-700 leading-none">{{ $metricas['revisados_hoy'] ?? 8 }}</span>
            <span class="text-[11px] text-[#44474d]">Revisados hoy</span>
        </div>
    </div>
    <div class="flex items-center gap-3 p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-xs">
        <div class="p-2 rounded-lg bg-[#eff4ff] text-[#0051d5] flex items-center justify-center">
            <span class="material-symbols-outlined text-base">sync_problem</span>
        </div>
        <div class="flex flex-col">
            <span class="text-xl font-bold text-[#0051d5] leading-none">{{ $metricas['en_subsanacion'] ?? 3 }}</span>
            <span class="text-[11px] text-[#44474d]">En subsanación</span>
        </div>
    </div>
    <div class="flex items-center gap-3 p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-xs">
        <div class="p-2 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center">
            <span class="material-symbols-outlined text-base">timer</span>
        </div>
        <div class="flex flex-col">
            <span class="text-xl font-bold text-rose-700 leading-none">{{ $metricas['prioritarios_sla'] ?? 1 }}</span>
            <span class="text-[11px] text-[#44474d]">Prioritario SLA &lt; 4h</span>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════ FILTROS ══ --}}
<div class="mb-4 flex flex-wrap items-center justify-between gap-3 bg-white rounded-xl border border-slate-200/80 shadow-xs p-4">
    <div class="flex items-center gap-2 overflow-x-auto">
        <span class="text-xs text-[#44474d] mr-1 font-medium shrink-0">Filtros:</span>
        <button class="px-3 py-1.5 rounded-full bg-rose-50 text-rose-800 text-xs font-semibold flex items-center gap-1.5" type="button">
            <span class="w-2 h-2 rounded-full bg-rose-600"></span>
            <span>Crítico</span>
        </button>
        <button class="px-3 py-1.5 rounded-full bg-amber-50 text-amber-800 text-xs font-semibold flex items-center gap-1.5 hover:bg-amber-100 transition-colors" type="button">
            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            <span>Ámbar SLA</span>
        </button>
        <button class="px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold flex items-center gap-1.5 hover:bg-slate-200 transition-colors" type="button">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Tiempo Óptimo</span>
        </button>
    </div>
    <div class="flex items-center gap-3">
        <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200">
            <span class="material-symbols-outlined text-base text-slate-400">category</span>
            <select aria-label="Filtrar por tipo de trámite" class="bg-transparent text-xs text-slate-700 focus:outline-none cursor-pointer">
                <option>Todos los trámites</option>
                @foreach ($tiposTramite ?? [] as $tipo)
                    <option value="{{ $tipo->ID_Tipo_Tramite }}">{{ $tipo->Nombre }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════ WORKSPACE PRINCIPAL ══ --}}
<div class="grid grid-cols-12 gap-6 items-start">

    {{-- COLUMNA IZQUIERDA: Expedientes en Cola --}}
    <section class="col-span-12 xl:col-span-4 flex flex-col gap-4">
        <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-xs flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-[#0b1c30]">Expedientes en Cola</h2>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px] font-semibold">{{ $solicitudes->count() ?? 4 }} visibles</span>
            </div>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none">search</span>
                <input class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-[#0b1c30] placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0051d5]/20 shadow-xs transition-all" placeholder="Buscar expediente, razón social o RUC..." type="text"/>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            @forelse ($solicitudes ?? [] as $sol)
                @php
                    $folio = $sol->folio?->Codigo_Folio ?? 'SETC-' . str_pad($sol->ID_Solicitud, 4, '0', STR_PAD_LEFT);
                    $esCritico = false; // lógica de SLA
                    $borderColor = $esCritico ? 'bg-rose-600' : 'bg-[#0051d5]';
                @endphp
                <a href="{{ route('portal.expedientes.evaluar', $sol->ID_Solicitud) }}"
                   class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all cursor-pointer relative overflow-hidden flex flex-col gap-3 group">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $borderColor }}"></div>
                    <div class="flex items-start justify-between gap-2 pl-1">
                        <div class="flex flex-col">
                            <span class="font-mono text-xs text-[#0051d5] font-bold">{{ $folio }}</span>
                            <h3 class="text-sm font-semibold text-[#0b1c30] leading-tight">{{ $sol->solicitante?->Nombre ?? 'Administrado' }}</h3>
                            <span class="text-xs text-[#44474d]">{{ $sol->versionTramite?->tipoTramite?->Nombre ?? 'Trámite' }}</span>
                        </div>
                        <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-700 text-[11px] font-semibold shrink-0 flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">schedule</span>
                            SLA
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 bg-slate-50 p-2.5 rounded-lg">
                        <div class="flex flex-col">
                            <span class="text-[11px] text-[#44474d]">Estado</span>
                            <span class="text-xs font-semibold text-[#0b1c30]">{{ $sol->Estado_Solicitud ?? 'En revisión' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[11px] text-[#44474d]">Radicación</span>
                            <span class="text-xs font-semibold text-[#0051d5]">{{ $sol->Fecha_Creacion_Solicitud?->format('d/m') ?? '--' }}</span>
                        </div>
                    </div>
                </a>
            @empty
                {{-- Estado vacío con expediente de demostración --}}
                <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-sm relative overflow-hidden flex flex-col gap-3">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-rose-600"></div>
                    <div class="flex items-start justify-between gap-2 pl-1">
                        <div class="flex flex-col">
                            <span class="font-mono text-xs text-[#0051d5] font-bold">SETC-2025-8841</span>
                            <h3 class="text-sm font-semibold text-[#0b1c30] leading-tight">Mendoza &amp; Asociados S.A.C.</h3>
                            <span class="text-xs text-[#44474d]">RUC: 20491823901 • Licencia Comercial Tipo A</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-rose-50 text-rose-800 text-[11px] font-bold shrink-0 flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">alarm</span>
                            3h 15m
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 bg-slate-50 p-2.5 rounded-lg">
                        <div class="flex flex-col">
                            <span class="text-[11px] text-[#44474d]">Preclasif. PIDE</span>
                            <span class="text-xs font-bold text-[#0b1c30] flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs text-emerald-600">verified</span>
                                SUNAT OK
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[11px] text-[#44474d]">Compatibilidad</span>
                            <span class="text-xs font-bold text-[#0051d5]">95% Documental</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px] font-semibold">En Revisión Activa</span>
                        <span class="text-xs text-[#0051d5] font-semibold flex items-center gap-1">
                            Examinando
                            <span class="material-symbols-outlined text-xs">arrow_forward</span>
                        </span>
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    {{-- COLUMNA DERECHA: Visor de Documentos + Consola de Dictamen --}}
    <section class="col-span-12 xl:col-span-8 flex flex-col gap-5">

        {{-- VISOR DE DOCUMENTOS --}}
        <div class="p-5 bg-white rounded-xl border border-slate-200/80 shadow-xs flex flex-col gap-4">
            {{-- Tabs de documentos --}}
            <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50 p-2 rounded-xl">
                <div class="flex items-center gap-1 overflow-x-auto">
                    <button class="px-3.5 py-2 rounded-lg bg-white text-[#0051d5] text-xs font-bold shadow-xs flex items-center gap-2" type="button">
                        <span class="material-symbols-outlined text-sm">verified_user</span>
                        <span>Comprobante Domicilio</span>
                        <span class="w-2 h-2 rounded-full bg-[#0051d5]"></span>
                    </button>
                    <button class="px-3.5 py-2 rounded-lg text-slate-500 hover:text-slate-800 text-xs flex items-center gap-2 transition-colors" type="button">
                        <span class="material-symbols-outlined text-sm">badge</span>
                        <span>DNI Representante</span>
                    </button>
                    <button class="px-3.5 py-2 rounded-lg text-slate-500 hover:text-slate-800 text-xs flex items-center gap-2 transition-colors" type="button">
                        <span class="material-symbols-outlined text-sm">architecture</span>
                        <span>Plano ITSE</span>
                    </button>
                    <button class="px-3.5 py-2 rounded-lg text-slate-500 hover:text-slate-800 text-xs flex items-center gap-2 transition-colors" type="button">
                        <span class="material-symbols-outlined text-sm">receipt_long</span>
                        <span>Voucher de Pago</span>
                    </button>
                </div>
                <div class="flex items-center gap-1 px-2">
                    <button aria-label="Acercar" class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800" type="button"><span class="material-symbols-outlined text-base">zoom_in</span></button>
                    <button aria-label="Alejar" class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800" type="button"><span class="material-symbols-outlined text-base">zoom_out</span></button>
                    <button aria-label="Rotar" class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800" type="button"><span class="material-symbols-outlined text-base">rotate_right</span></button>
                    <button aria-label="Pantalla completa" class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800" type="button"><span class="material-symbols-outlined text-base">fullscreen</span></button>
                    <div class="h-4 w-px bg-slate-200 mx-1"></div>
                    <button aria-label="Lápiz de anotación" class="p-1.5 rounded-lg bg-slate-100 text-[#0051d5]" type="button"><span class="material-symbols-outlined text-base">edit</span></button>
                    <button aria-label="Nota de observación" class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800" type="button"><span class="material-symbols-outlined text-base">sticky_note_2</span></button>
                    <button aria-label="Regla técnica" class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800" type="button"><span class="material-symbols-outlined text-base">straighten</span></button>
                </div>
            </div>

            {{-- Área de previsualización del documento --}}
            <div class="relative w-full h-[420px] bg-slate-50 rounded-xl overflow-hidden flex items-center justify-center p-6 border border-slate-200">
                <div class="relative w-full max-w-lg h-full bg-white rounded-lg shadow-md p-8 flex flex-col justify-between overflow-hidden border border-slate-100">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-[#0f2744] text-white flex items-center justify-center font-bold text-xs">SETC</div>
                            <div class="flex flex-col">
                                <span class="text-[11px] font-bold text-[#0b1c30] uppercase tracking-wider">Constancia de Domicilio Fiscal</span>
                                <span class="text-[11px] text-slate-400 font-mono">SETC-2025-8841-DOC01</span>
                            </div>
                        </div>
                        <span class="px-2 py-1 rounded bg-slate-100 text-slate-600 text-[11px] font-mono">Pág. 1 / 1</span>
                    </div>
                    <div class="space-y-3 my-auto">
                        <div class="h-2.5 bg-slate-100 rounded w-3/4"></div>
                        <div class="h-2.5 bg-slate-100 rounded w-full"></div>
                        <div class="h-2.5 bg-slate-100 rounded w-5/6"></div>
                        <div class="p-4 bg-slate-50 rounded-lg space-y-2.5 border border-slate-100">
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Titular Catastral:</span>
                                <span class="text-[#0b1c30] font-bold">Mendoza &amp; Asociados S.A.C.</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Dirección Declarada:</span>
                                <span class="text-[#0b1c30] font-semibold">Av. El Sol 450, Interior 3B, Surco</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Zonificación:</span>
                                <span class="text-[#0b1c30] font-semibold">Comercio Vecinal (CV-Z3)</span>
                            </div>
                        </div>
                        <div class="h-2.5 bg-slate-100 rounded w-2/3"></div>
                    </div>
                    <div class="flex items-end justify-between pt-4 border-t border-slate-100">
                        <div class="flex flex-col gap-1">
                            <span class="text-[11px] text-slate-400 font-mono">Huella Criptográfica SHA-256</span>
                            <span class="text-[11px] text-[#0b1c30] font-mono font-bold">e8a9...4b12_VALIDATED</span>
                        </div>
                        <div class="relative p-3 bg-slate-100 rounded-lg flex flex-col items-center">
                            <span class="material-symbols-outlined text-[#0051d5] text-2xl">verified</span>
                            <span class="text-[11px] font-bold text-[#0b1c30]">Sello Notarial</span>
                            <div class="absolute -top-12 -right-4 bg-white p-2.5 rounded-lg shadow-lg flex items-center gap-2 z-10 border border-amber-200">
                                <span class="material-symbols-outlined text-amber-600 text-base">priority_high</span>
                                <div class="flex flex-col">
                                    <span class="text-[11px] font-bold text-[#0b1c30] leading-none">Anotación Técnica</span>
                                    <span class="text-[11px] text-slate-400">Sello con bajo contraste</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CONSOLA DE DICTAMEN --}}
        <div class="p-6 bg-white rounded-xl border border-slate-200/80 shadow-xs flex flex-col gap-5">
            <div class="flex items-center justify-between">
                <div class="flex flex-col">
                    <h2 class="text-base font-bold text-[#0b1c30]">Consola de Dictamen y Notificación Directa</h2>
                    <span class="text-xs text-[#44474d]">La decisión generará una cédula formal y un mensaje accesible en el buzón ciudadano.</span>
                </div>
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                    Paso 3 de 3: Pronunciamiento
                </span>
            </div>

            {{-- Opciones de dictamen --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <button class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-left flex items-start gap-3 hover:border-emerald-400 hover:shadow-sm transition-all group" id="btn-favorable" onclick="selectDecision('favorable')" type="button">
                    <span class="material-symbols-outlined text-emerald-600 text-xl mt-0.5">check_circle</span>
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-[#0b1c30]">Dictamen Favorable</span>
                        <span class="text-xs text-[#44474d]">Cumple requisitos y normativa</span>
                    </div>
                </button>
                <button class="p-3.5 rounded-xl bg-[#eff4ff] border-2 border-[#0051d5] text-left flex items-start gap-3 shadow-sm transition-all" id="btn-correccion" onclick="selectDecision('correccion')" type="button">
                    <span class="material-symbols-outlined text-[#0051d5] text-xl mt-0.5">rule</span>
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-[#0b1c30]">Solicitar Subsanación</span>
                        <span class="text-xs text-[#44474d]">Requiere aclarar o reemplazar</span>
                    </div>
                </button>
                <button class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-left flex items-start gap-3 hover:border-rose-400 hover:bg-rose-50 hover:shadow-sm transition-all group" id="btn-rechazo" onclick="selectDecision('rechazo')" type="button">
                    <span class="material-symbols-outlined text-rose-600 text-xl mt-0.5">cancel</span>
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-[#0b1c30]">Observar con Rechazo</span>
                        <span class="text-xs text-[#44474d]">Incumplimiento irrecuperable</span>
                    </div>
                </button>
            </div>

            {{-- Editor de mensaje + Vista previa --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-[#0b1c30]" for="dictamen-editor">Mensaje Empático al Administrado</label>
                        <span class="text-xs text-[#0051d5] font-semibold cursor-pointer hover:underline">Cargar plantilla</span>
                    </div>
                    <textarea class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0b1c30] focus:outline-none focus:ring-2 focus:ring-[#0051d5]/20 focus:bg-white shadow-xs resize-none leading-relaxed" id="dictamen-editor" rows="6">Estimado administrado de Mendoza & Asociados:
Al revisar su "Comprobante de Domicilio", advertimos que el sello notarial inferior presenta baja legibilidad. Para evitar demoras en su licencia definitiva, por favor adjunte una copia escaneada nítida antes del 26 de marzo.</textarea>
                    <span class="text-[11px] text-slate-400 font-mono">Variables: [Nombre_Empresa], [Documento_Faltante], [Fecha_Limite]</span>
                </div>
                <div class="flex flex-col gap-2">
                    <span class="text-xs font-bold text-[#0b1c30]">Vista Previa Móvil del Administrado</span>
                    <div class="flex-1 bg-slate-50 border border-slate-200 p-4 rounded-xl flex flex-col justify-between min-h-[150px]">
                        <div class="flex items-center gap-3 pb-3">
                            <div class="w-7 h-7 rounded-full bg-[#0f2744] text-white flex items-center justify-center font-bold text-xs">S</div>
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-[#0b1c30]">Notificación Municipal SETC</span>
                                <span class="text-[11px] text-slate-400 font-mono">Buzón Electrónico Oficial</span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 italic leading-normal bg-white p-3 rounded-lg shadow-xs">
                            "Estimado administrado: Advertimos que el sello notarial inferior presenta baja legibilidad. Por favor adjunte una copia nítida..."
                        </p>
                        <div class="flex items-center justify-between pt-3">
                            <span class="text-xs text-[#0051d5] font-semibold">Botón: [Subir Subsanación]</span>
                            <span class="text-[11px] text-slate-400 font-mono">Plazo: 48h</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Acciones finales --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100">
                <div class="flex items-center gap-2">
                    <button class="px-4 py-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-[#0b1c30] text-xs font-semibold transition-colors flex items-center gap-2" type="button">
                        <span class="material-symbols-outlined text-base">save</span>
                        <span>Guardar Borrador</span>
                    </button>
                    <button class="px-4 py-2.5 rounded-lg text-slate-500 hover:text-slate-800 text-xs transition-colors" type="button">
                        Historial de Revisiones
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <button class="px-6 py-2.5 rounded-lg bg-[#0f2744] hover:bg-[#1a3860] text-white text-xs font-bold transition-all shadow-sm flex items-center gap-2" type="button">
                        <span class="material-symbols-outlined text-base">verified</span>
                        <span>Firmar y Notificar Resolución (PKI / FIDO2)</span>
                    </button>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    function selectDecision(tipo) {
        const btnF = document.getElementById('btn-favorable');
        const btnC = document.getElementById('btn-correccion');
        const btnR = document.getElementById('btn-rechazo');
        const editor = document.getElementById('dictamen-editor');

        // Reset styles
        btnF.className = btnF.className.replace('border-emerald-400 shadow-sm', '').replace('border-2', 'border');
        btnF.classList.add('bg-emerald-50', 'border', 'border-emerald-200');
        btnC.className = btnC.className.replace('border-2 border-[#0051d5]', 'border border-slate-200').replace('bg-[#eff4ff]', 'bg-slate-50');
        btnR.classList.remove('border-rose-400', 'bg-rose-50');

        if (tipo === 'favorable') {
            btnF.classList.add('border-emerald-400', 'shadow-sm');
            editor.value = "Estimado titular de Mendoza & Asociados S.A.C.:\nNos complace informarle que la evaluación técnica ha concluido satisfactoriamente. Su solicitud cumple plenamente con la normativa comercial vigente. Su certificado de Licencia Comercial Tipo A ha sido firmado digitalmente.";
        } else if (tipo === 'correccion') {
            btnC.classList.add('bg-[#eff4ff]', 'border-2', 'border-[#0051d5]', 'shadow-sm');
            editor.value = "Estimado administrado de Mendoza & Asociados:\nAl revisar su 'Comprobante de Domicilio', advertimos que el sello notarial inferior presenta baja legibilidad. Por favor adjunte una copia escaneada nítida antes del 26 de marzo.";
        } else if (tipo === 'rechazo') {
            btnR.classList.add('border-rose-400', 'bg-rose-50', 'shadow-sm');
            editor.value = "RESOLUCIÓN DENEGATORIA:\nSe observa formalmente la solicitud de Licencia Comercial Tipo A por incompatibilidad estricta con la zonificación reglamentaria según Ord. 1029-SETC. Plazo de 15 días hábiles para recurso de reconsideración o apelación.";
        }
    }
</script>
@endpush

@endsection
