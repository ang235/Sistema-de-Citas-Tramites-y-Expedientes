<?php

declare(strict_types=1);

namespace App\Http\Requests\Operativo;

use Illuminate\Foundation\Http\FormRequest;

class EmitirResolucionRequest extends FormRequest
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
            'tipo_resolucion' => ['required', 'string', 'in:Favorable,Desfavorable,Inadmisible,Cancelada'],
            'comentarios' => ['nullable', 'string', 'max:2000'],
        ];
    }
}

