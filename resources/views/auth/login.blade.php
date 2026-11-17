@extends('layouts.public')

@section('title', 'Iniciar Sesión')
@section('meta_description', 'Accede a la Ventanilla Única Digital del Sistema de Expedientes de Trámites y Citas.')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="breadcrumb-item">SETC</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <span class="breadcrumb-item current">Iniciar Sesión</span>
@endsection

@section('content')
<div class="min-h-[calc(100vh-14rem)] flex items-center justify-center py-12 px-6 lg:px-12">
    <div class="w-full max-w-md space-y-8">
        {{-- Card de Login --}}
        <div class="bg-white dark:bg-[#0e1c2e] rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800 p-8 sm:p-10 relative overflow-hidden">
            {{-- Detalle decorativo superior --}}
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-[#001229] via-[#0051d5] to-[#85f8c4]"></div>

            {{-- Cabecera del formulario --}}
            <div class="space-y-3 text-center mb-8">
                <div class="flex justify-center mb-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SETC" class="h-20 sm:h-24 w-auto object-contain drop-shadow-sm transition-transform duration-200 hover:scale-105">
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-[#001229] dark:text-white" style="font-family: var(--font-display);">
                    Acceso Ciudadano e Institucional
                </h1>
                <p class="text-sm text-[#44474d] dark:text-slate-400">
                    Ingresa con tu correo registrado para gestionar trámites, citas y consultar tus expedientes.
                </p>
            </div>

            {{-- Errores generales si existieran --}}
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm flex items-start gap-3">
                    <span class="material-symbols-outlined text-rose-600 dark:text-rose-400 text-lg shrink-0 mt-0.5">error</span>
                    <div>
                        <span class="font-semibold block">Verifica la información:</span>
                        <span class="text-xs">{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            {{-- Formulario --}}
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Campo Correo Electrónico --}}
                <div class="space-y-1.5">
                    <label for="email" class="block text-sm font-semibold text-[#0b1c30] dark:text-slate-200">
                        Correo Electrónico
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-lg pointer-events-none">mail</span>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               placeholder="ejemplo@correo.gob.mx"
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0b1624] text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5] focus:border-transparent transition-all @error('email') border-rose-400 ring-1 ring-rose-400 @enderror">
                    </div>
                    @error('email')
                        <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campo Contraseña --}}
                <div class="space-y-1.5" x-data="{ show: false }">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-semibold text-[#0b1c30] dark:text-slate-200">
                            Contraseña
                        </label>
                        <a href="{{ route('password.request') }}" class="text-xs text-[#0051d5] dark:text-blue-400 hover:underline font-medium">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-lg pointer-events-none">lock</span>
                        <input :type="show ? 'text' : 'password'"
                               id="password"
                               name="password"
                               required
                               placeholder="••••••••••••"
                               class="w-full pl-10 pr-11 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0b1624] text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5] focus:border-transparent transition-all @error('password') border-rose-400 ring-1 ring-rose-400 @enderror">
                        <button type="button"
                                @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 cursor-pointer"
                                tabindex="-1">
                            <span class="material-symbols-outlined text-lg" x-text="show ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Recordarme --}}
                <div class="flex items-center">
                    <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-[#44474d] dark:text-slate-400">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#0051d5] border-slate-300 dark:border-slate-700 focus:ring-[#0051d5]">
                        <span>Mantener sesión iniciada</span>
                    </label>
                </div>

                {{-- Botón Submit --}}
                <button type="submit"
                        class="w-full h-11 bg-[#001229] dark:bg-blue-600 hover:bg-[#0f2744] dark:hover:bg-blue-700 text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center justify-center gap-2 shadow-sm hover:shadow active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-[#0051d5] focus:ring-offset-2 cursor-pointer">
                    <span class="material-symbols-outlined text-lg">login</span>
                    <span>Ingresar al Sistema</span>
                </button>
            </form>

            {{-- Pie del card --}}
            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-center">
                <p class="text-sm text-[#44474d] dark:text-slate-400">
                    ¿Aún no tienes cuenta ciudadana?
                    <a href="{{ route('register') }}" class="text-[#0051d5] dark:text-blue-400 font-semibold hover:underline ml-1">
                        Regístrate aquí
                    </a>
                </p>
            </div>
        </div>

        {{-- Sello Institucional y Trazabilidad --}}
        <div class="text-center space-y-1 text-xs text-[#74777e] dark:text-slate-400">
            <div class="flex items-center justify-center gap-1.5 font-medium">
                <span class="material-symbols-outlined text-sm text-[#059669]">verified_user</span>
                <span>Conexión cifrada de alta seguridad GovTech</span>
            </div>
            <p>SETC • Sistema de Expedientes de Trámites y Citas</p>
        </div>
    </div>
</div>
@endsection
