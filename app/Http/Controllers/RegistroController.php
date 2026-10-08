<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CurpService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class RegistroController extends Controller
{
    /**
     * Mostrar formulario de registro.
     */
    public function create()
    {
        return view('registro');
    }

    /**
     * Guardar ciudadano.
     */
    public function store(Request $request, CurpService $curpService)
    {
        /*
         * Primero validamos los datos básicos.
         */
        $request->validate([
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
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers(),
            ],
        ], [
            'name.required' => 'El nombre es obligatorio.',

            'curp.required' => 'La CURP es obligatoria.',
            'curp.size' => 'La CURP debe contener 18 caracteres.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.regex' => 'El teléfono debe contener 10 números.',

            'email.email' => 'Ingresa un correo electrónico válido.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        /*
         * Normalizamos la CURP.
         */
        $curp = $curpService->normalizar($request->curp);

        /*
         * Validamos estructura y dígito verificador.
         */
        if (!$curpService->esValida($curp)) {
            return back()
                ->withErrors([
                    'curp' => 'La CURP ingresada no tiene una estructura válida.',
                ])
                ->withInput();
        }

        /*
         * Generamos el identificador seguro para búsquedas.
         */
        $curpLookup = $curpService->generarLookup($curp);

        /*
         * Evitamos registrar dos veces la misma CURP.
         */
        if (User::where('curp_lookup', $curpLookup)->exists()) {
            return back()
                ->withErrors([
                    'curp' => 'Ya existe una cuenta registrada con esta CURP.',
                ])
                ->withInput();
        }

        /*
         * Crear usuario.
         *
         * User.php se encargará automáticamente de:
         *
         * curp     -> cifrar
         * name     -> cifrar
         * email    -> cifrar
         * telefono -> cifrar
         * password -> hash
         */
        User::create([
            'name' => trim($request->name),
            'curp' => $curp,
            'curp_lookup' => $curpLookup,
            'telefono' => $request->telefono,
            'email' => $request->email ?: null,
            'password' => $request->password,

            'estatus' => 'ACTIVO',

            'estatus_verificacion' => 'PENDIENTE',
        ]);

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Tu cuenta fue registrada correctamente. La verificación de identidad está pendiente.'
            );
    }
}