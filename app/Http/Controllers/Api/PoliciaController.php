<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\Operador;
use App\Models\SeguimientoAlerta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PoliciaController extends Controller
{
    // Actualizar la ubicación actual del policía.
    public function actualizarUbicacion(Request $request)
    {
        $personal = $request->user();

        // Solamente un policía activo puede usar esta función.
        if (
            !$personal instanceof Operador ||
            !$personal->activo ||
            $personal->rol !== 'POLICIA'
        ) {
            return response()->json([
                'message' => 'No tienes permiso para realizar esta acción.',
            ], 403);
        }

        $datos = $request->validate([
            'latitud' => [
                'required',
                'numeric',
                'between:-90,90',
            ],
            'longitud' => [
                'required',
                'numeric',
                'between:-180,180',
            ],
            'precision_m' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ]);

        // Guardar la ubicación del policía.
        // Operador.php cifra automáticamente latitud y longitud.
        $personal->latitud_actual = (string) $datos['latitud'];
        $personal->longitud_actual = (string) $datos['longitud'];
        $personal->precision_m = $datos['precision_m'] ?? null;
        $personal->ubicacion_actualizada_at = now();

        $personal->save();

        // Buscar una alerta que este atendiendo este policía.
        $idAlerta = SeguimientoAlerta::join(
            'alertas',
            'seguimiento_alerta.id_alerta',
            '=',
            'alertas.id_alerta'
        )
            ->where(
                'seguimiento_alerta.id_operador',
                $personal->id_operador
            )
            ->where(
                'seguimiento_alerta.accion',
                'ACEPTO_ALERTA'
            )
            ->where(
                'alertas.estado',
                'EN_CAMINO'
            )
            ->orderByDesc('seguimiento_alerta.id')
            ->value('seguimiento_alerta.id_alerta');

        $distancia = null;
        $estadoAlerta = null;

        if ($idAlerta) {
            $alerta = Alerta::where(
                'id_alerta',
                $idAlerta
            )->first();

            if ($alerta) {
                $distancia = $this->calcularDistanciaMetros(
                    (float) $personal->latitud_actual,
                    (float) $personal->longitud_actual,
                    (float) $alerta->latitud,
                    (float) $alerta->longitud
                );

                // Si el policía esta a 50 metros o menos,
                // la alerta cambia automaticamente a EN_SITIO.
                if ($distancia <= 50) {
                    DB::transaction(function () use (
                        $alerta,
                        $personal
                    ) {
                        $alertaBloqueada = Alerta::where(
                            'id_alerta',
                            $alerta->id_alerta
                        )
                            ->lockForUpdate()
                            ->first();

                        if (
                            $alertaBloqueada &&
                            $alertaBloqueada->estado === 'EN_CAMINO'
                        ) {
                            $alertaBloqueada->estado = 'EN_SITIO';
                            $alertaBloqueada->save();

                            SeguimientoAlerta::create([
                                'id_alerta' =>
                                $alertaBloqueada->id_alerta,

                                'id_operador' =>
                                $personal->id_operador,

                                'accion' =>
                                'LLEGO_AL_SITIO',

                                'observaciones' =>
                                'El sistema detectó que el policía llegó al lugar de la alerta.',
                            ]);
                        }
                    });

                    $estadoAlerta = 'EN_SITIO';
                } else {
                    $estadoAlerta = 'EN_CAMINO';
                }
            }
        }

        return response()->json([
            'message' => 'Ubicación actualizada correctamente.',

            'policia' => [
                'id_operador' => $personal->id_operador,
                'latitud' => $personal->latitud_actual,
                'longitud' => $personal->longitud_actual,
                'precision_m' => $personal->precision_m,
                'actualizada_at' =>
                $personal->ubicacion_actualizada_at,
            ],

            'alerta' => $idAlerta ? [
                'id_alerta' => (int) $idAlerta,
                'estado' => $estadoAlerta,
                'distancia_m' => $distancia,
            ] : null,
        ]);
    }

    // Consultar la alerta que actualmente atiende el policía.
    public function servicioActual(Request $request)
    {
        $personal = $request->user();

        if (
            !$personal instanceof Operador ||
            !$personal->activo ||
            $personal->rol !== 'POLICIA'
        ) {
            return response()->json([
                'message' => 'No tienes permiso para realizar esta acción.',
            ], 403);
        }

        $idAlerta = SeguimientoAlerta::join(
            'alertas',
            'seguimiento_alerta.id_alerta',
            '=',
            'alertas.id_alerta'
        )
            ->where(
                'seguimiento_alerta.id_operador',
                $personal->id_operador
            )
            ->where(
                'seguimiento_alerta.accion',
                'ACEPTO_ALERTA'
            )
            ->whereIn(
                'alertas.estado',
                [
                    'EN_CAMINO',
                    'EN_SITIO',
                ]
            )
            ->orderByDesc(
                'seguimiento_alerta.id'
            )
            ->value(
                'seguimiento_alerta.id_alerta'
            );

        if (!$idAlerta) {
            return response()->json([
                'servicio' => null,
            ]);
        }

        $alerta = Alerta::with([
            'tipo:id_tipo,nombre'
        ])
            ->where(
                'id_alerta',
                $idAlerta
            )
            ->first();

        if (!$alerta) {
            return response()->json([
                'servicio' => null,
            ]);
        }

        return response()->json([
            'servicio' => [
                'id_alerta' =>
                $alerta->id_alerta,

                'folio' =>
                $alerta->folio,

                'tipo' =>
                $alerta->tipo?->nombre,

                'estado' =>
                $alerta->estado,

                'prioridad' =>
                $alerta->prioridad,

                'fecha_hora' =>
                $alerta->fecha_hora,

                'ubicacion' => [
                    'latitud' =>
                    $alerta->latitud,

                    'longitud' =>
                    $alerta->longitud,
                ],
            ],
        ]);
    }

    // Calcular distancia entre dos coordenadas.
    private function calcularDistanciaMetros(
        float $latitud1,
        float $longitud1,
        float $latitud2,
        float $longitud2
    ): float {
        $radioTierra = 6371000;

        $lat1 = deg2rad($latitud1);
        $lat2 = deg2rad($latitud2);

        $diferenciaLatitud =
            deg2rad($latitud2 - $latitud1);

        $diferenciaLongitud =
            deg2rad($longitud2 - $longitud1);

        $a =
            sin($diferenciaLatitud / 2) ** 2 +
            cos($lat1) *
            cos($lat2) *
            sin($diferenciaLongitud / 2) ** 2;

        $c = 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );

        return round(
            $radioTierra * $c,
            2
        );
    }
}
