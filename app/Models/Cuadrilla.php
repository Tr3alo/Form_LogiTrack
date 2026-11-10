<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuadrilla extends Model
{
    protected $table = 'cuadrillas';

    protected $fillable = [
        'slug',
        'nombre',
        'titulo_mapa',
        'vehiculo',
        'chofer',
        'jefe',
        'telefono',
        'lat',
        'lng',
        'destino_nombre',
        'destino_lat',
        'destino_lng',
        'ubicacion_actual',
        'distancia',
        'velocidad',
        'eta',
        'progreso',
        'estado',
        'color',
        'badge_class',
        'equipos_a_bordo',
        'encargo',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'destino_lat' => 'float',
        'destino_lng' => 'float',
        'progreso' => 'integer',
        'equipos_a_bordo' => 'array',
    ];
}
