<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeguimientoAlerta extends Model
{
    protected $table = 'seguimiento_alerta';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_alerta',
        'id_operador',
        'accion',
        'observaciones',
        'fecha_hora',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
        ];
    }

    public function alerta()
    {
        return $this->belongsTo(
            Alerta::class,
            'id_alerta',
            'id_alerta'
        );
    }

    public function operador()
    {
        return $this->belongsTo(
            Operador::class,
            'id_operador',
            'id_operador'
        );
    }
}