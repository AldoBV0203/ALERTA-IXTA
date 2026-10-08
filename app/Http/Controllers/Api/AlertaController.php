<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\TipoAlerta;
use App\Models\UbicacionAlerta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AlertaController extends Controller
{
    /**
     * Obtener los tipos de alerta activos.
     */
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


    /**
     * Crear una nueva alerta.
     */
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

        /*
         * Comprobar que el tipo de alerta exista
         * y esté activo.
         */
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


        /*
         * Usuario autenticado mediante Sanctum.
         */
        $usuario = $request->user();


        /*
         * Utilizamos una transacción para evitar
         * guardar una alerta incompleta.
         */
        $alerta = DB::transaction(function () use (
            $datos,
            $usuario
        ) {

            /*
             * Folio temporal único.
             *
             * Después de guardar conocemos el ID
             * y generamos el folio definitivo.
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


            /*
             * Ejemplo:
             *
             * AIX-20261007-000001
             */
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
             * Guardamos también la ubicación
             * en el historial de ubicaciones.
             *
             * Las coordenadas serán cifradas
             * automáticamente por el modelo.
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

    /**
     * Obtener las alertas del ciudadano autenticado.
     */
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
}
