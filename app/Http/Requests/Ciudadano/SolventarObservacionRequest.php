<?php

declare(strict_types=1);

namespace App\Http\Requests\Ciudadano;

use Illuminate\Foundation\Http\FormRequest;

class SolventarObservacionRequest extends FormRequest
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
            'mensaje_respuesta' => ['required', 'string', 'max:2000'],
            'archivo' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }
}

