<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreVersionTramiteRequest extends FormRequest
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
            'id_tipo_tramite' => ['required', 'integer', 'exists:tipo_tramite,ID_Tipo_Tramite'],
            'no_version' => ['required', 'string', 'max:50'],
            'estado_ver_tr' => ['required', 'string', 'in:Vigente,Histórica,Borrador'],
            'fecha_inicio_vig' => ['nullable', 'date'],
        ];
    }
}

