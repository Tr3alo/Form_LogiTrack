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

test('free account restricts access to gps, rutas and personal and triggers bell renewal notification', function () {
    // 1. Initialize database and switch Carlos Martínez to Free account
    $this->get('/dashboard')->assertStatus(200);

    $this->post('/suscripcion', ['action' => 'switch_freemium'])
        ->assertRedirect();

    // 2. Dashboard and Inventario remain accessible and render the bell renewal warning
    $this->get('/dashboard')
        ->assertStatus(200)
        ->assertSee('Suscripción Pendiente de Renovación')
        ->assertSee('renovar tu suscripción a Premium ($60/mes)')
        ->assertSee('Rastreo GPS Bloqueado (Cuenta Free)');

    $this->get('/inventario')
        ->assertStatus(200)
        ->assertSee('Suscripción Pendiente de Renovación')
        ->assertSee('renovar tu suscripción a Premium ($60/mes)');

    // 3. Restricted modules redirect Free users to /suscripcion with restricted_error
    $this->get('/rastreo-gps')
        ->assertRedirect(route('suscripcion'))
        ->assertSessionHas('restricted_error');

    $this->get('/rutas-envios')
        ->assertRedirect(route('suscripcion'))
        ->assertSessionHas('restricted_error');

    $this->get('/personal')
        ->assertRedirect(route('suscripcion'))
        ->assertSessionHas('restricted_error');
});

test('inventario supports direct registration and 1-click transfer to Bodega Central', function () {
    $this->get('/inventario')->assertStatus(200);

    // 1. Register equipment directly to Bodega Central using the direct button
    $this->post('/inventario', [
        'codigo' => 'EQ-BOD-901',
        'nombre' => 'Generador Inverter Yamaha 3000W',
        'categoria' => 'Maquinaria y Energía',
        'destino_tipo' => 'bodega',
        'enviar_a_bodega' => '1',
        'cantidad' => '2 Unidades',
        'responsable' => 'Marvin Centeno (Bodega)',
    ])->assertRedirect(route('inventario', ['cuadrilla' => 'Bodega Central']));

    $this->assertDatabaseHas('equipos', [
        'codigo' => 'EQ-BOD-901',
        'nombre' => 'Generador Inverter Yamaha 3000W',
        'cuadrilla' => 'Bodega Central',
        'estado' => 'En Bodega',
    ]);

    // 2. Send an existing assigned equipment directly to Bodega Central in 1 click
    $equipoEnCampo = \App\Models\Equipo::where('cuadrilla', '!=', 'Bodega Central')->first();
    expect($equipoEnCampo)->not->toBeNull();

    $this->post('/inventario/enviar-bodega', [
        'codigo' => $equipoEnCampo->codigo,
    ])->assertRedirect(route('inventario', ['cuadrilla' => 'Bodega Central']));

    $this->assertDatabaseHas('equipos', [
        'id' => $equipoEnCampo->id,
        'cuadrilla' => 'Bodega Central',
        'estado' => 'En Bodega',
    ]);
});

