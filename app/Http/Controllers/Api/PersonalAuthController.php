<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Operador;
use App\Services\CuipService;
use App\Services\CurpService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;

class PersonalAuthController extends Controller
{
    // Registro general del personal municipal.
    public function registro(Request $request)
    {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
            ],
            'usuario' => [
                'required',
                'string',
                'min:4',
                'max:50',
                'regex:/^[a-zA-Z0-9._-]+$/',
                'unique:operadores,usuario',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'usuario.required' => 'El usuario es obligatorio.',
            'usuario.unique' => 'Ese nombre de usuario ya está registrado.',
            'usuario.regex' =>
            'El usuario solo puede contener letras, números, punto, guion y guion bajo.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $operador = Operador::create([
            'nombre' => trim($datos['nombre']),
            'usuario' => strtolower(trim($datos['usuario'])),
            'password_hash' => Hash::make($datos['password']),
            'rol' => 'PENDIENTE',
            'activo' => false,
        ]);

        return response()->json([
            'message' =>
            'Registro realizado. La cuenta está pendiente de autorización.',
            'personal' => [
                'id_operador' => $operador->id_operador,
                'nombre' => $operador->nombre,
                'usuario' => $operador->usuario,
                'rol' => $operador->rol,
                'activo' => $operador->activo,
            ],
        ], 201);
    }

    // Registro policial pendiente de autorización.
    public function registroPolicia(
        Request $request,
        CurpService $curpService,
        CuipService $cuipService
    ) {
        foreach (
            [
                'curp',
                'numero_empleado',
                'numero_placa',
                'cuip',
                'numero_patrulla',
            ] as $campo
        ) {
            $valor = $request->input($campo);

            if (is_string($valor)) {
                $valor = strtoupper(trim($valor));

                $request->merge([
                    $campo => $valor === '' ? null : $valor,
                ]);
            }
        }

        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'curp' => 'required|string|size:18',
            'telefono' => 'required|string|regex:/^[0-9]{10}$/',
            'email' => 'required|email|max:150',
            'numero_empleado' =>
            'required|string|max:30|unique:operadores,numero_empleado',
            'numero_placa' => 'required|string|max:30',
            'cuip' => 'required|string|max:50',
            'adscripcion' => 'required|string|max:120',
            'numero_patrulla' => 'required|string|max:30',
            'password' => [
                'required',
                'string',
                'max:72',
                'confirmed',
                Password::min(8)->letters()->numbers(),
            ],
        ], [
            'nombre.required' => 'Escribe tu nombre completo.',
            'curp.required' => 'La CURP es obligatoria.',
            'curp.size' => 'La CURP debe contener 18 caracteres.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.regex' => 'El teléfono debe contener 10 números.',
            'email.required' => 'Escribe tu correo electrónico.',
            'email.email' => 'Escribe un correo electrónico válido.',
            'numero_empleado.required' => 'Escribe tu número de empleado.',
            'numero_empleado.unique' =>
            'Ese número de empleado ya está registrado.',
            'numero_placa.required' => 'Escribe tu número de placa.',
            'cuip.required' => 'La CUIP es obligatoria.',
            'cuip.max' => 'La CUIP no debe superar los 50 caracteres.',
            'adscripcion.required' => 'Escribe tu área de adscripción.',
            'numero_patrulla.required' => 'Escribe tu unidad asignada.',
            'password' =>
            'La contraseña debe tener entre 8 y 72 caracteres, con letras y números.',
            'password.required' => 'Escribe una contraseña.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $curp = $curpService->normalizar($datos['curp']);

        if (!$curpService->esValida($curp)) {
            throw ValidationException::withMessages([
                'curp' =>
                'La CURP ingresada no tiene una estructura válida.',
            ]);
        }

        $curpLookup = $curpService->generarLookup($curp);

        if (Operador::where('curp_lookup', $curpLookup)->exists()) {
            throw ValidationException::withMessages([
                'curp' =>
                'Ya existe una cuenta de personal con esta CURP.',
            ]);
        }

        $cuip = $cuipService->normalizar($datos['cuip']);
        $cuipLookup = $cuipService->generarLookup($cuip);

        if (Operador::where('cuip_lookup', $cuipLookup)->exists()) {
            throw ValidationException::withMessages([
                'cuip' =>
                'Ya existe una cuenta de personal con esta CUIP.',
            ]);
        }

        $email = strtolower(trim($datos['email']));

        $emailLookup = hash_hmac(
            'sha256',
            $email,
            config('app.key')
        );

        if (Operador::where('email_lookup', $emailLookup)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Ese correo electrónico ya está registrado.',
            ]);
        }

        try {
            $operador = Operador::create([
                'nombre' => trim($datos['nombre']),
                'curp' => $curp,
                'curp_lookup' => $curpLookup,
                'telefono' => $datos['telefono'],
                'email' => $email,
                'email_lookup' => $emailLookup,
                'email_verified_at' => null,
                'numero_empleado' => $datos['numero_empleado'],
                'numero_placa' => $datos['numero_placa'],
                'cuip' => $cuip,
                'cuip_lookup' => $cuipLookup,
                'adscripcion' => trim($datos['adscripcion']),
                'numero_patrulla' => $datos['numero_patrulla'],

                // Identificador interno generado automáticamente.
                'usuario' => 'policia_' . Str::uuid(),

                'password_hash' => Hash::make($datos['password']),
                'tipo_solicitud' => 'POLICIA',
                'rol' => 'POLICIA',
                'activo' => true,
                'disponible' => true,
            ]);
        } catch (UniqueConstraintViolationException $error) {
            return response()->json([
                'message' =>
                'La CURP, la CUIP o el número de empleado ya están registrados.',
            ], 422);
        }

        try {
            $operador->sendEmailVerificationNotification();
            $correoEnviado = true;
        } catch (\Throwable $error) {
            report($error);
            $correoEnviado = false;
        }

        return response()->json([
            'message' => $correoEnviado
                ? 'Cuenta registrada. Revisa tu correo para verificarla.'
                : 'Cuenta registrada, pero no pudimos enviar el correo. Solicita otro enlace desde el acceso policial.',
            'correo_enviado' => $correoEnviado,
            'personal' => [
                'id_operador' => $operador->id_operador,
                'nombre' => $operador->nombre,
                'rol' => $operador->rol,
                'activo' => $operador->activo,
            ],
        ], 201);
    }

    // Inicio de sesión del personal municipal.
    public function login(Request $request, CuipService $cuipService)
    {
        if ($request->has('cuip')) {
            $datos = $request->validate([
                'cuip' => 'required|string|max:50',
                'password' => 'required|string',
            ], [
                'cuip.required' => 'Escribe tu CUIP.',
                'cuip.max' => 'La CUIP no debe superar los 50 caracteres.',
                'password.required' => 'Escribe tu contraseña.',
            ]);

            $cuipLookup = $cuipService->generarLookup($datos['cuip']);

            $operador = Operador::where('cuip_lookup', $cuipLookup)
                ->where(function ($consulta) {
                    $consulta->where('rol', 'POLICIA')
                        ->orWhere(function ($pendiente) {
                            $pendiente->where('rol', 'PENDIENTE')
                                ->where('tipo_solicitud', 'POLICIA');
                        });
                })
                ->first();
        } else {
            $datos = $request->validate([
                'usuario' => 'required|string',
                'password' => 'required|string',
            ]);

            $usuario = strtolower(trim($datos['usuario']));

            $operador = Operador::where('usuario', $usuario)->first();
        }

        if (
            !$operador ||
            !Hash::check($datos['password'], $operador->password_hash)
        ) {
            return response()->json([
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        if (
            !$operador->activo ||
            $operador->rol === 'PENDIENTE'
        ) {
            return response()->json([
                'message' => 'La cuenta está pendiente de autorización.',
            ], 403);
        }

        if (
            $operador->rol === 'POLICIA' &&
            $operador->email !== null &&
            !$operador->hasVerifiedEmail()
        ) {
            return response()->json([
                'message' => 'Confirma tu correo electrónico antes de iniciar sesión.',
                'codigo' => 'CORREO_SIN_VERIFICAR',
            ], 403);
        }

        // Al iniciar sesión, se revocan los tokens policiales anteriores.
        if ($operador->rol === 'POLICIA') {
            $operador->tokens()->delete();
        }

        $token = $operador->createToken(
            'alerta-ixta-personal',
            [
                'personal',
                strtolower($operador->rol),
            ]
        )->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión correcto.',
            'token' => $token,
            'personal' => [
                'id_operador' => $operador->id_operador,
                'nombre' => $operador->nombre,
                'usuario' => $operador->usuario,
                'rol' => $operador->rol,
            ],
        ]);
    }

    public function reenviarVerificacionCorreoPolicia(
        Request $request,
        CuipService $cuipService
    ) {
        $datos = $request->validate([
            'cuip' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ]);

        $operador = Operador::where(
            'cuip_lookup',
            $cuipService->generarLookup($datos['cuip'])
        )->where('rol', 'POLICIA')->first();

        if (
            !$operador ||
            !Hash::check($datos['password'], $operador->password_hash)
        ) {
            return response()->json([
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        if (!$operador->activo) {
            return response()->json([
                'message' => 'La cuenta no se encuentra activa.',
            ], 403);
        }

        if (!$operador->email) {
            return response()->json([
                'message' => 'Esta cuenta policial no tiene un correo registrado.',
            ], 409);
        }

        if ($operador->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Tu correo ya está verificado. Puedes iniciar sesión.',
            ]);
        }

        $limite = 'reenviar-correo-policia:' . $operador->id_operador;

        if (RateLimiter::tooManyAttempts($limite, 1)) {
            return response()->json([
                'message' => 'Espera un minuto antes de solicitar otro correo.',
            ], 429);
        }

        try {
            $operador->sendEmailVerificationNotification();
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
}
