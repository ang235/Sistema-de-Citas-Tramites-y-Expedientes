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
            'id_solicitud' => ['nullable', 'integer', 'exists:solicitudes,ID_Solicitud'],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora' => ['required', 'date_format:H:i'],
        ];
    }
}

