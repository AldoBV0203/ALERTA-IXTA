<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AlertaController;
use App\Http\Controllers\Api\PersonalAuthController;
use App\Http\Controllers\Api\MonitoreoController;



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

    Route::get('/monitoreo/alertas', [
        MonitoreoController::class,
        'alertas'
    ]);

    Route::get('/monitoreo/alertas/{id}', [
        MonitoreoController::class,
        'detalle'
    ]);

    Route::post('/monitoreo/alertas/{id}/aceptar', [
        MonitoreoController::class,
        'aceptar'
    ]);

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
});
