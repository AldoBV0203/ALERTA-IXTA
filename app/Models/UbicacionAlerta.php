<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UbicacionAlerta extends Model
{
    protected $table = 'ubicaciones_alerta';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_alerta',
        'latitud',
        'longitud',
        'precision_m',
        'fecha_hora',
    ];

    protected function casts(): array
    {
        return [
            'latitud' => 'encrypted',
            'longitud' => 'encrypted',
            'precision_m' => 'decimal:2',
            'fecha_hora' => 'datetime',
        ];
    }
}