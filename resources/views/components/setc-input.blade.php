{{--
    Componente: x-setc-input
    Uso:
        <x-setc-input
            name="nombre"
            label="Nombre completo"
            placeholder="Ej. Juan Pérez García"
            helper="Tal como aparece en su documento de identidad"
            required />

        <x-setc-input
            name="folio"
            label="N° de Folio"
            type="text"
            icon="search"
            :error="$errors->first('folio')" />

    Props:
        $label       string  — Texto del label visible
        $name        string  — name/id del input
        $type        string  — Tipo HTML (default: text)
        $placeholder string  — Placeholder
        $helper      string  — Texto de ayuda bajo el campo
        $error       string  — Mensaje de error (sobreescribe helper)
        $icon        string  — Material Symbol a la izquierda del input
        $required    bool    — Muestra asterisco y atributo required
        $disabled    bool    — Deshabilita el campo
--}}
@props([
    'label'       => '',
    'name'        => '',
    'type'        => 'text',
    'placeholder' => '',
    'helper'      => '',
    'error'       => null,
    'icon'        => null,
    'required'    => false,
    'disabled'    => false,
])

@php
    $id       = $name ?: uniqid('input_');
    $hasError = $error || $errors->has($name);
    $errorMsg = $error ?: $errors->first($name);
@endphp

<div class="flex flex-col gap-1">
    @if($label)
        <label for="{{ $id }}" class="field-label">
            {{ $label }}
            @if($required)
                <span style="color: var(--color-danger);" aria-hidden="true"> *</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($icon)
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-sm pointer-events-none"
                  style="color: var(--color-on-surface-variant);" aria-hidden="true">{{ $icon }}</span>
        @endif

        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="{{ $type }}"
            placeholder="{{ $placeholder }}"
            @if($required)  required    @endif
            @if($disabled)  disabled    @endif
            @if($hasError)  aria-describedby="{{ $id }}-error" aria-invalid="true" @endif
            {{ $attributes->merge([
                'class' => 'input ' . ($icon ? 'pl-9 ' : '') . ($hasError ? '!border-[var(--color-danger)]' : ''),
                'value' => old($name),
            ]) }}
        />
    </div>

    @if($hasError)
        <span id="{{ $id }}-error" class="field-error" role="alert">
            <span class="material-symbols-outlined text-xs align-middle mr-0.5" aria-hidden="true">error</span>
            {{ $errorMsg }}
        </span>
    @elseif($helper)
        <span class="field-helper">{{ $helper }}</span>
    @endif
</div>
