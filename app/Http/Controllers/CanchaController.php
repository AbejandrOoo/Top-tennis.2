<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCanchaRequest;
use App\Http\Requests\UpdateCanchaRequest;
use App\Models\Cancha;
use App\Models\Reserva;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CanchaController extends Controller
{
    public function index(): View|RedirectResponse
    {
        try {
            // Lazy: restaura canchas cuyo mantenimiento programado ya venció
            Cancha::restaurarVencidas();

            $canchas = Cancha::withCount('horarios')->latest()->paginate(10);
            return view('canchas.index', compact('canchas'));
        } catch (\Throwable $e) {
            Log::error('Error al listar canchas', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard')
                ->withErrors(['general' => 'No se pudieron cargar las canchas. Intenta de nuevo.']);
        }
    }

    public function create(): View|RedirectResponse
    {
        try {
            return view('canchas.create');
        } catch (\Throwable $e) {
            Log::error('Error al mostrar formulario de cancha', ['error' => $e->getMessage()]);
            return redirect()->route('canchas.index')
                ->withErrors(['general' => 'No se pudo cargar el formulario. Intenta de nuevo.']);
        }
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

    public function edit(Cancha $cancha): View|RedirectResponse
    {
        try {
            return view('canchas.edit', compact('cancha'));
        } catch (\Throwable $e) {
            Log::error('Error al mostrar edición de cancha', ['cancha_id' => $cancha->id, 'error' => $e->getMessage()]);
            return redirect()->route('canchas.index')
                ->withErrors(['general' => 'No se pudo cargar el formulario de edición. Intenta de nuevo.']);
        }
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

    /**
     * Modal de mantenimiento: pone la cancha en mantenimiento,
     * cancela reservas aprobadas afectadas y emite log de reembolso.
     */
    public function ponerMantenimiento(Request $request, Cancha $cancha): RedirectResponse
    {
        $validated = $request->validate([
            'motivo_mantenimiento' => ['required', 'string', 'max:255'],
            'fin_mantenimiento'    => ['required', 'date', 'after:now'],
        ], [
            'motivo_mantenimiento.required' => 'El motivo es obligatorio.',
            'fin_mantenimiento.required'    => 'La fecha y hora de fin es obligatoria.',
            'fin_mantenimiento.after'       => 'La fecha de fin debe ser posterior a ahora.',
        ]);

        try {
            $reembolsos = 0;

            DB::transaction(function () use ($cancha, $validated, &$reembolsos) {
                // Cancela reservas APROBADAS dentro del rango de mantenimiento
                $afectadas = Reserva::whereHas('horario', function ($q) use ($cancha, $validated) {
                    $q->where('cancha_id', $cancha->id)
                      ->where('hora_inicio', '>=', now())
                      ->where('hora_inicio', '<=', $validated['fin_mantenimiento']);
                })
                ->where('estado_pago', Reserva::ESTADO_APROBADO)
                ->get();

                foreach ($afectadas as $reserva) {
                    $reserva->update(['estado_pago' => Reserva::ESTADO_CANCELADO_MANT]);
                    Log::info('Reembolso automático procesado por mantenimiento de cancha', [
                        'reserva'         => $reserva->codigo_validacion,
                        'cliente_email'   => $reserva->user?->email,
                        'monto'           => $reserva->monto_pagado,
                        'cancha'          => $cancha->nombre,
                    ]);
                    $reembolsos++;
                }

                $cancha->update([
                    'estado_mantenimiento' => 'en_mantenimiento',
                    'motivo_mantenimiento' => $validated['motivo_mantenimiento'],
                    'fin_mantenimiento'    => $validated['fin_mantenimiento'],
                ]);
            });

            $msg = "Cancha \"{$cancha->nombre}\" puesta en mantenimiento.";
            if ($reembolsos > 0) {
                $msg .= " Se procesaron {$reembolsos} reembolso(s) automático(s).";
            }

            return redirect()->route('canchas.index')->with('success', $msg);

        } catch (\Throwable $e) {
            Log::error('Error al poner cancha en mantenimiento', ['cancha_id' => $cancha->id, 'error' => $e->getMessage()]);
            return back()->withErrors(['general' => 'No se pudo procesar el mantenimiento. Intenta de nuevo.']);
        }
    }

    /**
     * Restaura manualmente una cancha a estado operativo.
     */
    public function restaurar(Cancha $cancha): RedirectResponse
    {
        try {
            $cancha->update([
                'estado_mantenimiento' => 'operativa',
                'motivo_mantenimiento' => null,
                'fin_mantenimiento'    => null,
            ]);

            return redirect()->route('canchas.index')
                ->with('success', "Cancha \"{$cancha->nombre}\" restaurada a operativa.");

        } catch (\Throwable $e) {
            Log::error('Error al restaurar cancha', ['cancha_id' => $cancha->id, 'error' => $e->getMessage()]);
            return back()->withErrors(['general' => 'No se pudo restaurar la cancha. Intenta de nuevo.']);
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
