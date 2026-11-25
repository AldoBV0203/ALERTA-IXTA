<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CurpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter;

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
                'required',
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

        // Normalizar CURP.

        $curp = $curpService->normalizar($datos['curp']);

        // Validar CURP.

        if (!$curpService->esValida($curp)) {
            return response()->json([
                'message' => 'La CURP ingresada no es válida.',
            ], 422);
        }

        // Generar curp_lookup.

        $curpLookup = $curpService->generarLookup($curp);

        // Evitar CURP duplicada.

        if (User::where('curp_lookup', $curpLookup)->exists()) {
            return response()->json([
                'message' => 'Ya existe una cuenta registrada con esta CURP.',
            ], 409);
        }

        $email = strtolower(trim($datos['email']));

        $emailLookup = hash_hmac(
            'sha256',
            $email,
            config('app.key')
        );

        if (User::where('email_lookup', $emailLookup)->exists()) {
            return response()->json([
                'message' => 'Ese correo electrónico ya está registrado.',
                'errors' => [
                    'email' => ['Ese correo electrónico ya está registrado.'],
                ],
            ], 422);
        }

        /* Crear usuario. User.php cifra automáticamente: curp, name, email y telefono.También hashea password. */

        $usuario = DB::transaction(function () use (
            $datos,
            $curp,
            $curpLookup,
            $curpService,
            $email,
            $emailLookup
        ) {
            $ciudadano = User::create([
                'name' => trim($datos['name']),
                'curp' => $curp,
                'curp_lookup' => $curpLookup,
                'telefono' => $datos['telefono'],
                'email' => $email,
                'email_lookup' => $emailLookup,
                'password' => $datos['password'],
                'estatus' => 'ACTIVO',
                'estatus_verificacion' => 'PENDIENTE',
            ]);

            $curpService->indexarPrefijos(
                (int) $ciudadano->id,
                $curp
            );

            return $ciudadano;
        });
        try {
            $usuario->sendEmailVerificationNotification();
            $correoEnviado = true;
        } catch (\Throwable $error) {
            report($error);
            $correoEnviado = false;
        }

        return response()->json([
            'message' => $correoEnviado
                ? 'Cuenta registrada. Revisa tu correo para verificarla.'
                : 'Cuenta registrada, pero no pudimos enviar el correo. Solicita otro enlace desde el inicio de sesión.',
            'correo_enviado' => $correoEnviado,
            'usuario' => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'estatus_verificacion' => $usuario->estatus_verificacion,
            ],
        ], 201);
    }


    // inicia sesion con curp y la contraseña

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

        // normalizamos la curp

        $curp = $curpService->normalizar($datos['curp']);

        // se comprueba que tenga una estructura válida

        if (!$curpService->esValida($curp)) {
            return response()->json([
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        // generamos el hmac para buscar la curp sin tener que descifrar todos los usuarios

        $curpLookup = $curpService->generarLookup($curp);

        // se busca al ciudadano

        $usuario = User::where(
            'curp_lookup',
            $curpLookup
        )->first();

        // Comprobar usuario y contraseña.
        if (
            !$usuario ||
            !Hash::check($datos['password'], $usuario->password)
        ) {
            return response()->json([
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        //Comprobamos que la cuenta esté activa.


        if ($usuario->estatus !== 'ACTIVO') {
            return response()->json([
                'message' => 'La cuenta no se encuentra activa.',
            ], 403);
        }

        if (!$usuario->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Confirma tu correo electrónico antes de iniciar sesión.',
                'codigo' => 'CORREO_SIN_VERIFICAR',
            ], 403);
        }

        //se crea token para la aplicación           

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

    public function reenviarVerificacionCorreo(
        Request $request,
        CurpService $curpService
    ) {
        $datos = $request->validate([
            'curp' => ['required', 'string', 'size:18'],
            'password' => ['required', 'string'],
        ]);

        $curp = $curpService->normalizar($datos['curp']);

        if (!$curpService->esValida($curp)) {
            return response()->json([
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        $usuario = User::where(
            'curp_lookup',
            $curpService->generarLookup($curp)
        )->first();

        if (
            !$usuario ||
            !Hash::check($datos['password'], $usuario->password)
        ) {
            return response()->json([
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        if ($usuario->estatus !== 'ACTIVO') {
            return response()->json([
                'message' => 'La cuenta no se encuentra activa.',
            ], 403);
        }

        if ($usuario->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Tu correo ya está verificado. Puedes iniciar sesión.',
            ], 200);
        }

        if (!$usuario->email) {
            return response()->json([
                'message' => 'Esta cuenta no tiene un correo registrado.',
            ], 409);
        }

        $limite = 'reenviar-correo-ciudadano:' . $usuario->id;

        if (RateLimiter::tooManyAttempts($limite, 1)) {
            return response()->json([
                'message' => 'Espera un minuto antes de solicitar otro correo.',
            ], 429);
        }

        try {
            $usuario->sendEmailVerificationNotification();
            RateLimiter::hit($limite, 60);
        } catch (\Throwable $error) {
            report($error);

            return response()->json([
                'message' => 'No pudimos enviar el correo. Intenta más tarde.',
            ], 503);
        }

        return response()->json([
            'message' => 'Te enviamos un nuevo enlace. Revisa tu correo y la carpeta de no deseados.',
        ]);
    }

    private function puedeEnviarIne(User $usuario): bool
    {
        return $usuario->estatus_verificacion === 'RECHAZADA'
            || ($usuario->estatus_verificacion === 'PENDIENTE'
                && blank($usuario->ine_foto_path));
    }

    public function reenviarIne(Request $request)
    {
        $usuario = $request->user();

        if (!($usuario instanceof User) || $usuario->estatus !== 'ACTIVO') {
            return response()->json([
                'message' => 'Necesitas una cuenta de ciudadano activa.',
            ], 403);
        }

        if (!$this->puedeEnviarIne($usuario)) {
            return response()->json([
                'message' => 'Tu cuenta no permite enviar otra foto en este momento.',
            ], 409);
        }

        $request->validate([
            'ine_foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'ine_foto.required' => 'Adjunta una nueva foto del INE.',
            'ine_foto.image' => 'Selecciona una imagen válida.',
            'ine_foto.mimes' => 'La foto debe ser JPG o PNG.',
            'ine_foto.max' => 'La foto debe pesar como máximo 2 MB.',
        ]);

        $disco = Storage::disk('local');
        $nuevaRuta = $request->file('ine_foto')->store('ine', 'local');

        if ($nuevaRuta === false) {
            return response()->json([
                'message' => 'No se pudo guardar la foto. Intenta nuevamente.',
            ], 500);
        }

        try {
            $cambio = DB::transaction(function () use ($usuario, $nuevaRuta) {
                $ciudadano = User::whereKey($usuario->id)
                    ->where('estatus', 'ACTIVO')
                    ->lockForUpdate()
                    ->first();

                if (!$ciudadano || !$this->puedeEnviarIne($ciudadano)) {
                    return null;
                }

                $rutaAnterior = $ciudadano->ine_foto_path;

                $ciudadano->ine_foto_path = $nuevaRuta;
                $ciudadano->estatus_verificacion = 'PENDIENTE';
                $ciudadano->ine_motivo_rechazo = null;
                $ciudadano->identidad_verificada_at = null;
                $ciudadano->save();

                return ['ruta_anterior' => $rutaAnterior];
            }, 3);
        } catch (\Throwable $error) {
            $disco->delete($nuevaRuta);
            report($error);

            return response()->json([
                'message' => 'No se pudo actualizar tu foto. Intenta nuevamente.',
            ], 500);
        }

        if ($cambio === null) {
            $disco->delete($nuevaRuta);

            return response()->json([
                'message' => 'El estado de tu verificación cambió. Consulta tu cuenta.',
            ], 409);
        }

        if ($cambio['ruta_anterior']) {
            $disco->delete($cambio['ruta_anterior']);
        }

        return response()->json([
            'message' => 'Foto enviada. Tu verificación está pendiente de revisión.',
            'estatus_verificacion' => 'PENDIENTE',
        ]);
    }


    // Obtener información del usuario autenticado.

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
                'tiene_foto_ine' => filled($usuario->ine_foto_path),
                'ine_motivo_rechazo' =>
                $usuario->estatus_verificacion === 'RECHAZADA'
                    ? $usuario->ine_motivo_rechazo
                    : null,
            ],
        ]);
    }


    // cerrar sesion

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
