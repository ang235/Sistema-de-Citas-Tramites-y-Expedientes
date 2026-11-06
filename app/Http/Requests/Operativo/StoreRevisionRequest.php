<?php

declare(strict_types=1);

namespace App\Http\Requests\Operativo;

use Illuminate\Foundation\Http\FormRequest;

class StoreRevisionRequest extends FormRequest
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
            'resultado' => ['required', 'string', 'in:Aprobado,Observado,Rechazado,En Revisión'],
            'observaciones' => ['nullable', 'array'],
            'observaciones.*.detalles' => ['required_with:observaciones', 'string', 'max:2000'],
        ];
    }
}

