<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Operador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PersonalAuthController extends Controller
{
    /*
      Registrar una cuenta de personal municipal.    
      La cuenta queda pendiente y sin acceso hasta que sea autorizada.      
     */
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
            'nombre.required' =>
                'El nombre es obligatorio.',

            'usuario.required' =>
                'El usuario es obligatorio.',

            'usuario.unique' =>
                'Ese nombre de usuario ya está registrado.',

            'usuario.regex' =>
                'El usuario solo puede contener letras, números, punto, guion y guion bajo.',

            'password.required' =>
                'La contraseña es obligatoria.',

            'password.confirmed' =>
                'Las contraseñas no coinciden.',
        ]);


        $operador = Operador::create([
            'nombre' => trim($datos['nombre']),

            'usuario' => strtolower(
                trim($datos['usuario'])
            ),

            'password_hash' => Hash::make(
                $datos['password']
            ),
            
            // No dejamos que el usuario se asigne su propio rol.
             
            'rol' => 'PENDIENTE',
            
             //No puede acceder al sistema hasta ser autorizado.
             
            'activo' => false,
        ]);


        return response()->json([
            'message' =>
                'Registro realizado. La cuenta está pendiente de autorización.',

            'personal' => [
                'id_operador' =>
                    $operador->id_operador,

                'nombre' =>
                    $operador->nombre,

                'usuario' =>
                    $operador->usuario,

                'rol' =>
                    $operador->rol,

                'activo' =>
                    $operador->activo,
            ],
        ], 201);
    }


    // Inicio de sesión del personal municipal.
    
    public function login(Request $request)
    {
        $datos = $request->validate([
            'usuario' => [
                'required',
                'string',
            ],

            'password' => [
                'required',
                'string',
            ],
        ]);


        $usuario = strtolower(
            trim($datos['usuario'])
        );


        $operador = Operador::where(
            'usuario',
            $usuario
        )->first();


        // Mensaje genérico para no revelar si un usuario existe o no.
       
        if (
            !$operador ||
            !Hash::check(
                $datos['password'],
                $operador->password_hash
            )
        ) {
            return response()->json([
                'message' =>
                    'Credenciales incorrectas.',
            ], 401);
        }


        // Una cuenta pendiente NO puede entrar.
        
        if (
            !$operador->activo ||
            $operador->rol === 'PENDIENTE'
        ) {
            return response()->json([
                'message' =>
                    'La cuenta está pendiente de autorización.',
            ], 403);
        }
        
        
        // Generar token solamente para personal autorizado.
        
        $token = $operador
            ->createToken(
                'alerta-ixta-personal',
                [
                    'personal',
                    strtolower($operador->rol)
                ]
            )
            ->plainTextToken;


        return response()->json([
            'message' =>
                'Inicio de sesión correcto.',

            'token' =>
                $token,

            'personal' => [
                'id_operador' =>
                    $operador->id_operador,

                'nombre' =>
                    $operador->nombre,

                'usuario' =>
                    $operador->usuario,

                'rol' =>
                    $operador->rol,
            ],
        ]);
    }
}