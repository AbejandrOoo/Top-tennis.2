<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePagoRequest;
use App\Models\Horario;
use App\Models\Pago;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PagoController extends Controller
{
    /**
     * Vista de "Confirmación y Pago" para una reserva en estado Reservado.
     */
    public function confirmar(Horario $horario): View|RedirectResponse
    {
        try {
            $user = auth()->user();
            $esStaff = in_array($user->rol, [\App\Enums\Rol::Admin, \App\Enums\Rol::Recepcionista]);

            if (!$esStaff && $horario->user_id !== $user->id) {
                return redirect()->route('dashboard')
                    ->with('error', 'No puedes pagar una reserva que no es tuya.');
            }

            if ($horario->estado !== 'Reservado') {
                return redirect()->route('dashboard')
                    ->with('open_tab', $esStaff ? 'reservas' : 'mis-reservas')
                    ->with('warning', "Esta reserva ya está {$horario->estado}.");
            }

            $horario->load(['cancha', 'tarifa', 'user']);
            $monto = $horario->tarifa?->precio_hora ?? 0;

            return view('pagos.confirmar', compact('horario', 'monto'));

        } catch (\Throwable $e) {
            Log::error('Error al abrir confirmación de pago', ['error' => $e->getMessage()]);

            return redirect()->route('dashboard')
                ->with('error', 'No se pudo abrir la pantalla de pago. Intenta de nuevo.');
        }
    }

    /**
     * Procesa el pago (Yape/Plin o Efectivo), confirma la reserva y emite el ticket.
     */
    public function procesar(StorePagoRequest $request): RedirectResponse
    {
        try {
            $horario = Horario::with('tarifa')->findOrFail($request->horario_id);
            $monto   = $horario->tarifa?->precio_hora ?? 0;
            $esYape  = $request->metodo_pago === 'Yape/Plin';

            $pago = DB::transaction(function () use ($request, $horario, $monto, $esYape) {
                $pago = Pago::create([
                    'horario_id'        => $horario->id,
                    'cobrado_por'       => auth()->id(),
                    'monto'             => $monto,
                    'metodo_pago'       => $request->metodo_pago,
                    'numero_operacion'  => $esYape ? $request->numero_operacion : null,
                    'codigo_validacion' => Pago::generarCodigoValidacion(),
                    // Yape/Plin queda Pagado al instante; Efectivo se cobra en recepción
                    'estado'            => $esYape ? 'Pagado' : 'Pendiente',
                    'fecha_pago'        => now()->toDateString(),
                ]);

                // La reserva pasa de Reservado a Confirmado
                $horario->update(['estado' => 'Confirmado', 'metodo_pago' => $request->metodo_pago]);

                return $pago;
            });

            return redirect()->route('pagos.ticket', $pago)
                ->with('success', '¡Reserva confirmada! Aquí está tu ticket digital.');

        } catch (QueryException $e) {
            Log::error('Error de BD al procesar pago', [
                'horario_id' => $request->horario_id,
                'error'      => $e->getMessage(),
            ]);

            return back()->withInput()
                ->with('error', 'No se pudo registrar el pago. Es posible que la reserva ya haya sido procesada.');

        } catch (\Throwable $e) {
            Log::error('Error inesperado al procesar pago', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->with('error', 'Ocurrió un error inesperado al procesar el pago. Contacta al administrador.');
        }
    }

    /**
     * Ticket digital en pantalla (con QR de validación).
     */
    public function ticket(Pago $pago): View|RedirectResponse
    {
        try {
            $this->autorizarTicket($pago);

            $pago->load(['horario.cancha', 'horario.tarifa', 'horario.user']);
            $qrSvg = $pago->qrSvg();

            return view('pagos.ticket', compact('pago', 'qrSvg'));

        } catch (\Throwable $e) {
            Log::error('Error al mostrar ticket', ['pago_id' => $pago->id, 'error' => $e->getMessage()]);

            return redirect()->route('dashboard')
                ->with('error', 'No se pudo cargar el ticket.');
        }
    }

    /**
     * Descarga del ticket como PDF (mismo diseño, con QR embebido).
     */
    public function descargarTicket(Pago $pago): Response|RedirectResponse
    {
        try {
            $this->autorizarTicket($pago);

            $pago->load(['horario.cancha', 'horario.tarifa', 'horario.user']);
            $qrSvg = $pago->qrSvg(200);

            $pdf = Pdf::loadView('pagos.ticket-pdf', compact('pago', 'qrSvg'))
                ->setPaper('a5', 'portrait');

            return $pdf->download("ticket-{$pago->codigo_validacion}.pdf");

        } catch (\Throwable $e) {
            Log::error('Error al descargar ticket PDF', ['pago_id' => $pago->id, 'error' => $e->getMessage()]);

            return back()->with('error', 'No se pudo generar el PDF del ticket. Intenta de nuevo.');
        }
    }

    /**
     * Solo el dueño de la reserva o el staff pueden ver/descargar el ticket.
     */
    private function autorizarTicket(Pago $pago): void
    {
        $user = auth()->user();
        $esStaff = in_array($user->rol, [\App\Enums\Rol::Admin, \App\Enums\Rol::Recepcionista]);

        if (!$esStaff && $pago->horario->user_id !== $user->id) {
            abort(403, 'No tienes permiso para ver este ticket.');
        }
    }
}
