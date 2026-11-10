<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AlertaController;
use App\Http\Controllers\Api\PersonalAuthController;
use App\Http\Controllers\Api\MonitoreoController;
use App\Http\Controllers\Api\PoliciaController;
use App\Http\Controllers\Api\SolicitudCancelacionController;


//Rutas públicas


Route::post('/registro', [AuthController::class, 'registro']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/personal/registro', [PersonalAuthController::class, 'registro']);

Route::post('/personal/login', [PersonalAuthController::class, 'login']);

//Rutas protegidas


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/usuario', [
        AuthController::class,
        'usuario'
    ]);

    Route::post('/logout', [
        AuthController::class,
        'logout'
    ]);

    Route::get('/tipos-alerta', [
        AlertaController::class,
        'tipos'
    ]);

    Route::post('/alertas', [
        AlertaController::class,
        'store'
    ]);

    Route::get('/mis-alertas', [
        AlertaController::class,
        'misAlertas'
    ]);

    Route::get('/monitoreo/solicitudes-cancelacion', [
        SolicitudCancelacionController::class,
        'pendientes'
    ]);

    Route::post('/monitoreo/solicitudes-cancelacion/{id}/generar-codigo', [
        SolicitudCancelacionController::class,
        'generarCodigo'
    ])
        ->whereNumber('id')
        ->middleware('throttle:5,1');

    Route::get('/monitoreo/alertas', [
        MonitoreoController::class,
        'alertas'
    ]);

    Route::get(
        '/monitoreo/policias',
        [MonitoreoController::class, 'policias']
    );

    Route::get('/monitoreo/alertas/{id}', [
        MonitoreoController::class,
        'detalle'
    ]);

    Route::post('/monitoreo/alertas/{id}/aceptar', [
        MonitoreoController::class,
        'aceptar'
    ]);

    Route::post(
        '/monitoreo/alertas/{id}/rechazar',
        [
            MonitoreoController::class,
            'rechazar'
        ]
    );

    Route::post('/monitoreo/alertas/{id}/seguimiento', [
        MonitoreoController::class,
        'seguimiento'
    ]);

    Route::post('/monitoreo/alertas/{id}/cerrar', [
        MonitoreoController::class,
        'cerrar'
    ]);

    Route::get('/monitoreo/alertas/{id}/seguimiento', [
        MonitoreoController::class,
        'historial'
    ]);

    Route::post(
        '/policia/ubicacion',
        [PoliciaController::class, 'actualizarUbicacion']
    );

    Route::get(
        '/mis-alertas/{id}/estado',
        [AlertaController::class, 'estado']
    );

    Route::post(
        '/mis-alertas/{id}/ubicacion',
        [AlertaController::class, 'actualizarUbicacion']
    );

    Route::post(
        '/mis-alertas/{id}/cancelar',
        [
            SolicitudCancelacionController::class,
            'solicitar'
        ]
    )->whereNumber('id');

    Route::get(
        '/policia/servicio-actual',
        [PoliciaController::class, 'servicioActual']
    );
});


// Generar códigos de invitación para el círculo de confianza.
Route::post(
    '/circulo-confianza/codigo',
    [
        \App\Http\Controllers\Api\CirculoConfianzaController::class,
        'generarCodigo'
    ]
)->middleware([
    'auth:sanctum',
    'throttle:5,1'
]);

// Enviar una solicitud de contacto mediante un código.
Route::post(
    '/circulo-confianza/vincular',
    [
        \App\Http\Controllers\Api\CirculoConfianzaController::class,
        'vincularContacto'
    ]
)->middleware([
    'auth:sanctum',
    'throttle:5,1'
]);




// Consultar los contactos de confianza confirmados.
Route::get(
    '/circulo-confianza/contactos',
    [
        \App\Http\Controllers\Api\CirculoConfianzaController::class,
        'misContactos'
    ]
)->middleware([
    'auth:sanctum',
    'throttle:30,1'
]);



// Eliminar un contacto de confianza confirmado.
Route::delete(
    '/circulo-confianza/contactos/{id}',
    [
        \App\Http\Controllers\Api\CirculoConfianzaController::class,
        'eliminarContacto'
    ]
)->middleware([
    'auth:sanctum',
    'throttle:10,1'
])->whereNumber('id');
