<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\TipoAlerta;
use App\Models\UbicacionAlerta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\SeguimientoAlerta;
use App\Models\Operador;

class AlertaController extends Controller
{
    // se obtiene los tipos de alerta activos.

    public function tipos()
    {
        $tipos = TipoAlerta::where('activo', true)
            ->select(
                'id_tipo',
                'nombre',
                'descripcion'
            )
            ->get();

        return response()->json([
            'tipos' => $tipos,
        ]);
    }


    //  Creamos la alerta nueva alerta.
    public function store(Request $request)
    {
        $datos = $request->validate([
            'id_tipo' => [
                'required',
                'integer',
            ],

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

        // Comprobamos que el tipo de alerta exista y esté activo.
        $tipo = TipoAlerta::where(
            'id_tipo',
            $datos['id_tipo']
        )
            ->where('activo', true)
            ->first();

        if (!$tipo) {
            return response()->json([
                'message' => 'El tipo de alerta seleccionado no es válido.',
            ], 422);
        }


        // usamos sanctum para auntenticar el usuario
        $usuario = $request->user();


        // Utilizamos una transacción para evitar guardar una alerta incompleta.
        $alerta = DB::transaction(function () use (
            $datos,
            $usuario
        ) {

            /*
             Folio temporal único.
             Después de guardar conocemos el ID y generamos el folio definitivo.
             */
            $folioTemporal = 'TMP-' . Str::uuid();


            $alerta = Alerta::create([
                'folio' => $folioTemporal,

                'id_usuario' => $usuario->id,

                'id_tipo' => $datos['id_tipo'],

                'latitud' => (string) $datos['latitud'],

                'longitud' => (string) $datos['longitud'],

                'prioridad' => 'ALTA',

                'estado' => 'NUEVA',
            ]);


            // Ejemplo: AIX-20261007-000001

            $folio = 'AIX-'
                . now()->format('Ymd')
                . '-'
                . str_pad(
                    $alerta->id_alerta,
                    6,
                    '0',
                    STR_PAD_LEFT
                );


            $alerta->folio = $folio;

            $alerta->save();


            /*
               Guardamos también la ubicación en el historial de ubicaciones.
               Las coordenadas serán cifradas automáticamente por el modelo.
             */
            UbicacionAlerta::create([
                'id_alerta' => $alerta->id_alerta,

                'latitud' => (string) $datos['latitud'],

                'longitud' => (string) $datos['longitud'],

                'precision_m' =>
                $datos['precision_m'] ?? null,
            ]);


            return $alerta;
        });


        return response()->json([
            'message' => 'Alerta enviada correctamente.',

            'alerta' => [
                'id_alerta' => $alerta->id_alerta,

                'folio' => $alerta->folio,

                'estado' => $alerta->estado,
            ],
        ], 201);
    }

    // con este metodo obtenemos las alertas del ciudadano autenticado.

    public function misAlertas(Request $request)
    {
        $usuario = $request->user();

        $alertas = Alerta::with([
            'tipo:id_tipo,nombre'
        ])
            ->where('id_usuario', $usuario->id)
            ->orderByDesc('id_alerta')
            ->get()
            ->map(function ($alerta) {

                return [
                    'id_alerta' => $alerta->id_alerta,

                    'folio' => $alerta->folio,

                    'tipo' => $alerta->tipo?->nombre,

                    'fecha_hora' => $alerta->fecha_hora,

                    'prioridad' => $alerta->prioridad,

                    'estado' => $alerta->estado,
                ];
            });

        return response()->json([
            'alertas' => $alertas,
        ]);
    }

    // Consultar el estado de una alerta propia del ciudadano.
    public function estado(Request $request, int $id)
    {
        $usuario = $request->user();

        // El ciudadano solamente puede consultar sus propias alertas.
        $alerta = Alerta::with([
            'tipo:id_tipo,nombre'
        ])
            ->where('id_alerta', $id)
            ->where('id_usuario', $usuario->id)
            ->first();

        if (!$alerta) {
            return response()->json([
                'message' => 'La alerta no existe o no pertenece al usuario.',
            ], 404);
        }

        // Buscar qué policía aceptó la alerta.
        $asignacion = SeguimientoAlerta::where(
            'id_alerta',
            $alerta->id_alerta
        )
            ->where('accion', 'ACEPTO_ALERTA')
            ->orderBy('id')
            ->first();

        $policia = null;

        if ($asignacion) {
            $personal = Operador::where(
                'id_operador',
                $asignacion->id_operador
            )
                ->where('rol', 'POLICIA')
                ->first();

            if ($personal) {

                // La ubicación del policía solamente se comparte
                // mientras la emergencia sigue activa.
                $mostrarUbicacion = in_array(
                    $alerta->estado,
                    [
                        'EN_CAMINO',
                        'EN_SITIO',
                    ],
                    true
                );

                $policia = [
                    'id_operador' => $personal->id_operador,
                    'nombre' => $personal->nombre,

                    'ubicacion' => $mostrarUbicacion ? [
                        'latitud' => $personal->latitud_actual,
                        'longitud' => $personal->longitud_actual,
                        'precision_m' => $personal->precision_m,
                        'actualizada_at' =>
                        $personal->ubicacion_actualizada_at,
                    ] : null,
                ];
            }
        }

        // Buscar la ubicación más reciente del ciudadano.
        $ultimaUbicacion =
            UbicacionAlerta::where(
                'id_alerta',
                $alerta->id_alerta
            )
            ->orderByDesc('id')
            ->first();


        $latitudCiudadano =
            $ultimaUbicacion
            ? $ultimaUbicacion->latitud
            : $alerta->latitud;


        $longitudCiudadano =
            $ultimaUbicacion
            ? $ultimaUbicacion->longitud
            : $alerta->longitud;


        return response()->json([
            'alerta' => [
                'id_alerta' => $alerta->id_alerta,
                'folio' => $alerta->folio,
                'tipo' => $alerta->tipo?->nombre,
                'estado' => $alerta->estado,
                'prioridad' => $alerta->prioridad,

                'ubicacion' => [
                    'latitud' => $latitudCiudadano,
                    'longitud' => $longitudCiudadano,
                ],

                'policia' => $policia,
            ],
        ]);
    }

    public function actualizarUbicacion(
        Request $request,
        int $id
    ) {
        $usuario = $request->user();


        $alerta = Alerta::where(
            'id_alerta',
            $id
        )
            ->where(
                'id_usuario',
                $usuario->id
            )
            ->first();


        if (!$alerta) {

            return response()->json([
                'message' =>
                'La alerta no existe o no pertenece al usuario.',
            ], 404);
        }


        if (
            $alerta->estado ===
            'CERRADA'
        ) {

            return response()->json([
                'message' =>
                'La alerta ya está cerrada.',
            ], 409);
        }


        $datos = $request->validate([

            'latitud' =>
            'required|numeric|between:-90,90',

            'longitud' =>
            'required|numeric|between:-180,180',

            'precision_m' =>
            'nullable|numeric|min:0',

        ]);


        $ubicacion =
            UbicacionAlerta::create([

                'id_alerta' =>
                $alerta->id_alerta,

                'latitud' =>
                $datos['latitud'],

                'longitud' =>
                $datos['longitud'],

                'precision_m' =>
                $datos['precision_m'] ?? null,

                'fecha_hora' =>
                now(),

            ]);


        return response()->json([

            'message' =>
            'Ubicación actualizada correctamente.',

            'ubicacion' => [

                'latitud' =>
                $ubicacion->latitud,

                'longitud' =>
                $ubicacion->longitud,

                'precision_m' =>
                $ubicacion->precision_m,

                'fecha_hora' =>
                $ubicacion->fecha_hora,

            ],

        ]);
    }
}
