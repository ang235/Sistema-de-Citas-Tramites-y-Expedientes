{{--
    Componente: x-setc-card
    Uso:
        <x-setc-card>
            <x-slot:header>
                <h2 class="text-headline-sm">Título</h2>
            </x-slot:header>
            Contenido de la card
            <x-slot:footer>
                <x-setc-button>Guardar</x-setc-button>
            </x-slot:footer>
        </x-setc-card>

    Props:
        $padding  string  — Padding del body (default: '')  — vacío usa card-body
        $noBorder bool    — Sin borde (default: false)
        $flat     bool    — Sin sombra (default: false)
--}}
@props([
    'header'   => null,
    'footer'   => null,
    'noBorder' => false,
    'flat'     => false,
])

@php
    $base = 'card';
    if ($noBorder) $base .= ' border-0';
    if ($flat)     $base .= ' shadow-none';
@endphp

<div {{ $attributes->merge(['class' => $base]) }}>

    @if($header)
        <div class="card-header">
            {{ $header }}
        </div>
    @endif

    <div class="card-body">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>
