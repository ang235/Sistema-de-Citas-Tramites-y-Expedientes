@extends('layouts.public')

@section('title', 'Registro Ciudadano')
@section('meta_description', 'Crea tu cuenta en la Ventanilla Única Digital del SETC para gestionar tus trámites institucionales.')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="breadcrumb-item">SETC</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <span class="breadcrumb-item current">Registro Ciudadano</span>
@endsection

@section('content')
<div class="min-h-[calc(100vh-14rem)] flex items-center justify-center py-12 px-6 lg:px-12">
    <div class="w-full max-w-lg space-y-8">
        {{-- Card de Registro --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-8 sm:p-10 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-[#0051d5] via-[#85f8c4] to-[#001229]"></div>

            {{-- Cabecera --}}
            <div class="space-y-3 text-center mb-8">
                <div class="inline-flex w-12 h-12 rounded-xl bg-[#eff4ff] items-center justify-center text-[#0051d5] mb-1 shadow-sm">
                    <span class="material-symbols-outlined text-2xl">person_add</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-[#001229]" style="font-family: var(--font-display);">
                    Crear Cuenta Ciudadana
                </h1>
                <p class="text-sm text-[#44474d]">
                    Únete a la plataforma telemática para iniciar solicitudes, adjuntar recaudos y agendar citas en línea.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3">
                    <span class="material-symbols-outlined text-rose-600 text-lg shrink-0 mt-0.5">error</span>
                    <div>
                        <span class="font-semibold block">Por favor corrige los siguientes datos:</span>
                        <ul class="list-disc list-inside text-xs mt-1 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Formulario --}}
            <form action="{{ route('register') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Nombre Completo --}}
                <div class="space-y-1.5">
                    <label for="nombre" class="block text-sm font-semibold text-[#0b1c30]">
                        Nombre Completo o Razón Social
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">badge</span>
                        <input type="text"
                               id="nombre"
                               name="nombre"
                               value="{{ old('nombre') }}"
                               required
                               placeholder="Ej. María Elena Morales Castro"
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 placeholder:text-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5] focus:border-transparent transition-all @error('nombre') border-rose-400 ring-1 ring-rose-400 @enderror">
                    </div>
                    @error('nombre')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Correo Electrónico --}}
                <div class="space-y-1.5">
                    <label for="email" class="block text-sm font-semibold text-[#0b1c30]">
                        Correo Electrónico
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">mail</span>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               placeholder="tu.correo@dominio.com"
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 placeholder:text-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5] focus:border-transparent transition-all @error('email') border-rose-400 ring-1 ring-rose-400 @enderror">
                    </div>
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Contraseña --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="password" class="block text-sm font-semibold text-[#0b1c30]">
                            Contraseña
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">lock</span>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   required
                                   placeholder="Mín. 8 caracteres"
                                   class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 placeholder:text-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5] focus:border-transparent transition-all @error('password') border-rose-400 ring-1 ring-rose-400 @enderror">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="block text-sm font-semibold text-[#0b1c30]">
                            Confirmar Contraseña
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">lock_reset</span>
                            <input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   required
                                   placeholder="Repite la contraseña"
                                   class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 placeholder:text-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5] focus:border-transparent transition-all">
                        </div>
                    </div>
                </div>

                {{-- Términos --}}
                <div class="p-3.5 rounded-xl bg-[#eff4ff] text-xs text-[#44474d] flex items-start gap-2.5">
                    <span class="material-symbols-outlined text-[#0051d5] text-base shrink-0 mt-0.5">policy</span>
                    <span>
                        Al registrarte aceptas las condiciones de uso telemático del Sistema Estatal SETC y el tratamiento protegido de tus datos conforme a la legislación de transparencia y privacidad.
                    </span>
                </div>

                {{-- Botón Submit --}}
                <button type="submit"
                        class="w-full h-11 bg-[#0051d5] hover:bg-[#003ea8] text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center justify-center gap-2 shadow-sm hover:shadow active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-[#0051d5] focus:ring-offset-2">
                    <span class="material-symbols-outlined text-lg">check_circle</span>
                    <span>Completar Registro</span>
                </button>
            </form>

            {{-- Pie --}}
            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                <p class="text-sm text-[#44474d]">
                    ¿Ya posees una cuenta activa?
                    <a href="{{ route('login') }}" class="text-[#0051d5] font-semibold hover:underline ml-1">
                        Inicia sesión aquí
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

