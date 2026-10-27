{{--
    Componente: x-setc-badge
    Uso:
        <x-setc-badge status="success">Verificado</x-setc-badge>
        <x-setc-badge status="warning" dot>En Revisión</x-setc-badge>
        <x-setc-badge status="danger">Acción Requerida</x-setc-badge>
        <x-setc-badge status="info">En Proceso</x-setc-badge>
        <x-setc-badge status="neutral">Sin Actividad</x-setc-badge>
        <x-setc-badge status="primary">Folio SETC</x-setc-badge>

    Props:
        $status  string  — success | warning | danger | info | neutral | primary
        $dot     bool    — mostrar punto indicador de color (default: false)
--}}
@props([
    'status' => 'neutral',
    'dot'    => false,
])

@php
    $classes = match($status) {
        'success' => 'badge badge-success',
        'warning' => 'badge badge-warning',
        'danger'  => 'badge badge-danger',
        'info'    => 'badge badge-info',
        'primary' => 'badge badge-primary',
        default   => 'badge badge-neutral',
    };
    $dotColor = match($status) {
        'success' => 'var(--color-success-dot)',
        'warning' => 'var(--color-warning)',
        'danger'  => 'var(--color-danger)',
        'info'    => 'var(--color-secondary)',
        'primary' => 'var(--color-secondary)',
        default   => 'var(--color-outline)',
    };
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($dot)
        <span class="inline-block w-1.5 h-1.5 rounded-full shrink-0"
              style="background-color: {{ $dotColor }}"></span>
    @endif
    {{ $slot }}
</span>
