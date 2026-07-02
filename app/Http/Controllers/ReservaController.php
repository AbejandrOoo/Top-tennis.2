<?php

namespace App\Http\Controllers;

use App\Enums\Rol;
use App\Http\Requests\StoreReservaRequest;
use App\Models\Cancha;
use App\Models\Horario;
use App\Models\Reserva;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

// CONTROLADOR DE RESERVAS: ORQUESTA EL FLUJO DE NEGOCIO PRINCIPAL DEL SISTEMA
// FLUJO CLIENTE: disponibles() -> confirmar() -> store() -> ticket() / descargarTicket()
// FLUJO STAFF:   crearManual() -> storeManual() | confirmarPago() (EFECTIVO) | index() (HISTORIAL)
// SEGURIDAD: autorizar() VERIFICA DUEÑO-O-STAFF; StoreReservaRequest VALIDA LA ENTRADA;
//            TODOS LOS METODOS USAN try/catch + Log PARA NO MOSTRAR ERRORES CRUDOS AL USUARIO
// ANTI-DOBLE RESERVA (2 CAPAS): 1) UNIQUE horario_id EN BD  2) UPDATE ATOMICO CONDICIONAL EN store()
class ReservaController extends Controller
{
    /**
     * Listado de horarios disponibles para que el cliente reserve.
     */
    public function disponibles(Request $request): View|RedirectResponse
    {
        try {
            // Lazy: restaura canchas cuyo mantenimiento ya terminó
            Cancha::restaurarVencidas();
            // Lazy: libera no-shows en Efectivo vencidos
            Reserva::liberarVencidas();

            $horarios = Horario::reservables()
                ->with(['cancha', 'tarifa'])
                ->orderBy('hora_inicio')
                ->get();

            // Canchas en mantenimiento para mostrarlas deshabilitadas al cliente
            $canchasEnMantenimiento = Cancha::where('estado_mantenimiento', 'en_mantenimiento')
                ->orderBy('nombre')
                ->get();

            return view('reservas.disponibles', compact('horarios', 'canchasEnMantenimiento'));

        } catch (\Throwable $e) {
            Log::error('Error al listar horarios disponibles', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard')
                ->withErrors(['general' => 'No se pudieron cargar los horarios disponibles. Intenta de nuevo.']);
        }
    }

    /**
     * Listado de reservas: admin/recepción ven todas (con filtro y total);
     * el cliente ve solo las suyas.
     */
    public function index(Request $request): View|RedirectResponse
    {
        try {
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

        } catch (\Throwable $e) {
            Log::error('Error al listar reservas', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard')
                ->withErrors(['general' => 'No se pudieron cargar las reservas. Intenta de nuevo.']);
        }
    }

    /**
     * Vista de confirmación y pago de un horario disponible.
     */
    public function confirmar(Horario $horario): View|RedirectResponse
    {
        try {
            // Modo lazy: por si este slot fue liberado por un no-show recién vencido.
            Reserva::liberarVencidas();

            $horario->refresh()->load(['cancha', 'tarifa']);

            // Null-check explícito de cancha antes de llamar estaOperativa(),
            // por si la cancha fue soft-deleted entre la carga y este punto.
            if ($horario->estado !== 'disponible'
                || ! $horario->hora_inicio
                || $horario->hora_inicio->isPast()
                || ! $horario->cancha
                || ! $horario->cancha->estaOperativa()) {
                return redirect()->route('reservas.disponibles')
                    ->withErrors(['general' => 'Ese horario ya no está disponible para reservar.']);
            }

            return view('reservas.confirmar', compact('horario'));

        } catch (\Throwable $e) {
            Log::error('Error al mostrar confirmación de horario', ['horario_id' => $horario->id, 'error' => $e->getMessage()]);
            return redirect()->route('reservas.disponibles')
                ->withErrors(['general' => 'No se pudo cargar el formulario de reserva. Intenta de nuevo.']);
        }
    }

    /**
     * Procesa la reserva con pago (Yape o Efectivo) y emite el ticket.
     */
    // METODO MAS IMPORTANTE DEL SISTEMA. TODO OCURRE DENTRO DE DB::transaction:
    // SI CUALQUIER PASO FALLA, SE HACE ROLLBACK Y NADA QUEDA A MEDIAS.
    // REGLA DE PAGO: Yape -> aprobado AL INSTANTE (EL CLIENTE YA PAGO, ADJUNTA N° OPERACION)
    //                Efectivo -> pendiente HASTA QUE RECEPCION CONFIRME EN CAJA
    public function store(StoreReservaRequest $request): RedirectResponse
    {
        try {
            $reserva = DB::transaction(function () use ($request) {
                $esYape  = $request->metodo_pago === 'Yape';
                // Cargar tarifa en la misma query para detectar si fue eliminada
                // antes de intentar leer su precio (evita null dereference en monto_pagado).
                $horario = Horario::with('tarifa')->findOrFail($request->horario_id);

                if (! $horario->tarifa) {
                    throw new \RuntimeException('tarifa_no_disponible');
                }

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
            $mensaje = match ($e->getMessage()) {
                'horario_no_disponible' => 'Ese horario acaba de ser reservado por otra persona. Elige otro.',
                'tarifa_no_disponible'  => 'La tarifa de ese horario fue eliminada. Contacta al administrador.',
                default                 => 'No se pudo procesar la reserva. Intenta de nuevo.',
            };
            return back()->withInput()->withErrors(['general' => $mensaje]);

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
        // Fuera del try: abort(403) debe propagarse como respuesta HTTP correcta,
        // no ser capturado y convertido en redirección al dashboard.
        $this->autorizar($reserva);

        try {
            $reserva->load(['horario.cancha', 'horario.tarifa', 'user']);
        } catch (\Throwable $e) {
            Log::error('Error al cargar relaciones del ticket', ['reserva_id' => $reserva->id, 'error' => $e->getMessage()]);
            return redirect()->route('reservas.index')
                ->withErrors(['general' => 'No se pudo cargar el ticket.']);
        }

        try {
            $qrSvg = $reserva->qrSvg();
        } catch (\Throwable $e) {
            // Si el QR falla, mostrar ticket sin código QR (degradación elegante)
            Log::warning('QR no disponible para ticket', ['reserva_id' => $reserva->id, 'error' => $e->getMessage()]);
            $qrSvg = '';
        }

        return view('reservas.ticket', compact('reserva', 'qrSvg'));
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
                return redirect()->route('dashboard')
                    ->with('error', 'Solo el personal puede confirmar pagos en efectivo.');
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
     * Formulario para que admin/recepción cree una reserva manual.
     */
    public function crearManual(): View|RedirectResponse
    {
        try {
            Reserva::liberarVencidas();

            $horarios = Horario::reservables()
                ->with(['cancha', 'tarifa'])
                ->orderBy('hora_inicio')
                ->orderBy('cancha_id')
                ->get();

            $canchasMap = $horarios->pluck('cancha')->unique('id')->sortBy('id')
                ->mapWithKeys(fn ($c) => [$c->id => [
                    'id'     => $c->id,
                    'nombre' => $c->nombre,
                    'tipo'   => $c->tipo_superficie,
                    'imagen' => $c->imagenUrl(),
                ]]);

            $horariosAgrupados = $horarios
                ->groupBy(fn ($h) => $h->hora_inicio->format('Y-m-d'))
                ->map(fn ($dayGroup) => $dayGroup
                    ->groupBy('cancha_id')
                    ->map(fn ($canchaGroup) => $canchaGroup->map(fn ($h) => [
                        'id'     => $h->id,
                        'hora'   => $h->hora_inicio->format('H:i'),
                        'precio' => (float) ($h->tarifa?->precio ?? 0),
                        'tarifa' => $h->tarifa?->nombre_tarifa ?? '',
                    ])->values())
                );

            $clientes = User::where('rol', Rol::Cliente)->orderBy('name')->get();

            return view('reservas.crear-manual', [
                'horariosJson' => $horariosAgrupados,
                'canchasJson'  => $canchasMap,
                'clientes'     => $clientes,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al cargar formulario de reserva manual', ['error' => $e->getMessage()]);
            return redirect()->route('reservas.index')
                ->withErrors(['general' => 'No se pudo cargar el formulario.']);
        }
    }

    /**
     * Procesa una reserva manual creada por admin/recepción.
     */
    // VALIDACION INLINE (NO FormRequest) PORQUE LAS REGLAS SON DINAMICAS:
    // SI modo_cliente = 'nuevo' SE EXIGE NOMBRE (Y SE CREA UN USER CLIENTE AL VUELO),
    // SI ES 'existente' SE EXIGE UN user_id VALIDO. EL PAGO SE APRUEBA DIRECTO
    // PORQUE EL STAFF COBRA EN EL MOSTRADOR ANTES DE REGISTRAR.
    public function storeManual(Request $request): RedirectResponse
    {
        $rules = [
            'horario_id'        => ['required', 'exists:horarios,id'],
            'metodo_pago'       => ['required', 'in:Yape,Efectivo'],
            'numero_operacion'  => ['nullable', 'string', 'max:50'],
            'modo_cliente'      => ['required', 'in:existente,nuevo'],
        ];

        if ($request->modo_cliente === 'nuevo') {
            $rules['nuevo_nombre']   = ['required', 'string', 'max:255'];
            $rules['nuevo_telefono'] = ['nullable', 'string', 'max:20'];
        } else {
            $rules['user_id'] = ['required', 'exists:users,id'];
        }

        $validated = $request->validate($rules);

        try {
            $reserva = DB::transaction(function () use ($validated) {
                if ($validated['modo_cliente'] === 'nuevo') {
                    $cliente = User::create([
                        'name'     => $validated['nuevo_nombre'],
                        'telefono' => $validated['nuevo_telefono'] ?? null,
                        'email'    => 'cliente-' . Str::random(8) . '@toptennis.local',
                        'password' => Hash::make(Str::random(16)),
                        'rol'      => Rol::Cliente,
                    ]);
                    $userId = $cliente->id;
                } else {
                    $userId = $validated['user_id'];
                }

                $horario = Horario::with('tarifa')->findOrFail($validated['horario_id']);

                if (! $horario->tarifa) {
                    throw new \RuntimeException('tarifa_no_disponible');
                }

                $tomado = Horario::where('id', $horario->id)
                    ->where('estado', 'disponible')
                    ->update(['estado' => 'reservado']);

                if ($tomado === 0) {
                    throw new \RuntimeException('horario_no_disponible');
                }

                $esYape = $validated['metodo_pago'] === 'Yape';

                return Reserva::create([
                    'user_id'           => $userId,
                    'horario_id'        => $horario->id,
                    'metodo_pago'       => $validated['metodo_pago'],
                    'numero_operacion'  => $esYape ? ($validated['numero_operacion'] ?? null) : null,
                    'estado_pago'       => Reserva::ESTADO_APROBADO,
                    'monto_pagado'      => $horario->tarifa->precio,
                    'expira_at'         => null,
                    'codigo_validacion' => Reserva::generarCodigoValidacion(),
                ]);
            });

            return redirect()->route('reservas.index')
                ->with('success', "Reserva {$reserva->codigo_validacion} creada correctamente.");

        } catch (\RuntimeException $e) {
            $msg = match ($e->getMessage()) {
                'horario_no_disponible' => 'Ese horario ya fue reservado.',
                'tarifa_no_disponible'  => 'La tarifa de ese horario fue eliminada.',
                default                 => 'No se pudo procesar la reserva.',
            };
            return back()->withInput()->withErrors(['general' => $msg]);
        } catch (\Throwable $e) {
            Log::error('Error al crear reserva manual', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['general' => 'Error al crear la reserva.']);
        }
    }

    /**
     * Solo el dueño de la reserva o el staff pueden verla/operarla.
     */
    // AUTORIZACION A NIVEL DE OBJETO: EL MIDDLEWARE role: PROTEGE LA RUTA POR ROL,
    // PERO ESTO PROTEGE EL REGISTRO CONCRETO (UN CLIENTE NO PUEDE VER EL TICKET DE OTRO
    // CAMBIANDO EL ID EN LA URL). SI NO PASA -> abort(403).
    private function autorizar(Reserva $reserva): void
    {
        $user    = auth()->user();
        $esStaff = in_array($user->rol, [Rol::Admin, Rol::Recepcionista]);

        if (! $esStaff && $reserva->user_id !== $user->id) {
            abort(403, 'No tienes permiso para acceder a esta reserva.');
        }
    }
}
