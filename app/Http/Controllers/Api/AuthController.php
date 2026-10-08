<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CurpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function registro(Request $request, CurpService $curpService)
    {
        $datos = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'curp' => [
                'required',
                'string',
                'size:18',
            ],

            'telefono' => [
                'required',
                'regex:/^[0-9]{10}$/',
            ],

            'email' => [
                'nullable',
                'email',
                'max:150',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        /*
     * Normalizar CURP.
     */
        $curp = $curpService->normalizar($datos['curp']);

        /*
     * Validar CURP.
     */
        if (!$curpService->esValida($curp)) {
            return response()->json([
                'message' => 'La CURP ingresada no es válida.',
            ], 422);
        }

        /*
     * Generar curp_lookup.
     */
        $curpLookup = $curpService->generarLookup($curp);

        /*
     * Evitar CURP duplicada.
     */
        if (User::where('curp_lookup', $curpLookup)->exists()) {
            return response()->json([
                'message' => 'Ya existe una cuenta registrada con esta CURP.',
            ], 409);
        }

        /*
     * Crear usuario.
     *
     * User.php cifra automáticamente:
     * curp, name, email y telefono.
     *
     * También hashea password.
     */
        $usuario = User::create([
            'name' => trim($datos['name']),
            'curp' => $curp,
            'curp_lookup' => $curpLookup,
            'telefono' => $datos['telefono'],
            'email' => $datos['email'] ?? null,
            'password' => $datos['password'],
            'estatus' => 'ACTIVO',
            'estatus_verificacion' => 'PENDIENTE',
        ]);

        return response()->json([
            'message' => 'Usuario registrado correctamente.',
            'usuario' => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'estatus_verificacion' => $usuario->estatus_verificacion,
            ],
        ], 201);
    }


    /**
     * Iniciar sesión con CURP y contraseña.
     */
    public function login(Request $request, CurpService $curpService)
    {
        $datos = $request->validate([
            'curp' => [
                'required',
                'string',
                'size:18',
            ],

            'password' => [
                'required',
                'string',
            ],
        ]);

        /*
         * Normalizar la CURP.
         */
        $curp = $curpService->normalizar($datos['curp']);

        /*
         * Comprobar que tenga una estructura válida.
         */
        if (!$curpService->esValida($curp)) {
            return response()->json([
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        /*
         * Generar el HMAC para buscar la CURP
         * sin tener que descifrar todos los usuarios.
         */
        $curpLookup = $curpService->generarLookup($curp);

        /*
         * Buscar ciudadano.
         */
        $usuario = User::where(
            'curp_lookup',
            $curpLookup
        )->first();

        /*
         * Comprobar usuario y contraseña.
         */
        if (
            !$usuario ||
            !Hash::check($datos['password'], $usuario->password)
        ) {
            return response()->json([
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        /*
         * Comprobar que la cuenta esté activa.
         */
        if ($usuario->estatus !== 'ACTIVO') {
            return response()->json([
                'message' => 'La cuenta no se encuentra activa.',
            ], 403);
        }

        /*
         * Crear token para la aplicación Android.
         */
        $token = $usuario
            ->createToken('alerta-ixta-android', ['ciudadano'])
            ->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión correcto.',

            'token' => $token,

            'usuario' => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'estatus_verificacion' =>
                $usuario->estatus_verificacion,
            ],
        ]);
    }




    /**
     * Obtener información del usuario autenticado.
     */
    public function usuario(Request $request)
    {
        $usuario = $request->user();

        return response()->json([
            'usuario' => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'estatus' => $usuario->estatus,
                'estatus_verificacion' =>
                $usuario->estatus_verificacion,
            ],
        ]);
    }


    /**
     * Cerrar sesión.
     */
    public function logout(Request $request)
    {
        $request
            ->user()
            ->currentAccessToken()
            ?->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }
}
