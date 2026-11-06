@extends('layouts.public')

@section('title', 'Recuperar Contraseña')
@section('meta_description', 'Recupera el acceso a tu cuenta del portal SETC mediante enlace seguro.')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="breadcrumb-item">SETC</a>
    <span class="breadcrumb-separator material-symbols-outlined">chevron_right</span>
    <span class="breadcrumb-item current">Recuperar Contraseña</span>
@endsection

@section('content')
<div class="min-h-[calc(100vh-14rem)] flex items-center justify-center py-12 px-6 lg:px-12">
    <div class="w-full max-w-md space-y-8">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-8 sm:p-10 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-[#0051d5]"></div>

            <div class="space-y-3 text-center mb-8">
                <div class="inline-flex w-12 h-12 rounded-xl bg-[#eff4ff] items-center justify-center text-[#0051d5] mb-1 shadow-sm">
                    <span class="material-symbols-outlined text-2xl">key</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-[#001229]" style="font-family: var(--font-display);">
                    Recuperar Contraseña
                </h1>
                <p class="text-sm text-[#44474d]">
                    Ingresa el correo electrónico asociado a tu cuenta y te enviaremos las instrucciones de restablecimiento.
                </p>
            </div>

            @if (session('status'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3">
                    <span class="material-symbols-outlined text-emerald-600 text-lg shrink-0 mt-0.5">check_circle</span>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                @csrf

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
                               placeholder="tu.correo@dominio.com"
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 placeholder:text-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#0051d5] focus:border-transparent transition-all @error('email') border-rose-400 ring-1 ring-rose-400 @enderror">
                    </div>
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full h-11 bg-[#001229] hover:bg-[#0f2744] text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center justify-center gap-2 shadow-sm hover:shadow active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-[#0051d5] focus:ring-offset-2">
                    <span class="material-symbols-outlined text-lg">send</span>
                    <span>Enviar Enlace de Recuperación</span>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm text-[#0051d5] font-semibold hover:underline">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    <span>Regresar al inicio de sesión</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

