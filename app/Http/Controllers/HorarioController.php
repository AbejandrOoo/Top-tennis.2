<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHorarioRequest;
use App\Http\Requests\UpdateHorarioRequest;
use App\Models\Cancha;
use App\Models\Horario;
use App\Models\Tarifa;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class HorarioController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

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
        $tarifas = Tarifa::with('cancha')->where('estado', 'Activa')->get();

        if ($canchas->isEmpty()) {
            return view('horarios.create', compact('canchas', 'tarifas'))
                ->with('warning', 'No hay canchas disponibles para reservar en este momento.');
        }

        if ($tarifas->isEmpty()) {
            return view('horarios.create', compact('canchas', 'tarifas'))
                ->with('warning', 'No hay tarifas activas configuradas. Contactá a recepción.');
        }

        return view('horarios.create', compact('canchas', 'tarifas'));
    }

    public function store(StoreHorarioRequest $request): RedirectResponse
    {
        try {
            // Verificación de negocio: la cancha debe seguir disponible en el momento de guardar
            $cancha = Cancha::find($request->cancha_id);
            if (!$cancha || $cancha->estado !== 'Disponible') {
                return back()->withInput()
                    ->with('error', 'La cancha seleccionada ya no está disponible. Elegí otra cancha.');
            }

            // Verificación de negocio: la tarifa debe seguir activa
            $tarifa = Tarifa::find($request->tarifa_id);
            if (!$tarifa || $tarifa->estado !== 'Activa') {
                return back()->withInput()
                    ->with('error', 'La tarifa seleccionada ya no está activa. Elegí otra tarifa.');
            }

            Horario::create([
                ...$request->validated(),
                'user_id' => auth()->id(),
                'estado'  => 'Reservado',
            ]);

            return redirect()->route('horarios.index')
                ->with('success', "Reserva creada correctamente para el {$request->fecha} de {$request->hora_inicio} a {$request->hora_fin}.");

        } catch (QueryException $e) {
            Log::error('Error al crear horario', [
                'usuario'  => auth()->user()->email,
                'datos'    => $request->validated(),
                'error'    => $e->getMessage(),
            ]);

            return back()->withInput()
                ->with('error', 'No se pudo guardar la reserva. Es posible que el horario ya fue tomado. Intentá de nuevo.');

        } catch (\Exception $e) {
            Log::error('Error inesperado al crear horario', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->with('error', 'Ocurrió un error inesperado. Contactá al administrador del club.');
        }
    }

    public function edit(Horario $horario): View
    {
        try {
            $this->authorize('update', $horario);

        } catch (AuthorizationException $e) {
            abort(403, 'Solo puedes editar tus propias reservas mientras estén en estado Reservado.');
        }

        // Un horario completado o cancelado no se puede editar
        if (in_array($horario->estado, ['Completado', 'Cancelado'])) {
            return redirect()->route('horarios.index')
                ->with('warning', "El horario ya está {$horario->estado} y no puede modificarse.");
        }

        $canchas = Cancha::where('estado', 'Disponible')->get();
        $tarifas = Tarifa::with('cancha')->where('estado', 'Activa')->get();

        return view('horarios.edit', compact('horario', 'canchas', 'tarifas'));
    }

    public function update(UpdateHorarioRequest $request, Horario $horario): RedirectResponse
    {
        try {
            $this->authorize('update', $horario);

        } catch (AuthorizationException $e) {
            return redirect()->route('horarios.index')
                ->with('error', 'No tienes permisos para modificar este horario.');
        }

        try {
            // Un cliente no puede sacar un horario del estado Cancelado o Completado
            if (
                in_array($horario->estado, ['Cancelado', 'Completado'])
                && !in_array(auth()->user()->rol, [\App\Enums\Rol::Admin, \App\Enums\Rol::Recepcionista])
            ) {
                return redirect()->route('horarios.index')
                    ->with('error', "No se puede modificar un horario en estado {$horario->estado}.");
            }

            $horario->update($request->validated());

            return redirect()->route('horarios.index')
                ->with('success', 'Horario actualizado correctamente.');

        } catch (QueryException $e) {
            Log::error('Error al actualizar horario', [
                'horario_id' => $horario->id,
                'error'      => $e->getMessage(),
            ]);

            return back()->withInput()
                ->with('error', 'No se pudo actualizar el horario. Es posible que el nuevo horario solape con otra reserva.');

        } catch (\Exception $e) {
            Log::error('Error inesperado al actualizar horario', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->with('error', 'Ocurrió un error inesperado. Contactá al administrador.');
        }
    }

    public function destroy(Horario $horario): RedirectResponse
    {
        try {
            $this->authorize('delete', $horario);

        } catch (AuthorizationException $e) {
            return redirect()->route('horarios.index')
                ->with('error', 'Solo los administradores pueden eliminar horarios.');
        }

        try {
            // Un horario Confirmado requiere confirmación extra del admin (protección adicional)
            if ($horario->estado === 'Confirmado') {
                return back()->with(
                    'warning',
                    'El horario está Confirmado. Para eliminarlo, primero cambiá el estado a Cancelado.'
                );
            }

            $horario->delete();

            return redirect()->route('horarios.index')
                ->with('success', 'Horario eliminado correctamente.');

        } catch (QueryException $e) {
            Log::error('Error al eliminar horario', [
                'horario_id' => $horario->id,
                'error'      => $e->getMessage(),
            ]);

            return back()->with('error', 'No se pudo eliminar el horario. Intentá de nuevo.');

        } catch (\Exception $e) {
            Log::error('Error inesperado al eliminar horario', ['error' => $e->getMessage()]);

            return back()->with('error', 'Ocurrió un error inesperado al eliminar el horario.');
        }
    }
}
