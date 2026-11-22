<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Operador;
use App\Models\User;
use App\Services\CurpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CiudadanoController extends Controller
{
    public function index(Request $request, CurpService $curpService)
    {
        $this->verificarPermiso($request);

        $datos = $request->validate([
            'curp' => 'nullable|string|max:18|regex:/^[A-Za-z0-9]+$/',
            'verificacion' => 'nullable|in:PENDIENTE,VERIFICADA,RECHAZADA',
            'page' => 'sometimes|integer|min:1',
        ], [
            'curp.max' => 'La CURP puede tener hasta 18 caracteres.',
            'curp.regex' => 'Escribe únicamente letras y números.',
        ]);

        $consulta = User::query();

        $prefijo = $curpService->normalizar($datos['curp'] ?? '');

        if ($prefijo !== '') {
            $consulta->whereIn(
                'id',
                DB::table('curp_prefijos')
                    ->select('id_usuario')
                    ->where('longitud', strlen($prefijo))
                    ->where(
                        'prefijo_hash',
                        $curpService->generarLookupPrefijo($prefijo)
                    )
            );
        }

        if (!empty($datos['verificacion'])) {
            $consulta->where(
                'estatus_verificacion',
                $datos['verificacion']
            );
        }

        $pagina = $consulta
            ->orderByDesc('id')
            ->paginate(20, [
                'id',
                'name',
                'curp',
                'estatus',
                'estatus_verificacion',
                'ine_foto_path',
                'created_at',
            ]);

        $ciudadanos = $pagina->getCollection()
            ->map(function (User $ciudadano) {
                return [
                    'id' => $ciudadano->id,
                    'nombre' => $ciudadano->name,
                    'curp' => $ciudadano->curp,
                    'estatus' => $ciudadano->estatus,
                    'estatus_verificacion' => $ciudadano->estatus_verificacion,
                    'tiene_foto_ine' => filled($ciudadano->ine_foto_path),
                    'fecha_registro' => $ciudadano->created_at?->toIso8601String(),
                ];
            });

        return response()->json([
            'ciudadanos' => $ciudadanos,
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

    public function resumen(Request $request)
    {
        $this->verificarPermiso($request);

        $pendientes = User::where('estatus', 'ACTIVO')
            ->where('estatus_verificacion', 'PENDIENTE')
            ->whereNotNull('ine_foto_path')
            ->count();

        return response()->json([
            'pendientes_identidad' => $pendientes,
        ], 200, [
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function detalle(Request $request, int $id)
    {
        $this->verificarPermiso($request);

        $ciudadano = User::findOrFail($id);

        return response()->json([
            'ciudadano' => [
                'id' => $ciudadano->id,
                'nombre' => $ciudadano->name,
                'curp' => $ciudadano->curp,
                'telefono' => $ciudadano->telefono,
                'email' => $ciudadano->email,
                'estatus' => $ciudadano->estatus,
                'estatus_verificacion' => $ciudadano->estatus_verificacion,
                'tiene_foto_ine' => filled($ciudadano->ine_foto_path),
                'motivo_rechazo' => $ciudadano->estatus_verificacion === 'RECHAZADA'
                    ? $ciudadano->ine_motivo_rechazo
                    : null,
                'fecha_registro' => $ciudadano->created_at?->toIso8601String(),
                'identidad_verificada_at' => $ciudadano->identidad_verificada_at?->toIso8601String(),
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
            'No tienes permiso para consultar ciudadanos.'
        );
    }
}
