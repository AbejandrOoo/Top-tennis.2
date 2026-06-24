<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTarifaRequest;
use App\Http\Requests\UpdateTarifaRequest;
use App\Models\Cancha;
use App\Models\Tarifa;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TarifaController extends Controller
{
    public function index(): View
    {
        $tarifas = Tarifa::with('cancha')->latest()->paginate(10);

        return view('tarifas.index', compact('tarifas'));
    }

    public function create(): View
    {
        $canchas = Cancha::where('estado', 'Disponible')->get();

        if ($canchas->isEmpty()) {
            return view('tarifas.create', compact('canchas'))
                ->with('warning', 'No hay canchas disponibles. Creá una cancha antes de agregar tarifas.');
        }

        return view('tarifas.create', compact('canchas'));
    }

    public function store(StoreTarifaRequest $request): RedirectResponse
    {
        try {
            // Verificación de negocio: la cancha debe seguir disponible al momento de guardar
            $cancha = Cancha::find($request->cancha_id);
            if (!$cancha || $cancha->estado !== 'Disponible') {
                return back()->withInput()
                    ->with('error', 'La cancha seleccionada no está disponible. Actualizá la página e intentá de nuevo.');
            }

            Tarifa::create($request->validated());

            return redirect()->route('tarifas.index')
                ->with('success', 'Tarifa creada correctamente.');

        } catch (QueryException $e) {
            Log::error('Error al crear tarifa', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->with('error', 'No se pudo guardar la tarifa. Verificá los datos e intentá de nuevo.');

        } catch (\Exception $e) {
            Log::error('Error inesperado al crear tarifa', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->with('error', 'Ocurrió un error inesperado. Contactá al administrador.');
        }
    }

    public function edit(Tarifa $tarifa): View
    {
        $canchas = Cancha::where('estado', 'Disponible')->get();

        return view('tarifas.edit', compact('tarifa', 'canchas'));
    }

    public function update(UpdateTarifaRequest $request, Tarifa $tarifa): RedirectResponse
    {
        try {
            $tarifa->update($request->validated());

            return redirect()->route('tarifas.index')
                ->with('success', 'Tarifa actualizada correctamente.');

        } catch (QueryException $e) {
            Log::error('Error al actualizar tarifa', [
                'tarifa_id' => $tarifa->id,
                'error'     => $e->getMessage(),
            ]);

            return back()->withInput()
                ->with('error', 'No se pudo actualizar la tarifa. Intentá de nuevo.');

        } catch (\Exception $e) {
            Log::error('Error inesperado al actualizar tarifa', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->with('error', 'Ocurrió un error inesperado. Contactá al administrador.');
        }
    }

    public function destroy(Tarifa $tarifa): RedirectResponse
    {
        try {
            // Verificamos si tiene horarios activos antes de eliminar
            $horariosActivos = $tarifa->horarios()
                ->whereNotIn('estado', ['Cancelado', 'Completado'])
                ->count();

            if ($horariosActivos > 0) {
                return back()->with(
                    'error',
                    "No se puede eliminar esta tarifa porque tiene {$horariosActivos} horario(s) activo(s) asociado(s). Cancelalos primero."
                );
            }

            $tarifa->delete();

            return redirect()->route('tarifas.index')
                ->with('success', 'Tarifa eliminada correctamente.');

        } catch (QueryException $e) {
            Log::error('Error al eliminar tarifa', [
                'tarifa_id' => $tarifa->id,
                'error'     => $e->getMessage(),
            ]);

            return back()->with(
                'error',
                'No se puede eliminar esta tarifa porque tiene registros asociados en el sistema.'
            );

        } catch (\Exception $e) {
            Log::error('Error inesperado al eliminar tarifa', ['error' => $e->getMessage()]);

            return back()->with('error', 'Ocurrió un error inesperado al eliminar la tarifa.');
        }
    }
}
