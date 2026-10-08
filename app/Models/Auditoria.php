<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditoria';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'usuario_sistema',
        'accion',
        'entidad',
        'id_entidad',
        'fecha_hora',
        'ip',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
        ];
    }
}