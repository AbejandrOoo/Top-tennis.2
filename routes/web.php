<?php

use App\Http\Controllers\CanchaController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\TarifaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user    = auth()->user();
    $esStaff = in_array($user->rol, [\App\Enums\Rol::Admin, \App\Enums\Rol::Recepcionista]);

    if ($esStaff) {
        $stats = [
            'canchas'           => \App\Models\Cancha::count(),
            'canchas_operativas'=> \App\Models\Cancha::where('estado_mantenimiento', 'operativa')->count(),
            'tarifas'           => \App\Models\Tarifa::count(),
            'horarios_disp'     => \App\Models\Horario::where('estado', 'disponible')->count(),
            'reservas'          => \App\Models\Reserva::count(),
            // Suma monto_pagado (precio congelado al momento de reservar),
            // nunca tarifa->precio actual que puede haber cambiado.
            'ingresos'          => (float) \App\Models\Reserva::where('estado_pago', 'aprobado')->sum('monto_pagado'),
        ];

        return view('dashboard', compact('esStaff', 'stats'));
    }

    $stats = [
        'disponibles' => \App\Models\Horario::reservables()->count(),
        'mis_reservas'=> $user->reservas()->count(),
    ];

    return view('dashboard', compact('esStaff', 'stats'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ===== CRUDs exclusivos del Admin =====
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Canchas
    Route::get('/canchas',                 [CanchaController::class, 'index'])->name('canchas.index');
    Route::get('/canchas/create',          [CanchaController::class, 'create'])->name('canchas.create');
    Route::post('/canchas',                [CanchaController::class, 'store'])->name('canchas.store');
    Route::get('/canchas/{cancha}/edit',   [CanchaController::class, 'edit'])->name('canchas.edit');
    Route::patch('/canchas/{cancha}',      [CanchaController::class, 'update'])->name('canchas.update');
    Route::delete('/canchas/{cancha}',     [CanchaController::class, 'destroy'])->name('canchas.destroy');

    // Tarifas
    Route::get('/tarifas',                 [TarifaController::class, 'index'])->name('tarifas.index');
    Route::get('/tarifas/create',          [TarifaController::class, 'create'])->name('tarifas.create');
    Route::post('/tarifas',                [TarifaController::class, 'store'])->name('tarifas.store');
    Route::get('/tarifas/{tarifa}/edit',   [TarifaController::class, 'edit'])->name('tarifas.edit');
    Route::patch('/tarifas/{tarifa}',      [TarifaController::class, 'update'])->name('tarifas.update');
    Route::delete('/tarifas/{tarifa}',     [TarifaController::class, 'destroy'])->name('tarifas.destroy');

    // Horarios (slots)
    Route::get('/horarios',                [HorarioController::class, 'index'])->name('horarios.index');
    Route::get('/horarios/create',         [HorarioController::class, 'create'])->name('horarios.create');
    Route::post('/horarios',               [HorarioController::class, 'store'])->name('horarios.store');
    Route::get('/horarios/{horario}/edit', [HorarioController::class, 'edit'])->name('horarios.edit');
    Route::patch('/horarios/{horario}',    [HorarioController::class, 'update'])->name('horarios.update');
    Route::delete('/horarios/{horario}',   [HorarioController::class, 'destroy'])->name('horarios.destroy');
});

// ===== Reservas (flujo cliente + ticket) =====
Route::middleware('auth')->group(function () {
    Route::get('/reservar',                       [ReservaController::class, 'disponibles'])->name('reservas.disponibles');
    Route::get('/reservas',                        [ReservaController::class, 'index'])->name('reservas.index');
    Route::get('/reservar/{horario}/confirmar',    [ReservaController::class, 'confirmar'])->name('reservas.confirmar');
    Route::post('/reservas',                       [ReservaController::class, 'store'])->name('reservas.store');
    Route::get('/reservas/{reserva}/ticket',       [ReservaController::class, 'ticket'])->name('reservas.ticket');
    Route::get('/reservas/{reserva}/ticket/pdf',   [ReservaController::class, 'descargarTicket'])->name('reservas.ticket.pdf');
    Route::delete('/reservas/{reserva}',           [ReservaController::class, 'cancelar'])->name('reservas.cancelar');
    // Recepción confirma un pago en efectivo
    Route::patch('/reservas/{reserva}/confirmar-pago', [ReservaController::class, 'confirmarPago'])
        ->middleware('role:admin,recepcionista')->name('reservas.confirmarPago');
});

require __DIR__.'/auth.php';
