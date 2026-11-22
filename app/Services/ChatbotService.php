<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TipoTramite;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    /**
     * Responde a la consulta de un usuario utilizando IA generativa (Gemini 1.5 Flash Free Tier)
     * con fallback automático a la base de conocimiento local cuando no hay solicitudes disponibles.
     *
     * @param  array<int, array{role: string, content: string}>  $historial
     * @return array{respuesta: string, sugerencias: array<int, string>}
     */
    public function responder(string $mensaje, array $historial = []): array
    {
        $mensajeLimpio = trim($mensaje);
        if ($mensajeLimpio === '') {
            return [
                'respuesta' => '¡Hola! 👋 Soy el asistente virtual del **SETC** y estoy aquí para ayudarte con tus trámites, expedientes y citas. ¿En qué puedo orientarte hoy?',
                'sugerencias' => [
                    '¿Qué requisitos necesito para Licencia de Funcionamiento?',
                    '¿Cuándo puedo agendar mi cita?',
                    '¿Qué trámites puedo hacer en línea?',
                ],
            ];
        }

        // 1. Intentar llamar a API de Gemini si está configurada la llave y no se agotó la cuota
        $geminiKey = config('services.gemini.key') ?? env('GEMINI_API_KEY');
        $cuotaAgotada = Cache::has('gemini_cuota_agotada');

        if (! empty($geminiKey) && ! $cuotaAgotada) {
            try {
                $respuestaIa = $this->consultarGemini($mensajeLimpio, $historial, (string) $geminiKey);
                if (! empty($respuestaIa)) {
                    return [
                        'respuesta' => $respuestaIa,
                        'sugerencias' => $this->generarSugerenciasDinamicas($mensajeLimpio),
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('ChatbotService: Error en llamada a Gemini API, activando fallback local.', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // 2. Fallback al modelo de conocimiento local del sistema
        return $this->responderConBaseConocimiento($mensajeLimpio);
    }

    /**
     * Consulta a Google Gemini API (gemini-1.5-flash) restringida exclusivamente al sistema SETC.
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
            'parts' => [['text' => 'Entendido. Soy el asistente virtual del SETC, con mucho gusto ayudaré a los ciudadanos de forma cálida, clara y cercana, como si fuera un funcionario amable en ventanilla. Responderé únicamente sobre trámites, expedientes, requisitos y citas del sistema SETC. Si alguien pregunta algo ajeno al sistema, lo explicaré con cordialidad sin usar tecnicismos ni mensajes de error fríos.']],
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
                    'temperature' => 0.1,
                    'maxOutputTokens' => 800,
                ],
            ]
        );

        // Si se agota la cuota gratuita (1,500 RPD o 15 RPM)
        if ($response->status() === 429) {
            Log::warning('ChatbotService: Cuota de Gemini API agotada (429 Too Many Requests). Pasando automáticamente al modelo local.');
            // Pausar llamadas externas por 5 minutos para responder de inmediato con el modelo local
            Cache::put('gemini_cuota_agotada', true, now()->addMinutes(5));

            return null;
        }

        if ($response->successful()) {
            Cache::forget('gemini_cuota_agotada');
            $data = $response->json();

            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        }

        Log::warning('ChatbotService: Respuesta no exitosa de Gemini (HTTP '.$response->status().'). Usando modelo local.');

        return null;
    }

    /**
     * Construye el prompt con las reglas del SETC y la prohibición estricta de responder temas ajenos.
     */
    protected function construirContextoInstitucional(): string
    {
        $tramites = TipoTramite::where('Estado_Tipo_Tr', 'Activo')
            ->with(['versiones' => fn ($q) => $q->where('Estado_Ver_Tr', 'Vigente')->with('requisitos')])
            ->get();

        $info = "Eres el asistente virtual oficial del Sistema de Expedientes de Trámites y Citas (SETC).\n\n";

        $info .= "PERSONALIDAD Y TONO:\n";
        $info .= "- Eres amable, cercano y empático, como un funcionario de atención al ciudadano que quiere genuinamente ayudar.\n";
        $info .= "- Usa un lenguaje natural y cálido. Evita el tono robótico, frío o burocrático.\n";
        $info .= "- Puedes usar emojis ocasionalmente para hacer la respuesta más amigable (📋, 📅, ✅, 📄, etc.).\n";
        $info .= "- Tutea al ciudadano con naturalidad (di 'tus documentos', 'tu cita', 'puedes hacerlo').\n\n";

        $info .= "OBJETIVO EXCLUSIVO Y LÍMITE DE DOMINIO:\n";
        $info .= "- Tu función es orientar ÚNICAMENTE sobre el sistema SETC: trámites municipales, requisitos, recaudos en PDF, expedientes, resoluciones, plazos, horarios y citas.\n";
        $info .= "- Si el usuario pregunta algo ajeno al SETC (recetas, deportes, chistes, tareas, matemáticas, etc.), respóndele de forma cordial y natural, por ejemplo:\n";
        $info .= "  'Huy, eso sí se me escapa 😅 Soy el asistente del SETC, así que solo manejo lo que tiene que ver con trámites, expedientes y citas de este portal. ¿Te puedo ayudar con algún trámite?'\n";
        $info .= "- Nunca uses mensajes de error técnicos ni advertencias formales para preguntas fuera de contexto.\n\n";

        $info .= "REGLAS INSTITUCIONALES DEL SETC:\n";
        $info .= "- Trámites 100% digitales con trazabilidad, firma digital activa y sello institucional.\n";
        $info .= "- Los ciudadanos inician sus solicitudes en la Ventanilla Digital (/tramites).\n";
        $info .= "- Deben subir sus recaudos en formato PDF.\n";
        $info .= "- Un Revisor Técnico califica los documentos (Aprobado, Observado o Rechazado).\n";
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
     * Motor local semántico para responder consultas oficiales del SETC cuando
     * Gemini no está disponible o se agotaron los requests diarios.
     *
     * @return array{respuesta: string, sugerencias: array<int, string>}
     */
    public function responderConBaseConocimiento(string $mensaje): array
    {
        $q = mb_strtolower(trim($mensaje), 'UTF-8');

        // 1. Saludos
        if (preg_match('/\b(hola|buenos d[ií]as|buenas tardes|buenas noches|saludos|hey|qué tal|que tal|buenas)\b/u', $q)) {
            return [
                'respuesta' => "¡Hola! 👋 Qué gusto que estés aquí. Soy el asistente del **SETC** y estoy listo para ayudarte con todo lo que necesites sobre tus trámites, documentos, expedientes o citas.\n\n¿Con qué te puedo apoyar hoy?",
                'sugerencias' => [
                    '¿Qué requisitos necesito para Licencia de Funcionamiento?',
                    '¿Cómo agendo una cita?',
                    '¿Cuáles son todos los trámites disponibles?',
                ],
            ];
        }

        // 2. Agradecimientos y despedidas
        if (preg_match('/\b(gracias|muchas gracias|agradezco|adi[oó]s|hasta luego|nos vemos|chao|bye|ok gracias|ya gracias)\b/u', $q)) {
            return [
                'respuesta' => '¡Con mucho gusto! 😊 Si en algún momento tienes otra pregunta sobre tus trámites o citas, aquí estaré. ¡Que tengas un excelente día!',
                'sugerencias' => [
                    '¿Cuáles son los trámites disponibles?',
                    'Consultar el estado de mi expediente',
                    '¿Cuáles son los horarios de atención?',
                ],
            ];
        }

        // 3. Preguntas sobre CITAS Y TURNOS
        if (preg_match('/\b(cita|citas|turno|turnos|agendar|agendamiento|calendario|ventanilla|comparecer|comparecencia|horario|horarios)\b/u', $q)) {
            return [
                'respuesta' => "📅 **¿Cómo agendar tu cita?**\n\n".
                    "Para reservar tu cita en ventanilla, el sistema tiene estas reglas que debes tener en cuenta:\n\n".
                    "1. **Primero, aprobación de documentos:** Tu cita solo se habilita cuando el Revisor Técnico haya **aprobado todos tus documentos**. Mientras estén en revisión o pendientes, aún no puedes agendar.\n".
                    "2. **Tienes 15 días hábiles** desde que se aprueben tus recaudos para elegir tu fecha de cita.\n".
                    "3. **Solo puedes tener 1 cita activa a la vez** — si ya tienes una programada, espera a que concluya antes de agendar otra.\n".
                    "4. **Horario de atención:** Lunes a Viernes de **08:30 a 16:30 hrs**, Ventanilla 04 (Sede Central).\n\n".
                    '¿Tienes tus documentos aprobados? Puedes agendar desde [Mis Citas](/citas) 🚀',
                'sugerencias' => [
                    '¿Qué requisitos piden para Licencia de Funcionamiento?',
                    '¿Cómo saber si mis documentos fueron aprobados?',
                    '¿Qué pasa si no puedo ir a mi cita?',
                ],
            ];
        }

        // 4. Preguntas sobre SEGUIMIENTO, EXPEDIENTES O ESTADOS
        if (preg_match('/\b(estado|estatus|expediente|expedientes|seguimiento|revisi[oó]n|cu[aá]nto tarda|tiempo|sla|folio|resoluci[oó]n|observado|aprobado)\b/u', $q)) {
            return [
                'respuesta' => "🔍 **¿Cómo saber el estado de tu trámite?**\n\n".
                    "Tu expediente puede pasar por los siguientes estados:\n\n".
                    "* **Pendiente** — Ya ingresaste tu solicitud, estamos asignando un revisor técnico.\n".
                    "* **En Revisión** — Tu revisor ya está evaluando los documentos que subiste.\n".
                    "* **Observado** — Algún documento tiene un problema (ilegible, incompleto, etc.). Puedes corregirlo subiendo una nueva versión en PDF.\n".
                    "* **Aprobado** ✅ — ¡Todo en orden! Ya puedes agendar tu cita en ventanilla.\n".
                    "* **Concluido** — Se emitió tu resolución oficial con sello institucional.\n\n".
                    "⏱ El tiempo promedio de revisión es de **48 a 72 horas hábiles**.\n\n".
                    'Puedes ver el estado en tiempo real en [Mis Expedientes](/expedientes) 📂',
                'sugerencias' => [
                    '¿Cómo corrijo un documento observado?',
                    '¿Cuándo puedo agendar mi cita?',
                    '¿Qué formato deben tener mis archivos?',
                ],
            ];
        }

        // 5. Preguntas sobre FORMATOS DE ARCHIVOS O DOCUMENTOS
        if (preg_match('/\b(formato|pdf|archivo|archivos|subir|documento|documentos|peso|megas|mb|recaudos|recaudo)\b/u', $q)) {
            return [
                'respuesta' => "📄 **Sobre los documentos que debes subir:**\n\n".
                    "* Todos los archivos deben estar en **formato PDF** (no se aceptan fotos en JPG o Word).\n".
                    "* Verifica que los documentos sean **legibles**: que se lean bien los sellos, firmas y números.\n".
                    "* Cada archivo que subas queda registrado con un sello de tiempo institucional, así que conserva la versión correcta.\n".
                    "* Si tu revisor marca un documento como **Observado**, puedes subir una versión corregida desde tu expediente sin necesidad de reiniciar el trámite.\n\n".
                    '¿Tienes dudas sobre los requisitos específicos de tu trámite?',
                'sugerencias' => [
                    'Requisitos de Licencia de Funcionamiento',
                    'Requisitos de Inspección Técnica ITSE',
                    '¿Cómo corrijo un documento rechazado?',
                ],
            ];
        }

        // 6. Búsqueda por Trámite Específico en la Base de Datos
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
                $resp = "📋 **{$tramite->Nombre}**\n\n";
                if (! empty($tramite->Descripcion)) {
                    $resp .= "{$tramite->Descripcion}\n\n";
                }

                if ($version && $version->requisitos->isNotEmpty()) {
                    $resp .= "Para este trámite necesitarás los siguientes documentos:\n\n";
                    foreach ($version->requisitos as $i => $req) {
                        $prioBadge = $req->Prioridad === 'Obligatorio' ? '*(obligatorio)*' : '*(opcional)*';
                        $formato = $req->Formato ?? 'PDF';
                        $num = $i + 1;
                        $resp .= "{$num}. **{$req->Nombre}** {$prioBadge} — en formato `{$formato}`\n";
                    }
                    $resp .= "\n";
                } else {
                    $resp .= "Para este procedimiento necesitas tu documento de identidad (DNI/RFC) y llenar el formulario de solicitud.\n\n";
                }

                $resp .= "🚀 ¿Listo para empezar? [Iniciar Solicitud](/tramites/{$tramite->ID_Tipo_Tramite}/iniciar) o mira todos los trámites en [Ver Catálogo](/tramites).";

                return [
                    'respuesta' => $resp,
                    'sugerencias' => [
                        '¿Cómo agendar mi cita una vez aprobados los documentos?',
                        '¿Cuánto tiempo tarda la revisión?',
                        'Ver otros trámites disponibles',
                    ],
                ];
            }
        }

        // 7. Lista o catálogo general de trámites
        if (preg_match('/\b(todos|cu[aá]les|lista|tr[aá]mites|cat[aá]logo|servicios|procedimientos|qu[eé] puedo hacer|ventanilla digital)\b/u', $q)) {
            $resp = "🏛️ **Trámites disponibles en el SETC**\n\n".
                "Estos son los trámites que puedes iniciar completamente en línea:\n\n";

            foreach ($tramites as $t) {
                $resp .= "* **{$t->Nombre}:** {$t->Descripcion} → [Iniciar](/tramites/{$t->ID_Tipo_Tramite}/iniciar)\n";
            }

            $resp .= "\nPuedes preguntarme por los requisitos específicos de cualquiera y te explico con detalle 😊";

            return [
                'respuesta' => $resp,
                'sugerencias' => [
                    'Requisitos de Licencia de Funcionamiento',
                    'Requisitos de Certificado de Zonificación',
                    '¿Cómo agendo mi cita?',
                ],
            ];
        }

        // 8. Preguntas generales sobre REQUISITOS O AYUDA DEL SISTEMA
        if (preg_match('/\b(requisito|requisitos|recaudo|recaudos|ayuda|orientaci[oó]n|informaci[oó]n|qu[eé] necesito|qu[eé] piden)\b/u', $q)) {
            return [
                'respuesta' => "Claro, con gusto te ayudo 😊 Dime el nombre del trámite que te interesa y te explico paso a paso qué documentos necesitas, en qué formato subirlos y cuánto tarda la revisión.\n\n¿Cuál trámite te interesa?",
                'sugerencias' => [
                    'Requisitos de Licencia de Funcionamiento',
                    '¿Cómo y cuándo puedo agendar una cita?',
                    'Ver lista de todos los trámites',
                ],
            ];
        }

        // 9. FLEXIBILIDAD: si el mensaje menciona algo relacionado al sistema, intenta ayudar
        //    en lugar de rechazar directamente.
        $relacionadoConSistema = preg_match(
            '/\b(setc|tr[aá]mite|tr[aá]mites|cita|citas|expediente|expedientes|documento|documentos|'.
            'requisito|requisitos|recaudo|recaudos|pdf|subir|ventanilla|solicitud|solicitudes|'.
            'licencia|permiso|permisos|revisi[oó]n|revisor|resoluci[oó]n|municipio|municipal|'.
            'folio|estado|estatus|aprobado|observado|rechazado|firma|sello|digital|'.
            'horario|horarios|comparec|plazo|plazos|formato|formatos|archivo|archivos|'.
            'iniciar|portal|sistema|tramitar|gestor|gesti[oó]n|inscripci[oó]n|registro|'.
            'constancia|certificado|habilitaci[oó]n|inspecci[oó]n)\b/u',
            $q
        );

        if ($relacionadoConSistema) {
            return [
                'respuesta' => "Con gusto te ayudo 😊 No estoy seguro de entender exactamente tu pregunta, pero si tiene que ver con alguno de estos temas del **SETC**, dime más y te oriento:\n\n".
                    "* 📋 **Trámites y requisitos** — ¿Qué documentos necesitas para tu gestión?\n".
                    "* 📄 **Documentos y recaudos** — ¿Cómo subirlos, en qué formato, cómo corregir?\n".
                    "* 🔍 **Expediente y seguimiento** — ¿Cómo va tu solicitud? ¿Qué significa cada estado?\n".
                    "* 📅 **Citas y ventanilla** — ¿Cuándo y cómo agendar tu cita presencial?\n\n".
                    '¿Puedes contarme un poco más sobre lo que necesitas?',
                'sugerencias' => [
                    '¿Cuáles son los trámites disponibles?',
                    '¿Cómo agendo una cita?',
                    'Requisitos para Licencia de Funcionamiento',
                ],
            ];
        }

        // 10. PREGUNTA COMPLETAMENTE FUERA DE ÁMBITO
        return [
            'respuesta' => "Lo sentimos, el sistema solo puede ayudarte con temas relacionados al **SETC** 😅\n\n".
                "Estoy aquí para orientarte sobre:\n".
                "* 📋 Requisitos y documentos de trámites municipales.\n".
                "* 📄 Cómo subir y corregir tus recaudos en PDF.\n".
                "* 🔍 Estado y seguimiento de tus expedientes.\n".
                "* 📅 Cómo y cuándo agendar tu cita en ventanilla.\n\n".
                '¿Te puedo ayudar con alguno de estos temas?',
            'sugerencias' => [
                '¿Cuáles son los trámites disponibles?',
                '¿Cómo agendo una cita?',
                'Requisitos para Licencia de Funcionamiento',
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
