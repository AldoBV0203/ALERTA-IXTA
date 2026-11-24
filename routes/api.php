<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AlertaController;
use App\Http\Controllers\Api\PersonalAuthController;
use App\Http\Controllers\Api\MonitoreoController;
use App\Http\Controllers\Api\PoliciaController;
use App\Http\Controllers\Api\SolicitudCancelacionController;
use App\Http\Controllers\Api\VerificacionIdentidadController;
use App\Http\Controllers\Api\CiudadanoController;
use App\Http\Controllers\Api\PoliciaMonitoreoController;


//Rutas públicas


Route::post('/registro', [AuthController::class, 'registro']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/personal/registro', [PersonalAuthController::class, 'registro']);
Route::post('/personal/registro-policia', [
    PersonalAuthController::class,
    'registroPolicia',
])->middleware('throttle:5,1,registro-policia:');

Route::post('/personal/login', [PersonalAuthController::class, 'login']);

//Rutas protegidas


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/usuario', [
        AuthController::class,
        'usuario'
    ]);

    Route::post('/usuario/ine/reenviar', [
        AuthController::class,
        'reenviarIne',
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

    Route::get('/monitoreo/ciudadanos/resumen', [
        CiudadanoController::class,
        'resumen',
    ])->middleware('throttle:30,1,ciudadanos-resumen:');

    Route::get('/monitoreo/ciudadanos', [
        CiudadanoController::class,
        'index',
    ])->middleware('throttle:120,1,ciudadanos-listado:');

    Route::get('/monitoreo/ciudadanos/{id}', [
        CiudadanoController::class,
        'detalle',
    ])
        ->whereNumber('id')
        ->middleware('throttle:60,1,ciudadanos-detalle:');

    Route::patch('/monitoreo/ciudadanos/{id}', [
        CiudadanoController::class,
        'actualizar',
    ])
        ->whereNumber('id')
        ->middleware('throttle:20,1,ciudadanos-actualizar:');

    Route::get('/monitoreo/verificaciones-identidad', [
        VerificacionIdentidadController::class,
        'pendientes',
    ]);

    Route::get('/monitoreo/verificaciones-identidad/{id}/foto', [
        VerificacionIdentidadController::class,
        'foto',
    ])->whereNumber('id');

    Route::post('/monitoreo/verificaciones-identidad/{id}/resolver', [
        VerificacionIdentidadController::class,
        'resolver',
    ])->whereNumber('id');

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

    Route::post('/monitoreo/solicitudes-cancelacion/{id}/rechazar', [
        SolicitudCancelacionController::class,
        'rechazar'
    ])->whereNumber('id');

    Route::get(
        '/monitoreo/policias',
        [MonitoreoController::class, 'policias']
    );

    Route::get('/monitoreo/policias/registrados', [
        PoliciaMonitoreoController::class,
        'index',
    ])->middleware('throttle:120,1,policias-listado:');

    Route::get('/monitoreo/policias/registrados/{id}', [
        PoliciaMonitoreoController::class,
        'detalle',
    ])
        ->whereNumber('id')
        ->middleware('throttle:60,1,policias-detalle:');

    Route::get('/monitoreo/policias/registrados/{id}/servicios', [
        PoliciaMonitoreoController::class,
        'servicios',
    ])
        ->whereNumber('id')
        ->middleware('throttle:60,1,policias-servicios:');

    Route::patch('/monitoreo/policias/registrados/{id}', [
        PoliciaMonitoreoController::class,
        'actualizarDatosLaborales',
    ])
        ->whereNumber('id')
        ->middleware('throttle:20,1,policias-actualizar:');

    Route::patch('/monitoreo/policias/registrados/{id}/estado', [
        PoliciaMonitoreoController::class,
        'cambiarEstadoCuenta',
    ])
        ->whereNumber('id')
        ->middleware('throttle:10,1,policias-estado:');

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

    Route::get('/monitoreo/alertas/{id}/evidencia', [
        MonitoreoController::class,
        'verEvidenciaPolicial',
    ])->whereNumber('id');

    Route::post('/monitoreo/alertas/{id}/evidencia', [
        MonitoreoController::class,
        'guardarEvidenciaPolicial',
    ])->whereNumber('id');

    Route::post('/monitoreo/alertas/{id}/cerrar', [
        MonitoreoController::class,
        'cerrar',
    ])
        ->whereNumber('id')
        ->middleware('throttle:10,1');

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

    Route::post('/mis-alertas/{id}/qr-atencion', [
        AlertaController::class,
        'generarQrAtencion',
    ])
        ->whereNumber('id')
        ->middleware('throttle:5,1');



    Route::post('/mis-alertas/{id}/confirmar-cancelacion', [
        SolicitudCancelacionController::class,
        'confirmarCancelacion'
    ])
        ->whereNumber('id')
        ->middleware('throttle:10,1,confirmar-cancelacion');

    Route::get(
        '/policia/servicio-actual',
        [PoliciaController::class, 'servicioActual']
    );
});


// Grupos: invitaciones y contactos.
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/circulo-confianza/codigo', [
        \App\Http\Controllers\Api\CirculoConfianzaController::class,
        'generarCodigo',
    ])->middleware('throttle:5,1,grupos-codigo:');

    Route::post('/circulo-confianza/vincular', [
        \App\Http\Controllers\Api\CirculoConfianzaController::class,
        'vincularContacto',
    ])->middleware('throttle:5,1,grupos-vincular:');

    Route::get('/circulo-confianza/contactos', [
        \App\Http\Controllers\Api\CirculoConfianzaController::class,
        'misContactos',
    ])->middleware('throttle:30,1,grupos-listar:');

    Route::delete('/circulo-confianza/contactos/{id}', [
        \App\Http\Controllers\Api\CirculoConfianzaController::class,
        'eliminarContacto',
    ])
        ->whereNumber('id')
        ->middleware('throttle:10,1,grupos-eliminar:');

    Route::post('/circulo-vecinos/codigo', [
        \App\Http\Controllers\Api\CirculoVecinosController::class,
        'generarCodigo',
    ])->middleware('throttle:5,1,grupos-codigo:');

    Route::post('/circulo-vecinos/vincular', [
        \App\Http\Controllers\Api\CirculoVecinosController::class,
        'vincularContacto',
    ])->middleware('throttle:5,1,grupos-vincular:');

    Route::get('/circulo-vecinos/contactos', [
        \App\Http\Controllers\Api\CirculoVecinosController::class,
        'misContactos',
    ])->middleware('throttle:30,1,grupos-listar:');

    Route::delete('/circulo-vecinos/contactos/{id}', [
        \App\Http\Controllers\Api\CirculoVecinosController::class,
        'eliminarContacto',
    ])
        ->whereNumber('id')
        ->middleware('throttle:10,1,grupos-eliminar:');
});
