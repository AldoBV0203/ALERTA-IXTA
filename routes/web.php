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

Route::view('/policia', 'policia')->name('policia');
Route::view('/registro-policial', 'registro-policia')->name('registro.policia');

Route::view(
    '/monitoreo',
    'monitoreo.index'
)->name('monitoreo');

Route::get('/verificar-correo/{id}/{hash}', function (int $id, string $hash) {
    $ciudadano = \App\Models\User::findOrFail($id);

    abort_unless(
        hash_equals(sha1($ciudadano->getEmailForVerification()), $hash),
        403
    );

    if ($ciudadano->markEmailAsVerified()) {
        event(new \Illuminate\Auth\Events\Verified($ciudadano));
    }

    return redirect()
        ->route('login')
        ->with('success', 'Tu correo electrónico quedó verificado.');
})->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
