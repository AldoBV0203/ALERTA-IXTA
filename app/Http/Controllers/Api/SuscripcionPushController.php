<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class SuscripcionPushController extends Controller
{
    public function clavePublica(): JsonResponse
    {
        $clave = config('services.webpush.public_key');

        if (!$clave) {
            return response()->json(['message' => 'Notificaciones no configuradas.'], 503);
        }

        return response()->json(['publicKey' => $clave]);
    }

    public function estado(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'endpoint' => ['nullable', 'url', 'starts_with:https://', 'max:2048'],
        ]);

        $registrada = !empty($datos['endpoint'])
            && DB::table('suscripciones_push')
            ->where('id_usuario', $request->user()->id)
            ->where('endpoint_hash', hash('sha256', $datos['endpoint']))
            ->exists();

        return response()->json([
            'id_usuario' => $request->user()->id,
            'registrada' => $registrada,
        ]);
    }

    public function guardar(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'endpoint' => ['required', 'url', 'starts_with:https://', 'max:2048'],
            'keys' => ['required', 'array'],
            'keys.p256dh' => ['required', 'string', 'max:255'],
            'keys.auth' => ['required', 'string', 'max:255'],
        ]);

        $suscripcion = [
            'endpoint' => $datos['endpoint'],
            'keys' => [
                'p256dh' => $datos['keys']['p256dh'],
                'auth' => $datos['keys']['auth'],
            ],
        ];

        DB::table('suscripciones_push')->updateOrInsert(
            ['endpoint_hash' => hash('sha256', $datos['endpoint'])],
            [
                'id_usuario' => $request->user()->id,
                'suscripcion_cifrada' => Crypt::encryptString(
                    json_encode($suscripcion, JSON_THROW_ON_ERROR)
                ),
                'updated_at' => now(),
            ]
        );

        return response()->json(['registrada' => true]);
    }

    public function eliminar(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'endpoint' => ['required', 'url', 'starts_with:https://', 'max:2048'],
        ]);

        DB::table('suscripciones_push')
            ->where('id_usuario', $request->user()->id)
            ->where('endpoint_hash', hash('sha256', $datos['endpoint']))
            ->delete();

        return response()->json(['eliminada' => true]);
    }
}
