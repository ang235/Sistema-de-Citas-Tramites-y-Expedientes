<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct(
        protected ChatbotService $chatbotService
    ) {}

    /**
     * Procesa la consulta del usuario y devuelve la respuesta del Asistente Virtual.
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mensaje' => ['required', 'string', 'max:500'],
            'historial' => ['nullable', 'array', 'max:10'],
            'historial.*.role' => ['required_with:historial', 'string', 'in:user,assistant,model'],
            'historial.*.content' => ['required_with:historial', 'string', 'max:1000'],
        ]);

        $historial = $validated['historial'] ?? [];
        $respuesta = $this->chatbotService->responder($validated['mensaje'], $historial);

        return response()->json([
            'success' => true,
            'respuesta' => $respuesta['respuesta'],
            'sugerencias' => $respuesta['sugerencias'],
        ]);
    }
}

