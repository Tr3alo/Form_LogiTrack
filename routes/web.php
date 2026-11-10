<?php

use App\Http\Controllers\LogiTrackController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LogiTrackController::class, 'dashboard'])->name('dashboard');
Route::get('/dashboard', [LogiTrackController::class, 'dashboard']);
Route::get('/inventario', [LogiTrackController::class, 'inventario'])->name('inventario');
Route::post('/inventario', [LogiTrackController::class, 'storeEquipo'])->name('inventario.store');
Route::get('/rastreo-gps', [LogiTrackController::class, 'rastreo'])->name('rastreo');
Route::get('/rutas-envios', [LogiTrackController::class, 'rutas'])->name('rutas');
Route::get('/personal', [LogiTrackController::class, 'personal'])->name('personal');
