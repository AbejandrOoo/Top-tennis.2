<?php

namespace App\Http\Controllers;

use App\Enums\Rol;
use App\Http\Requests\StoreReservaRequest;
use App\Models\Horario;
use App\Models\Reserva;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ReservaController extends Controller
{
    /**
     * Listado de horarios disponibles para que el cliente reserve.
     */
    public function disponibles(): View
    {
        // Modo lazy: libera no-shows en Efectivo vencidos antes de listar,
        // así los slots reaparecen aunque el scheduler no esté corriendo.
        Reserva::liberarVencidas();

        $horarios = Horario::reservables()
            ->with(['cancha', 'tarifa'])
            ->orderBy('hora_inicio')
            ->get();

        return view('reservas.disponibles', compact('horarios'));
    }

    /**
     * Listado de reservas: admin/recepción ven todas (con filtro y total);
     * el cliente ve solo las suyas.
     */
    public function index(Request $request): View
    {
        $user    = auth()->user();
        $esStaff = in_array($user->rol, [Rol::Admin, Rol::Recepcionista]);

        $query = Reserva::with(['horario.cancha', 'horario.tarifa', 'user'])->latest();

        if (! $esStaff) {
            $query->where('user_id', $user->id);
        }

        // Filtro por método de pago (dashboard financiero del admin)
        if ($esStaff && in_array($request->metodo_pago, ['Yape', 'Efectivo'])) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        $reservas = $query->get();

        $totalIngresos = $esStaff
            ? $reservas->where('estado_pago', 'aprobado')->sum('monto_pagado')
            : 0;

        return view('reservas.index', compact('reservas', 'totalIngresos', 'esStaff'));
    }

    /**
     * Vista de confirmación y pago de un horario disponible.
     */
    public function confirmar(Horario $horario): View|RedirectResponse
    {
        // Modo lazy: por si este slot fue liberado por un no-show recién vencido.
        Reserva::liberarVencidas();

        $horario->refresh()->load(['cancha', 'tarifa']);

        if ($horario->estado !== 'disponible' || $horario->hora_inicio->isPast() || ! $horario->cancha->estaOperativa()) {
            return redirect()->route('reservas.disponibles')
                ->withErrors(['general' => 'Ese horario ya no está disponible para reservar.']);
        }

        return view('reservas.confirmar', compact('horario'));
    }

    /**
     * Procesa la reserva con pago (Yape o Efectivo) y emite el ticket.
     */
    public function store(StoreReservaRequest $request): RedirectResponse
    {
        try {
            $reserva = DB::transaction(function () use ($request) {
                $esYape   = $request->metodo_pago === 'Yape';
                $horario  = Horario::findOrFail($request->horario_id);

                // Capa 2 anti-race: update atómico condicional. Si afecta 0 filas,
                // otro usuario tomó el horario primero → abortamos.
                $tomado = Horario::where('id', $horario->id)
                    ->where('estado', 'disponible')
                    ->update(['estado' => 'reservado']);

                if ($tomado === 0) {
                    throw new \RuntimeException('horario_no_disponible');
                }

                return Reserva::create([
                    'user_id'           => auth()->id(),
                    'horario_id'        => $horario->id,
                    'metodo_pago'       => $request->metodo_pago,
                    'numero_operacion'  => $esYape ? $request->numero_operacion : null,
                    'estado_pago'       => $esYape ? Reserva::ESTADO_APROBADO : Reserva::ESTADO_PENDIENTE,
                    // Snapshot inmutable del precio al momento de la reserva
                    'monto_pagado'      => $horario->tarifa->precio,
                    // Solo Efectivo caduca: límite = hora_inicio - 30 min
                    'expira_at'         => $esYape ? null : $horario->hora_inicio->copy()->subMinutes(Reserva::MINUTOS_GRACIA),
                    'codigo_validacion' => Reserva::generarCodigoValidacion(),
                ]);
            });

            return redirect()->route('reservas.ticket', $reserva)
                ->with('success', '¡Reserva confirmada! Aquí está tu ticket digital.');

        } catch (\RuntimeException $e) {
            return back()->withInput()
                ->withErrors(['general' => 'Ese horario acaba de ser reservado por otra persona. Elige otro.']);

        } catch (QueryException $e) {
            Log::error('Error de BD al crear reserva', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->withErrors(['general' => 'No se pudo registrar la reserva. Intenta de nuevo.']);

        } catch (\Throwable $e) {
            Log::error('Error inesperado al crear reserva', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->withErrors(['general' => 'Ocurrió un error inesperado. Contacta al administrador.']);
        }
    }

    public function ticket(Reserva $reserva): View|RedirectResponse
    {
        try {
            $this->autorizar($reserva);

            $reserva->load(['horario.cancha', 'horario.tarifa', 'user']);
            $qrSvg = $reserva->qrSvg();

            return view('reservas.ticket', compact('reserva', 'qrSvg'));

        } catch (\Throwable $e) {
            Log::error('Error al mostrar ticket', ['reserva_id' => $reserva->id, 'error' => $e->getMessage()]);

            return redirect()->route('dashboard')->withErrors(['general' => 'No se pudo cargar el ticket.']);
        }
    }

    public function descargarTicket(Reserva $reserva): Response|RedirectResponse
    {
        try {
            $this->autorizar($reserva);

            $reserva->load(['horario.cancha', 'horario.tarifa', 'user']);
            $qrSvg = $reserva->qrSvg(200);

            $pdf = Pdf::loadView('reservas.ticket-pdf', compact('reserva', 'qrSvg'))
                ->setPaper('a5', 'portrait');

            return $pdf->download("ticket-{$reserva->codigo_validacion}.pdf");

        } catch (\Throwable $e) {
            Log::error('Error al descargar ticket PDF', ['reserva_id' => $reserva->id, 'error' => $e->getMessage()]);

            return back()->withErrors(['general' => 'No se pudo generar el PDF del ticket.']);
        }
    }

    /**
     * Cancela una reserva y libera el horario (vuelve a 'disponible').
     */
    public function cancelar(Reserva $reserva): RedirectResponse
    {
        try {
            $this->autorizar($reserva);

            // Una reserva anulada (no-show) ya liberó su horario; no tocarla.
            if ($reserva->estado_pago === Reserva::ESTADO_ANULADA) {
                return back()->withErrors(['general' => 'Esta reserva ya fue anulada y no puede cancelarse.']);
            }

            DB::transaction(function () use ($reserva) {
                // UPDATE condicional: solo libera si el horario aún está 'reservado'.
                // Protege contra el caso en que liberarVencidas() actuó entre la
                // verificación anterior y este UPDATE (mínima ventana TOCTOU).
                Horario::where('id', $reserva->horario_id)
                    ->where('estado', 'reservado')
                    ->update(['estado' => 'disponible']);
                $reserva->delete();
            });

            return redirect()->route('reservas.index')
                ->with('success', 'Reserva cancelada. El horario quedó disponible nuevamente.');

        } catch (\Throwable $e) {
            Log::error('Error al cancelar reserva', ['reserva_id' => $reserva->id, 'error' => $e->getMessage()]);

            return back()->withErrors(['general' => 'No se pudo cancelar la reserva. Intenta de nuevo.']);
        }
    }

    /**
     * Recepción confirma un pago en Efectivo (pendiente → aprobado).
     */
    public function confirmarPago(Reserva $reserva): RedirectResponse
    {
        try {
            $user = auth()->user();
            if (! in_array($user->rol, [Rol::Admin, Rol::Recepcionista])) {
                abort(403, 'Solo el personal puede confirmar pagos en efectivo.');
            }

            if ($reserva->metodo_pago !== 'Efectivo' || $reserva->estado_pago !== Reserva::ESTADO_PENDIENTE) {
                return back()->withErrors(['general' => 'Solo se puede confirmar el pago de reservas en Efectivo pendientes.']);
            }

            if ($reserva->estaVencida()) {
                return back()->withErrors(['general' => 'Esta reserva ya caducó y no puede confirmarse.']);
            }

            $reserva->update(['estado_pago' => Reserva::ESTADO_APROBADO, 'expira_at' => null]);

            return back()->with('success', "Pago en efectivo confirmado para {$reserva->codigo_validacion}.");

        } catch (\Throwable $e) {
            Log::error('Error al confirmar pago en efectivo', ['reserva_id' => $reserva->id, 'error' => $e->getMessage()]);

            return back()->withErrors(['general' => 'No se pudo confirmar el pago. Intenta de nuevo.']);
        }
    }

    /**
     * Solo el dueño de la reserva o el staff pueden verla/operarla.
     */
    private function autorizar(Reserva $reserva): void
    {
        $user    = auth()->user();
        $esStaff = in_array($user->rol, [Rol::Admin, Rol::Recepcionista]);

        if (! $esStaff && $reserva->user_id !== $user->id) {
            abort(403, 'No tienes permiso para acceder a esta reserva.');
        }
    }
}
