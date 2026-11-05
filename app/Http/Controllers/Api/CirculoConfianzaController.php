<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CirculoConfianzaController extends Controller
{
    // Generar un código temporal de invitación.
    public function generarCodigo(Request $request)
    {
        $usuario = $request->user();

        // Solamente los ciudadanos registrados pueden generar códigos.
        if (!$usuario instanceof User) {
            return response()->json([
                'message' => 'Esta función es exclusiva para ciudadanos.'
            ], 403);
        }

        // Crear un código aleatorio.
        $codigo = 'IXTA-' . strtoupper(
            bin2hex(random_bytes(6))
        );

        // El código será válido durante 15 minutos.
        $expira = now()->addMinutes(15);

        DB::transaction(function () use ($usuario, $codigo, $expira) {

            // Bloquear temporalmente al usuario para evitar
            // la generación simultánea de varios códigos activos.
            DB::table('users')
                ->where('id', $usuario->id)
                ->lockForUpdate()
                ->first();

            // Invalidar los códigos anteriores del ciudadano.
            DB::table('codigos_confianza')
                ->where('id_usuario', $usuario->id)
                ->whereNull('usado_en')
                ->update([
                    'usado_en' => now(),
                    'updated_at' => now()
                ]);

            // Guardar solamente el hash del código.
            DB::table('codigos_confianza')->insert([
                'id_usuario' => $usuario->id,
                'codigo_hash' => hash('sha256', $codigo),
                'expira_en' => $expira,
                'usado_en' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        });

        return response()->json([
            'message' => 'Código generado correctamente.',
            'codigo' => $codigo,
            'expira_en' => $expira->toIso8601String()
        ]);
    }

    // Crear una solicitud utilizando un código de invitación.
    // Vincular automáticamente a dos ciudadanos mediante un código.
    public function vincularContacto(Request $request)
    {
        $usuario = $request->user();

        if (!($usuario instanceof User)) {
            return response()->json([
                'message' => 'Esta función es exclusiva para ciudadanos.'
            ], 403);
        }

        $datos = $request->validate([
            'codigo' => 'required|string|max:30'
        ]);

        $codigo = strtoupper(trim($datos['codigo']));

        if (!preg_match('/^IXTA-[0-9A-F]{12}$/', $codigo)) {
            return response()->json([
                'message' => 'El código no es válido.'
            ], 422);
        }

        // Localizar el código sin almacenar ni comparar su valor original.
        $registro = DB::table('codigos_confianza')
            ->where('codigo_hash', hash('sha256', $codigo))
            ->first();

        if (!$registro) {
            return response()->json([
                'message' => 'El código no es válido o ya venció.'
            ], 422);
        }

        $miId = (int) $usuario->id;
        $otroId = (int) $registro->id_usuario;

        if ($miId === $otroId) {
            return response()->json([
                'message' => 'No puedes agregarte a ti mismo.'
            ], 422);
        }

        $ids = [$miId, $otroId];
        sort($ids, SORT_NUMERIC);

        $resultado = DB::transaction(function () use (
            $ids,
            $registro,
            $miId
        ) {

            // Bloquear ambas cuentas para evitar vínculos simultáneos.
            $usuarios = DB::table('users')
                ->whereIn('id', $ids)
                ->orderBy('id')
                ->lockForUpdate()
                ->get(['id']);

            if ($usuarios->count() !== 2) {
                return 'INVALIDO';
            }

            // Verificar nuevamente que el código esté vigente.
            $codigoActivo = DB::table('codigos_confianza')
                ->where('id', $registro->id)
                ->where('id_usuario', $registro->id_usuario)
                ->whereNull('usado_en')
                ->where('expira_en', '>', now())
                ->lockForUpdate()
                ->first();

            if (!$codigoActivo) {
                return 'INVALIDO';
            }

            // Buscar si estas dos personas ya tienen una relación.
            $relacion = DB::table('contactos_confianza')
                ->where('id_usuario_1', $ids[0])
                ->where('id_usuario_2', $ids[1])
                ->lockForUpdate()
                ->first();

            if ($relacion && $relacion->estado === 'ACEPTADA') {
                return 'DUPLICADO';
            }

            // Verificar el máximo de cinco contactos confirmados.
            foreach ($ids as $id) {

                $total = DB::table('contactos_confianza')
                    ->where('estado', 'ACEPTADA')
                    ->where(function ($consulta) use ($id) {
                        $consulta->where('id_usuario_1', $id)
                            ->orWhere('id_usuario_2', $id);
                    })
                    ->count();

                if ($total >= 10) {
                    return 'LIMITE';
                }
            }

            // Si había una solicitud antigua, convertirla en vínculo.
            if ($relacion) {

                DB::table('contactos_confianza')
                    ->where('id', $relacion->id)
                    ->update([
                        'id_solicitante' => $miId,
                        'estado' => 'ACEPTADA',
                        'aceptada_en' => now(),
                        'updated_at' => now()
                    ]);
            } else {

                // Crear directamente la relación bidireccional.
                DB::table('contactos_confianza')->insert([
                    'id_usuario_1' => $ids[0],
                    'id_usuario_2' => $ids[1],
                    'id_solicitante' => $miId,
                    'estado' => 'ACEPTADA',
                    'aceptada_en' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Consumir el código para impedir que se reutilice.
            DB::table('codigos_confianza')
                ->where('id', $registro->id)
                ->update([
                    'usado_en' => now(),
                    'updated_at' => now()
                ]);

            return 'VINCULADA';
        }, 3);

        if ($resultado === 'INVALIDO') {
            return response()->json([
                'message' => 'El código no es válido o ya venció.'
            ], 422);
        }

        if ($resultado === 'DUPLICADO') {
            return response()->json([
                'message' => 'Ambos ciudadanos ya son contactos de confianza.'
            ], 409);
        }

        if ($resultado === 'LIMITE') {
            return response()->json([
                'message' => 'Uno de los ciudadanos alcanzó el límite de diez contactos.'
            ], 409);
        }

        return response()->json([
            'message' => 'Contacto vinculado correctamente.',
            'estado' => 'ACEPTADA'
        ], 201);
    }

    // Consultar los contactos de confianza confirmados.
    public function misContactos(Request $request)
    {
        $usuario = $request->user();

        if (!($usuario instanceof User)) {
            return response()->json([
                'message' => 'Esta función es exclusiva para ciudadanos.'
            ], 403);
        }

        $miId = (int) $usuario->id;

        // Buscar vínculos aceptados donde participe el ciudadano.
        $relaciones = DB::table('contactos_confianza')
            ->where('estado', 'ACEPTADA')
            ->where(function ($consulta) use ($miId) {
                $consulta->where('id_usuario_1', $miId)
                    ->orWhere('id_usuario_2', $miId);
            })
            ->orderBy('id')
            ->get();

        // Identificar a la otra persona de cada relación.
        $otrosIds = $relaciones->map(function ($relacion) use ($miId) {

            return (int) $relacion->id_usuario_1 === $miId
                ? $relacion->id_usuario_2
                : $relacion->id_usuario_1;
        });

        // Usar el modelo User para descifrar los nombres.
        $usuarios = User::whereIn('id', $otrosIds)
            ->get()
            ->keyBy('id');

        $contactos = $relaciones->map(function ($relacion) use (
            $miId,
            $usuarios
        ) {

            $otroId = (int) $relacion->id_usuario_1 === $miId
                ? $relacion->id_usuario_2
                : $relacion->id_usuario_1;

            $otro = $usuarios->get($otroId);

            return [
                'id_vinculo' => $relacion->id,
                'nombre' => $otro?->name ?? 'Ciudadano',
                'aceptada_en' => $relacion->aceptada_en
            ];
        });

        return response()->json([
            'contactos' => $contactos,
            'total' => $contactos->count(),
            'maximo' => 10
        ]);
    }    

    // Eliminar una vinculación de confianza.
    public function eliminarContacto(Request $request, int $id)
    {
        $usuario = $request->user();

        if (!($usuario instanceof User)) {
            return response()->json([
                'message' => 'Esta función es exclusiva para ciudadanos.'
            ], 403);
        }

        $miId = (int) $usuario->id;

        $resultado = DB::transaction(function () use ($id, $miId) {

            // Buscar la relación antes de bloquear los usuarios.
            $relacionInicial = DB::table('contactos_confianza')
                ->where('id', $id)
                ->first();

            if (!$relacionInicial) {
                return 'NO_EXISTE';
            }

            $ids = [
                (int) $relacionInicial->id_usuario_1,
                (int) $relacionInicial->id_usuario_2
            ];

            // Solamente las dos personas vinculadas pueden eliminarla.
            if (!in_array($miId, $ids, true)) {
                return 'NO_EXISTE';
            }

            sort($ids, SORT_NUMERIC);

            // Bloquear ambos usuarios para evitar operaciones simultáneas.
            $usuarios = DB::table('users')
                ->whereIn('id', $ids)
                ->orderBy('id')
                ->lockForUpdate()
                ->get(['id']);

            if ($usuarios->count() !== 2) {
                return 'NO_EXISTE';
            }

            // Volver a consultar la relación dentro de la transacción.
            $relacion = DB::table('contactos_confianza')
                ->where('id', $id)
                ->lockForUpdate()
                ->first();

            if (!$relacion) {
                return 'NO_EXISTE';
            }

            // Comprobar que sigue siendo la misma vinculación.
            if (
                (int) $relacion->id_usuario_1 !== $ids[0] ||
                (int) $relacion->id_usuario_2 !== $ids[1]
            ) {
                return 'NO_EXISTE';
            }

            // Solamente eliminar contactos ya aceptados.
            if ($relacion->estado !== 'ACEPTADA') {
                return 'NO_ACEPTADA';
            }

            // Un único registro representa la relación bidireccional.
            DB::table('contactos_confianza')
                ->where('id', $id)
                ->delete();

            return 'ELIMINADA';
        }, 3);

        if ($resultado === 'NO_EXISTE') {
            return response()->json([
                'message' => 'El contacto no existe o no pertenece a tu círculo.'
            ], 404);
        }

        if ($resultado === 'NO_ACEPTADA') {
            return response()->json([
                'message' => 'Esta relación todavía no es un contacto confirmado.'
            ], 409);
        }

        return response()->json([
            'message' => 'Contacto eliminado correctamente.',
            'estado' => 'ELIMINADA'
        ]);
    }
}
