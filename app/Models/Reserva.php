<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reserva extends Model
{
    use HasFactory, SoftDeletes;

    // Minutos antes de la hora_inicio en que caduca una reserva en Efectivo no pagada
    public const MINUTOS_GRACIA = 30;

    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_APROBADO  = 'aprobado';
    public const ESTADO_ANULADA   = 'anulada';

    protected $fillable = [
        'user_id',
        'horario_id',
        'metodo_pago',
        'numero_operacion',
        'estado_pago',
        'monto_pagado',
        'codigo_validacion',
        'expira_at',
    ];

    protected $casts = [
        'expira_at'    => 'datetime',
        'monto_pagado' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }

    /**
     * Reservas en Efectivo pendientes cuyo plazo de pago ya venció
     * (faltan MINUTOS_GRACIA o menos para la hora_inicio).
     */
    public function scopeVencidas($query)
    {
        return $query->where('metodo_pago', 'Efectivo')
            ->where('estado_pago', self::ESTADO_PENDIENTE)
            ->whereNotNull('expira_at')
            ->where('expira_at', '<=', now());
    }

    /**
     * ¿Esta reserva en Efectivo ya caducó por no pagar a tiempo?
     */
    public function estaVencida(): bool
    {
        return $this->metodo_pago === 'Efectivo'
            && $this->estado_pago === self::ESTADO_PENDIENTE
            && $this->expira_at !== null
            && $this->expira_at->isPast();
    }

    /**
     * Caduca las reservas en Efectivo vencidas: las marca 'anulada' y
     * libera su horario (vuelve a 'disponible'). Devuelve cuántas liberó.
     *
     * La usan tanto el command programado (reservas:liberar-vencidas) como
     * el modo "lazy" (al listar/confirmar), para que la regla se vea siempre.
     */
    public static function liberarVencidas(): int
    {
        $liberadas = 0;

        // lockForUpdate evita que el job y un pago en caja toquen la misma fila a la vez
        \Illuminate\Support\Facades\DB::transaction(function () use (&$liberadas) {
            $vencidas = static::vencidas()->lockForUpdate()->get();

            foreach ($vencidas as $reserva) {
                $reserva->horario()->update(['estado' => 'disponible']);
                $reserva->update(['estado_pago' => self::ESTADO_ANULADA]);
                $liberadas++;
            }
        });

        return $liberadas;
    }

    /**
     * Genera un código de validación único para el ticket, ej. TT-2406.
     */
    public static function generarCodigoValidacion(): string
    {
        do {
            $codigo = 'TT-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('codigo_validacion', $codigo)->exists());

        return $codigo;
    }

    /**
     * Texto plano que se codifica dentro del QR del ticket.
     */
    public function contenidoQr(): string
    {
        $this->loadMissing('horario.cancha', 'horario.tarifa', 'user');
        $h = $this->horario;

        return implode("\n", [
            'TOP TENNIS - TICKET DE RESERVA',
            'Codigo: '   . $this->codigo_validacion,
            'Cliente: '  . ($this->user->name ?? '-'),
            'Cancha: '   . (optional($h->cancha)->nombre ?? '-'),
            'Fecha: '    . optional($h->hora_inicio)->format('d/m/Y'),
            'Horario: '  . optional($h->hora_inicio)->format('H:i') . ' - ' . optional($h->hora_fin)->format('H:i'),
            'Monto: S/ ' . number_format((float) $this->monto_pagado, 2),
            'Metodo: '   . $this->metodo_pago,
            'Estado: '   . $this->estado_pago,
            ($this->numero_operacion ? 'Operacion: ' . $this->numero_operacion : ''),
        ]);
    }

    /**
     * QR de la reserva como SVG (sin GD) con el logo (pelota) en el centro, B/N.
     * Nivel de corrección H (~30%) para que el logo no rompa el escaneo.
     */
    public function qrSvg(int $tamano = 220): string
    {
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle($tamano),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );

        $svg = (new \BaconQrCode\Writer($renderer))->writeString(
            $this->contenidoQr(),
            'utf-8',
            \BaconQrCode\Common\ErrorCorrectionLevel::H()
        );

        return str_replace('</svg>', $this->logoCentralSvg($tamano) . '</svg>', $svg);
    }

    private function logoCentralSvg(int $tamano): string
    {
        $centro = $tamano / 2;
        $caja   = $tamano * 0.30;
        $cajaX  = $centro - $caja / 2;
        $logo   = $tamano * 0.255;
        $escala = $logo / 56;
        $logoX  = $centro - $logo / 2;
        $radio  = $tamano * 0.05;

        return sprintf(
            '<rect x="%1$.2F" y="%2$.2F" width="%3$.2F" height="%3$.2F" rx="%4$.2F" fill="#ffffff"/>'
            . '<g transform="translate(%5$.2F,%5$.2F) scale(%6$.4F)">'
            . '<circle cx="28" cy="28" r="26" fill="#ffffff" stroke="#000000" stroke-width="3.5"/>'
            . '<path d="M6 22 Q28 13 50 22" stroke="#000000" stroke-width="3" fill="none" stroke-linecap="round"/>'
            . '<path d="M6 34 Q28 43 50 34" stroke="#000000" stroke-width="3" fill="none" stroke-linecap="round"/>'
            . '</g>',
            $cajaX, $cajaX, $caja, $radio, $logoX, $escala
        );
    }
}
