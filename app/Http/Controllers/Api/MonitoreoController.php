<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\Operador;
use Illuminate\Http\Request;
use App\Models\UbicacionAlerta;
use App\Models\SeguimientoAlerta;
use Illuminate\Support\Facades\DB;
use App\Models\Auditoria;

class MonitoreoController extends Controller
{
    //Mostrar las alertas recibidas al personal autorizado.

    public function alertas(Request $request)
    {
        $personal = $request->user();

        // Un ciudadano autenticado NO puede entrar al centro de monitoreo          

        if (!$personal instanceof Operador) {
            return response()->json([
                'message' => 'No tienes permiso para acceder al centro de monitoreo.',
            ], 403);
        }

        // La cuenta debe estar activa.

        if (!$personal->activo) {
            return response()->json([
                'message' => 'La cuenta no está autorizada.',
            ], 403);
        }

        // Solo estos roles pueden entrar.

        $rolesPermitidos = [
            'POLICIA',
            'OPERADOR',
            'SUPERVISOR',
            'ADMINISTRADOR',
        ];

        if (!in_array($personal->rol, $rolesPermitidos, true)) {
            return response()->json([
                'message' => 'No tienes permisos suficientes.',
            ], 403);
        }

        // Consultar alertas.

        $alertas = Alerta::with([
            'tipo:id_tipo,nombre'
        ])
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


    // metodo para mostrar el detalle de una alerta.
    public function detalle(Request $request, int $id)
    {
        $personal = $request->user();

        // Solo personal municipal autorizado.
        if (!$personal instanceof Operador) {
            return response()->json([
                'message' => 'No tienes permiso para acceder al centro de monitoreo.',
            ], 403);
        }

        if (!$personal->activo) {
            return response()->json([
                'message' => 'La cuenta no está autorizada.',
            ], 403);
        }

        $rolesPermitidos = [
            'POLICIA',
            'OPERADOR',
            'SUPERVISOR',
            'ADMINISTRADOR',
        ];

        if (!in_array($personal->rol, $rolesPermitidos, true)) {
            return response()->json([
                'message' => 'No tienes permisos suficientes.',
            ], 403);
        }

        // buscar alerta

        $alerta = Alerta::with([
            'tipo:id_tipo,nombre'
        ])
            ->where('id_alerta', $id)
            ->first();

        if (!$alerta) {
            return response()->json([
                'message' => 'La alerta no existe.',
            ], 404);
        }

        // Obtener la ubicación más reciente.

        $ubicacion = UbicacionAlerta::where(
            'id_alerta',
            $alerta->id_alerta
        )
            ->orderByDesc('id')
            ->first();

        return response()->json([
            'alerta' => [
                'id_alerta' => $alerta->id_alerta,

                'folio' => $alerta->folio,

                'tipo' => $alerta->tipo?->nombre,

                'fecha_hora' => $alerta->fecha_hora,

                'prioridad' => $alerta->prioridad,

                'estado' => $alerta->estado,

                'ubicacion' => [
                    'latitud' =>
                    $ubicacion?->latitud ?? $alerta->latitud,

                    'longitud' =>
                    $ubicacion?->longitud ?? $alerta->longitud,

                    'precision_m' =>
                    $ubicacion?->precision_m,
                ],
            ],
        ]);
    }

    // Aceptar una alerta.

    public function aceptar(Request $request, int $id)
    {
        $personal = $request->user();

        // Solo personal municipal autorizado.

        if (!$personal instanceof Operador) {
            return response()->json([
                'message' => 'No tienes permiso para realizar esta acción.',
            ], 403);
        }

        if (!$personal->activo) {
            return response()->json([
                'message' => 'La cuenta no está autorizada.',
            ], 403);
        }

        $rolesPermitidos = [
            'POLICIA',
        ];

        if (!in_array($personal->rol, $rolesPermitidos, true)) {
            return response()->json([
                'message' => 'No tienes permisos suficientes.',
            ], 403);
        }

        if (!$personal->disponible) {
            return response()->json([
                'message' => 'El policía ya se encuentra atendiendo otra alerta.',
            ], 409);
        }

        // Usamos transacción para que el cambio de estado y el seguimiento se guarden juntos.

        $alerta = DB::transaction(function () use ($id, $personal) {

            $policiaBloqueado = Operador::where(
                'id_operador',
                $personal->id_operador
            )
                ->lockForUpdate()
                ->first();

            if (
                !$policiaBloqueado ||
                !$policiaBloqueado->disponible
            ) {
                return 'POLICIA_OCUPADO';
            }

            $alerta = Alerta::where(
                'id_alerta',
                $id
            )
                ->lockForUpdate()
                ->first();

            if (!$alerta) {
                return null;
            }

            // Solo se puede aceptar una alerta NUEVA.

            if ($alerta->estado !== 'NUEVA') {
                return 'NO_DISPONIBLE';
            }

            // Cambiar estado.

            $alerta->estado = 'EN_CAMINO';
            $alerta->save();

            $policiaBloqueado->disponible = false;
            $policiaBloqueado->save();

            // Registrar quién aceptó la alerta.

            SeguimientoAlerta::create([
                'id_alerta' => $alerta->id_alerta,

                'id_operador' => $personal->id_operador,

                'accion' => 'ACEPTO_ALERTA',

                'observaciones' =>
                'El policia aceptó la alerta y se encuentra en camino.',
            ]);

            return $alerta;
        });

        if ($alerta === null) {
            return response()->json([
                'message' => 'La alerta no existe.',
            ], 404);
        }

        if ($alerta === 'NO_DISPONIBLE') {
            return response()->json([
                'message' => 'La alerta ya fue aceptada o cambió de estado.',
            ], 409);
        }

        if ($alerta === 'POLICIA_OCUPADO') {
            return response()->json([
                'message' => 'El policía ya se encuentra atendiendo otra alerta.',
            ], 409);
        }

        $this->registrarAuditoria(
            $request,
            $personal,
            'ACEPTAR_ALERTA',
            $alerta->id_alerta
        );

        return response()->json([
            'message' => 'Alerta aceptada correctamente.',

            'alerta' => [
                'id_alerta' => $alerta->id_alerta,

                'folio' => $alerta->folio,

                'estado' => $alerta->estado,

                'id_operador' => $personal->id_operador,

                'operador' => $personal->nombre,
            ],
        ]);
    }

    // Registrar seguimiento de una alerta.

    public function seguimiento(Request $request, int $id)
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

        $datos = $request->validate([
            'observaciones' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        $alerta = Alerta::where(
            'id_alerta',
            $id
        )->first();

        if (!$alerta) {
            return response()->json([
                'message' => 'La alerta no existe.',
            ], 404);
        }

        if ($alerta->estado === 'NUEVA') {
            return response()->json([
                'message' => 'La alerta debe ser aceptada antes de registrar seguimiento.',
            ], 409);
        }

        if ($alerta->estado === 'CERRADA') {
            return response()->json([
                'message' => 'No se puede modificar una alerta cerrada.',
            ], 409);
        }

        $idOperadorAsignado = $this->obtenerOperadorAsignado(
            $alerta->id_alerta
        );

        if ($idOperadorAsignado !== $personal->id_operador) {
            return response()->json([
                'message' =>
                'Esta alerta está asignada a otro operador.',
            ], 403);
        }

        $seguimiento = SeguimientoAlerta::create([
            'id_alerta' => $alerta->id_alerta,

            'id_operador' => $personal->id_operador,

            'accion' => 'SEGUIMIENTO',

            'observaciones' => $datos['observaciones'],
        ]);

        $this->registrarAuditoria(
            $request,
            $personal,
            'REGISTRAR_SEGUIMIENTO',
            $alerta->id_alerta
        );

        return response()->json([
            'message' => 'Seguimiento registrado correctamente.',

            'seguimiento' => [
                'id' => $seguimiento->id,

                'id_alerta' => $seguimiento->id_alerta,

                'accion' => $seguimiento->accion,

                'observaciones' => $seguimiento->observaciones,

                'operador' => $personal->nombre,
            ],
        ], 201);
    }

    // Cerrar una alerta.

    public function cerrar(Request $request, int $id)
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

        $datos = $request->validate([
            'observaciones' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        $resultado = DB::transaction(function () use (
            $id,
            $personal,
            $datos
        ) {

            $alerta = Alerta::where(
                'id_alerta',
                $id
            )
                ->lockForUpdate()
                ->first();

            if (!$alerta) {
                return 'NO_EXISTE';
            }

            if ($alerta->estado === 'NUEVA') {
                return 'NO_ACEPTADA';
            }

            if ($alerta->estado === 'CERRADA') {
                return 'YA_CERRADA';
            }

            if ($alerta->estado !== 'EN_SITIO') {
                return 'NO_EN_SITIO';
            }

            $idOperadorAsignado = $this->obtenerOperadorAsignado(
                $alerta->id_alerta
            );

            if ($idOperadorAsignado !== $personal->id_operador) {
                return 'OTRO_OPERADOR';
            }

            $alerta->estado = 'CERRADA';
            $alerta->save();

            SeguimientoAlerta::create([
                'id_alerta' => $alerta->id_alerta,

                'id_operador' => $personal->id_operador,

                'accion' => 'CIERRE_ALERTA',

                'observaciones' => $datos['observaciones'],
            ]);

            // El policía vuelve a estar disponible.
            $personal->disponible = true;
            $personal->save();

            return $alerta;
        });

        if ($resultado === 'NO_EXISTE') {
            return response()->json([
                'message' => 'La alerta no existe.',
            ], 404);
        }

        if ($resultado === 'NO_ACEPTADA') {
            return response()->json([
                'message' => 'La alerta debe ser aceptada antes de cerrarse.',
            ], 409);
        }

        if ($resultado === 'YA_CERRADA') {
            return response()->json([
                'message' => 'La alerta ya está cerrada.',
            ], 409);
        }

        if ($resultado === 'OTRO_OPERADOR') {
            return response()->json([
                'message' => 'Esta alerta está asignada a otro operador.',
            ], 403);
        }

        if ($resultado === 'NO_EN_SITIO') {
            return response()->json([
                'message' => 'La alerta solo puede cerrarse cuando el policía haya llegado al sitio.',
            ], 409);
        }

        $this->registrarAuditoria(
            $request,
            $personal,
            'CERRAR_ALERTA',
            $resultado->id_alerta
        );

        return response()->json([
            'message' => 'Alerta cerrada correctamente.',

            'alerta' => [
                'id_alerta' => $resultado->id_alerta,

                'folio' => $resultado->folio,

                'estado' => $resultado->estado,
            ],
        ]);
    }

    // Consultar el historial de seguimiento de una alerta.

    public function historial(Request $request, int $id)
    {
        $personal = $request->user();

        if (!$personal instanceof Operador) {
            return response()->json([
                'message' => 'No tienes permiso para acceder al centro de monitoreo.',
            ], 403);
        }

        if (!$personal->activo) {
            return response()->json([
                'message' => 'La cuenta no está autorizada.',
            ], 403);
        }

        $rolesPermitidos = [
            'POLICIA',
            'OPERADOR',
            'SUPERVISOR',
            'ADMINISTRADOR',
        ];

        if (!in_array($personal->rol, $rolesPermitidos, true)) {
            return response()->json([
                'message' => 'No tienes permisos suficientes.',
            ], 403);
        }

        $alerta = Alerta::where('id_alerta', $id)->first();

        if (!$alerta) {
            return response()->json([
                'message' => 'La alerta no existe.',
            ], 404);
        }

        $historial = SeguimientoAlerta::with([
            'operador:id_operador,nombre'
        ])
            ->where('id_alerta', $id)
            ->orderBy('id')
            ->get()
            ->map(function ($seguimiento) {

                return [
                    'id' => $seguimiento->id,

                    'accion' => $seguimiento->accion,

                    'observaciones' => $seguimiento->observaciones,

                    'fecha_hora' => $seguimiento->fecha_hora,

                    'operador' =>
                    $seguimiento->operador?->nombre,
                ];
            });

        return response()->json([
            'folio' => $alerta->folio,

            'estado' => $alerta->estado,

            'historial' => $historial,
        ]);
    }

    public function policias(Request $request)
    {
        $personal = $request->user();

        if (
            !$personal ||
            !in_array(
                $personal->rol,
                [
                    'OPERADOR',
                    'SUPERVISOR',
                    'ADMINISTRADOR'
                ]
            )
        ) {
            return response()->json([
                'message' => 'No autorizado.'
            ], 403);
        }


        $policias = Operador::where(
            'rol',
            'POLICIA'
        )
            ->where(
                'activo',
                true
            )
            ->orderBy(
                'nombre'
            )
            ->get()
            ->map(function ($policia) {

                $ubicacion = null;

                if (
                    $policia->latitud_actual !== null &&
                    $policia->longitud_actual !== null
                ) {

                    $ubicacion = [
                        'latitud' =>
                        (float) $policia->latitud_actual,

                        'longitud' =>
                        (float) $policia->longitud_actual
                    ];
                }


                return [

                    'id_operador' =>
                    $policia->id_operador,

                    'nombre' =>
                    $policia->nombre,

                    'disponible' =>
                    (bool) $policia->disponible,

                    'ubicacion' =>
                    $ubicacion,

                    'precision_m' =>
                    $policia->precision_m !== null
                        ? (float) $policia->precision_m
                        : null,

                    'ubicacion_actualizada_at' =>
                    $policia->ubicacion_actualizada_at
                        ? $policia->ubicacion_actualizada_at
                        ->toIso8601String()
                        : null
                ];
            });


        return response()->json([
            'policias' => $policias
        ]);
    }

    // Registrar una acción en la auditoría.

    private function registrarAuditoria(
        Request $request,
        Operador $personal,
        string $accion,
        int $idAlerta
    ): void {
        Auditoria::create([
            'usuario_sistema' => $personal->usuario,
            'accion' => $accion,
            'entidad' => 'ALERTA',
            'id_entidad' => $idAlerta,
            'ip' => $request->ip(),
        ]);
    }

    // Obtener el operador que aceptó una alerta.

    private function obtenerOperadorAsignado(int $idAlerta): ?int
    {
        $idOperador = SeguimientoAlerta::where(
            'id_alerta',
            $idAlerta
        )
            ->where('accion', 'ACEPTO_ALERTA')
            ->orderBy('id')
            ->value('id_operador');

        return $idOperador
            ? (int) $idOperador
            : null;
    }
}
