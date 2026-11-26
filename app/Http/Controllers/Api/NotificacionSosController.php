<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\UbicacionAlerta;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificacionSosController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user() instanceof User, 403);

        $notificaciones = DB::table('notificaciones_sos')
            ->where('id_destinatario', $request->user()->id)
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        $alertas = Alerta::whereIn(
            'id_alerta',
            $notificaciones->pluck('id_alerta')
        )->get()->keyBy('id_alerta');

        $personas = User::whereIn(
            'id',
            $alertas->pluck('id_usuario')
        )->get()->keyBy('id');

        return response()->json([
            'notificaciones' => $notificaciones->map(function ($notificacion) use (
                $alertas,
                $personas
            ) {
                $alerta = $alertas->get($notificacion->id_alerta);

                return [
                    'id' => $notificacion->id,
                    'circulo' => $notificacion->circulo,
                    'nombre' => $personas->get($alerta?->id_usuario)?->name
                        ?? 'Contacto',
                    'folio' => $alerta?->folio,
                    'estado' => $alerta?->estado,
                    'leida_at' => $notificacion->leida_at,
                    'created_at' => $notificacion->created_at,
                ];
            }),
        ])->header('Cache-Control', 'private, no-store');
    }

    public function show(Request $request, int $id)
    {
        abort_unless($request->user() instanceof User, 403);

        $notificacion = DB::table('notificaciones_sos')
            ->where('id', $id)
            ->where('id_destinatario', $request->user()->id)
            ->first();

        abort_unless($notificacion, 404);

        $alerta = Alerta::with('tipo')
            ->findOrFail($notificacion->id_alerta);

        $persona = User::findOrFail($alerta->id_usuario);

        $ubicacion = UbicacionAlerta::where(
            'id_alerta',
            $alerta->id_alerta
        )->orderByDesc('id')->first();

        if ($notificacion->leida_at === null) {
            DB::table('notificaciones_sos')
                ->where('id', $id)
                ->where('id_destinatario', $request->user()->id)
                ->update([
                    'leida_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        return response()->json([
            'alerta' => [
                'nombre' => $persona->name,
                'circulo' => $notificacion->circulo,
                'folio' => $alerta->folio,
                'tipo' => $alerta->tipo?->nombre,
                'estado' => $alerta->estado,
                'fecha_hora' => $alerta->fecha_hora,
                'ubicacion' => [
                    'latitud' => (float) (
                        $ubicacion?->latitud ?? $alerta->latitud
                    ),
                    'longitud' => (float) (
                        $ubicacion?->longitud ?? $alerta->longitud
                    ),
                    'actualizada_at' => $ubicacion?->fecha_hora
                        ?? $alerta->fecha_hora,
                ],
            ],
        ])->header('Cache-Control', 'private, no-store');
    }
}