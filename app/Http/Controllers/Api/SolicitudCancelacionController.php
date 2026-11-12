<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\Operador;
use App\Models\SeguimientoAlerta;
use App\Models\SolicitudCancelacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SolicitudCancelacionController extends Controller
{

    // Consultar las solicitudes que requieren revisión del operador.
    public function pendientes(Request $request)
    {
        $personal = $request->user();

        // Solo personal activo del centro de monitoreo.
        if (
            !($personal instanceof Operador) ||
            !$personal->activo ||
            !in_array(
                $personal->rol,
                ['OPERADOR', 'SUPERVISOR', 'ADMINISTRADOR'],
                true
            )
        ) {
            return response()->json([
                'message' => 'No tienes permiso para revisar solicitudes de cancelación.',
            ], 403);
        }

        $solicitudes = SolicitudCancelacion::with([
            'alerta.tipo',
            'ciudadano',
        ])
            ->whereIn('estado', [
                'PENDIENTE',
                'CODIGO_GENERADO',
            ])
            ->whereHas('alerta', function ($consulta) {
                $consulta->whereIn('estado', [
                    'NUEVA',
                    'EN_CAMINO',
                    'EN_SITIO',
                ]);
            })
            ->orderBy('id')
            ->get();

        return response()->json([
            'solicitudes' => $solicitudes->map(function ($solicitud) {
                return [
                    'id' => $solicitud->id,
                    'estado' => $solicitud->estado,
                    'motivo' => $solicitud->motivo,
                    'fecha_solicitud' => $solicitud->created_at?->toIso8601String(),
                    'codigo_expira_at' => $solicitud->codigo_expira_at?->toIso8601String(),

                    'alerta' => [
                        'id_alerta' => $solicitud->alerta->id_alerta,
                        'folio' => $solicitud->alerta->folio,
                        'tipo' => $solicitud->alerta->tipo?->nombre,
                        'estado' => $solicitud->alerta->estado,
                    ],

                    'ciudadano' => [
                        'nombre' => $solicitud->ciudadano?->name,
                        'telefono' => $solicitud->ciudadano?->telefono,
                    ],
                ];
            }),
        ]);
    }

    // Generar un código después de verificar el contacto con el ciudadano.
    public function generarCodigo(Request $request, int $id)
    {
        $personal = $request->user();

        if (
            !($personal instanceof Operador) ||
            !$personal->activo ||
            !in_array(
                $personal->rol,
                ['OPERADOR', 'SUPERVISOR', 'ADMINISTRADOR'],
                true
            )
        ) {
            return response()->json([
                'message' => 'No tienes permiso para generar códigos de cancelación.',
            ], 403);
        }

        $datos = $request->validate([
            'contacto_verificado' => 'required|accepted',
            'observaciones_operador' => 'nullable|string|max:1000',
        ], [
            'contacto_verificado.required' =>
            'Debes confirmar que verificaste el contacto con el ciudadano.',
            'contacto_verificado.accepted' =>
            'Debes confirmar que verificaste el contacto con el ciudadano.',
            'observaciones_operador.string' =>
            'Las observaciones deben ser texto.',
            'observaciones_operador.max' =>
            'Las observaciones no deben superar los 1000 caracteres.',
        ]);

        $idAlerta = SolicitudCancelacion::whereKey($id)
            ->value('id_alerta');

        if ($idAlerta === null) {
            return response()->json([
                'message' => 'La solicitud no existe.',
            ], 404);
        }

        return DB::transaction(function () use (
            $id,
            $idAlerta,
            $personal,
            $datos
        ) {
            // Bloquear primero la alerta y después la solicitud.
            $alerta = Alerta::where('id_alerta', $idAlerta)
                ->lockForUpdate()
                ->first();

            $solicitud = SolicitudCancelacion::whereKey($id)
                ->where('id_alerta', $idAlerta)
                ->lockForUpdate()
                ->first();

            if (!$alerta || !$solicitud) {
                return response()->json([
                    'message' => 'La alerta o la solicitud ya no existe.',
                ], 404);
            }

            if (!in_array(
                $alerta->estado,
                ['NUEVA', 'EN_CAMINO', 'EN_SITIO'],
                true
            )) {
                return response()->json([
                    'message' => 'La alerta ya terminó.',
                ], 409);
            }

            if (!in_array(
                $solicitud->estado,
                ['PENDIENTE', 'CODIGO_GENERADO'],
                true
            )) {
                return response()->json([
                    'message' => 'Esta solicitud ya fue resuelta.',
                ], 409);
            }

            // No reemplazar un código que todavía puede usar el ciudadano.
            if (
                $solicitud->estado === 'CODIGO_GENERADO' &&
                $solicitud->codigo_expira_at?->isFuture()
            ) {
                return response()->json([
                    'message' => 'Ya existe un código vigente. Espera a que venza para generar otro.',
                ], 409);
            }

            $codigo = str_pad(
                (string) random_int(0, 999999),
                6,
                '0',
                STR_PAD_LEFT
            );

            $ahora = now();
            $expira = $ahora->copy()->addMinutes(5);

            $solicitud->id_operador = $personal->id_operador;
            $solicitud->estado = 'CODIGO_GENERADO';
            $solicitud->codigo_hash = Hash::make($codigo);
            $solicitud->codigo_expira_at = $expira;
            $solicitud->intentos = 0;
            $solicitud->contacto_verificado_at = $ahora;
            if (isset($datos['observaciones_operador'])) {
                $solicitud->observaciones_operador = trim($datos['observaciones_operador']);
            }
            $solicitud->save();

            SeguimientoAlerta::create([
                'id_alerta' => $alerta->id_alerta,
                'id_operador' => $personal->id_operador,
                'accion' => 'CODIGO_CANCELACION_GENERADO',
                'observaciones' =>
                'El operador verificó el contacto y generó un código para la solicitud #' .
                    $solicitud->id . '.',
                'fecha_hora' => $ahora,
            ]);

            // Entregar el código al operador sin guardarlo como texto legible.
            return response()->json([
                'message' => 'Código generado. Comunícalo al ciudadano durante la llamada.',
                'codigo' => $codigo,
                'codigo_expira_at' => $expira->toIso8601String(),
                'solicitud' => [
                    'id' => $solicitud->id,
                    'estado' => $solicitud->estado,
                ],
            ], 200, [
                'Cache-Control' => 'no-store',
            ]);
        });
    }

    public function rechazar(Request $request, int $id)
    {
        $personal = $request->user();

        if (
            !($personal instanceof Operador) ||
            !$personal->activo ||
            !in_array(
                $personal->rol,
                ['OPERADOR', 'SUPERVISOR', 'ADMINISTRADOR'],
                true
            )
        ) {
            return response()->json([
                'message' => 'No tienes permiso para rechazar solicitudes de cancelación.',
            ], 403);
        }

        $datos = $request->validate([
            'observaciones_operador' => 'required|string|max:1000',
        ], [
            'observaciones_operador.required' => 'Escribe el motivo del rechazo.',
            'observaciones_operador.string' => 'El motivo del rechazo debe ser texto.',
            'observaciones_operador.max' => 'El motivo no debe superar los 1000 caracteres.',
        ]);

        $idAlerta = SolicitudCancelacion::whereKey($id)->value('id_alerta');

        if ($idAlerta === null) {
            return response()->json([
                'message' => 'La solicitud no existe.',
            ], 404);
        }

        return DB::transaction(function () use ($id, $idAlerta, $personal, $datos) {
            // Usar el mismo orden de bloqueo que al confirmar la cancelación.
            $alerta = Alerta::where('id_alerta', $idAlerta)
                ->lockForUpdate()
                ->first();

            $solicitud = SolicitudCancelacion::whereKey($id)
                ->where('id_alerta', $idAlerta)
                ->lockForUpdate()
                ->first();

            if (!$alerta || !$solicitud) {
                return response()->json([
                    'message' => 'La alerta o la solicitud ya no existe.',
                ], 404);
            }

            if (!in_array($alerta->estado, ['NUEVA', 'EN_CAMINO', 'EN_SITIO'], true)) {
                return response()->json([
                    'message' => 'La alerta ya terminó.',
                ], 409);
            }

            if (!in_array($solicitud->estado, ['PENDIENTE', 'CODIGO_GENERADO'], true)) {
                return response()->json([
                    'message' => 'Esta solicitud ya fue resuelta.',
                ], 409);
            }

            $ahora = now();

            $solicitud->id_operador = $personal->id_operador;
            $solicitud->estado = 'RECHAZADA';
            $solicitud->observaciones_operador = trim($datos['observaciones_operador']);
            $solicitud->codigo_hash = null;
            $solicitud->codigo_expira_at = null;
            $solicitud->resuelta_at = $ahora;
            $solicitud->save();

            SeguimientoAlerta::create([
                'id_alerta' => $idAlerta,
                'id_operador' => $personal->id_operador,
                'accion' => 'RECHAZO_CANCELACION',
                'observaciones' =>
                'El operador rechazó la solicitud de cancelación #' . $solicitud->id . '.',
                'fecha_hora' => $ahora,
            ]);

            return response()->json([
                'message' => 'Solicitud rechazada. La alerta continúa activa.',
                'solicitud' => [
                    'id' => $solicitud->id,
                    'estado' => $solicitud->estado,
                ],
            ]);
        }, 3);
    }


    public function confirmarCancelacion(Request $request, int $id)
    {
        $ciudadano = $request->user();

        if (
            !($ciudadano instanceof User) ||
            $ciudadano->estatus !== 'ACTIVO'
        ) {
            return response()->json([
                'message' => 'Solo un ciudadano con cuenta activa puede confirmar la cancelación.',
            ], 403);
        }

        $datos = $request->validate([
            'codigo' => 'required|string|digits:6',
        ], [
            'codigo.required' => 'Escribe el código que te dio el operador.',
            'codigo.string' => 'Envía el código como texto.',
            'codigo.digits' => 'El código debe tener seis dígitos.',
        ]);

        return DB::transaction(function () use ($id, $ciudadano, $datos) {
            // Bloquear primero la alerta, igual que al generar el código.
            $alerta = Alerta::where('id_alerta', $id)
                ->where('id_usuario', $ciudadano->id)
                ->lockForUpdate()
                ->first();

            if (!$alerta) {
                return response()->json([
                    'message' => 'No se encontró la alerta en tu cuenta.',
                ], 404);
            }

            if (!in_array(
                $alerta->estado,
                ['NUEVA', 'EN_CAMINO', 'EN_SITIO'],
                true
            )) {
                return response()->json([
                    'message' => 'La alerta ya terminó.',
                ], 409);
            }

            $solicitud = SolicitudCancelacion::where('id_alerta', $id)
                ->where('id_usuario', $ciudadano->id)
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if (
                !$solicitud ||
                $solicitud->estado !== 'CODIGO_GENERADO' ||
                !$solicitud->contacto_verificado_at ||
                !$solicitud->codigo_hash
            ) {
                return response()->json([
                    'message' => 'La solicitud aún no tiene un código autorizado.',
                ], 409);
            }

            if (!($solicitud->codigo_expira_at?->isFuture())) {
                return response()->json([
                    'message' => 'El código venció. Contacta al operador para obtener uno nuevo.',
                ], 409);
            }

            if ($solicitud->intentos >= 5) {
                return response()->json([
                    'message' => 'Agotaste los intentos para este código. Contacta al operador.',
                ], 429);
            }

            if (!Hash::check($datos['codigo'], $solicitud->codigo_hash)) {
                $solicitud->intentos++;
                $solicitud->save();

                return response()->json([
                    'message' => $solicitud->intentos >= 5
                        ? 'Agotaste los intentos para este código. Contacta al operador.'
                        : 'El código es incorrecto.',
                    'intentos_restantes' => 5 - $solicitud->intentos,
                ], 422);
            }

            $ahora = now();

            $alerta->estado = 'CANCELADA';
            $alerta->save();

            $solicitud->estado = 'CONFIRMADA';
            $solicitud->codigo_hash = null;
            $solicitud->codigo_expira_at = null;
            $solicitud->resuelta_at = $ahora;
            $solicitud->save();

            $idPolicia = SeguimientoAlerta::where('id_alerta', $id)
                ->where('accion', 'ACEPTO_ALERTA')
                ->orderByDesc('id')
                ->value('id_operador');

            if ($idPolicia !== null) {
                Operador::where('id_operador', $idPolicia)
                    ->where('rol', 'POLICIA')
                    ->update(['disponible' => true]);
            }

            SeguimientoAlerta::create([
                'id_alerta' => $id,
                'id_operador' => null,
                'accion' => 'CANCELACION_CIUDADANO',
                'observaciones' =>
                'El ciudadano confirmó la solicitud de cancelación #' .
                    $solicitud->id . ' con el código del operador.',
                'fecha_hora' => $ahora,
            ]);

            return response()->json([
                'message' => 'Alerta cancelada correctamente.',
                'alerta' => [
                    'id_alerta' => $alerta->id_alerta,
                    'folio' => $alerta->folio,
                    'estado' => $alerta->estado,
                ],
            ]);
        }, 3);
    }


    public function solicitar(Request $request, int $id)
    {
        $ciudadano = $request->user();

        // Solo los ciudadanos activos pueden enviar solicitudes.
        if (
            !($ciudadano instanceof User) ||
            $ciudadano->estatus !== 'ACTIVO'
        ) {
            return response()->json([
                'message' =>
                'Solo un ciudadano con cuenta activa puede solicitar la cancelación.',
            ], 403);
        }

        $datos = $request->validate(
            [
                'motivo' => 'required|string|max:1000',
            ],
            [
                'motivo.required' =>
                'Escribe el motivo de la solicitud de cancelación.',
                'motivo.string' =>
                'El motivo debe ser texto.',
                'motivo.max' =>
                'El motivo no debe superar los 1000 caracteres.',
            ]
        );

        return DB::transaction(function () use ($id, $ciudadano, $datos) {

            // Buscar una alerta propia y bloquearla durante esta operación.
            $alerta = Alerta::where('id_alerta', $id)
                ->where('id_usuario', $ciudadano->id)
                ->lockForUpdate()
                ->first();

            if (!$alerta) {
                return response()->json([
                    'message' =>
                    'La alerta no existe o no pertenece a tu cuenta.',
                ], 404);
            }

            // La solicitud solo corresponde a una emergencia activa.
            if (!in_array(
                $alerta->estado,
                ['NUEVA', 'EN_CAMINO', 'EN_SITIO'],
                true
            )) {
                return response()->json([
                    'message' =>
                    'Esta alerta ya terminó y no admite solicitudes de cancelación.',
                ], 409);
            }

            // Evitar varias solicitudes pendientes para la misma alerta.
            $pendiente = SolicitudCancelacion::where(
                'id_alerta',
                $alerta->id_alerta
            )
                ->whereIn('estado', ['PENDIENTE', 'CODIGO_GENERADO'])
                ->orderByDesc('id')
                ->first();

            if ($pendiente) {
                return response()->json([
                    'message' =>
                    'Ya tienes una solicitud de cancelación en proceso.',
                    'solicitud' => [
                        'id' => $pendiente->id,
                        'estado' => $pendiente->estado,
                    ],
                    'alerta' => [
                        'id_alerta' => $alerta->id_alerta,
                        'estado' => $alerta->estado,
                    ],
                ]);
            }

            $solicitud = SolicitudCancelacion::create([
                'id_alerta' => $alerta->id_alerta,
                'id_usuario' => $ciudadano->id,
                'motivo' => trim($datos['motivo']),
                'estado' => 'PENDIENTE',
            ]);

            // Registrar la acción en el seguimiento de la alerta.
            SeguimientoAlerta::create([
                'id_alerta' => $alerta->id_alerta,
                'id_operador' => null,
                'accion' => 'SOLICITUD_CANCELACION',
                'observaciones' =>
                'El ciudadano registró la solicitud de cancelación #' .
                    $solicitud->id . '.',
                'fecha_hora' => now(),
            ]);

            return response()->json([
                'message' =>
                'Solicitud registrada. La atención continúa mientras el operador la revisa.',
                'solicitud' => [
                    'id' => $solicitud->id,
                    'estado' => $solicitud->estado,
                ],
                'alerta' => [
                    'id_alerta' => $alerta->id_alerta,
                    'estado' => $alerta->estado,
                ],
            ], 201);
        });
    }
}
