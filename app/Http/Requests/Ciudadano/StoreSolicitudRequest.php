<?php

declare(strict_types=1);

namespace App\Http\Requests\Ciudadano;

use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitudRequest extends FormRequest
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
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'documentos' => ['nullable', 'array'],
            'documentos.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'], // max 10MB
        ];
    }
}

