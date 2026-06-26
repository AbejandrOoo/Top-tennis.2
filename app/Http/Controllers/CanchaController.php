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
        $canchas = Cancha::withCount('horarios')->latest()->paginate(10);

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
            Log::error('Error al crear cancha', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->withErrors(['general' => 'No se pudo guardar la cancha. Intenta de nuevo.']);

        } catch (\Throwable $e) {
            Log::error('Error inesperado al crear cancha', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->withErrors(['general' => 'Ocurrió un error inesperado. Contacta al administrador.']);
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
            Log::error('Error al actualizar cancha', ['cancha_id' => $cancha->id, 'error' => $e->getMessage()]);

            return back()->withInput()
                ->withErrors(['general' => 'No se pudo actualizar la cancha. Intenta de nuevo.']);

        } catch (\Throwable $e) {
            Log::error('Error inesperado al actualizar cancha', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->withErrors(['general' => 'Ocurrió un error inesperado. Contacta al administrador.']);
        }
    }

    public function destroy(Cancha $cancha): RedirectResponse
    {
        try {
            if ($cancha->horarios()->exists()) {
                return back()->withErrors([
                    'general' => "No se puede eliminar \"{$cancha->nombre}\": tiene horarios asociados. Elimínalos primero.",
                ]);
            }

            $nombre = $cancha->nombre;
            $cancha->delete();

            return redirect()->route('canchas.index')
                ->with('success', "Cancha \"{$nombre}\" eliminada correctamente.");

        } catch (QueryException $e) {
            Log::error('Error al eliminar cancha', ['cancha_id' => $cancha->id, 'error' => $e->getMessage()]);

            return back()->withErrors([
                'general' => "No se puede eliminar \"{$cancha->nombre}\": tiene registros dependientes.",
            ]);

        } catch (\Throwable $e) {
            Log::error('Error inesperado al eliminar cancha', ['error' => $e->getMessage()]);

            return back()->withErrors(['general' => 'Ocurrió un error inesperado al eliminar la cancha.']);
        }
    }
}
