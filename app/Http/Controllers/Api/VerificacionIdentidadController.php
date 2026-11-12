<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Operador;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Auditoria;
use Illuminate\Support\Facades\DB;

class VerificacionIdentidadController extends Controller
{
    public function pendientes(Request $request)
    {
        $this->verificarPermiso($request);

        $ciudadanos = User::where('estatus', 'ACTIVO')
            ->where('estatus_verificacion', 'PENDIENTE')
            ->whereNotNull('ine_foto_path')
            ->orderBy('id')
            ->get(['id', 'name', 'curp']);

        return response()->json([
            'ciudadanos' => $ciudadanos->map(function (User $ciudadano) {
                return [
                    'id' => $ciudadano->id,
                    'nombre' => $ciudadano->name,
                    'curp' => $ciudadano->curp,
                ];
            }),
        ], 200, [
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function foto(Request $request, int $id)
    {
        $this->verificarPermiso($request);

        $ciudadano = User::whereKey($id)
            ->where('estatus', 'ACTIVO')
            ->where('estatus_verificacion', 'PENDIENTE')
            ->whereNotNull('ine_foto_path')
            ->firstOrFail(['ine_foto_path']);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disco */
        $disco = Storage::disk('local');
        $ruta = $ciudadano->ine_foto_path;

        abort_unless($disco->fileExists($ruta), 404, 'No se encontró la foto del INE.');

        return $disco->response($ruta, null, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function resolver(Request $request, int $id)
    {
        $this->verificarPermiso($request);
        $personal = $request->user();

        $datos = $request->validate([
            'decision' => 'required|in:VERIFICADA,RECHAZADA',
            'motivo' => 'required_if:decision,RECHAZADA|nullable|string|max:500',
        ], [
            'decision.in' => 'Selecciona una decisión válida.',
            'motivo.required_if' => 'Escribe el motivo del rechazo.',
            'motivo.max' => 'El motivo puede tener hasta 500 caracteres.',
        ]);

        return DB::transaction(function () use ($id, $datos, $personal, $request) {
            $ciudadano = User::whereKey($id)
                ->where('estatus', 'ACTIVO')
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $ciudadano->estatus_verificacion !== 'PENDIENTE' ||
                blank($ciudadano->ine_foto_path)
            ) {
                return response()->json([
                    'message' => 'Esta cuenta ya no tiene una foto pendiente de revisión.',
                ], 409);
            }

            $aprobada = $datos['decision'] === 'VERIFICADA';

            /** @var \Illuminate\Filesystem\FilesystemAdapter $disco */
            $disco = Storage::disk('local');

            if ($aprobada && !$disco->fileExists($ciudadano->ine_foto_path)) {
                return response()->json([
                    'message' => 'No se puede aprobar porque no se encontró la foto.',
                ], 409);
            }

            $ahora = now();

            $ciudadano->estatus_verificacion = $datos['decision'];
            $ciudadano->identidad_verificada_at = $aprobada ? $ahora : null;
            $ciudadano->ine_motivo_rechazo = $aprobada ? null : $datos['motivo'];
            $ciudadano->save();

            Auditoria::create([
                'usuario_sistema' => $personal->usuario,
                'accion' => $aprobada ? 'IDENTIDAD_VERIFICADA' : 'IDENTIDAD_RECHAZADA',
                'entidad' => 'USUARIO',
                'id_entidad' => $ciudadano->id,
                'fecha_hora' => $ahora,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'message' => $aprobada
                    ? 'Identidad verificada correctamente.'
                    : 'Foto rechazada. Se guardó el motivo.',
                'estatus_verificacion' => $ciudadano->estatus_verificacion,
            ]);
        }, 3);
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
            'No tienes permiso para revisar identidades.'
        );
    }
}
