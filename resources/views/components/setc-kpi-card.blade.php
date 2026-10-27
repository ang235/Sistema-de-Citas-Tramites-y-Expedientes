{{--
    Componente: x-setc-kpi-card
    Uso:
        <x-setc-kpi-card
            label="Total Registrados"
            value="2,845"
            icon="people"
            trend="+12 nuevos esta semana"
            trend-positive />

        <x-setc-kpi-card
            label="Expedientes en Cola"
            value="14"
            icon="hourglass_top"
            badge="4 con SLA < 6 hrs"
            badge-status="danger" />

    Props:
        $label          string  — Etiqueta del KPI
        $value          string  — Valor principal
        $icon           string  — Material Symbol
        $iconColor      string  — CSS color del ícono (default: secondary)
        $trend          string  — Texto de tendencia
        $trendPositive  bool    — Verde si es positivo (default: neutral)
        $trendNegative  bool    — Rojo si es negativo
        $badge          string  — Chip de alerta debajo del valor
        $badgeStatus    string  — success|warning|danger|info|neutral
--}}
@props([
    'label'          => '',
    'value'          => '—',
    'icon'           => 'analytics',
    'iconColor'      => 'var(--color-secondary)',
    'trend'          => null,
    'trendPositive'  => false,
    'trendNegative'  => false,
    'badge'          => null,
    'badgeStatus'    => 'warning',
])

@php
    $trendColor = 'var(--color-on-surface-variant)';
    if ($trendPositive) $trendColor = 'var(--color-success)';
    if ($trendNegative) $trendColor = 'var(--color-danger)';

    $trendIcon  = $trendPositive ? 'trending_up' : ($trendNegative ? 'trending_down' : 'remove');
@endphp

<div {{ $attributes->merge(['class' => 'kpi-card flex flex-col gap-3']) }}>
    <div class="flex items-start justify-between gap-3">
        <p class="kpi-label">{{ $label }}</p>
        <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
             style="background-color: var(--color-surface-container-low);">
            <span class="material-symbols-outlined text-base" style="color: {{ $iconColor }};">{{ $icon }}</span>
        </div>
    </div>

    <div class="flex items-end justify-between gap-2">
        <p class="kpi-value">{{ $value }}</p>

        @if($trend)
            <div class="flex items-center gap-1 pb-0.5"
                 style="color: {{ $trendColor }}; font-size: 12px; font-weight: 500;">
                <span class="material-symbols-outlined text-sm">{{ $trendIcon }}</span>
                {{ $trend }}
            </div>
        @endif
    </div>

    @if($badge)
        <x-setc-badge :status="$badgeStatus" dot>{{ $badge }}</x-setc-badge>
    @endif
</div>
