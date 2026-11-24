<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Operador;
use App\Services\CuipService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Auditoria;
use Illuminate\Support\Facades\DB;
use App\Models\SeguimientoAlerta;

class PoliciaMonitoreoController extends Controller
{
    public function index(Request $request, CuipService $cuipService)
    {
        $this->verificarPermiso($request);

        $datos = $request->validate([
            'buscar' => 'nullable|string|max:100',
            'estado' => 'nullable|in:ACTIVA,INACTIVA',
            'page' => 'sometimes|integer|min:1',
            'por_pagina' => 'sometimes|integer|min:20|max:100',
        ], [
            'buscar.max' =>
            'La búsqueda no debe superar los 100 caracteres.',
            'estado.in' => 'Selecciona un estado de cuenta válido.',
        ]);

        $consulta = Operador::where('rol', 'POLICIA');

        $busqueda = trim($datos['buscar'] ?? '');

        if ($busqueda !== '') {
            // Los caracteres especiales se buscan como texto literal.
            $texto = str_replace(
                ['!', '%', '_'],
                ['!!', '!%', '!_'],
                $busqueda
            );

            $patron = '%' . $texto . '%';

            $cuipLookup = strlen($busqueda) <= 50
                ? $cuipService->generarLookup($busqueda)
                : null;

            $consulta->where(function ($filtro) use (
                $patron,
                $cuipLookup
            ) {
                $filtro->whereRaw(
                    "nombre LIKE ? ESCAPE '!'",
                    [$patron]
                )->orWhereRaw(
                    "numero_empleado LIKE ? ESCAPE '!'",
                    [$patron]
                );

                if ($cuipLookup !== null) {
                    $filtro->orWhere('cuip_lookup', $cuipLookup);
                }
            });
        }

        if (isset($datos['estado'])) {
            $consulta->where(
                'activo',
                $datos['estado'] === 'ACTIVA'
            );
        }

        $pagina = $consulta
            ->orderByDesc('id_operador')
            ->paginate($datos['por_pagina'] ?? 20, [
                'id_operador',
                'nombre',
                'numero_empleado',
                'cuip',
                'adscripcion',
                'numero_patrulla',
                'activo',
                'disponible',
            ]);

        $policias = $pagina->getCollection()
            ->map(function (Operador $policia) {
                return [
                    'id_operador' => $policia->id_operador,
                    'nombre' => $policia->nombre,
                    'numero_empleado' => $policia->numero_empleado,
                    'cuip' => $policia->cuip,
                    'adscripcion' => $policia->adscripcion,
                    'numero_patrulla' => $policia->numero_patrulla,
                    'activo' => $policia->activo,
                    'disponible' => $policia->disponible,
                ];
            });

        return response()->json([
            'policias' => $policias,
            'paginacion' => [
                'actual' => $pagina->currentPage(),
                'ultima' => $pagina->lastPage(),
                'por_pagina' => $pagina->perPage(),
                'total' => $pagina->total(),
            ],
        ], 200, [
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function detalle(Request $request, int $id)
    {
        $this->verificarPermiso($request);

        $policia = Operador::where('rol', 'POLICIA')
            ->where('id_operador', $id)
            ->firstOrFail();

        return response()->json([
            'policia' => [
                'id_operador' => $policia->id_operador,
                'nombre' => $policia->nombre,
                'curp' => $policia->curp,
                'telefono' => $policia->telefono,
                'numero_empleado' => $policia->numero_empleado,
                'numero_placa' => $policia->numero_placa,
                'cuip' => $policia->cuip,
                'adscripcion' => $policia->adscripcion,
                'numero_patrulla' => $policia->numero_patrulla,
                'activo' => $policia->activo,
                'disponible' => $policia->disponible,
            ],
        ], 200, [
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function actualizarDatosLaborales(Request $request, int $id)
    {
        $this->verificarPermiso($request);

        $policia = Operador::where('rol', 'POLICIA')
            ->where('id_operador', $id)
            ->firstOrFail();

        $datos = $request->validate([
            'numero_empleado' => [
                'required',
                'string',
                'max:30',
                Rule::unique('operadores', 'numero_empleado')
                    ->ignore($id, 'id_operador'),
            ],
            'numero_placa' => ['required', 'string', 'max:30'],
            'numero_patrulla' => ['required', 'string', 'max:30'],
            'adscripcion' => ['required', 'string', 'max:120'],
        ], [
            'numero_empleado.unique' =>
            'Ese número de empleado ya está registrado.',
        ]);

        $policia->numero_empleado = trim($datos['numero_empleado']);
        $policia->numero_placa = trim($datos['numero_placa']);
        $policia->numero_patrulla = trim($datos['numero_patrulla']);
        $policia->adscripcion = trim($datos['adscripcion']);
        $policia->save();

        return response()->json([
            'message' => 'Datos laborales actualizados correctamente.',
            'policia' => [
                'id_operador' => $policia->id_operador,
                'numero_empleado' => $policia->numero_empleado,
                'numero_placa' => $policia->numero_placa,
                'numero_patrulla' => $policia->numero_patrulla,
                'adscripcion' => $policia->adscripcion,
            ],
        ], 200, [
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function cambiarEstadoCuenta(Request $request, int $id)
    {
        $this->verificarPermiso($request);

        $datos = $request->validate([
            'activo' => ['required', 'boolean'],
        ]);

        $activar = (bool) $datos['activo'];

        return DB::transaction(function () use ($request, $id, $activar) {
            $policia = Operador::where('rol', 'POLICIA')
                ->where('id_operador', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((bool) $policia->activo === $activar) {
                return response()->json([
                    'message' => $activar
                        ? 'La cuenta ya está activa.'
                        : 'La cuenta ya está inactiva.',
                    'activo' => (bool) $policia->activo,
                    'disponible' => (bool) $policia->disponible,
                ]);
            }

            // Comprobar los servicios tanto al activar como al desactivar.
            $atiendeAlerta = DB::table('alertas')
                ->join(
                    'seguimiento_alerta',
                    'seguimiento_alerta.id_alerta',
                    '=',
                    'alertas.id_alerta'
                )
                ->where(
                    'seguimiento_alerta.id_operador',
                    $policia->id_operador
                )
                ->where(
                    'seguimiento_alerta.accion',
                    'ACEPTO_ALERTA'
                )
                ->whereIn(
                    'alertas.estado',
                    ['EN_CAMINO', 'EN_SITIO']
                )
                ->exists();

            if (!$activar && $atiendeAlerta) {
                return response()->json([
                    'message' =>
                    'Este policía tiene una alerta en atención. Finaliza o reasigna el servicio antes de desactivar la cuenta.',
                ], 409);
            }

            $policia->activo = $activar;

            // Solo queda disponible si está activo y no atiende una alerta.
            $policia->disponible = $activar && !$atiendeAlerta;

            $policia->save();

            if (!$activar) {
                $policia->tokens()->delete();
            }

            Auditoria::create([
                'usuario_sistema' => $request->user()->usuario,
                'accion' => $activar
                    ? 'ACTIVO_CUENTA_POLICIAL'
                    : 'DESACTIVO_CUENTA_POLICIAL',
                'entidad' => 'OPERADOR',
                'id_entidad' => $policia->id_operador,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'message' => $activar
                    ? 'Cuenta policial activada.'
                    : 'Cuenta policial desactivada.',
                'activo' => (bool) $policia->activo,
                'disponible' => (bool) $policia->disponible,
            ], 200, [
                'Cache-Control' => 'private, no-store',
            ]);
        }, 3);
    }

    public function servicios(Request $request, int $id)
    {
        $this->verificarPermiso($request);

        Operador::where('rol', 'POLICIA')
            ->where('id_operador', $id)
            ->firstOrFail();

        $pagina = SeguimientoAlerta::with('alerta.tipo')
            ->where('id_operador', $id)
            ->where('accion', 'ACEPTO_ALERTA')
            ->orderByDesc('fecha_hora')
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json([
            'servicios' => $pagina->getCollection()->map(
                function ($registro) {
                    return [
                        'folio' => $registro->alerta?->folio,
                        'tipo' => $registro->alerta?->tipo?->nombre,
                        'estado' => $registro->alerta?->estado,
                        'fecha_aceptacion' =>
                        $registro->fecha_hora?->toIso8601String(),
                    ];
                }
            ),
            'paginacion' => [
                'actual' => $pagina->currentPage(),
                'ultima' => $pagina->lastPage(),
                'total' => $pagina->total(),
            ],
        ], 200, [
            'Cache-Control' => 'private, no-store',
        ]);
    }

    private function verificarPermiso(Request $request): void
    {
        $personal = $request->user();

        abort_unless(
            $personal instanceof Operador &&
                $personal->activo &&
                in_array(
                    $personal->rol,
                    ['OPERADOR', 'SUPERVISOR', 'ADMINISTRADOR'],
                    true
                ),
            403,
            'No tienes permiso para consultar el personal policial.'
        );
    }
}
