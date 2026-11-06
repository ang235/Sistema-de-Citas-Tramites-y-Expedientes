<?php

declare(strict_types=1);

namespace App\Http\Requests\Operativo;

use Illuminate\Foundation\Http\FormRequest;

class AsignarExpedienteRequest extends FormRequest
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
            'id_revisor' => ['required', 'integer', 'exists:usuarios,ID_Usuarios'],
        ];
    }
}

