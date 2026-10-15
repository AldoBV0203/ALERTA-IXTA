<?php

use App\Http\Controllers\RegistroController;
use Illuminate\Support\Facades\Route;

//inicio de sesio
Route::view('/', 'login')->name('login');

//registro del ciudadano
Route::get('/registro', [RegistroController::class, 'create'])
    ->name('registro');

Route::post('/registro', [RegistroController::class, 'store'])
    ->name('registro.store');

//demas pantallas
Route::view('/inicio', 'inicio')->name('inicio');

Route::view('/alerta-enviada', 'alerta-enviada')->name('alerta.enviada');

Route::view('/policia','policia')->name('policia');

Route::view(
    '/monitoreo',
    'monitoreo'
)->name('monitoreo');