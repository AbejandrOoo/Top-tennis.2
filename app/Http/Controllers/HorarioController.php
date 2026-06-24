<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHorarioRequest;
use App\Http\Requests\UpdateHorarioRequest;
use App\Models\Cancha;
use App\Models\Horario;
use App\Models\Tarifa;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HorarioController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Admin y Recepcionista ven todos; Cliente solo los suyos
        if (in_array($user->rol, [\App\Enums\Rol::Admin, \App\Enums\Rol::Recepcionista])) {
            $horarios = Horario::with(['cancha', 'tarifa', 'user'])
                ->orderBy('fecha', 'desc')
                ->orderBy('hora_inicio')
                ->paginate(15);
        } else {
            $horarios = Horario::with(['cancha', 'tarifa'])
                ->where('user_id', $user->id)
                ->orderBy('fecha', 'desc')
                ->orderBy('hora_inicio')
                ->paginate(15);
        }

        return view('horarios.index', compact('horarios'));
    }

    public function create(): View
    {
        $canchas = Cancha::where('estado', 'Disponible')->get();
        // Se cargan todas las tarifas activas; el filtro por cancha lo hace Alpine.js en el front
        $tarifas = Tarifa::with('cancha')->where('estado', 'Activa')->get();

        return view('horarios.create', compact('canchas', 'tarifas'));
    }

    public function store(StoreHorarioRequest $request): RedirectResponse
    {
        Horario::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
            'estado'  => 'Reservado',
        ]);

        return redirect()->route('horarios.index')
            ->with('success', 'Horario reservado correctamente.');
    }

    public function edit(Horario $horario): View
    {
        $this->authorize('update', $horario);

        $canchas = Cancha::where('estado', 'Disponible')->get();
        $tarifas = Tarifa::with('cancha')->where('estado', 'Activa')->get();

        return view('horarios.edit', compact('horario', 'canchas', 'tarifas'));
    }

    public function update(UpdateHorarioRequest $request, Horario $horario): RedirectResponse
    {
        $this->authorize('update', $horario);

        $horario->update($request->validated());

        return redirect()->route('horarios.index')
            ->with('success', 'Horario actualizado correctamente.');
    }

    public function destroy(Horario $horario): RedirectResponse
    {
        $this->authorize('delete', $horario);

        $horario->delete();

        return redirect()->route('horarios.index')
            ->with('success', 'Horario eliminado correctamente.');
    }
}
