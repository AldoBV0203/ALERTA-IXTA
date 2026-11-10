<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudCancelacion extends Model
{
    protected $table = 'solicitudes_cancelacion';

    protected $fillable = [
        'id_alerta',
        'id_usuario',
        'id_operador',
        'motivo',
        'estado',
        'codigo_hash',
        'codigo_expira_at',
        'intentos',
        'contacto_verificado_at',
        'observaciones_operador',
        'resuelta_at',
    ];

    // Evitar que el hash del código aparezca al convertir el modelo a JSON.
    protected $hidden = [
        'codigo_hash',
    ];

    protected function casts(): array
    {
        return [
            'intentos' => 'integer',
            'codigo_expira_at' => 'datetime',
            'contacto_verificado_at' => 'datetime',
            'resuelta_at' => 'datetime',
        ];
    }

    // Alerta a la que pertenece la solicitud.
    public function alerta()
    {
        return $this->belongsTo(
            Alerta::class,
            'id_alerta',
            'id_alerta'
        );
    }

    // Ciudadano que solicitó la cancelación.
    public function ciudadano()
    {
        return $this->belongsTo(
            User::class,
            'id_usuario',
            'id'
        );
    }

    // Operador que gestiona la verificación.
    public function operador()
    {
        return $this->belongsTo(
            Operador::class,
            'id_operador',
            'id_operador'
        );
    }
}