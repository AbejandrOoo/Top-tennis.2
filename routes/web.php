<?php

use App\Http\Controllers\CanchaController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TarifaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    $canchasLibres    = \App\Models\Cancha::where('estado', 'Disponible')->count();
    $reservasActivas  = $user->horarios()->whereIn('estado', ['Reservado', 'Confirmado'])->count();
    $horariosDisp     = \App\Models\Tarifa::where('estado', 'Activa')->count();
    $canchas          = \App\Models\Cancha::with(['tarifas' => fn($q) => $q->where('estado','Activa')->orderBy('precio_hora')])->get();
    $misReservas      = $user->horarios()->with('cancha','tarifa')->orderByDesc('fecha')->orderByDesc('hora_inicio')->get();
    return view('dashboard', compact('canchasLibres', 'reservasActivas', 'horariosDisp', 'canchas', 'misReservas'));
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

// Horarios: todos los autenticados pueden crear y ver los suyos; admin/recep ven todos
Route::middleware('auth')->group(function () {
    Route::get('/horarios', [HorarioController::class, 'index'])->name('horarios.index');
    Route::get('/horarios/create', [HorarioController::class, 'create'])->name('horarios.create');
    Route::post('/horarios', [HorarioController::class, 'store'])->name('horarios.store');
    Route::get('/horarios/{horario}/edit', [HorarioController::class, 'edit'])->name('horarios.edit');
    Route::patch('/horarios/{horario}', [HorarioController::class, 'update'])->name('horarios.update');
    Route::delete('/horarios/{horario}', [HorarioController::class, 'destroy'])
        ->middleware('role:admin')->name('horarios.destroy');
});

require __DIR__.'/auth.php';
