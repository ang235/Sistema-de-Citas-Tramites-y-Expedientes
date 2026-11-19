<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TipoTramite;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    /**
     * Responde a la consulta de un usuario utilizando IA generativa (Gemini/OpenAI)
     * con fallback automático a la base de conocimiento local estructurada.
     *
     * @param  array<int, array{role: string, content: string}>  $historial
     * @return array{respuesta: string, sugerencias: array<int, string>}
     */
    public function responder(string $mensaje, array $historial = []): array
    {
        $mensajeLimpio = trim($mensaje);
        if ($mensajeLimpio === '') {
            return [
                'respuesta' => 'Hola, soy el Asistente Virtual del SETC. ¿En qué trámite o procedimiento te puedo orientar hoy?',
                'sugerencias' => [
                    '¿Qué requisitos piden para Licencia de Funcionamiento?',
                    '¿Cómo y cuándo puedo agendar una cita?',
                    '¿Qué trámites puedo realizar en línea?',
                ],
            ];
        }

        // 1. Intentar llamar a API de Gemini si está configurada la llave en el entorno
        $geminiKey = config('services.gemini.key') ?? env('GEMINI_API_KEY');
        if (! empty($geminiKey)) {
            try {
                $respuestaIa = $this->consultarGemini($mensajeLimpio, $historial, (string) $geminiKey);
                if (! empty($respuestaIa)) {
                    return [
                        'respuesta' => $respuestaIa,
                        'sugerencias' => $this->generarSugerenciasDinamicas($mensajeLimpio),
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('ChatbotService: Falló la llamada a Gemini API, usando base de conocimiento local.', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // 2. Fallback inteligente directo con la base de conocimiento de la base de datos
        return $this->responderConBaseConocimiento($mensajeLimpio);
    }

    /**
     * Consulta a Google Gemini API con el catálogo institucional inyectado.
     *
     * @param  array<int, array{role: string, content: string}>  $historial
     */
    protected function consultarGemini(string $mensaje, array $historial, string $apiKey): ?string
    {
        $contexto = $this->construirContextoInstitucional();

        $contents = [];
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => "INSTRUCCIÓN DEL SISTEMA:\n".$contexto]],
        ];
        $contents[] = [
            'role' => 'model',
            'parts' => [['text' => 'Entendido. Soy el Asistente Virtual Oficial del Sistema de Expedientes de Trámites y Citas (SETC). Responderé a los ciudadanos de forma profesional, cordial, precisa y estructurada, basándome estrictamente en los trámites, normativas y requisitos provistos.']],
        ];

        // Añadir últimos turnos de historial para mantener conversación
        $turnosRecientes = array_slice($historial, -4);
        foreach ($turnosRecientes as $turno) {
            $role = ($turno['role'] ?? 'user') === 'assistant' ? 'model' : 'user';
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => (string) ($turno['content'] ?? '')]],
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $mensaje]],
        ];

        $response = Http::timeout(10)->post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key='.$apiKey,
            [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => 800,
                ],
            ]
        );

        if ($response->successful()) {
            $data = $response->json();

            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        }

        return null;
    }

    /**
     * Construye el prompt con los trámites reales, versiones y requisitos de la BD.
     */
    protected function construirContextoInstitucional(): string
    {
        $tramites = TipoTramite::where('Estado_Tipo_Tr', 'Activo')
            ->with(['versiones' => fn ($q) => $q->where('Estado_Ver_Tr', 'Vigente')->with('requisitos')])
            ->get();

        $info = "Eres el Asistente Virtual Oficial del Sistema de Expedientes de Trámites y Citas (SETC).\n";
        $info .= "REGLAS INSTITUCIONALES:\n";
        $info .= "- Trámites 100% digitales con trazabilidad, firma digital activa y sello institucional.\n";
        $info .= "- Los ciudadanos inician sus solicitudes en la Ventanilla Digital (/tramites).\n";
        $info .= "- Deben subir sus recaudos en formato PDF.\n";
        $info .= "- Un Revisor Técnico asignado califica los documentos (Aprobado, Observado o Rechazado).\n";
        $info .= "- CITAS: Sólo se pueden agendar una vez que TODOS los recaudos técnicos han sido APROBADOS por el revisor.\n";
        $info .= "- El ciudadano tiene una ventana de hasta 15 días posteriores a la aprobación para comparecer.\n";
        $info .= "- Límite estricto de 1 cita activa por ciudadano.\n";
        $info .= "- Horarios de atención en ventanilla: Lunes a Viernes de 08:30 a 16:30 hrs. Ventanilla 04, Sede Central.\n\n";

        $info .= "CATÁLOGO DE TRÁMITES Y REQUISITOS ACTIVOS:\n";
        foreach ($tramites as $t) {
            $version = $t->versiones->first();
            $info .= "- Trámite #{$t->ID_Tipo_Tramite}: {$t->Nombre}\n";
            $info .= "  Descripción: {$t->Descripcion}\n";
            if ($version && $version->requisitos->isNotEmpty()) {
                $info .= "  Requisitos obligatorios:\n";
                foreach ($version->requisitos as $r) {
                    $info .= "    * {$r->Nombre} ({$r->Prioridad}, formato: {$r->Formato})\n";
                }
            } else {
                $info .= "  Requisitos: Documentos de acreditación de identidad y solicitud formal.\n";
            }
        }

        return $info;
    }

    /**
     * Motor local semántico para responder de inmediato sin depender de API externa.
     *
     * @return array{respuesta: string, sugerencias: array<int, string>}
     */
    public function responderConBaseConocimiento(string $mensaje): array
    {
        $q = mb_strtolower(trim($mensaje), 'UTF-8');

        // 1. Saludos
        if (preg_match('/\b(hola|buenos d[ií]as|buenas tardes|buenas noches|saludos|hey)\b/u', $q)) {
            return [
                'respuesta' => "¡Hola! Bienvenido al **Asistente Virtual del SETC**.\n\nPuedo orientarte sobre los requisitos de cualquier trámite, cómo solicitar tu cita en ventanilla, plazos de atención y formatos de documentos.\n\n¿Qué gestión deseas realizar hoy?",
                'sugerencias' => [
                    '¿Qué requisitos piden para Licencia de Funcionamiento?',
                    '¿Cómo agendar una cita?',
                    '¿Cuáles son todos los trámites disponibles?',
                ],
            ];
        }

        // 2. Preguntas sobre CITAS
        if (preg_match('/\b(cita|citas|turno|turnos|agendar|agendamiento|calendario|ventanilla)\b/u', $q)) {
            return [
                'respuesta' => "### 📅 Reglas para Agendar Cita en Ventanilla\n\n".
                    "Para agendar una cita presencial o virtual, el sistema aplica las siguientes reglas oficiales:\n\n".
                    "1. **Aprobación Previa:** Primero debes iniciar tu trámite digital y esperar a que el **Revisor Técnico califique y apruebe todos tus documentos**.\n".
                    "2. **Ventana de 15 Días:** Una vez aprobado tu expediente, tienes un plazo máximo de **15 días hábiles** para agendar tu comparecencia.\n".
                    "3. **Límite de Cita Activa:** Solo se permite tener **1 cita activa** por ciudadano a la vez.\n".
                    "4. **Horarios de Atención:** Lunes a Viernes de 08:30 a 16:30 hrs en la **Ventanilla 04 (Sede Central)**.\n\n".
                    'Puedes gestionar tus turnos desde la sección [Mis Citas](/citas) una vez que tu trámite esté aprobado.',
                'sugerencias' => [
                    '¿Qué requisitos piden para Licencia de Funcionamiento?',
                    '¿Cómo saber si mis documentos fueron aprobados?',
                    'Ver mis trámites en curso',
                ],
            ];
        }

        // 3. Preguntas sobre SEGUIMIENTO, EXPEDIENTES O ESTADOS
        if (preg_match('/\b(estado|estatus|expediente|expedientes|seguimiento|revisi[oó]n|cu[aá]nto tarda|tiempo|sla)\b/u', $q)) {
            return [
                'respuesta' => "### 🔍 Estados de tu Expediente y Tiempos de Atención\n\n".
                    "Tu solicitud atraviesa el siguiente flujo oficial:\n\n".
                    "* **Pendiente:** El trámite fue ingresado y está en espera de asignación técnica.\n".
                    "* **En Revisión:** Un revisor técnico especialista está evaluando tus recaudos y planos.\n".
                    "* **Observado:** Si algún documento es ilegible o incompleto, podrás solventarlo subiendo una nueva versión en PDF.\n".
                    "* **Aprobado:** Documentos calificados con éxito. ¡Ya puedes agendar tu cita!\n".
                    "* **Concluido:** Se emitió la resolución vinculante con sello institucional.\n\n".
                    "⏱ **Plazos SLA:** El tiempo promedio normativo de revisión es de **48 a 72 horas hábiles**.\n\n".
                    'Puedes consultar el estado en tiempo real en [Mis Expedientes](/expedientes).',
                'sugerencias' => [
                    '¿Cómo agendar una cita?',
                    '¿Qué formato deben tener los archivos?',
                    'Ver catálogo de trámites',
                ],
            ];
        }

        // 4. Preguntas sobre FORMATOS DE ARCHIVOS O DOCUMENTOS
        if (preg_match('/\b(formato|pdf|archivo|archivos|subir|documento|documentos|peso|megas|mb)\b/u', $q)) {
            return [
                'respuesta' => "### 📄 Formatos Aceptados para Recaudos Digitales\n\n".
                    "* **Formato Oficial:** Todos los documentos deben cargarse en formato **PDF** digital.\n".
                    "* **Legibilidad:** Asegúrate de que los sellos, firmas y números de partida sean claramente legibles.\n".
                    "* **Trazabilidad:** Cada versión cargada queda protegida con sello de tiempo institucional inmutable.\n".
                    '* Si tu documento es observado, podrás sustituirlo con una nueva versión corregida desde tu expediente.',
                'sugerencias' => [
                    'Requisitos de Licencia de Funcionamiento',
                    'Requisitos de Inspección Técnica ITSE',
                    '¿Cómo agendar una cita?',
                ],
            ];
        }

        // 5. Búsqueda por Trámite Específico en la Base de Datos
        $tramites = TipoTramite::where('Estado_Tipo_Tr', 'Activo')
            ->with(['versiones' => fn ($q) => $q->where('Estado_Ver_Tr', 'Vigente')->with('requisitos')])
            ->get();

        foreach ($tramites as $tramite) {
            $nombreTramite = mb_strtolower($tramite->Nombre, 'UTF-8');
            $palabrasClave = explode(' ', str_replace(['de', 'y', 'en', 'para', 'el', 'la', 'los', 'las'], '', $nombreTramite));

            $coincide = false;
            foreach ($palabrasClave as $p) {
                $p = trim($p);
                if (mb_strlen($p) >= 4 && str_contains($q, $p)) {
                    $coincide = true;
                    break;
                }
            }

            if ($coincide) {
                $version = $tramite->versiones->first();
                $resp = "### 📋 Trámite: {$tramite->Nombre}\n\n";
                if (! empty($tramite->Descripcion)) {
                    $resp .= "{$tramite->Descripcion}\n\n";
                }

                if ($version && $version->requisitos->isNotEmpty()) {
                    $resp .= "**Requisitos obligatorios para este trámite:**\n\n";
                    foreach ($version->requisitos as $i => $req) {
                        $prioBadge = $req->Prioridad === 'Obligatorio' ? '*(Obligatorio)*' : '*(Opcional)*';
                        $formato = $req->Formato ?? 'PDF';
                        $num = $i + 1;
                        $resp .= "{$num}. **{$req->Nombre}** {$prioBadge} — Formato: `{$formato}`\n";
                    }
                    $resp .= "\n";
                } else {
                    $resp .= "Para este procedimiento se requiere acreditación de identidad (DNI/RFC) y formulario de solicitud debidamente completado.\n\n";
                }

                $resp .= "🚀 Puedes iniciar este trámite 100% digital aquí: [Iniciar Solicitud](/tramites/{$tramite->ID_Tipo_Tramite}/iniciar) o consultar la ficha en [Ver Catálogo](/tramites).";

                return [
                    'respuesta' => $resp,
                    'sugerencias' => [
                        '¿Cómo agendar la cita para este trámite?',
                        '¿Cuánto tiempo tarda la revisión?',
                        'Ver otros trámites disponibles',
                    ],
                ];
            }
        }

        // 6. Lista general de trámites
        if (preg_match('/\b(todos|cu[aá]les|lista|tr[aá]mites|cat[aá]logo|servicios|procedimientos)\b/u', $q)) {
            $resp = "### 🏛️ Catálogo de Trámites Disponibles en SETC\n\n".
                "Actualmente puedes realizar los siguientes trámites de manera 100% telemática:\n\n";

            foreach ($tramites as $t) {
                $resp .= "* **{$t->Nombre}:** {$t->Descripcion} → [Iniciar Trámite](/tramites/{$t->ID_Tipo_Tramite}/iniciar)\n";
            }

            $resp .= "\nPuedes preguntarme por los requisitos específicos de cualquiera de ellos.";

            return [
                'respuesta' => $resp,
                'sugerencias' => [
                    'Requisitos de Licencia de Funcionamiento',
                    'Requisitos de Certificado de Zonificación',
                    '¿Cómo agendar mi cita?',
                ],
            ];
        }

        // 7. Respuesta por defecto
        return [
            'respuesta' => "Puedo ayudarte con información oficial sobre los **trámites institucionales**, requisitos de recaudos, plazos de atención y agendamiento de **citas en ventanilla**.\n\n¿Sobre cuál de estos temas deseas consultar?",
            'sugerencias' => [
                'Requisitos de Licencia de Funcionamiento',
                '¿Cómo y cuándo puedo agendar una cita?',
                '¿Qué formato deben tener mis documentos?',
            ],
        ];
    }

    /**
     * Genera sugerencias rápidas dinámicas basadas en el mensaje.
     *
     * @return array<int, string>
     */
    protected function generarSugerenciasDinamicas(string $mensaje): array
    {
        $q = mb_strtolower($mensaje, 'UTF-8');
        if (str_contains($q, 'cita')) {
            return [
                '¿Cuándo se habilita mi cita?',
                'Requisitos para Licencia de Funcionamiento',
                'Consultar el estado de mi trámite',
            ];
        }

        if (str_contains($q, 'licencia') || str_contains($q, 'requisito')) {
            return [
                '¿Cómo agendar mi cita?',
                '¿En qué formato subo los documentos?',
                '¿Cuánto tiempo tarda la revisión?',
            ];
        }

        return [
            'Requisitos de Licencia de Funcionamiento',
            '¿Cómo agendar una cita?',
            'Ver todos los trámites disponibles',
        ];
    }
}
