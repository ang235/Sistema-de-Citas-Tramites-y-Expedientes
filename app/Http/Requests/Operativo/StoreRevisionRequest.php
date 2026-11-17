<?php

declare(strict_types=1);

namespace App\Http\Requests\Operativo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRevisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Limpiar observaciones vacías antes de validar
        if ($this->has('observaciones') && is_array($this->input('observaciones'))) {
            $filtradas = array_values(array_filter($this->input('observaciones'), function ($obs) {
                return is_array($obs) && isset($obs['detalles']) && trim((string) $obs['detalles']) !== '';
            }));

            if (empty($filtradas)) {
                $this->merge(['observaciones' => null]);
            } else {
                $this->merge(['observaciones' => $filtradas]);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $esObservadoORechazado = in_array($this->input('resultado'), ['Observado', 'Rechazado']);

        return [
            'resultado' => ['required', 'string', 'in:Aprobado,Observado,Rechazado,En Revisión'],
            'observaciones' => [
                Rule::requiredIf($esObservadoORechazado),
                'nullable',
                'array',
            ],
            'observaciones.*.detalles' => [
                $esObservadoORechazado ? 'required' : 'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'resultado.required' => 'Debe seleccionar el resultado oficial del dictamen técnico.',
            'resultado.in' => 'El resultado seleccionado no es válido.',
            'observaciones.required' => 'Debe ingresar la fundamentación jurídica u observación técnica cuando el dictamen es Observado o Rechazado.',
            'observaciones.*.detalles.required' => 'El detalle de la observación técnica es obligatorio.',
            'observaciones.*.detalles.max' => 'El detalle de la observación no puede exceder 2000 caracteres.',
        ];
    }
}
