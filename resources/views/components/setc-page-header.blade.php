{{--
    Componente: x-setc-page-header
    Uso (portal público):
        <x-setc-page-header
            tag="Módulo de Gestión de Turnos"
            tag-folio="SETC-2025-8841"
            title="Agenda tu turno presencial o virtual"
            description="Elige la fecha y horario que mejor te convenga. ¡Solo te tomará 2 minutos!"
            icon="calendar_month">

            {{-- Slot de acción (esquina derecha) --}}
            <x-setc-button>Nueva Cita</x-setc-button>
        </x-setc-page-header>

    Uso (portal interno):
        <x-setc-page-header
            title="Gestión de Usuarios y Control de Accesos"
            description="Supervise el padrón de identidades institucionales."
            compact>

            <x-setc-button icon="download" variant="secondary">Exportar</x-setc-button>
            <x-setc-button icon="person_add">Nuevo Usuario</x-setc-button>
        </x-setc-page-header>

    Props:
        $title       string  — Título principal (h1)
        $description string  — Descripción
        $tag         string  — Etiqueta de módulo
        $tagFolio    string  — Código/folio secundario
        $icon        string  — Material Symbol decorativo
        $compact     bool    — Versión compacta para portal interno
        $glow        bool    — Efecto de glow de fondo (default: true en public)
--}}
@props([
    'title'       => '',
    'description' => '',
    'tag'         => null,
    'tagFolio'    => null,
    'icon'        => null,
    'compact'     => false,
    'glow'        => null,
])

@php
    $glow = $glow ?? !$compact;
    $padding = $compact ? 'p-6' : 'p-8';
@endphp

<div class="relative overflow-hidden rounded-xl {{ $padding }} shadow-sm"
     style="background-color: var(--color-surface-container-lowest);">

    {{-- Glow decorativo --}}
    @if($glow)
        <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full pointer-events-none"
             style="background-color: rgba(49,107,243,0.08); filter: blur(64px);" aria-hidden="true"></div>
    @endif

    <div class="relative z-10 flex flex-col {{ $compact ? 'lg:flex-row lg:items-center' : 'lg:flex-row lg:items-start' }} justify-between gap-6">

        {{-- Texto principal --}}
        <div class="space-y-2 flex-1 min-w-0">

            {{-- Tag / módulo --}}
            @if($tag || $tagFolio)
                <div class="flex flex-wrap items-center gap-2">
                    @if($tag)
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-label-sm"
                             style="background-color: var(--color-surface-container); color: var(--color-on-surface);">
                            @if($icon)
                                <span class="material-symbols-outlined text-xs" aria-hidden="true">{{ $icon }}</span>
                            @else
                                <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background-color: var(--color-on-tertiary-fixed-variant);"></span>
                            @endif
                            <span>{{ $tag }}</span>
                        </div>
                    @endif
                    @if($tagFolio)
                        <span class="text-code-sm font-medium px-2 py-0.5 rounded"
                              style="background-color: var(--color-surface-container-high); color: var(--color-secondary);">
                            {{ $tagFolio }}
                        </span>
                    @endif
                </div>
            @endif

            {{-- Título --}}
            @if($title)
                <h1 class="{{ $compact ? 'text-headline-md' : 'text-headline-xl' }} tracking-tight"
                    style="font-family: var(--font-display); color: var(--color-primary);">
                    {{ $title }}
                </h1>
            @endif

            {{-- Descripción --}}
            @if($description)
                <p class="text-body-md" style="color: var(--color-on-surface-variant); max-width: 65ch;">
                    {{ $description }}
                </p>
            @endif
        </div>

        {{-- Slot de acciones --}}
        @if($slot->isNotEmpty())
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
