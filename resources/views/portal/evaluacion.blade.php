@extends('layouts.portal')

@section('title', 'Evaluación de Expediente #' . ($solicitud->folio?->Codigo_Folio ?? $solicitud->ID_Solicitud))

@section('breadcrumb')
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <a href="{{ route('portal.dashboard') }}">Operaciones</a>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <a href="{{ route('portal.mesa-dictamen') }}">Mesa de Dictamen</a>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="current">Expediente {{ $solicitud->folio?->Codigo_Folio ?? '#' . $solicitud->ID_Solicitud }}</span>
@endsection

@section('content')
<div class="flex flex-col w-full gap-space-lg">

    {{-- ══ EXPEDIENTE HEADER ══ --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 lg:p-6 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-5">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-primary-container text-white flex items-center justify-center font-bold text-lg shadow-sm shrink-0">
                <span class="material-symbols-outlined text-2xl text-blue-200">folder_open</span>
            </div>
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-mono font-bold text-xs px-2.5 py-1 rounded-md bg-[#eff4ff] text-[#0051d5]">
                        {{ $solicitud->folio?->Codigo_Folio ?? 'EXP-2025-' . str_pad($solicitud->ID_Solicitud, 5, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                        {{ $solicitud->versionTramite?->tipoTramite?->Nombre ?? 'Trámite Ordinario' }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        En Calificación Técnica
                    </span>
                </div>
                <h1 class="text-xl lg:text-2xl font-bold tracking-tight text-[#001229]" style="font-family: var(--font-display);">
                    Mesa de Dictamen y Calificación Jurídica
                </h1>
                <p class="text-xs text-slate-500">
                    Solicitante: <span class="font-semibold text-slate-800">{{ $solicitud->solicitante?->Nombre ?? 'Ciudadano Registrado' }}</span> • Correo: {{ $solicitud->solicitante?->Email ?? 'N/A' }} • Fecha de Ingreso: {{ $solicitud->Fecha_Creacion_Solicitud ? $solicitud->Fecha_Creacion_Solicitud->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('portal.mesa-dictamen') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                <span>Cola de Expedientes</span>
            </a>
            <button class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs transition-colors">
                <span class="material-symbols-outlined text-base text-slate-500">history</span>
                <span>Trazabilidad</span>
            </button>
        </div>
    </div>

    {{-- ══ WORKSPACE: DOCUMENTOS (IZQ) & DICTAMEN (DER) ══ --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg items-start">

        {{-- ── COLUMNA IZQUIERDA: VISOR DE DOCUMENTOS Y RECAUDOS (7 COLS) ── --}}
        <div class="xl:col-span-7 flex flex-col gap-space-md">

            {{-- VISOR TÉCNICO --}}
            <div class="p-5 bg-white rounded-xl border border-slate-200/80 shadow-xs flex flex-col gap-4">
                <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50 p-2 rounded-xl">
                    <div class="flex items-center gap-1 overflow-x-auto">
                        @forelse($solicitud->documentos as $idx => $doc)
                            <button class="px-3.5 py-2 rounded-lg text-xs font-bold shadow-xs flex items-center gap-2 transition-colors {{ $idx === 0 ? 'bg-white text-[#0051d5]' : 'text-slate-500 hover:text-slate-800' }}" type="button">
                                <span class="material-symbols-outlined text-sm">description</span>
                                <span>{{ Str::limit($doc->requisito?->Nombre ?? 'Doc #' . ($idx + 1), 18) }}</span>
                                <span class="w-2 h-2 rounded-full {{ $doc->Estado_Doc === 'Aprobado' ? 'bg-emerald-500' : ($doc->Estado_Doc === 'Observado' ? 'bg-amber-500' : 'bg-slate-300') }}"></span>
                            </button>
                        @empty
                            <button class="px-3.5 py-2 rounded-lg bg-white text-[#0051d5] text-xs font-bold shadow-xs flex items-center gap-2" type="button">
                                <span class="material-symbols-outlined text-sm">description</span>
                                <span>Comprobante Domicilio</span>
                                <span class="w-2 h-2 rounded-full bg-[#0051d5]"></span>
                            </button>
                        @endforelse
                    </div>
                    <div class="flex items-center gap-1 px-2">
                        <button class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800" title="Acercar" type="button"><span class="material-symbols-outlined text-base">zoom_in</span></button>
                        <button class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800" title="Alejar" type="button"><span class="material-symbols-outlined text-base">zoom_out</span></button>
                        <button class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800" title="Rotar" type="button"><span class="material-symbols-outlined text-base">rotate_right</span></button>
                        <button class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800" title="Pantalla completa" type="button"><span class="material-symbols-outlined text-base">fullscreen</span></button>
                    </div>
                </div>

                {{-- Mockup Visual del Recaudo Digital --}}
                <div class="relative w-full h-[380px] bg-slate-50 rounded-xl overflow-hidden flex items-center justify-center p-6 border border-slate-200">
                    <div class="relative w-full max-w-md h-full bg-white rounded-lg shadow-md p-6 flex flex-col justify-between overflow-hidden border border-slate-100">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-[#0f2744] text-white flex items-center justify-center font-bold text-xs">SETC</div>
                                <div class="flex flex-col">
                                    <span class="text-[11px] font-bold text-[#0b1c30] uppercase tracking-wider">Recaudo Digital Verificado</span>
                                    <span class="text-[10px] text-slate-400 font-mono">EXP-{{ $solicitud->ID_Solicitud }}-RECAUDO-01</span>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-mono">Pág. 1 / 1</span>
                        </div>
                        <div class="space-y-2.5 my-auto">
                            <div class="h-2 bg-slate-100 rounded w-3/4"></div>
                            <div class="h-2 bg-slate-100 rounded w-full"></div>
                            <div class="h-2 bg-slate-100 rounded w-5/6"></div>
                            <div class="p-3 bg-slate-50 rounded-lg space-y-1.5 border border-slate-100 text-[11px]">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Titular Solicitante:</span>
                                    <span class="text-[#0b1c30] font-bold">{{ $solicitud->solicitante?->Nombre ?? 'Mendoza & Asociados S.A.C.' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Procedimiento TUPA:</span>
                                    <span class="text-[#0b1c30] font-semibold">{{ Str::limit($solicitud->versionTramite?->tipoTramite?->Nombre ?? 'Licencia de Funcionamiento', 30) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Firma Electrónica:</span>
                                    <span class="text-emerald-700 font-semibold flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px]">verified</span>
                                        Válida (RENIEC / ONPE)
                                    </span>
                                </div>
                            </div>
                            <div class="h-2 bg-slate-100 rounded w-2/3"></div>
                        </div>
                        <div class="flex items-end justify-between pt-3 border-t border-slate-100">
                            <div class="flex flex-col">
                                <span class="text-[10px] text-slate-400 font-mono">Hash SHA-256</span>
                                <span class="text-[10px] text-[#0b1c30] font-mono font-bold">e8a9...4b12_VALIDATED</span>
                            </div>
                            <div class="px-2 py-1 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">verified</span>
                                Cédula Timbrada
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LISTA Y CALIFICACIÓN DE DOCUMENTOS INDIVIDUALES --}}
            <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-[#001229]">Recaudos Aportados por el Ciudadano</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Evalúa el cumplimiento de cada requisito normativo antes del dictamen final.</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                        {{ $solicitud->documentos->count() }} {{ Str::plural('recaudo', $solicitud->documentos->count()) }}
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($solicitud->documentos as $doc)
                        <div class="py-3.5 space-y-2">
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base text-slate-400">attach_file</span>
                                        <h3 class="text-xs font-semibold text-slate-900">
                                            {{ $doc->requisito?->Nombre ?? 'Documento Normativo #' . $doc->ID_Documento }}
                                        </h3>
                                    </div>
                                    <div class="flex items-center gap-2 text-[11px] text-slate-400 pl-6">
                                        <span>Formato: {{ $doc->requisito?->Formato ?? 'PDF' }}</span>
                                        <span>•</span>
                                        <span>{{ $doc->versionesDoc->count() }} {{ Str::plural('versión', $doc->versionesDoc->count()) }} cargadas</span>
                                    </div>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $doc->Estado_Doc === 'Aprobado' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($doc->Estado_Doc === 'Observado' ? 'bg-amber-50 text-amber-800 border-amber-200' : ($doc->Estado_Doc === 'Rechazado' ? 'bg-rose-50 text-rose-800 border-rose-200' : 'bg-slate-100 text-slate-700 border-slate-200')) }}">
                                    {{ $doc->Estado_Doc }}
                                </span>
                            </div>

                            {{-- Botones de calificación rápida por documento --}}
                            <form action="{{ route('portal.documentos.evaluar', $doc->ID_Documento) }}" method="POST" class="flex items-center gap-2 pl-6 pt-1">
                                @csrf
                                <span class="text-[11px] text-slate-500 font-medium">Calificar:</span>
                                <button type="submit" name="estado_doc" value="Aprobado" class="px-2.5 py-1 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-[11px] font-semibold transition-colors flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">check</span> Aprobar
                                </button>
                                <button type="submit" name="estado_doc" value="Observado" class="px-2.5 py-1 rounded-md bg-amber-50 hover:bg-amber-100 text-amber-900 text-[11px] font-semibold transition-colors flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">warning</span> Observar
                                </button>
                                <button type="submit" name="estado_doc" value="Rechazado" class="px-2.5 py-1 rounded-md bg-rose-50 hover:bg-rose-100 text-rose-800 text-[11px] font-semibold transition-colors flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">close</span> Rechazar
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            No hay documentos cargados en este expediente.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ── COLUMNA DERECHA: CONSOLA DE DICTAMEN OFICIAL (5 COLS) ── --}}
        <div class="xl:col-span-5 flex flex-col gap-space-md sticky top-20">

            {{-- BANNER DE SLA --}}
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-amber-600 text-xl">schedule</span>
                    <div>
                        <span class="text-xs font-bold text-amber-900 block">Plazo Normativo SLA</span>
                        <span class="text-[11px] text-amber-700">Tiempo restante estimado: 18h 42m</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold uppercase">
                    Alerta SLA
                </span>
            </div>

            {{-- FORMULARIO DE DICTAMEN --}}
            <div class="bg-white p-5 lg:p-6 rounded-xl border border-slate-200/80 shadow-xs flex flex-col gap-5" x-data="{ decision: 'Aprobado' }">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-base font-bold text-[#001229]">Consola de Pronunciamiento</h2>
                    <p class="text-xs text-slate-500 mt-0.5">El dictamen suscrito emitirá la cédula vinculante con sello institucional.</p>
                </div>

                @if ($asignacionActiva)
                    <form action="{{ route('portal.revisiones.store', $asignacionActiva->ID_Asignacion) }}" method="POST" class="space-y-4">
                        @csrf

                        {{-- Botones de selección de dictamen tipo tarjeta --}}
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold text-slate-800">Resultado Oficial</label>
                            <input type="hidden" name="resultado" :value="decision">

                            <div class="grid grid-cols-1 gap-2.5">
                                {{-- Aprobado --}}
                                <button type="button"
                                        @click="decision = 'Aprobado'"
                                        :class="decision === 'Aprobado' ? 'bg-emerald-50 border-2 border-emerald-600 shadow-xs' : 'bg-white border border-slate-200 hover:bg-slate-50'"
                                        class="p-3 rounded-xl text-left flex items-start gap-3 transition-all">
                                    <span class="material-symbols-outlined text-emerald-600 text-xl mt-0.5">check_circle</span>
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 block">Dictamen Favorable (Aprobado)</span>
                                        <span class="text-[11px] text-slate-500">Cumple íntegramente los requisitos de admisibilidad.</span>
                                    </div>
                                </button>

                                {{-- Observado --}}
                                <button type="button"
                                        @click="decision = 'Observado'"
                                        :class="decision === 'Observado' ? 'bg-amber-50 border-2 border-amber-500 shadow-xs' : 'bg-white border border-slate-200 hover:bg-slate-50'"
                                        class="p-3 rounded-xl text-left flex items-start gap-3 transition-all">
                                    <span class="material-symbols-outlined text-amber-600 text-xl mt-0.5">rule</span>
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 block">Observar y Requerir Subsanación</span>
                                        <span class="text-[11px] text-slate-500">Notifica al solicitante plazo de 48h para corregir recaudos.</span>
                                    </div>
                                </button>

                                {{-- Rechazado --}}
                                <button type="button"
                                        @click="decision = 'Rechazado'"
                                        :class="decision === 'Rechazado' ? 'bg-rose-50 border-2 border-rose-600 shadow-xs' : 'bg-white border border-slate-200 hover:bg-slate-50'"
                                        class="p-3 rounded-xl text-left flex items-start gap-3 transition-all">
                                    <span class="material-symbols-outlined text-rose-600 text-xl mt-0.5">cancel</span>
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 block">Dictamen Desfavorable (Rechazado)</span>
                                        <span class="text-[11px] text-slate-500">Incumplimiento insubsanable de la normativa TUPA vigente.</span>
                                    </div>
                                </button>
                            </div>
                        </div>

                        {{-- Área de observaciones si no es Aprobado --}}
                        <div x-show="decision === 'Observado' || decision === 'Rechazado'" class="space-y-1.5 pt-2" x-cloak>
                            <label class="block text-xs font-semibold text-slate-800">
                                Fundamentación Jurídica / Observación Técnica <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="observaciones[0][detalles]"
                                      rows="4"
                                      placeholder="Describa con precisión los recaudos observados, artículos de la norma aplicable y el plazo conferido para subsanar..."
                                      class="w-full p-3 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary"></textarea>
                        </div>

                        {{-- Indicador Criptográfico PKI --}}
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-[11px]">
                            <span class="flex items-center gap-1.5 text-slate-600 font-medium">
                                <span class="material-symbols-outlined text-secondary text-base">verified_user</span>
                                Firma PKI L2 Activa
                            </span>
                            <span class="font-mono text-emerald-700 font-semibold">Certificado #8941-OK</span>
                        </div>

                        <button type="submit" class="w-full h-11 bg-primary-container hover:bg-[#1a3860] text-white rounded-xl text-xs font-semibold transition-all flex items-center justify-center gap-2 shadow-sm">
                            <span class="material-symbols-outlined text-base">gavel</span>
                            <span>Emitir Dictamen y Sellar Expediente</span>
                        </button>
                    </form>
                @else
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 space-y-1">
                        <span class="font-bold block">Sin Asignación Activa</span>
                        <p class="text-[11px]">Para emitir un dictamen formal, el expediente debe tener asignado al revisor en la Matriz de Cargas.</p>
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
