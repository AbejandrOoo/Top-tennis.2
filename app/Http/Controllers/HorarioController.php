<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHorarioRequest;
use App\Http\Requests\UpdateHorarioRequest;
use App\Models\Cancha;
use App\Models\Horario;
use App\Models\Tarifa;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class HorarioController extends Controller
{
    public function index(): View
    {
        $horarios = Horario::with(['cancha', 'tarifa', 'reserva'])
            ->orderByDesc('hora_inicio')
            ->paginate(15);

        return view('horarios.index', compact('horarios'));
    }

    public function create(): View
    {
        $canchas = Cancha::where('estado_mantenimiento', 'operativa')->orderBy('nombre')->get();
        $tarifas = Tarifa::orderBy('nombre_tarifa')->get();

        return view('horarios.create', compact('canchas', 'tarifas'));
    }

    public function store(StoreHorarioRequest $request): RedirectResponse
    {
        try {
            Horario::create([
                ...$request->validated(),
                'estado' => 'disponible',
            ]);

            return redirect()->route('horarios.index')
                ->with('success', 'Horario creado correctamente.');

        } catch (QueryException $e) {
            Log::error('Error al crear horario', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->withErrors(['general' => 'No se pudo guardar el horario. Intenta de nuevo.']);

        } catch (\Throwable $e) {
            Log::error('Error inesperado al crear horario', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->withErrors(['general' => 'Ocurrió un error inesperado. Contacta al administrador.']);
        }
    }

    public function edit(Horario $horario): View|RedirectResponse
    {
        if ($horario->estado === 'reservado') {
            return redirect()->route('horarios.index')
                ->withErrors(['general' => 'No se puede editar un horario que ya está reservado.']);
        }

        $canchas = Cancha::where('estado_mantenimiento', 'operativa')->orderBy('nombre')->get();
        $tarifas = Tarifa::orderBy('nombre_tarifa')->get();

        return view('horarios.edit', compact('horario', 'canchas', 'tarifas'));
    }

    public function update(UpdateHorarioRequest $request, Horario $horario): RedirectResponse
    {
        try {
            $horario->update($request->validated());

            return redirect()->route('horarios.index')
                ->with('success', 'Horario actualizado correctamente.');

        } catch (QueryException $e) {
            Log::error('Error al actualizar horario', ['horario_id' => $horario->id, 'error' => $e->getMessage()]);

            return back()->withInput()
                ->withErrors(['general' => 'No se pudo actualizar el horario. Intenta de nuevo.']);

        } catch (\Throwable $e) {
            Log::error('Error inesperado al actualizar horario', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->withErrors(['general' => 'Ocurrió un error inesperado. Contacta al administrador.']);
        }
    }

    public function destroy(Horario $horario): RedirectResponse
    {
        try {
            if ($horario->estado === 'reservado') {
                return back()->withErrors([
                    'general' => 'No se puede eliminar un horario que tiene una reserva. Cancélala primero.',
                ]);
            }

            $horario->delete();

            return redirect()->route('horarios.index')
                ->with('success', 'Horario eliminado correctamente.');

        } catch (QueryException $e) {
            Log::error('Error al eliminar horario', ['horario_id' => $horario->id, 'error' => $e->getMessage()]);

            return back()->withErrors(['general' => 'No se pudo eliminar el horario: tiene registros dependientes.']);

        } catch (\Throwable $e) {
            Log::error('Error inesperado al eliminar horario', ['error' => $e->getMessage()]);

            return back()->withErrors(['general' => 'Ocurrió un error inesperado al eliminar el horario.']);
        }
    }
}
