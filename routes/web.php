<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'login')->name('login');

Route::view('/inicio', 'inicio')->name('inicio');

Route::view('/alerta-enviada', 'alerta-enviada')->name('alerta.enviada');