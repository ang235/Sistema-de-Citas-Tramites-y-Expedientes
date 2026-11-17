@extends('layouts.public')

@section('title', 'Mi Perfil Ciudadano')
@section('meta_description', 'Consulta tus citas agendadas, expedientes activos y administra tus datos personales en el portal SETC.')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="breadcrumb-item">SETC</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <span class="breadcrumb-item current">Mi Perfil</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-12 py-8 w-full space-y-10">

    {{-- ══ ALERTA DE NOTIFICACIONES / VALIDACIONES ══ --}}
    @if (session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('info'))
        <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-sm flex items-center gap-3">
            <span class="material-symbols-outlined text-blue-600">info</span>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            <span class="font-semibold block mb-1">Por favor verifica los siguientes campos:</span>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ══ CABECERA DE PERFIL DEL USUARIO ══ --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 lg:p-8 shadow-xs relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-[#eff4ff] blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start md:items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-[#001229] text-white flex items-center justify-center font-bold text-2xl shrink-0 shadow-md">
                    {{ strtoupper(substr($usuario->Nombre ?? $usuario->name ?? 'C', 0, 1)) }}
                </div>
                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full bg-[#eff4ff] text-[#0051d5] text-xs font-semibold">
                            {{ $usuario->role_label ?? 'Solicitante / Ciudadano' }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Cuenta Activa
                        </span>
                    </div>
                    <h1 class="text-2xl lg:text-3xl font-bold tracking-tight text-[#001229]" style="font-family: var(--font-display);">
                        {{ $usuario->Nombre ?? $usuario->name }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-4 text-xs text-[#44474d]">
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm text-slate-400">mail</span>
                            {{ $usuario->Email ?? $usuario->email }}
                        </span>
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm text-slate-400">calendar_today</span>
                            Miembro desde {{ $usuario->Fecha_Creacion ? \Carbon\Carbon::parse($usuario->Fecha_Creacion)->isoFormat('MMMM YYYY') : '2026' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('citas.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#001229] hover:bg-[#0f2744] text-white rounded-xl text-xs font-semibold transition-all shadow-sm">
                    <span class="material-symbols-outlined text-base">calendar_add_on</span>
                    <span>Agendar Nueva Cita</span>
                </a>
                <a href="{{ route('tramites.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#eff4ff] hover:bg-[#dce9ff] text-[#0051d5] rounded-xl text-xs font-semibold transition-all">
                    <span class="material-symbols-outlined text-base">add_circle</span>
                    <span>Nuevo Trámite</span>
                </a>
            </div>
        </div>

        {{-- Métricas Rápidas --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 pt-6 border-t border-slate-100">
            <div class="p-3 bg-slate-50 rounded-xl">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Citas</span>
                <span class="text-xl font-bold text-[#001229] mt-0.5 block">{{ $citas->count() }}</span>
            </div>
            <div class="p-3 bg-blue-50/60 rounded-xl">
                <span class="text-[11px] font-semibold text-[#0051d5] uppercase tracking-wider block">Programadas</span>
                <span class="text-xl font-bold text-[#0051d5] mt-0.5 block">{{ $citas->where('Estado_Citas', 'Programada')->count() }}</span>
            </div>
            <div class="p-3 bg-emerald-50/60 rounded-xl">
                <span class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider block">Atendidas</span>
                <span class="text-xl font-bold text-emerald-700 mt-0.5 block">{{ $citas->where('Estado_Citas', 'Atendida')->count() }}</span>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Expedientes</span>
                <span class="text-xl font-bold text-[#001229] mt-0.5 block">{{ $solicitudes->count() }}</span>
            </div>
        </div>
    </div>

    {{-- ══ SECCIÓN 1: MIS CITAS AGENDADAS (REQUERIMIENTO PRINCIPAL) ══ --}}
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-xl font-bold text-[#001229] flex items-center gap-2" style="font-family: var(--font-display);">
                    <span class="material-symbols-outlined text-[#0051d5]">event</span>
                    <span>Mis Citas Agendadas</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Historial de citas telemáticas y presenciales. Las citas pasadas no atendidas se cancelan automáticamente conforme a normativa.
                </p>
            </div>
            <a href="{{ route('citas.create') }}" class="inline-flex items-center gap-1.5 text-xs text-[#0051d5] font-semibold hover:underline">
                <span class="material-symbols-outlined text-sm">add</span>
                <span>Agendar otra cita</span>
            </a>
        </div>

        @if ($citas->isEmpty())
            <div class="py-12 px-6 text-center space-y-4 bg-white rounded-2xl border border-slate-200/80">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-[#eff4ff] text-[#0051d5] flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl">event_busy</span>
                </div>
                <div class="space-y-1">
                    <h3 class="text-base font-bold text-[#001229]">Aún no has agendado ninguna cita</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                        Puedes programar una cita para recibir orientación presencial o dar seguimiento a tus trámites ante las ventanillas municipales.
                    </p>
                </div>
                <a href="{{ route('citas.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#001229] hover:bg-[#0f2744] text-white rounded-xl text-xs font-semibold transition-all shadow-sm">
                    <span class="material-symbols-outlined text-base">calendar_month</span>
                    <span>Agendar mi primera cita</span>
                </a>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4">Fecha & Horario</th>
                                <th class="py-3 px-4">Trámite o Folio Asociado</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                                <th class="py-3 px-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($citas as $cita)
                                @php
                                    $fechaObj = \Carbon\Carbon::parse($cita->Fecha);
                                    $horaFmt = substr((string) $cita->Hora, 0, 5);
                                    $folio = $cita->solicitud?->folio?->Codigo_Folio;
                                    $tramiteNombre = $cita->solicitud?->versionTramite?->tipoTramite?->Nombre ?? 'Orientación Ciudadana General';
                                    $esProgramada = $cita->Estado_Citas === 'Programada';
                                    $esAtendida = $cita->Estado_Citas === 'Atendida';
                                    $esCancelada = in_array($cita->Estado_Citas, ['Cancelada', 'No asistió']);
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-9 h-9 rounded-lg bg-[#eff4ff] text-[#0051d5] flex items-center justify-center font-bold text-xs shrink-0">
                                                <span class="material-symbols-outlined text-base">calendar_month</span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-[#001229] block">
                                                    {{ $fechaObj->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                                                </span>
                                                <span class="text-[11px] text-slate-500 font-mono flex items-center gap-1 mt-0.5">
                                                    <span class="material-symbols-outlined text-xs text-slate-400">schedule</span>
                                                    {{ $horaFmt }} hrs
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div>
                                            <span class="font-semibold text-[#001229] block">{{ $tramiteNombre }}</span>
                                            @if ($folio)
                                                <a href="{{ route('expedientes.show', $folio) }}" class="text-[11px] font-mono text-[#0051d5] hover:underline flex items-center gap-0.5 mt-0.5">
                                                    <span class="material-symbols-outlined text-xs">folder_open</span>
                                                    <span>{{ $folio }}</span>
                                                </a>
                                            @else
                                                <span class="text-[11px] text-slate-400">Sin expediente vinculado</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if ($esProgramada)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-[#0051d5] border border-blue-200 text-[11px] font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#0051d5] animate-pulse"></span>
                                                Programada
                                            </span>
                                        @elseif ($esAtendida)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-semibold">
                                                <span class="material-symbols-outlined text-xs">check_circle</span>
                                                Atendida
                                            </span>
                                        @elseif ($esCancelada)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-[11px] font-semibold">
                                                <span class="material-symbols-outlined text-xs">cancel</span>
                                                {{ $cita->Estado_Citas }}
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[11px] font-semibold">
                                                {{ $cita->Estado_Citas }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('citas.pase', $cita->ID_Citas) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-[#0051d5] text-[#0b1c30] hover:text-white text-xs font-semibold transition-all"
                                               title="Ver pase digital y código QR">
                                                <span class="material-symbols-outlined text-sm">qr_code</span>
                                                <span>Pase</span>
                                            </a>
                                            @if ($esProgramada)
                                                <form action="{{ route('citas.cancelar', $cita->ID_Citas) }}" method="POST" onsubmit="return confirm('¿Confirmas que deseas cancelar esta cita?');" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold transition-colors"
                                                            title="Cancelar cita">
                                                        <span class="material-symbols-outlined text-sm">close</span>
                                                        <span>Cancelar</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    {{-- ══ SECCIÓN 2: MIS EXPEDIENTES RECIENTES ══ --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-xl font-bold text-[#001229] flex items-center gap-2" style="font-family: var(--font-display);">
                    <span class="material-symbols-outlined text-[#0051d5]">folder_managed</span>
                    <span>Mis Expedientes de Trámites</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Expedientes telemáticos con trazabilidad de dictámenes y resoluciones oficiales.
                </p>
            </div>
            <a href="{{ route('expedientes.index') }}" class="inline-flex items-center gap-1.5 text-xs text-[#0051d5] font-semibold hover:underline">
                <span>Ver todos mis expedientes</span>
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>

        @if ($solicitudes->isEmpty())
            <div class="p-8 text-center bg-white rounded-2xl border border-slate-200/80">
                <p class="text-xs text-slate-500">No tienes expedientes activos en este momento.</p>
                <a href="{{ route('tramites.index') }}" class="mt-2 inline-flex items-center gap-1 text-xs text-[#0051d5] font-semibold hover:underline">
                    <span>Iniciar un nuevo trámite</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($solicitudes as $sol)
                    @php
                        $folioCod = $sol->folio?->Codigo_Folio ?? sprintf('SETC-%s-%05d', date('Y'), $sol->ID_Solicitud);
                    @endphp
                    <div class="p-5 bg-white rounded-xl border border-slate-200/80 shadow-xs flex flex-col justify-between hover:shadow-md transition-all group">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs font-bold text-[#0051d5] bg-[#eff4ff] px-2 py-0.5 rounded-md">
                                    {{ $folioCod }}
                                </span>
                                <span class="text-[11px] text-slate-400">
                                    {{ $sol->Fecha_Creacion_Solicitud ? $sol->Fecha_Creacion_Solicitud->format('d/m/Y') : '' }}
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-[#001229] group-hover:text-[#0051d5] transition-colors line-clamp-2">
                                {{ $sol->versionTramite?->tipoTramite?->Nombre ?? 'Expediente Institucional' }}
                            </h3>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-end">
                            <a href="{{ route('expedientes.show', $folioCod) }}" class="inline-flex items-center gap-1 text-xs text-[#0051d5] font-semibold hover:underline">
                                <span>Ver Expediente</span>
                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ══ SECCIÓN 3: ACTUALIZAR DATOS PERSONALES & CONTRASEÑA ══ --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 lg:p-8 shadow-xs space-y-6">
        <div class="border-b border-slate-100 pb-4">
            <h2 class="text-xl font-bold text-[#001229] flex items-center gap-2" style="font-family: var(--font-display);">
                <span class="material-symbols-outlined text-[#0051d5]">manage_accounts</span>
                <span>Actualizar Datos de Cuenta y Contraseña</span>
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Mantén tus datos de contacto al día para recibir notificaciones sobre tus citas y dictámenes oficiales.
            </p>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-6 max-w-2xl">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="nombre" class="block text-xs font-semibold text-[#0b1c30] mb-1">Nombre Completo</label>
                    <input type="text"
                           name="nombre"
                           id="nombre"
                           value="{{ old('nombre', $usuario->Nombre ?? $usuario->name) }}"
                           required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-[#0b1c30] mb-1">Correo Electrónico Oficial</label>
                    <input type="email"
                           name="email"
                           id="email"
                           value="{{ old('email', $usuario->Email ?? $usuario->email) }}"
                           required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 space-y-4">
                <span class="block text-xs font-bold text-[#001229] uppercase tracking-wider">
                    Cambio de Contraseña (Opcional)
                </span>

                <div>
                    <label for="current_password" class="block text-xs font-semibold text-[#0b1c30] mb-1">Contraseña Actual</label>
                    <input type="password"
                           name="current_password"
                           id="current_password"
                           placeholder="Ingresa tu contraseña actual solo si deseas cambiarla"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-[#0b1c30] mb-1">Nueva Contraseña</label>
                        <input type="password"
                               name="password"
                               id="password"
                               placeholder="Mínimo 8 caracteres"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-[#0b1c30] mb-1">Confirmar Nueva Contraseña</label>
                        <input type="password"
                               name="password_confirmation"
                               id="password_confirmation"
                               placeholder="Repite la nueva contraseña"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="px-6 py-2.5 bg-[#001229] hover:bg-[#0f2744] text-white text-xs font-semibold rounded-xl transition-all shadow-sm">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

