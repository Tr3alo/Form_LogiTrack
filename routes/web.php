<?php

use App\Http\Controllers\LogiTrackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Web de LogiTrack SaaS (León, Nicaragua)
|--------------------------------------------------------------------------
| 1. Landing Page comercial (`/` y `/landing`): Presenta la plataforma SaaS,
|    sus módulos de logística (Inventario + Rastreo GPS) y el modelo Freemium
|    con prueba gratuita de 14 días seguida de $60 USD/mes.
| 2. Autenticación (`/login`, `/register`, `/logout`): Registro e inicio de sesión
|    conectados a la tabla `users` en MySQL (`logitrack_demostration`).
| 3. Panel Operativo (`/dashboard`, `/inventario`, `/rastreo-gps`, `/rutas-envios`,
|    `/personal`, `/suscripcion`): Vistas del sistema y administración de suscripción.
*/

// Landing Page Comercial del SaaS
Route::get('/', [LogiTrackController::class, 'landing'])->name('landing');
Route::get('/landing', [LogiTrackController::class, 'landing']);

// Autenticación (Login, Registro con Prueba de 14 Días y Cierre de Sesión)
Route::get('/login', [LogiTrackController::class, 'showLogin'])->name('login');
Route::post('/login', [LogiTrackController::class, 'login'])->name('login.submit');
Route::get('/register', [LogiTrackController::class, 'showRegister'])->name('register');
Route::post('/register', [LogiTrackController::class, 'register'])->name('register.submit');
Route::post('/logout', [LogiTrackController::class, 'logout'])->name('logout');

// Panel Operativo (Dashboard y Módulos Logísticos)
Route::get('/dashboard', [LogiTrackController::class, 'dashboard'])->name('dashboard');
Route::get('/inventario', [LogiTrackController::class, 'inventario'])->name('inventario');
Route::post('/inventario', [LogiTrackController::class, 'storeEquipo'])->name('inventario.store');
Route::get('/rastreo-gps', [LogiTrackController::class, 'rastreo'])->name('rastreo');
Route::get('/rutas-envios', [LogiTrackController::class, 'rutas'])->name('rutas');
Route::get('/personal', [LogiTrackController::class, 'personal'])->name('personal');

// Administración de Suscripción SaaS (Carlos Martínez - Prueba 14 días / $60 al mes)
Route::get('/suscripcion', [LogiTrackController::class, 'suscripcion'])->name('suscripcion');
Route::post('/suscripcion', [LogiTrackController::class, 'updateSuscripcion'])->name('suscripcion.update');
