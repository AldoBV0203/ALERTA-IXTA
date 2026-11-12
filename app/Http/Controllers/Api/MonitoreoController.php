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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class MonitoreoController extends Controller
{
    //Mostrar las alertas recibidas al personal autorizado.

    public function alertas(Request $request)
    {
        $personal = $request->user();


        // Un ciudadano autenticado NO puede entrar
        // al centro de monitoreo.
        if (!$personal instanceof Operador) {

            return response()->json([
                'message' =>
                'No tienes permiso para acceder al centro de monitoreo.',
            ], 403);
        }


        // La cuenta debe estar activa.
        if (!$personal->activo) {

            return response()->json([
                'message' =>
                'La cuenta no está autorizada.',
            ], 403);
        }


        $rolesPermitidos = [
            'POLICIA',
            'OPERADOR',
            'SUPERVISOR',
            'ADMINISTRADOR',
        ];


        if (
            !in_array(
                $personal->rol,
                $rolesPermitidos,
                true
            )
        ) {

            return response()->json([
                'message' =>
                'No tienes permisos suficientes.',
            ], 403);
        }


        // Primero consultamos todas las alertas. El centro de monitoreo podrá seguir viendo todas.
        $alertas =
            Alerta::with([
                'tipo:id_tipo,nombre'
            ])->withExists('confirmacionAtencionCiudadano as atencion_confirmada')
            ->orderByDesc('id_alerta')
            ->get();


        //Si quien consulta es un policía, solamente verá las alertas nuevas de Seguridad para las que él sea el policía disponible más cercano.
        if (
            $personal->rol ===
            'POLICIA'
        ) {

            // Si ya está atendiendo otra alerta, no debe recibir nuevas ofertas.             
            if (!$personal->disponible) {

                return response()->json([
                    'alertas' => [],
                ]);
            }


            $alertas =
                $alertas
                ->filter(
                    function ($alerta) use (
                        $personal
                    ) {

                        if (
                            $alerta->estado !==
                            'NUEVA'
                        ) {
                            return false;
                        }


                        if (
                            $alerta->tipo?->nombre !==
                            'Seguridad'
                        ) {
                            return false;
                        }


                        $policiaEnOferta =
                            $this
                            ->obtenerPoliciaEnOferta(
                                $alerta
                            );


                        if (!$policiaEnOferta) {
                            return false;
                        }


                        return
                            (int)
                            $policiaEnOferta
                                ->id_operador
                            ===
                            (int)
                            $personal
                                ->id_operador;
                    }
                )
                ->values();
        }


        $alertas =
            $alertas->map(
                function ($alerta) {

                    return [

                        'id_alerta' =>
                        $alerta->id_alerta,

                        'folio' =>
                        $alerta->folio,

                        'tipo' =>
                        $alerta->tipo?->nombre,

                        'fecha_hora' =>
                        $alerta->fecha_hora,

                        'prioridad' =>
                        $alerta->prioridad,

                        'estado' =>
                        $alerta->estado,

                        'atencion_confirmada' => (bool) $alerta->atencion_confirmada,

                    ];
                }
            );


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
        ])->withExists('confirmacionAtencionCiudadano as atencion_confirmada')
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

        // Buscar al policía que aceptó la alerta.
        $asignacion =
            \App\Models\SeguimientoAlerta::where(
                'id_alerta',
                $alerta->id_alerta
            )
            ->where(
                'accion',
                'ACEPTO_ALERTA'
            )
            ->orderByDesc('id')
            ->first();


        $policiaAsignado =
            null;


        if ($asignacion) {

            $policia =
                Operador::where(
                    'id_operador',
                    $asignacion->id_operador
                )
                ->where(
                    'rol',
                    'POLICIA'
                )
                ->first();


            if ($policia) {

                $policiaAsignado = [

                    'id_operador' =>
                    $policia->id_operador,

                    'nombre' =>
                    $policia->nombre,

                    'ubicacion' => [

                        'latitud' =>
                        $policia->latitud_actual,

                        'longitud' =>
                        $policia->longitud_actual,

                        'actualizada_at' =>
                        $policia->ubicacion_actualizada_at,

                    ],

                ];
            }
        }

        return response()->json([
            'alerta' => [
                'id_alerta' => $alerta->id_alerta,

                'folio' => $alerta->folio,

                'tipo' => $alerta->tipo?->nombre,

                'fecha_hora' => $alerta->fecha_hora,

                'prioridad' => $alerta->prioridad,

                'estado' => $alerta->estado,
                'atencion_confirmada' => (bool) $alerta->atencion_confirmada,

                'ubicacion' => [
                    'latitud' =>
                    $ubicacion?->latitud ?? $alerta->latitud,

                    'longitud' =>
                    $ubicacion?->longitud ?? $alerta->longitud,

                    'precision_m' =>
                    $ubicacion?->precision_m,
                ],
                'policia' =>
                $policiaAsignado,
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

        // Comprobar que la alerta exista antes de revisar la asignación.
        $alertaParaOferta =
            Alerta::where(
                'id_alerta',
                $id
            )
            ->first();


        if (!$alertaParaOferta) {

            return response()->json([
                'message' =>
                'La alerta no existe.',
            ], 404);
        }


        // Solamente una alerta NUEVA puede estar disponible.
        if (
            $alertaParaOferta->estado !==
            'NUEVA'
        ) {

            return response()->json([
                'message' =>
                'La alerta ya fue aceptada o cambió de estado.',
            ], 409);
        }


        // Verificar que este policía sea actualmente el más cercano.
        $policiaEnOferta =
            $this
            ->obtenerPoliciaEnOferta(
                $alertaParaOferta
            );


        if (
            !$policiaEnOferta ||
            (int)
            $policiaEnOferta->id_operador
            !==
            (int)
            $personal->id_operador
        ) {

            return response()->json([

                'message' =>
                'El tiempo de esta oferta terminó o la alerta fue enviada a otro policía.',

            ], 403);
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

    // Rechazar una oferta de alerta.

    public function rechazar(
        Request $request,
        int $id
    ) {

        $personal =
            $request->user();


        /*
     * Solo un policía activo
     * puede rechazar una oferta.
     */
        if (
            !$personal instanceof Operador ||
            !$personal->activo ||
            $personal->rol !== 'POLICIA'
        ) {

            return response()->json([
                'message' =>
                'No tienes permiso para realizar esta acción.',
            ], 403);
        }


        /*
     * Buscar la alerta.
     */
        $alerta =
            Alerta::where(
                'id_alerta',
                $id
            )
            ->first();


        if (!$alerta) {

            return response()->json([
                'message' =>
                'La alerta no existe.',
            ], 404);
        }


        /*
     * Solo se puede rechazar
     * mientras siga siendo NUEVA.
     */
        if (
            $alerta->estado !==
            'NUEVA'
        ) {

            return response()->json([
                'message' =>
                'La alerta ya fue aceptada o cambió de estado.',
            ], 409);
        }


        /*
     * Verificar que la oferta actual
     * realmente pertenece a este policía.
     */
        $policiaEnOferta =
            $this
            ->obtenerPoliciaEnOferta(
                $alerta
            );


        if (
            !$policiaEnOferta ||
            (int)
            $policiaEnOferta->id_operador
            !==
            (int)
            $personal->id_operador
        ) {

            return response()->json([
                'message' =>
                'Esta alerta ya no está asignada a tu oferta.',
            ], 403);
        }


        /*
     * Registrar el rechazo.
     */
        SeguimientoAlerta::create([

            'id_alerta' =>
            $alerta->id_alerta,

            'id_operador' =>
            $personal->id_operador,

            'accion' =>
            'OFERTA_RECHAZADA',

            'observaciones' =>
            'El policía rechazó la oferta de atención.',

        ]);


        /*
     * Registrar auditoría.
     */
        $this->registrarAuditoria(
            $request,
            $personal,
            'RECHAZAR_OFERTA_ALERTA',
            $alerta->id_alerta
        );


        /*
     * Buscar inmediatamente al
     * siguiente policía disponible.
     */
        $this
            ->obtenerPoliciaEnOferta(
                $alerta
            );


        return response()->json([
            'message' =>
            'Oferta rechazada correctamente.',
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

    public function guardarEvidenciaPolicial(Request $request, int $id)
    {
        $personal = $request->user();

        if (
            !$personal instanceof Operador ||
            !$personal->activo ||
            $personal->rol !== 'POLICIA'
        ) {
            return response()->json([
                'message' => 'No tienes permiso para subir evidencia.',
            ], 403);
        }

        $datos = $request->validate([
            'evidencia_foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'evidencia_foto.required' => 'Selecciona una foto de evidencia.',
            'evidencia_foto.image' => 'Selecciona una imagen válida.',
            'evidencia_foto.mimes' => 'La foto debe ser JPG o PNG.',
            'evidencia_foto.max' => 'La foto debe pesar como máximo 2 MB.',
        ]);

        $foto = $datos['evidencia_foto'];
        $disco = Storage::disk('local');
        $rutaNueva = null;

        try {
            return DB::transaction(function () use ($id, $personal, $foto, $disco, &$rutaNueva) {
                $alerta = Alerta::whereKey($id)->lockForUpdate()->first();

                if (!$alerta) {
                    return response()->json(['message' => 'La alerta no existe.'], 404);
                }

                if ($this->obtenerOperadorAsignado($id) !== (int) $personal->id_operador) {
                    return response()->json([
                        'message' => 'Esta alerta no está asignada a tu cuenta.',
                    ], 403);
                }

                if ($alerta->estado !== 'EN_SITIO') {
                    return response()->json([
                        'message' => 'Solo puedes subir evidencia cuando la alerta esté en sitio.',
                    ], 409);
                }

                $evidencia = SeguimientoAlerta::firstOrNew([
                    'id_alerta' => $id,
                    'id_operador' => $personal->id_operador,
                    'accion' => 'EVIDENCIA_POLICIAL',
                ]);

                if ($evidencia->evidencia_foto_path && $disco->exists($evidencia->evidencia_foto_path)) {
                    return response()->json(['message' => 'Evidencia guardada.']);
                }

                $rutaNueva = $foto->store('evidencias-alertas', 'local');

                if ($rutaNueva === false) {
                    throw new \RuntimeException('No se pudo guardar la foto de evidencia.');
                }

                $evidencia->evidencia_foto_path = $rutaNueva;
                $evidencia->observaciones = 'El policía guardó una foto de evidencia.';
                $evidencia->fecha_hora = now();
                $evidencia->save();

                return response()->json(['message' => 'Evidencia guardada.'], 201);
            });
        } catch (\Throwable $error) {
            if ($rutaNueva) {
                $disco->delete($rutaNueva);
            }

            report($error);

            return response()->json([
                'message' => 'No fue posible guardar la evidencia. Intenta nuevamente.',
            ], 500);
        }
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
            'observaciones' => 'required|string|max:1000',
        ]);

        return DB::transaction(function () use ($request, $id, $personal, $datos) {
            $alerta = Alerta::whereKey($id)->lockForUpdate()->first();

            if (!$alerta) {
                return response()->json(['message' => 'La alerta no existe.'], 404);
            }

            if ($alerta->estado === 'NUEVA') {
                return response()->json([
                    'message' => 'La alerta debe ser aceptada antes de finalizar la atención.',
                ], 409);
            }

            if ($this->obtenerOperadorAsignado($id) !== (int) $personal->id_operador) {
                return response()->json([
                    'message' => 'Esta alerta está asignada a otro operador.',
                ], 403);
            }

            if ($alerta->estado === 'EN_SITIO') {
                $rutaEvidencia = SeguimientoAlerta::where('id_alerta', $id)
                    ->where('id_operador', $personal->id_operador)
                    ->where('accion', 'EVIDENCIA_POLICIAL')
                    ->value('evidencia_foto_path');

                if (blank($rutaEvidencia) || !Storage::disk('local')->exists($rutaEvidencia)) {
                    return response()->json([
                        'message' => 'Sube la foto de evidencia antes de finalizar la atención.',
                    ], 409);
                }

                SeguimientoAlerta::create([
                    'id_alerta' => $id,
                    'id_operador' => $personal->id_operador,
                    'accion' => 'ATENCION_FINALIZADA_POLICIA',
                    'observaciones' => $datos['observaciones'],
                    'fecha_hora' => now(),
                ]);

                $alerta->estado = 'PENDIENTE_CONFIRMACION';
                $alerta->save();

                $personal->disponible = true;
                $personal->save();

                $this->registrarAuditoria($request, $personal, 'FINALIZAR_ATENCION_POLICIA', $id);
            } else {
                $yaFinalizada = in_array($alerta->estado, ['PENDIENTE_CONFIRMACION', 'CERRADA'], true)
                    && SeguimientoAlerta::where('id_alerta', $id)
                    ->where('id_operador', $personal->id_operador)
                    ->where('accion', 'ATENCION_FINALIZADA_POLICIA')
                    ->exists();

                if (!$yaFinalizada) {
                    return response()->json([
                        'message' => 'La alerta no permite finalizar la atención en su estado actual.',
                    ], 409);
                }
            }

            $alerta->cerrarSiAtencionConfirmada();

            return response()->json([
                'message' => $alerta->estado === 'CERRADA'
                    ? 'Alerta cerrada correctamente.'
                    : 'Atención finalizada. Falta la confirmación del ciudadano.',
                'alerta' => [
                    'id_alerta' => $alerta->id_alerta,
                    'folio' => $alerta->folio,
                    'estado' => $alerta->estado,
                ],
            ]);
        });
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

    /*
    Buscar al policía disponible más cercano a una alerta.
 */
    private function obtenerPoliciaMasCercano(
        Alerta $alerta,
        array $excluirIds = [],
        array $tiemposPorPolicia = []
    ): ?Operador {

        $ultimaUbicacion =
            UbicacionAlerta::where(
                'id_alerta',
                $alerta->id_alerta
            )
            ->orderByDesc('id')
            ->first();


        $latitudCiudadano =
            $ultimaUbicacion
            ? (float) $ultimaUbicacion->latitud
            : (float) $alerta->latitud;


        $longitudCiudadano =
            $ultimaUbicacion
            ? (float) $ultimaUbicacion->longitud
            : (float) $alerta->longitud;


        $limiteUbicacion =
            now()->subSeconds(30); //si el policia deja de mandar su ubicacion durante 30s ya no recibe alertas


        $consultaPolicias =
            Operador::where(
                'rol',
                'POLICIA'
            )
            ->where(
                'activo',
                true
            )
            ->where(
                'disponible',
                true
            );


        /*
     * No volver a ofrecer la misma
     * alerta a policías cuyo turno
     * ya terminó.
     */
        if (
            count($excluirIds) > 0
        ) {

            $consultaPolicias
                ->whereNotIn(
                    'id_operador',
                    $excluirIds
                );
        }


        $policias =
            $consultaPolicias
            ->get()
            ->filter(
                function ($policia) use (
                    $limiteUbicacion
                ) {

                    if (
                        $policia->latitud_actual ===
                        null ||
                        $policia->longitud_actual ===
                        null ||
                        $policia->ubicacion_actualizada_at ===
                        null
                    ) {
                        return false;
                    }


                    return
                        $policia
                        ->ubicacion_actualizada_at
                        ->greaterThanOrEqualTo(
                            $limiteUbicacion
                        );
                }
            );

        // Preparar la lista de policías disponibles.
        $policias = $policias->values();

        // Utilizar los tiempos de recorrido previamente calculados.
        if (!empty($tiemposPorPolicia)) {

            $policiaMasRapido = $policias
                ->filter(function ($policia) use ($tiemposPorPolicia) {

                    $id = (int) $policia->id_operador;

                    return isset($tiemposPorPolicia[$id])
                        && is_numeric($tiemposPorPolicia[$id]);
                })
                ->sortBy(function ($policia) use ($tiemposPorPolicia) {

                    $id = (int) $policia->id_operador;

                    return (float) $tiemposPorPolicia[$id];
                })
                ->first();

            if ($policiaMasRapido !== null) {
                return $policiaMasRapido;
            }
        }

        // Si no hay tiempos de ruta, utilizar la distancia en línea recta.

        $policiaMasCercano =
            null;


        $menorDistancia =
            null;


        foreach (
            $policias as $policia
        ) {

            $distancia =
                $this
                ->calcularDistanciaMetros(
                    (float)
                    $policia->latitud_actual,

                    (float)
                    $policia->longitud_actual,

                    $latitudCiudadano,

                    $longitudCiudadano
                );


            if (
                $menorDistancia === null ||
                $distancia <
                $menorDistancia
            ) {

                $menorDistancia =
                    $distancia;


                $policiaMasCercano =
                    $policia;
            }
        }


        return $policiaMasCercano;
    }

    /*
 * Obtener al policía que tiene
 * actualmente la oferta de la alerta.
 */
    // Consultar los tiempos de llegada sin bloquear una alerta en MySQL.
    private function consultarTiemposRutaPolicias(Alerta $alerta): array
    {
        if ($alerta->estado !== 'NUEVA') {
            return [];
        }

        // Evitar consultar OSRM mientras exista una oferta vigente.
        $ultimaOferta = SeguimientoAlerta::where(
            'id_alerta',
            $alerta->id_alerta
        )
            ->where('accion', 'OFERTA_ALERTA')
            ->orderByDesc('id')
            ->first();

        if ($ultimaOferta) {
            $vence = \Carbon\Carbon::parse(
                $ultimaOferta->fecha_hora
            )->addSeconds(40);

            if (now()->lt($vence)) {
                $rechazada = SeguimientoAlerta::where(
                    'id_alerta',
                    $alerta->id_alerta
                )
                    ->where('accion', 'OFERTA_RECHAZADA')
                    ->where('id', '>', $ultimaOferta->id)
                    ->exists();

                $disponible = Operador::where(
                    'id_operador',
                    $ultimaOferta->id_operador
                )
                    ->where('rol', 'POLICIA')
                    ->where('activo', true)
                    ->where('disponible', true)
                    ->exists();

                if (!$rechazada && $disponible) {
                    return [];
                }
            }
        }

        // Obtener la ubicación más reciente del ciudadano.
        $ubicacion = UbicacionAlerta::where(
            'id_alerta',
            $alerta->id_alerta
        )
            ->orderByDesc('id')
            ->first();

        $latitud = (float) (
            $ubicacion?->latitud ?? $alerta->latitud
        );

        $longitud = (float) (
            $ubicacion?->longitud ?? $alerta->longitud
        );

        // Considerar únicamente policías disponibles con GPS reciente.
        $limite = now()->subSeconds(30);

        $policias = Operador::where('rol', 'POLICIA')
            ->where('activo', true)
            ->where('disponible', true)
            ->whereNotNull('latitud_actual')
            ->whereNotNull('longitud_actual')
            ->whereNotNull('ubicacion_actualizada_at')
            ->get()
            ->filter(function ($policia) use ($limite) {
                return $policia->ubicacion_actualizada_at
                    ->greaterThanOrEqualTo($limite);
            })
            ->values();

        // Con un solo policía no es necesario comparar rutas.
        if ($policias->count() < 2) {
            return [];
        }

        // OSRM recibe las coordenadas en orden longitud, latitud.
        $puntos = [];

        foreach ($policias as $policia) {
            $puntos[] =
                (float) $policia->longitud_actual . ',' .
                (float) $policia->latitud_actual;
        }

        $puntos[] = $longitud . ',' . $latitud;

        $origenes = implode(
            ';',
            range(0, $policias->count() - 1)
        );

        $destino = $policias->count();

        // Reutilizar brevemente resultados de coordenadas idénticas.
        $claveCache = 'osrm_alerta_' .
            $alerta->id_alerta . '_' .
            md5(implode(';', $puntos));

        try {
            return Cache::remember(
                $claveCache,
                15,
                function () use (
                    $puntos,
                    $origenes,
                    $destino,
                    $policias
                ) {
                    $respuesta = Http::withOptions([
                        'curl' => [
                            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
                        ]
                    ])
                        ->connectTimeout(4)
                        ->timeout(7)
                        ->get(
                            'https://router.project-osrm.org/table/v1/driving/' .
                                implode(';', $puntos),
                            [
                                'sources' => $origenes,
                                'destinations' => $destino,
                                'annotations' => 'duration'
                            ]
                        );

                    if (
                        !$respuesta->successful() ||
                        $respuesta->json('code') !== 'Ok'
                    ) {
                        return [];
                    }

                    $duraciones = $respuesta->json('durations', []);
                    $tiempos = [];

                    foreach ($policias as $indice => $policia) {
                        $segundos = $duraciones[$indice][0] ?? null;

                        if (is_numeric($segundos)) {
                            $tiempos[(int) $policia->id_operador] =
                                (float) $segundos;
                        }
                    }

                    return $tiempos;
                }
            );
        } catch (\Throwable $error) {
            // Recordar el fallo para evitar consultas repetidas inmediatamente.
            Cache::put($claveCache, [], now()->addSeconds(15));

            // Registrar el error sin detener la asignación policial.
            \Illuminate\Support\Facades\Log::warning(
                'Error al consultar tiempos de recorrido OSRM.',
                [
                    'id_alerta' => $alerta->id_alerta,
                    'error' => $error->getMessage()
                ]
            );

            // Utilizar temporalmente la distancia en línea recta.
            return [];
        }
    }


    private function obtenerPoliciaEnOferta(
        Alerta $alerta
    ): ?Operador {

        // Calcular los tiempos antes de iniciar la transacción.
        $tiemposPorPolicia = $this->consultarTiemposRutaPolicias(
            $alerta
        );

        return DB::transaction(
            function () use ($alerta, $tiemposPorPolicia) {

                // Bloquear brevemente la alerta para evitar que dos policías reciban una nueva oferta al mismo tiempo.

                $alertaBloqueada =
                    Alerta::where(
                        'id_alerta',
                        $alerta->id_alerta
                    )
                    ->lockForUpdate()
                    ->first();


                if (
                    !$alertaBloqueada ||
                    $alertaBloqueada->estado !==
                    'NUEVA'
                ) {

                    return null;
                }


                // Buscar la oferta más reciente.
                $ultimaOferta =
                    SeguimientoAlerta::where(
                        'id_alerta',
                        $alertaBloqueada->id_alerta
                    )
                    ->where(
                        'accion',
                        'OFERTA_ALERTA'
                    )
                    ->orderByDesc('id')
                    ->first();


                if ($ultimaOferta) {

                    $ofertaRechazada =
                        SeguimientoAlerta::where(
                            'id_alerta',
                            $alertaBloqueada->id_alerta
                        )
                        ->where(
                            'id_operador',
                            $ultimaOferta->id_operador
                        )
                        ->where(
                            'accion',
                            'OFERTA_RECHAZADA'
                        )
                        ->where(
                            'id',
                            '>',
                            $ultimaOferta->id
                        )
                        ->exists();


                    // Si el policía NO rechazó, revisar si todavía siguen vigentes sus 20 segundos.
                    if (!$ofertaRechazada) {

                        $vence =
                            \Carbon\Carbon::parse(
                                $ultimaOferta->fecha_hora
                            )
                            ->addSeconds(40);


                        $policiaOferta =
                            Operador::where(
                                'id_operador',
                                $ultimaOferta->id_operador
                            )
                            ->where(
                                'rol',
                                'POLICIA'
                            )
                            ->where(
                                'activo',
                                true
                            )
                            ->where(
                                'disponible',
                                true
                            )
                            ->first();


                        // La oferta todavía está vigente.
                        if (
                            now()->lt($vence) &&
                            $policiaOferta
                        ) {

                            return $policiaOferta;
                        }


                        // La oferta terminó por tiempo.
                        $vencimientoRegistrado =
                            SeguimientoAlerta::where(
                                'id_alerta',
                                $alertaBloqueada->id_alerta
                            )
                            ->where(
                                'id_operador',
                                $ultimaOferta->id_operador
                            )
                            ->where(
                                'accion',
                                'OFERTA_VENCIDA'
                            )
                            ->where(
                                'id',
                                '>',
                                $ultimaOferta->id
                            )
                            ->exists();


                        if (
                            !$vencimientoRegistrado
                        ) {

                            SeguimientoAlerta::create([

                                'id_alerta' =>
                                $alertaBloqueada->id_alerta,

                                'id_operador' =>
                                $ultimaOferta->id_operador,

                                'accion' =>
                                'OFERTA_VENCIDA',

                                'observaciones' =>
                                'El policía no aceptó la alerta dentro de los 40 segundos.',

                            ]);
                        }
                    }
                }


                // Buscar si anteriormente se inició una nueva ronda de ofertas.
                $ultimoReinicio =
                    SeguimientoAlerta::where(
                        'id_alerta',
                        $alertaBloqueada->id_alerta
                    )
                    ->where(
                        'accion',
                        'REINICIO_RONDA'
                    )
                    ->orderByDesc('id')
                    ->first();


                // Obtener únicamente los policías intentados durante la ronda actual.

                $consultaIntentados =
                    SeguimientoAlerta::where(
                        'id_alerta',
                        $alertaBloqueada->id_alerta
                    )
                    ->where(
                        'accion',
                        'OFERTA_ALERTA'
                    );


                if ($ultimoReinicio) {

                    $consultaIntentados
                        ->where(
                            'id',
                            '>',
                            $ultimoReinicio->id
                        );
                }


                $policiasIntentados =
                    $consultaIntentados
                    ->pluck(
                        'id_operador'
                    )
                    ->map(
                        function ($id) {

                            return (int) $id;
                        }
                    )
                    ->unique()
                    ->values()
                    ->all();


                // Buscar al siguiente más cercano, excluyendo a los anteriores.
                $siguientePolicia =
                    $this
                    ->obtenerPoliciaMasCercano(
                        $alertaBloqueada,
                        $policiasIntentados,
                        $tiemposPorPolicia
                    );


                if (!$siguientePolicia) {

                    // Buscar la última vez que una oferta terminó por rechazo o vencimiento.
                    $ultimaFinalizacion =
                        SeguimientoAlerta::where(
                            'id_alerta',
                            $alertaBloqueada->id_alerta
                        )
                        ->whereIn(
                            'accion',
                            [
                                'OFERTA_VENCIDA',
                                'OFERTA_RECHAZADA'
                            ]
                        )
                        ->orderByDesc('id')
                        ->first();


                    //  Si nunca hubo una oferta, simplemente no hay policías disponibles en este momento.
                    if (!$ultimaFinalizacion) {

                        return null;
                    }


                    /* Si ya reiniciamos la ronda después
                    de la última finalización pero todavía no apareció ningún policía disponible, no crear otro reinicio. */
                    if (
                        $ultimoReinicio &&
                        $ultimoReinicio->id >
                        $ultimaFinalizacion->id
                    ) {

                        return null;
                    }


                    // Esperar 20 segundos antes de iniciar nuevamente una ronda completa.
                    $momentoReinicio =
                        \Carbon\Carbon::parse(
                            $ultimaFinalizacion->fecha_hora
                        )
                        ->addSeconds(20);


                    if (
                        now()->lt(
                            $momentoReinicio
                        )
                    ) {

                        return null;
                    }


                    /* Registrar el inicio de una nueva ronda.
                    Usamos el último policía de la oferta anterior para conservar la relación existente en seguimiento_alerta. */
                    SeguimientoAlerta::create([

                        'id_alerta' =>
                        $alertaBloqueada->id_alerta,

                        'id_operador' =>
                        $ultimaOferta->id_operador,

                        'accion' =>
                        'REINICIO_RONDA',

                        'observaciones' =>
                        'Todos los policías de la ronda anterior fueron descartados. Se inicia una nueva ronda de búsqueda.',

                    ]);


                    // En la nueva ronda ya se pueden considerar nuevamente todos los policías disponibles.
                    $policiasIntentados =
                        [];


                    $siguientePolicia =
                        $this
                        ->obtenerPoliciaMasCercano(
                            $alertaBloqueada,
                            $policiasIntentados,
                            $tiemposPorPolicia
                        );


                    // Puede ocurrir que después de los 20 segundos ningún policía siga conectado o disponible.
                    if (!$siguientePolicia) {

                        return null;
                    }
                }


                // Registrar la nueva oferta.
                SeguimientoAlerta::create([

                    'id_alerta' =>
                    $alertaBloqueada->id_alerta,

                    'id_operador' =>
                    $siguientePolicia->id_operador,

                    'accion' =>
                    'OFERTA_ALERTA',

                    'observaciones' =>
                    'Alerta ofrecida al policía durante 40 segundos.',

                ]);


                return $siguientePolicia;
            }
        );
    }


    // Calcular distancia aproximada entre dos coordenadas.
    private function calcularDistanciaMetros(
        float $latitud1,
        float $longitud1,
        float $latitud2,
        float $longitud2
    ): float {

        $radioTierra =
            6371000;


        $lat1 =
            deg2rad(
                $latitud1
            );


        $lat2 =
            deg2rad(
                $latitud2
            );


        $diferenciaLatitud =
            deg2rad(
                $latitud2 -
                    $latitud1
            );


        $diferenciaLongitud =
            deg2rad(
                $longitud2 -
                    $longitud1
            );


        $a =
            sin(
                $diferenciaLatitud / 2
            ) ** 2
            +
            cos($lat1)
            *
            cos($lat2)
            *
            sin(
                $diferenciaLongitud / 2
            ) ** 2;


        $c =
            2
            *
            atan2(
                sqrt($a),
                sqrt(1 - $a)
            );


        return
            $radioTierra *
            $c;
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
