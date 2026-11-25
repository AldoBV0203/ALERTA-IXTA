<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;

class Operador extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    protected $table = 'operadores';

    protected $primaryKey = 'id_operador';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'curp',
        'curp_lookup',
        'telefono',
        'email',
        'email_lookup',
        'email_verified_at',
        'numero_empleado',
        'numero_placa',
        'cuip',
        'cuip_lookup',
        'grado',
        'adscripcion',
        'numero_patrulla',
        'tipo_solicitud',
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
        'curp',
        'curp_lookup',
        'telefono',
        'email',
        'email_lookup',
        'numero_empleado',
        'numero_placa',
        'cuip',
        'cuip_lookup',
        'grado',
        'adscripcion',
        'numero_patrulla',
        'tipo_solicitud',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'disponible' => 'boolean',
            'curp' => 'encrypted',
            'telefono' => 'encrypted',
            'email' => 'encrypted',
            'email_verified_at' => 'datetime',
            'cuip' => 'encrypted',

            'latitud_actual' => 'encrypted',
            'longitud_actual' => 'encrypted',

            'precision_m' => 'decimal:2',
            'ubicacion_actualizada_at' => 'datetime',
        ];
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerificarCorreoPolicia());
    }
}
