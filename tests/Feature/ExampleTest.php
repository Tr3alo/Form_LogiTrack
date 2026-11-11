<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('logitrack saas landing page, login and register load successfully', function () {
    $this->get('/')
        ->assertStatus(200)
        ->assertSee('LogiTrack')
        ->assertSee('14 días')
        ->assertSee('$60');

    $this->get('/login')
        ->assertStatus(200)
        ->assertSee('Iniciar Sesión')
        ->assertSee('Carlos Martínez');

    $this->get('/register')
        ->assertStatus(200)
        ->assertSee('Registrar Empresa en LogiTrack')
        ->assertSee('Prueba Gratis de 14 Días');
});

test('logitrack dashboard loads successfully with database data', function () {
    $response = $this->get('/dashboard');

    $response->assertStatus(200)
        ->assertSee('LogiTrack')
        ->assertSee('Resumen Operativo')
        ->assertSee('Equipos Asignados')
        ->assertSee('124')
        ->assertSee('Multímetro Digital Fluke')
        ->assertSee('Cuadrilla C - León Centro');
});

test('logitrack inventario, rastreo gps, rutas, personal y suscripcion load from database', function () {
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

    $this->get('/suscripcion')
        ->assertStatus(200)
        ->assertSee('Suscripción y Facturación SaaS')
        ->assertSee('Carlos Martínez')
        ->assertSee('60.00');
});
