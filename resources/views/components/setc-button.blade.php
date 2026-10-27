{{--
    Componente: x-setc-button
    Uso:
        <x-setc-button>Guardar</x-setc-button>
        <x-setc-button variant="secondary" href="/ruta">Cancelar</x-setc-button>
        <x-setc-button variant="danger" type="submit">Eliminar</x-setc-button>
        <x-setc-button variant="ghost" icon="add">Nuevo</x-setc-button>
        <x-setc-button :loading="true">Procesando…</x-setc-button>

    Props:
        $variant  string  — primary | secondary | danger | ghost  (default: primary)
        $type     string  — button | submit | reset                (default: button)
        $href     string  — Si se provee, renderiza como <a>
        $icon     string  — Nombre de Material Symbol izquierda
        $iconEnd  string  — Nombre de Material Symbol derecha
        $loading  bool    — Estado de carga con spinner
        $size     string  — sm | md (default: md)
--}}
@props([
    'variant' => 'primary',
    'type'    => 'button',
    'href'    => null,
    'icon'    => null,
    'iconEnd' => null,
    'loading' => false,
    'size'    => 'md',
])

@php
    $cls = match($variant) {
        'secondary' => 'btn-secondary',
        'danger'    => 'btn-danger',
        'ghost'     => 'btn-ghost',
        default     => 'btn-primary',
    };
    if ($size === 'sm') $cls .= ' !h-8 !px-3 !text-xs';

    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    @if(!$href) type="{{ $type }}" @endif
    @if($loading) disabled aria-busy="true" @endif
    {{ $attributes->merge(['class' => $cls]) }}
>
    @if($loading)
        <svg class="animate-spin w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
    @elseif($icon)
        <span class="material-symbols-outlined text-base shrink-0" aria-hidden="true">{{ $icon }}</span>
    @endif

    {{ $slot }}

    @if($iconEnd && !$loading)
        <span class="material-symbols-outlined text-base shrink-0" aria-hidden="true">{{ $iconEnd }}</span>
    @endif
</{{ $tag }}>
