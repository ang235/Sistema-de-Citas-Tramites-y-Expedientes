<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequisitoRequest extends FormRequest
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
            'id_version_tramite' => ['required', 'integer', 'exists:version_tramite,ID_Version_Tramite'],
            'nombre' => ['required', 'string', 'max:200'],
            'prioridad' => ['required', 'string', 'in:Obligatorio,Opcional'],
            'formato' => ['required', 'string', 'max:50'],
        ];
    }
}

