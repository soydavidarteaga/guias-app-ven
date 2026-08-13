<?php

use App\Http\Controllers\ConductorController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\GuiaController;
use App\Http\Controllers\RubroController;
use App\Http\Controllers\VehiculoController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

// Rutas Públicas de Verificación de Guía por Código QR / Hash
Route::get('/guias/v/{hash}', [GuiaController::class, 'verificarPublico'])->where('hash', '.*')->name('guias.verificar');
Route::get('/verificar/{hash}', [GuiaController::class, 'verificarPublico'])->where('hash', '.*');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    // Guías SICA / SUNAGRO
    Route::get('/guias', [GuiaController::class, 'index'])->name('guias.index');
    Route::get('/guias/crear', [GuiaController::class, 'create'])->name('guias.create');
    Route::post('/guias', [GuiaController::class, 'store'])->name('guias.store');
    Route::get('/guias/{guia}', [GuiaController::class, 'show'])->name('guias.show')->where('guia', '^[0-9]{1,6}$');
    Route::get('/guias/{guia}/pdf', [GuiaController::class, 'descargarPdf'])->name('guias.pdf');

    // Catálogos CRUD
    Route::resource('empresas', EmpresaController::class)->except(['create', 'edit', 'show']);
    Route::resource('rubros', RubroController::class)->except(['create', 'edit', 'show']);
    Route::resource('conductores', ConductorController::class)->except(['create', 'edit', 'show']);
    Route::resource('vehiculos', VehiculoController::class)->except(['create', 'edit', 'show']);
});

// Captura de URLs públicas en /guias/{hash} (para hashes base64 o nro_guia de 9 dígitos)
Route::get('/guias/{hash}', [GuiaController::class, 'verificarPublico'])->where('hash', '.*');

require __DIR__.'/settings.php';
