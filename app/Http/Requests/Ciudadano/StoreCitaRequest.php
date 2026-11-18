<?php

declare(strict_types=1);

namespace App\Http\Requests\Ciudadano;

use Illuminate\Foundation\Http\FormRequest;

class StoreCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'id_solicitud' => ['required', 'integer', 'exists:solicitudes,ID_Solicitud'],
            'id_revisor' => ['nullable', 'integer', 'exists:usuarios,ID_Usuarios'],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora' => ['required', 'date_format:H:i'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id_solicitud.required' => 'Debe seleccionar un expediente con recaudos aprobados por el revisor técnico para agendar la cita.',
            'id_solicitud.exists' => 'El expediente seleccionado no es válido.',
            'fecha.required' => 'La fecha de la cita es obligatoria.',
            'fecha.after_or_equal' => 'No se permiten citas en fechas anteriores a la actual.',
            'hora.required' => 'Debe seleccionar un horario disponible para su cita.',
            'hora.date_format' => 'El formato del horario no es válido.',
        ];
    }
}
