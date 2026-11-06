<?php

declare(strict_types=1);

namespace App\Http\Requests\Ciudadano;

use App\Models\VersionTramite;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'documentos.*' => ['nullable', 'file', 'mimes:pdf', 'max:10240'], // Solo archivos PDF (máx 10MB)
        ];
    }

    /**
     * Valida que todos los requisitos marcados como 'Obligatorio' hayan sido subidos.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $idVersion = (int) $this->input('id_version_tramite');
                if (! $idVersion) {
                    return;
                }

                $version = VersionTramite::with('requisitos')->find($idVersion);
                if (! $version) {
                    return;
                }

                $archivos = $this->file('documentos', []);

                foreach ($version->requisitos as $req) {
                    if ($req->Prioridad === 'Obligatorio') {
                        if (! isset($archivos[$req->ID_Requisitos]) || ! $archivos[$req->ID_Requisitos]->isValid()) {
                            $validator->errors()->add(
                                "documentos.{$req->ID_Requisitos}",
                                "El requisito obligatorio '{$req->Nombre}' debe ser adjuntado en formato PDF."
                            );
                        }
                    }
                }
            },
        ];
    }
}


