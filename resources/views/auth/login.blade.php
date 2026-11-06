@extends('layouts.public')

@section('title', 'Iniciar Sesión')
@section('meta_description', 'Accede a la Ventanilla Única Digital del Sistema Estatal de Citas, Trámites y Expedientes.')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="breadcrumb-item">SETC</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <span class="breadcrumb-item current">Iniciar Sesión</span>
@endsection

@section('content')
<div class="min-h-[calc(100vh-14rem)] flex items-center justify-center py-12 px-6 lg:px-12">
    <div class="w-full max-w-md space-y-8">
        {{-- Card de Login --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-8 sm:p-10 relative overflow-hidden">
            {{-- Detalle decorativo superior --}}
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-[#001229] via-[#0051d5] to-[#85f8c4]"></div>

            {{-- Cabecera del formulario --}}
            <div class="space-y-3 text-center mb-8">
                <div class="inline-flex w-12 h-12 rounded-xl bg-[#eff4ff] items-center justify-center text-[#0051d5] mb-1 shadow-sm">
                    <span class="material-symbols-outlined text-2xl">account_circle</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-[#001229]" style="font-family: var(--font-display);">
                    Acceso Ciudadano e Institucional
                </h1>
                <p class="text-sm text-[#44474d]">
                    Ingresa con tu correo registrado para gestionar trámites, citas y consultar tus expedientes.
                </p>
            </div>

            {{-- Errores generales si existieran --}}
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3">
                    <span class="material-symbols-outlined text-rose-600 text-lg shrink-0 mt-0.5">error</span>
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
                               autofocus
                               placeholder="ejemplo@correo.gob.mx"
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 placeholder:text-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5] focus:border-transparent transition-all @error('email') border-rose-400 ring-1 ring-rose-400 @enderror">
                    </div>
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campo Contraseña --}}
                <div class="space-y-1.5" x-data="{ show: false }">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-semibold text-[#0b1c30]">
                            Contraseña
                        </label>
                        <a href="{{ route('password.request') }}" class="text-xs text-[#0051d5] hover:underline font-medium">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">lock</span>
                        <input :type="show ? 'text' : 'password'"
                               id="password"
                               name="password"
                               required
                               placeholder="••••••••••••"
                               class="w-full pl-10 pr-11 py-2.5 rounded-lg border border-slate-300 text-slate-900 placeholder:text-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5] focus:border-transparent transition-all @error('password') border-rose-400 ring-1 ring-rose-400 @enderror">
                        <button type="button"
                                @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1"
                                tabindex="-1">
                            <span class="material-symbols-outlined text-lg" x-text="show ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Recordarme --}}
                <div class="flex items-center">
                    <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-[#44474d]">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#0051d5] border-slate-300 focus:ring-[#0051d5]">
                        <span>Mantener sesión iniciada</span>
                    </label>
                </div>

                {{-- Botón Submit --}}
                <button type="submit"
                        class="w-full h-11 bg-[#001229] hover:bg-[#0f2744] text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center justify-center gap-2 shadow-sm hover:shadow active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-[#0051d5] focus:ring-offset-2">
                    <span class="material-symbols-outlined text-lg">login</span>
                    <span>Ingresar al Sistema</span>
                </button>
            </form>

            {{-- Pie del card --}}
            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                <p class="text-sm text-[#44474d]">
                    ¿Aún no tienes cuenta ciudadana?
                    <a href="{{ route('register') }}" class="text-[#0051d5] font-semibold hover:underline ml-1">
                        Regístrate aquí
                    </a>
                </p>
            </div>
        </div>

        {{-- Sello Institucional y Trazabilidad --}}
        <div class="text-center space-y-1 text-xs text-[#74777e]">
            <div class="flex items-center justify-center gap-1.5 font-medium">
                <span class="material-symbols-outlined text-sm text-[#059669]">verified_user</span>
                <span>Conexión cifrada de alta seguridad GovTech</span>
            </div>
            <p>SETC • Sistema Estatal de Citas, Trámites y Expedientes</p>
        </div>
    </div>
</div>
@endsection

