<?php

use App\Http\Controllers\CanchaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TarifaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Canchas: todos los autenticados pueden ver, solo admin/recepcionista pueden modificar
Route::middleware('auth')->group(function () {
    Route::get('/canchas', [CanchaController::class, 'index'])->name('canchas.index');
    Route::get('/canchas/create', [CanchaController::class, 'create'])
        ->middleware('role:admin,recepcionista')->name('canchas.create');
    Route::post('/canchas', [CanchaController::class, 'store'])
        ->middleware('role:admin,recepcionista')->name('canchas.store');
    Route::get('/canchas/{cancha}/edit', [CanchaController::class, 'edit'])
        ->middleware('role:admin,recepcionista')->name('canchas.edit');
    Route::patch('/canchas/{cancha}', [CanchaController::class, 'update'])
        ->middleware('role:admin,recepcionista')->name('canchas.update');
    Route::delete('/canchas/{cancha}', [CanchaController::class, 'destroy'])
        ->middleware('role:admin')->name('canchas.destroy');
});

// Tarifas: todos los autenticados pueden ver, solo admin/recepcionista pueden modificar
Route::middleware('auth')->group(function () {
    Route::get('/tarifas', [TarifaController::class, 'index'])->name('tarifas.index');
    Route::get('/tarifas/create', [TarifaController::class, 'create'])
        ->middleware('role:admin,recepcionista')->name('tarifas.create');
    Route::post('/tarifas', [TarifaController::class, 'store'])
        ->middleware('role:admin,recepcionista')->name('tarifas.store');
    Route::get('/tarifas/{tarifa}/edit', [TarifaController::class, 'edit'])
        ->middleware('role:admin,recepcionista')->name('tarifas.edit');
    Route::patch('/tarifas/{tarifa}', [TarifaController::class, 'update'])
        ->middleware('role:admin,recepcionista')->name('tarifas.update');
    Route::delete('/tarifas/{tarifa}', [TarifaController::class, 'destroy'])
        ->middleware('role:admin')->name('tarifas.destroy');
});

require __DIR__.'/auth.php';
