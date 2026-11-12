<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CurpService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

class RegistroController extends Controller
{

    // Mostrar formulario de registro.    
    public function create()
    {
        return view('registro');
    }


    // Guardar ciudadano.     
    public function store(Request $request, CurpService $curpService)
    {

        // Primero validamos los datos básicos.        
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

            'ine_foto' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
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

            'ine_foto.required' => 'Adjunta una foto del INE.',
            'ine_foto.image' => 'La foto del INE debe ser una imagen válida.',
            'ine_foto.mimes' => 'La foto del INE debe ser JPG o PNG.',
            'ine_foto.max' => 'La foto del INE debe pesar como máximo 2 MB.',
            'ine_foto.uploaded' => 'No se pudo cargar la foto. Selecciona un JPG o PNG de hasta 2 MB.',
        ]);


        //Normalizamos la CURP.
        $curp = $curpService->normalizar($request->curp);


        //Validamos estructura y dígito verificador.

        if (!$curpService->esValida($curp)) {
            return back()
                ->withErrors([
                    'curp' => 'La CURP ingresada no tiene una estructura válida.',
                ])
                ->withInput();
        }


        // Generamos el identificador seguro para búsquedas.

        $curpLookup = $curpService->generarLookup($curp);


        // Evitamos registrar dos veces la misma CURP.

        if (User::where('curp_lookup', $curpLookup)->exists()) {
            return back()
                ->withErrors([
                    'curp' => 'Ya existe una cuenta registrada con esta CURP.',
                ])
                ->withInput();
        }

        /*
         Crear usuario.
         
         User.php se encargará automáticamente de:
        
         curp     -> cifrarla
         name     -> cifrar
         email    -> cifrar
         telefono -> cifrar
         password -> hash
         */
        $rutaIne = $request->file('ine_foto')->store('ine', 'local');

        if ($rutaIne === false) {
            return back()
                ->withErrors(['ine_foto' => 'No se pudo guardar la foto del INE. Intenta nuevamente.'])
                ->withInput($request->only(['name', 'curp', 'telefono', 'email']));
        }

        try {
            User::create([
                'name' => trim($request->name),
                'curp' => $curp,
                'curp_lookup' => $curpLookup,
                'telefono' => $request->telefono,
                'email' => $request->email ?: null,
                'password' => $request->password,
                'ine_foto_path' => $rutaIne,
                'estatus' => 'ACTIVO',
                'estatus_verificacion' => 'PENDIENTE',
            ]);
        } catch (\Throwable $error) {
            Storage::disk('local')->delete($rutaIne);
            report($error);

            return back()
                ->withErrors(['ine_foto' => 'No se pudo completar el registro. Intenta nuevamente.'])
                ->withInput($request->only(['name', 'curp', 'telefono', 'email']));
        }

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Tu cuenta fue registrada correctamente. La verificación de identidad está pendiente.'
            );
    }
}
