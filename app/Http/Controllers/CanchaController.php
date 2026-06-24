<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCanchaRequest;
use App\Http\Requests\UpdateCanchaRequest;
use App\Models\Cancha;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CanchaController extends Controller
{
    public function index(): View
    {
        $canchas = Cancha::latest()->paginate(10);

        return view('canchas.index', compact('canchas'));
    }

    public function create(): View
    {
        return view('canchas.create');
    }

    public function store(StoreCanchaRequest $request): RedirectResponse
    {
        try {
            Cancha::create($request->validated());

            return redirect()->route('canchas.index')
                ->with('success', 'Cancha creada correctamente.');

        } catch (QueryException $e) {
            // Error de integridad: nombre duplicado a nivel de base de datos
            if ($e->getCode() === '23000') {
                return back()->withInput()
                    ->with('error', 'Ya existe una cancha con ese nombre. Elegí un nombre diferente.');
            }

            Log::error('Error al crear cancha', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->with('error', 'No se pudo guardar la cancha. Intentá de nuevo.');

        } catch (\Exception $e) {
            Log::error('Error inesperado al crear cancha', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->with('error', 'Ocurrió un error inesperado. Contactá al administrador.');
        }
    }

    public function edit(Cancha $cancha): View
    {
        return view('canchas.edit', compact('cancha'));
    }

    public function update(UpdateCanchaRequest $request, Cancha $cancha): RedirectResponse
    {
        try {
            $cancha->update($request->validated());

            return redirect()->route('canchas.index')
                ->with('success', 'Cancha actualizada correctamente.');

        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->withInput()
                    ->with('error', 'Ya existe una cancha con ese nombre. Elegí un nombre diferente.');
            }

            Log::error('Error al actualizar cancha', [
                'cancha_id' => $cancha->id,
                'error'     => $e->getMessage(),
            ]);

            return back()->withInput()
                ->with('error', 'No se pudo actualizar la cancha. Intentá de nuevo.');

        } catch (\Exception $e) {
            Log::error('Error inesperado al actualizar cancha', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->with('error', 'Ocurrió un error inesperado. Contactá al administrador.');
        }
    }

    public function destroy(Cancha $cancha): RedirectResponse
    {
        try {
            // Verificamos si tiene horarios activos (no cancelados ni completados)
            $horariosActivos = $cancha->horarios()
                ->whereNotIn('estado', ['Cancelado', 'Completado'])
                ->count();

            if ($horariosActivos > 0) {
                return back()->with(
                    'error',
                    "No se puede eliminar la cancha \"{$cancha->nombre}\" porque tiene {$horariosActivos} horario(s) activo(s). Cancelalos primero."
                );
            }

            $cancha->delete();

            return redirect()->route('canchas.index')
                ->with('success', "Cancha \"{$cancha->nombre}\" eliminada correctamente.");

        } catch (QueryException $e) {
            // FK restrict: la BD impide borrar la cancha por tarifas asociadas
            Log::error('Error al eliminar cancha', [
                'cancha_id' => $cancha->id,
                'error'     => $e->getMessage(),
            ]);

            return back()->with(
                'error',
                "No se puede eliminar la cancha \"{$cancha->nombre}\" porque tiene tarifas o registros asociados. Eliminá primero los datos relacionados."
            );

        } catch (\Exception $e) {
            Log::error('Error inesperado al eliminar cancha', ['error' => $e->getMessage()]);

            return back()->with('error', 'Ocurrió un error inesperado al eliminar la cancha.');
        }
    }
}
