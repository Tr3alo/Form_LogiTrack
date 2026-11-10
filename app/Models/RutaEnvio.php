<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutaEnvio extends Model
{
    protected $table = 'rutas_envios';

    protected $fillable = [
        'codigo',
        'cuadrilla',
        'responsable',
        'chofer',
        'vehiculo',
        'origen',
        'destino',
        'direccion_destino',
        'coordenadas_destino',
        'senal_destino',
        'prioridad',
        'hora_salida',
        'hora_estimada',
        'progreso',
        'estado',
        'estado_color',
        'items',
        'focus_id',
    ];

    protected $casts = [
        'progreso' => 'integer',
        'items' => 'array',
    ];
}
