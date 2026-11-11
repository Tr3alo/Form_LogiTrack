<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migración principal del dominio operativo y comercial de LogiTrack.
     * Define las tablas para:
     * 1. `cuadrillas`: Unidades móviles y coordenadas GPS en León, Nicaragua.
     * 2. `equipos`: Inventario de herramientas, materiales y movimientos de bodega.
     * 3. `rutas_envios`: Despachos activos y señales de destino por cuadrilla.
     * 4. `personal`: Directorio de encargados de bodega, choferes, técnicos y gerencia.
     * 5. `pagos_suscripcion`: Historial de facturación del modelo SaaS (Prueba 14 días / $60 USD al mes).
     */
    public function up(): void
    {
        // 1. Tabla de Cuadrillas y Rastreo GPS (León, Nicaragua)
        Schema::create('cuadrillas', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // Identificador para enfocar en el mapa (ej: cuadrilla-c)
            $table->string('nombre');
            $table->string('titulo_mapa');
            $table->string('vehiculo');
            $table->string('chofer');
            $table->string('jefe');
            $table->string('telefono')->nullable();
            $table->decimal('lat', 10, 6); // Latitud actual en León
            $table->decimal('lng', 10, 6); // Longitud actual en León
            $table->string('destino_nombre');
            $table->decimal('destino_lat', 10, 6); // Latitud de la señal de destino
            $table->decimal('destino_lng', 10, 6); // Longitud de la señal de destino
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
            $table->string('cuadrilla'); // Permite filtrar por Cuadrilla A, B, C, D, Encargo Técnico o Bodega Central
            $table->string('responsable');
            $table->string('encargo'); // Permite filtrar por proyecto o encargo asignado
            $table->string('ubicacion');
            $table->string('destino_movimiento')->nullable();
            $table->string('estado'); // Entregado, En Tránsito, Reporte Fallo, Operativo, En Bodega
            $table->string('estado_color')->default('blue');
            $table->string('ultima_actualizacion')->default('Hoy');
            $table->boolean('es_movimiento_bodega')->default(false); // Indica si se muestra en la tabla principal del Dashboard
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
            $table->string('senal_destino'); // Texto descriptivo de la baliza/señal de destino
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

        // 5. Tabla de Facturas / Historial de Suscripción SaaS ($60/mes y Prueba de 14 días)
        Schema::create('pagos_suscripcion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('referencia')->unique(); // ej: INV-2026-001
            $table->string('concepto'); // ej: Activación Prueba Gratis 14 Días / Mensualidad Plan Pro LogiTrack
            $table->string('plan'); // trial, pro, freemium
            $table->decimal('monto', 8, 2)->default(0.00); // $0.00 en prueba/freemium o $60.00 en Plan Pro
            $table->string('moneda', 10)->default('USD');
            $table->string('metodo_pago');
            $table->string('estado')->default('Pagado'); // Activo, Pagado, Pendiente
            $table->string('fecha_emision');
            $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones en orden inverso.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_suscripcion');
        Schema::dropIfExists('personal');
        Schema::dropIfExists('rutas_envios');
        Schema::dropIfExists('equipos');
        Schema::dropIfExists('cuadrillas');
    }
};
