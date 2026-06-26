<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTarifaRequest;
use App\Http\Requests\UpdateTarifaRequest;
use App\Models\Tarifa;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TarifaController extends Controller
{
    public function index(): View
    {
        $tarifas = Tarifa::withCount('horarios')->latest()->paginate(10);

        return view('tarifas.index', compact('tarifas'));
    }

    public function create(): View
    {
        return view('tarifas.create');
    }

    public function store(StoreTarifaRequest $request): RedirectResponse
    {
        try {
            Tarifa::create($request->validated());

            return redirect()->route('tarifas.index')
                ->with('success', 'Tarifa creada correctamente.');

        } catch (QueryException $e) {
            Log::error('Error al crear tarifa', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->withErrors(['general' => 'No se pudo guardar la tarifa. Intenta de nuevo.']);

        } catch (\Throwable $e) {
            Log::error('Error inesperado al crear tarifa', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->withErrors(['general' => 'Ocurrió un error inesperado. Contacta al administrador.']);
        }
    }

    public function edit(Tarifa $tarifa): View
    {
        return view('tarifas.edit', compact('tarifa'));
    }

    public function update(UpdateTarifaRequest $request, Tarifa $tarifa): RedirectResponse
    {
        try {
            $tarifa->update($request->validated());

            return redirect()->route('tarifas.index')
                ->with('success', 'Tarifa actualizada correctamente.');

        } catch (QueryException $e) {
            Log::error('Error al actualizar tarifa', ['tarifa_id' => $tarifa->id, 'error' => $e->getMessage()]);

            return back()->withInput()
                ->withErrors(['general' => 'No se pudo actualizar la tarifa. Intenta de nuevo.']);

        } catch (\Throwable $e) {
            Log::error('Error inesperado al actualizar tarifa', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->withErrors(['general' => 'Ocurrió un error inesperado. Contacta al administrador.']);
        }
    }

    public function destroy(Tarifa $tarifa): RedirectResponse
    {
        try {
            if ($tarifa->horarios()->exists()) {
                return back()->withErrors([
                    'general' => 'No se puede eliminar esta tarifa: tiene horarios asociados. Elimínalos primero.',
                ]);
            }

            $tarifa->delete();

            return redirect()->route('tarifas.index')
                ->with('success', 'Tarifa eliminada correctamente.');

        } catch (QueryException $e) {
            Log::error('Error al eliminar tarifa', ['tarifa_id' => $tarifa->id, 'error' => $e->getMessage()]);

            return back()->withErrors([
                'general' => 'No se puede eliminar esta tarifa: tiene registros dependientes.',
            ]);

        } catch (\Throwable $e) {
            Log::error('Error inesperado al eliminar tarifa', ['error' => $e->getMessage()]);

            return back()->withErrors(['general' => 'Ocurrió un error inesperado al eliminar la tarifa.']);
        }
    }
}
