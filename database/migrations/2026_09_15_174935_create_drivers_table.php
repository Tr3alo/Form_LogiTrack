<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabla de Cuadrillas y Rastreo GPS (León, Nicaragua)
        Schema::create('cuadrillas', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // ej: cuadrilla-c, cuadrilla-a
            $table->string('nombre');
            $table->string('titulo_mapa');
            $table->string('vehiculo');
            $table->string('chofer');
            $table->string('jefe');
            $table->string('telefono')->nullable();
            $table->decimal('lat', 10, 6);
            $table->decimal('lng', 10, 6);
            $table->string('destino_nombre');
            $table->decimal('destino_lat', 10, 6);
            $table->decimal('destino_lng', 10, 6);
            $table->string('ubicacion_actual');
            $table->string('distancia');
            $table->string('velocidad');
            $table->string('eta')->nullable();
            $table->unsignedTinyInteger('progreso')->default(0);
            $table->string('estado');
            $table->string('color')->default('#2563eb');
            $table->string('badge_class')->default('bg-blue-100 text-blue-800 border-blue-200');
            $table->json('equipos_a_bordo')->nullable();
            $table->string('encargo')->nullable();
            $table->timestamps();
        });

        // 2. Tabla de Inventario de Equipos y Movimientos de Bodega
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->string('nombre_corto');
            $table->string('categoria');
            $table->string('serie')->nullable();
            $table->string('cantidad');
            $table->string('cuadrilla'); // Cuadrilla A, Cuadrilla B, Cuadrilla C, Cuadrilla D, Encargo Técnico, Bodega Central
            $table->string('responsable');
            $table->string('encargo');
            $table->string('ubicacion');
            $table->string('destino_movimiento')->nullable();
            $table->string('estado'); // Entregado, En Tránsito, Reporte Fallo, Operativo, En Bodega
            $table->string('estado_color')->default('blue'); // green, yellow, red, blue, slate
            $table->string('ultima_actualizacion')->default('Hoy');
            $table->boolean('es_movimiento_bodega')->default(false);
            $table->timestamps();
        });

        // 3. Tabla de Rutas y Envíos con Señal de Destino
        Schema::create('rutas_envios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('cuadrilla');
            $table->string('responsable');
            $table->string('chofer');
            $table->string('vehiculo');
            $table->string('origen');
            $table->string('destino');
            $table->string('direccion_destino');
            $table->string('coordenadas_destino');
            $table->string('senal_destino');
            $table->string('prioridad')->default('Normal');
            $table->string('hora_salida');
            $table->string('hora_estimada');
            $table->unsignedTinyInteger('progreso')->default(0);
            $table->string('estado');
            $table->string('estado_color')->default('yellow');
            $table->json('items')->nullable();
            $table->string('focus_id')->default('cuadrilla-c');
            $table->timestamps();
        });

        // 4. Tabla de Personal (Encargados de Bodega, Choferes, Técnicos, Administración)
        Schema::create('personal', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('iniciales', 5);
            $table->string('nombre');
            $table->string('cargo');
            $table->string('categoria_rol'); // Encargado de Bodega, Chofer, Técnico / Encargado, Administración
            $table->string('departamento');
            $table->string('asignacion');
            $table->string('telefono');
            $table->string('email')->nullable();
            $table->string('estado');
            $table->string('estado_color')->default('green');
            $table->string('avatar_bg')->default('bg-blue-600');
            $table->string('experiencia')->default('3 años');
            $table->string('equipos_a_cargo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal');
        Schema::dropIfExists('rutas_envios');
        Schema::dropIfExists('equipos');
        Schema::dropIfExists('cuadrillas');
    }
};
