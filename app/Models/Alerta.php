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

    public function confirmacionAtencionCiudadano()
    {
        return $this->hasOne(SeguimientoAlerta::class, 'id_alerta', 'id_alerta')
            ->where('accion', 'ATENCION_CONFIRMADA_CIUDADANO');
    }

    public function cerrarSiAtencionConfirmada(): void
    {
        if ($this->estado !== 'PENDIENTE_CONFIRMACION') {
            return;
        }

        $registros = SeguimientoAlerta::where('id_alerta', $this->id_alerta)
            ->whereIn('accion', [
                'ATENCION_FINALIZADA_POLICIA',
                'ATENCION_CONFIRMADA_CIUDADANO',
            ])
            ->get()
            ->keyBy('accion');

        $finalizacion = $registros->get('ATENCION_FINALIZADA_POLICIA');

        if (!$finalizacion || !$registros->has('ATENCION_CONFIRMADA_CIUDADANO')) {
            return;
        }

        $this->estado = 'CERRADA';
        $this->save();

        SeguimientoAlerta::create([
            'id_alerta' => $this->id_alerta,
            'id_operador' => $finalizacion->id_operador,
            'accion' => 'CIERRE_ALERTA',
            'observaciones' => 'El policía finalizó la atención y el ciudadano confirmó que la recibió.',
            'fecha_hora' => now(),
        ]);
    }
}
