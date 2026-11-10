<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('logitrack dashboard loads successfully with database data', function () {
    $response = $this->get('/');

    $response->assertStatus(200)
        ->assertSee('LogiTrack')
        ->assertSee('Resumen Operativo')
        ->assertSee('Equipos Asignados')
        ->assertSee('124')
        ->assertSee('Multímetro Digital Fluke')
        ->assertSee('Cuadrilla C - León Centro');
});

test('logitrack inventario, rastreo gps, rutas y personal load from database', function () {
    $this->get('/inventario')
        ->assertStatus(200)
        ->assertSee('Inventario de Equipos y Recursos')
        ->assertSee('Cuadrilla A')
        ->assertSee('Cuadrilla C');

    $this->get('/rastreo-gps')
        ->assertStatus(200)
        ->assertSee('Rastreo GPS en Tiempo Real')
        ->assertSee('León, Nicaragua');

    $this->get('/rutas-envios')
        ->assertStatus(200)
        ->assertSee('Rutas y Envíos Activos')
        ->assertSee('Señal de Destino Asignado')
        ->assertSee('Residencial Las Mercedes');

    $this->get('/personal')
        ->assertStatus(200)
        ->assertSee('Personal y Cuadrillas')
        ->assertSee('Encargado de Bodega')
        ->assertSee('Chofer');
});
