<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHorarioRequest;
use App\Http\Requests\UpdateHorarioRequest;
use App\Models\Cancha;
use App\Models\Horario;
use App\Models\Tarifa;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class HorarioController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        try {
            $fechaStr = $request->query('fecha', Carbon::today()->toDateString());
            $canchaId = $request->query('cancha');

            $fecha = Carbon::parse($fechaStr)->startOfDay();

            $canchas = Cancha::orderBy('nombre')->get();

            $query = Horario::with(['cancha', 'tarifa'])
                ->whereDate('hora_inicio', $fecha)
                ->orderBy('hora_inicio');

            if ($canchaId) {
                $query->where('cancha_id', $canchaId);
            }

            $horarios = $query->get();

            $fechas = collect(range(0, 6))->map(fn ($i) => Carbon::today()->addDays($i));

            $stats = [
                'total'      => $horarios->count(),
                'disponible' => $horarios->where('estado', 'disponible')->count(),
                'reservado'  => $horarios->where('estado', 'reservado')->count(),
            ];

            return view('horarios.index', compact(
                'horarios', 'canchas', 'fecha', 'fechaStr', 'canchaId', 'fechas', 'stats'
            ));
        } catch (\Throwable $e) {
            Log::error('Error al listar horarios', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard')
                ->withErrors(['general' => 'No se pudieron cargar los horarios. Intenta de nuevo.']);
        }
    }

    public function create(): View|RedirectResponse
    {
        try {
            $canchas = Cancha::where('estado_mantenimiento', 'operativa')->orderBy('nombre')->get();
            $tarifas = Tarifa::orderBy('nombre_tarifa')->get();
            return view('horarios.create', compact('canchas', 'tarifas'));
        } catch (\Throwable $e) {
            Log::error('Error al mostrar formulario de horario', ['error' => $e->getMessage()]);
            return redirect()->route('horarios.index')
                ->withErrors(['general' => 'No se pudo cargar el formulario. Intenta de nuevo.']);
        }
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fecha_inicio'  => ['required', 'date', 'after_or_equal:today'],
            'fecha_fin'     => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'hora_desde'    => ['required', 'integer', 'min:0', 'max:23'],
            'hora_hasta'    => ['required', 'integer', 'min:1', 'max:24', 'gt:hora_desde'],
            'canchas'       => ['required', 'array', 'min:1'],
            'canchas.*'     => ['exists:canchas,id'],
            'tarifa_dia'   => ['nullable', 'exists:tarifas,id'],
            'tarifa_noche' => ['nullable', 'exists:tarifas,id'],
        ], [
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha fin debe ser igual o posterior a la de inicio.',
            'canchas.required' => 'Selecciona al menos una cancha.',
            'hora_hasta.gt' => 'La hora final debe ser mayor que la hora de inicio.',
        ]);

        try {
            $inicio = Carbon::parse($validated['fecha_inicio']);
            $fin    = Carbon::parse($validated['fecha_fin']);
            $horaDesde = (int) $validated['hora_desde'];
            $horaHasta = (int) $validated['hora_hasta'];

            $tarifaDia   = $validated['tarifa_dia']   ?? null;
            $tarifaNoche = $validated['tarifa_noche'] ?? null;

            $creados   = 0;
            $omitidos  = 0;

            for ($fecha = $inicio->copy(); $fecha->lte($fin); $fecha->addDay()) {
                foreach ($validated['canchas'] as $canchaId) {
                    for ($hora = $horaDesde; $hora < $horaHasta; $hora++) {
                        $horaInicio = $fecha->copy()->setTime($hora, 0);
                        $horaFin    = $fecha->copy()->setTime($hora + 1, 0);

                        $existe = Horario::where('cancha_id', $canchaId)
                            ->where('hora_inicio', $horaInicio)
                            ->exists();

                        if ($existe) {
                            $omitidos++;
                            continue;
                        }

                        $tarifaId = $hora < 18 ? $tarifaDia : $tarifaNoche;

                        if (! $tarifaId) {
                            $omitidos++;
                            continue;
                        }

                        Horario::create([
                            'cancha_id'   => $canchaId,
                            'tarifa_id'   => $tarifaId,
                            'hora_inicio' => $horaInicio,
                            'hora_fin'    => $horaFin,
                            'estado'      => 'disponible',
                        ]);
                        $creados++;
                    }
                }
            }

            $msg = "{$creados} horarios generados correctamente.";
            if ($omitidos > 0) {
                $msg .= " {$omitidos} omitidos (ya existían o sin tarifa).";
            }

            return redirect()->route('horarios.index')->with('success', $msg);

        } catch (\Throwable $e) {
            Log::error('Error al generar horarios masivos', ['error' => $e->getMessage()]);
            return back()->withInput()
                ->withErrors(['general' => 'Error al generar los horarios: ' . $e->getMessage()]);
        }
    }

    public function edit(Horario $horario): View|RedirectResponse
    {
        try {
            if ($horario->estado === 'reservado') {
                return redirect()->route('horarios.index')
                    ->withErrors(['general' => 'No se puede editar un horario que ya está reservado.']);
            }

            $canchas = Cancha::where('estado_mantenimiento', 'operativa')->orderBy('nombre')->get();
            $tarifas = Tarifa::orderBy('nombre_tarifa')->get();

            return view('horarios.edit', compact('horario', 'canchas', 'tarifas'));

        } catch (\Throwable $e) {
            Log::error('Error al mostrar edición de horario', ['horario_id' => $horario->id, 'error' => $e->getMessage()]);
            return redirect()->route('horarios.index')
                ->withErrors(['general' => 'No se pudo cargar el formulario de edición. Intenta de nuevo.']);
        }
    }

    public function update(UpdateHorarioRequest $request, Horario $horario): RedirectResponse
    {
        try {
            if ($horario->estado === 'reservado') {
                return back()->withErrors([
                    'general' => 'No se puede modificar un horario que ya está reservado.',
                ]);
            }

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

    public function eliminarDia(Request $request): RedirectResponse
    {
        $request->validate(['fecha' => ['required', 'date']]);

        try {
            $fecha    = Carbon::parse($request->fecha);
            $canchaId = $request->cancha_id;

            $query = Horario::whereDate('hora_inicio', $fecha)
                ->where('estado', 'disponible');

            if ($canchaId) {
                $query->where('cancha_id', $canchaId);
            }

            $eliminados = $query->delete();

            return redirect()->route('horarios.index', ['fecha' => $request->fecha, 'cancha' => $canchaId])
                ->with('success', "{$eliminados} horarios eliminados.");
        } catch (\Throwable $e) {
            Log::error('Error al eliminar horarios del día', ['error' => $e->getMessage()]);
            return back()->withErrors(['general' => 'No se pudieron eliminar los horarios.']);
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
