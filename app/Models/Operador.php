<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Operador extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'operadores';

    protected $primaryKey = 'id_operador';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'usuario',
        'password_hash',
        'rol',
        'activo',
        'disponible',
        'latitud_actual',
        'longitud_actual',
        'precision_m',
        'ubicacion_actualizada_at',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'disponible' => 'boolean',

            'latitud_actual' => 'encrypted',
            'longitud_actual' => 'encrypted',

            'precision_m' => 'decimal:2',
            'ubicacion_actualizada_at' => 'datetime',
        ];
    }
}
