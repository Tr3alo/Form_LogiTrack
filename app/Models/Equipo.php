<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $table = 'equipos';

    protected $fillable = [
        'codigo',
        'nombre',
        'nombre_corto',
        'categoria',
        'serie',
        'cantidad',
        'cuadrilla',
        'responsable',
        'encargo',
        'ubicacion',
        'destino_movimiento',
        'estado',
        'estado_color',
        'ultima_actualizacion',
        'es_movimiento_bodega',
    ];

    protected $casts = [
        'es_movimiento_bodega' => 'boolean',
    ];
}
