@extends('layouts.public')

@section('title', 'Iniciar Solicitud — ' . $tramite->Nombre)
@section('meta_description', 'Inicia tu expediente telemático adjuntando los requisitos solicitados.')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="breadcrumb-item">SETC</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <a href="{{ route('tramites.index') }}" class="breadcrumb-item">Trámites</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <span class="breadcrumb-item current">Iniciar {{ Str::limit($tramite->Nombre, 20) }}</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-6 lg:px-12 py-8 w-full space-y-8">
    {{-- Cabecera --}}
    <div class="space-y-3">
        <span class="px-3 py-1 rounded-full bg-[#eff4ff] text-[#0051d5] text-xs font-semibold">Ventanilla Telemática</span>
        <h1 class="text-3xl font-bold tracking-tight text-[#001229]" style="font-family: var(--font-display);">
            Iniciar Expediente: {{ $tramite->Nombre }}
        </h1>
        <p class="text-sm text-[#44474d]">
            Ingresa la descripción de tu solicitud y adjunta los recaudos requeridos conforme a la versión normativa vigente (Ver. {{ $versionVigente->No_Version }}).
        </p>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            <span class="font-semibold block">Corrige los siguientes campos:</span>
            <ul class="list-disc list-inside text-xs mt-1 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $reqObligatorios = $versionVigente->requisitos->where('Prioridad', 'Obligatorio')->pluck('ID_Requisitos')->values();
    @endphp

    {{-- Formulario de Carga del Expediente --}}
    <form action="{{ route('solicitudes.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-8"
          x-data="{
              obligatorios: {{ json_encode($reqObligatorios) }},
              cargados: {},
              onFileChange(e, id) {
                  this.cargados[id] = e.target.files.length > 0;
              },
              get todosObligatoriosListos() {
                  return this.obligatorios.every(id => this.cargados[id]);
              }
          }">
        @csrf
        <input type="hidden" name="id_version_tramite" value="{{ $versionVigente->ID_Version_Tramite }}">

        {{-- 1. Datos complementarios --}}
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <h2 class="text-base font-bold text-[#001229] flex items-center gap-2" style="font-family: var(--font-display);">
                <span class="material-symbols-outlined text-[#0051d5]">edit_note</span>
                <span>1. Descripción de la Solicitud</span>
            </h2>
            <div class="space-y-1.5">
                <label for="descripcion" class="block text-xs font-semibold text-[#0b1c30]">
                    Detalles, motivo o referencias del establecimiento / predio
                </label>
                <textarea name="descripcion"
                          id="descripcion"
                          rows="3"
                          placeholder="Indica información adicional útil para el revisor (dirección del local, actividad comercial, antecedentes)..."
                          class="w-full p-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">{{ old('descripcion') }}</textarea>
            </div>
        </div>

        {{-- 2. Carga de Recaudos Normativos --}}
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-base font-bold text-[#001229] flex items-center gap-2" style="font-family: var(--font-display);">
                        <span class="material-symbols-outlined text-[#0051d5]">upload_file</span>
                        <span>2. Documentación y Requisitos Digitales</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Solo se aceptan archivos en formato <strong>.PDF</strong> (Máx. 10MB por archivo). Los marcados como <span class="text-amber-700 font-semibold">Obligatorio</span> son indispensables para continuar.
                    </p>
                </div>
                <div x-show="obligatorios.length > 0" class="shrink-0">
                    <span :class="todosObligatoriosListos ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'"
                          class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border">
                        <span class="material-symbols-outlined text-sm" x-text="todosObligatoriosListos ? 'check_circle' : 'pending'"></span>
                        <span x-text="todosObligatoriosListos ? 'Requisitos obligatorios completos' : 'Faltan documentos obligatorios'"></span>
                    </span>
                </div>
            </div>

            <div class="space-y-6">
                @forelse ($versionVigente->requisitos as $index => $req)
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-[#001229] text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <h3 class="text-sm font-semibold text-[#0b1c30]">{{ $req->Nombre }}</h3>
                                    <span class="text-xs text-slate-500">Formato admitido: Únicamente PDF</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold shrink-0 {{ $req->Prioridad === 'Obligatorio' ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-slate-200 text-slate-700' }}">
                                {{ $req->Prioridad }}
                            </span>
                        </div>

                        <div>
                            <input type="file"
                                   name="documentos[{{ $req->ID_Requisitos }}]"
                                   accept=".pdf,application/pdf"
                                   @change="onFileChange($event, {{ $req->ID_Requisitos }})"
                                   @if($req->Prioridad === 'Obligatorio') required @endif
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#eff4ff] file:text-[#0051d5] hover:file:bg-[#dce9ff]">
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500">Este trámite no exige requisitos adicionales de carga inicial.</p>
                @endforelse
            </div>
        </div>

        {{-- Botón Enviar con bloqueo interactivo --}}
        <div class="flex items-center justify-between pt-4">
            <a href="{{ route('tramites.show', $tramite->ID_Tipo_Tramite) }}" class="text-xs text-slate-500 hover:underline">
                Cancelar y volver
            </a>
            <button type="submit"
                    :disabled="!todosObligatoriosListos"
                    :class="todosObligatoriosListos ? 'bg-[#001229] hover:bg-[#0f2744] text-white cursor-pointer shadow-sm hover:shadow active:scale-[0.99]' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                    class="h-12 px-8 rounded-xl font-semibold text-sm transition-all duration-200 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">send</span>
                <span x-text="todosObligatoriosListos ? 'Registrar Solicitud y Emitir Folio' : 'Adjunte los requisitos obligatorios para enviar'">Registrar Solicitud y Emitir Folio</span>
            </button>
        </div>
    </form>
</div>
@endsection

