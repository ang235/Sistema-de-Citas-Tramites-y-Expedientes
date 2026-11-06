@extends('layouts.public')

@section('title', 'Restablecer Contraseña')
@section('meta_description', 'Define una nueva contraseña para tu cuenta SETC.')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="breadcrumb-item">SETC</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <span class="breadcrumb-item current">Nueva Contraseña</span>
@endsection

@section('content')
<div class="min-h-[calc(100vh-14rem)] flex items-center justify-center py-12 px-6 lg:px-12">
    <div class="w-full max-w-md space-y-8">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-8 sm:p-10 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-[#0051d5]"></div>

            <div class="space-y-3 text-center mb-8">
                <div class="inline-flex w-12 h-12 rounded-xl bg-[#eff4ff] items-center justify-center text-[#0051d5] mb-1 shadow-sm">
                    <span class="material-symbols-outlined text-2xl">lock_reset</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-[#001229]" style="font-family: var(--font-display);">
                    Crear Nueva Contraseña
                </h1>
                <p class="text-sm text-[#44474d]">
                    Establece tu nueva credencial de seguridad de al menos 8 caracteres.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                    <span class="font-semibold block">Error de validación:</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="space-y-1.5">
                    <label for="email" class="block text-sm font-semibold text-[#0b1c30]">
                        Confirmar Correo Electrónico
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email', $email ?? '') }}"
                           required
                           class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="block text-sm font-semibold text-[#0b1c30]">
                        Nueva Contraseña
                    </label>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           placeholder="Mínimo 8 caracteres"
                           class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                </div>

                <div class="space-y-1.5">
                    <label for="password_confirmation" class="block text-sm font-semibold text-[#0b1c30]">
                        Repetir Nueva Contraseña
                    </label>
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           required
                           class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5]">
                </div>

                <button type="submit"
                        class="w-full h-11 bg-[#0051d5] hover:bg-[#003ea8] text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center justify-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-lg">check_circle</span>
                    <span>Actualizar Contraseña</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

