<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Campos que Laravel puede guardar mediante asignación masiva.
     */
    protected $fillable = [
        'curp',
        'curp_lookup',
        'name',
        'email',
        'telefono',
        'password',
        'estatus',
        'estatus_verificacion',
        'telefono_verificado_at',
        'identidad_verificada_at',
    ];

    /**
     * Campos que no deben aparecer cuando el usuario
     * se convierta a JSON o se envíe mediante la API.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'curp_lookup',
    ];

    /**
     * Conversión y protección automática de atributos.
     */
    protected function casts(): array
    {
        return [
            'curp' => 'encrypted',
            'name' => 'encrypted',
            'email' => 'encrypted',
            'telefono' => 'encrypted',

            'password' => 'hashed',

            'email_verified_at' => 'datetime',
            'telefono_verificado_at' => 'datetime',
            'identidad_verificada_at' => 'datetime',
        ];
    }
}

