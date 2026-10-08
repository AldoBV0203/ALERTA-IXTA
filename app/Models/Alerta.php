<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    protected $table = 'alertas';

    protected $primaryKey = 'id_alerta';

    public $timestamps = false;

    protected $fillable = [
        'folio',
        'id_usuario',
        'id_tipo',
        'fecha_hora',
        'latitud',
        'longitud',
        'prioridad',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'latitud' => 'encrypted',
            'longitud' => 'encrypted',
            'fecha_hora' => 'datetime',
        ];
    }

    public function tipo()
{
    return $this->belongsTo(
        TipoAlerta::class,
        'id_tipo',
        'id_tipo'
    );
}
}