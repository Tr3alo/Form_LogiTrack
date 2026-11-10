<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    protected $table = 'personal';

    protected $fillable = [
        'codigo',
        'iniciales',
        'nombre',
        'cargo',
        'categoria_rol',
        'departamento',
        'asignacion',
        'telefono',
        'email',
        'estado',
        'estado_color',
        'avatar_bg',
        'experiencia',
        'equipos_a_cargo',
    ];
}
