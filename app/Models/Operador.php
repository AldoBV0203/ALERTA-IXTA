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
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }
}