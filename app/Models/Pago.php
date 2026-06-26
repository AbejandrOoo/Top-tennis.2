<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pago extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'horario_id',
        'cobrado_por',
        'monto',
        'metodo_pago',
        'numero_operacion',
        'codigo_validacion',
        'estado',
        'fecha_pago',
        'notas',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto'      => 'decimal:2',
    ];

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }

    public function cobrador()
    {
        return $this->belongsTo(User::class, 'cobrado_por');
    }

    /**
     * Genera un código de validación único para el ticket, ej. TT-2406.
     * Reintenta si por casualidad colisiona con uno existente.
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
     * Contiene toda la información de la reserva para validarla en recepción.
     */
    public function contenidoQr(): string
    {
        $this->loadMissing('horario.cancha', 'horario.tarifa', 'horario.user');
        $h = $this->horario;

        return implode("\n", [
            'TOP TENNIS - TICKET DE RESERVA',
            'Codigo: '   . $this->codigo_validacion,
            'Cliente: '  . ($h->user->name ?? '-'),
            'Cancha: '   . ($h->cancha->nombre ?? '-'),
            'Fecha: '    . optional($h->fecha)->format('d/m/Y'),
            'Horario: '  . substr($h->hora_inicio, 0, 5) . ' - ' . substr($h->hora_fin, 0, 5),
            'Monto: S/ ' . number_format((float) $this->monto, 2),
            'Metodo: '   . $this->metodo_pago,
            'Estado: '   . $this->estado,
            ($this->numero_operacion ? 'Operacion: ' . $this->numero_operacion : ''),
        ]);
    }

    /**
     * Devuelve el QR de la reserva renderizado como SVG en línea (sin GD),
     * con el logo (pelota de tenis) incrustado en el centro en blanco y negro.
     * Usa corrección de errores nivel H (~30%) para que el logo no rompa el escaneo.
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

    /**
     * Pelota de tenis (logo oficial) en blanco y negro, centrada para el QR.
     */
    private function logoCentralSvg(int $tamano): string
    {
        $centro    = $tamano / 2;
        $caja      = $tamano * 0.30;            // recuadro blanco de protección
        $cajaX     = $centro - $caja / 2;
        $logo      = $tamano * 0.255;           // pelota
        $escala    = $logo / 56;                // el SVG original es 56x56
        $logoX     = $centro - $logo / 2;
        $radio     = $tamano * 0.05;

        return sprintf(
            '<rect x="%1$.2F" y="%2$.2F" width="%3$.2F" height="%3$.2F" rx="%4$.2F" fill="#ffffff"/>'
            . '<g transform="translate(%5$.2F,%5$.2F) scale(%6$.4F)">'
            . '<circle cx="28" cy="28" r="26" fill="#ffffff" stroke="#000000" stroke-width="3.5"/>'
            . '<path d="M6 22 Q28 13 50 22" stroke="#000000" stroke-width="3" fill="none" stroke-linecap="round"/>'
            . '<path d="M6 34 Q28 43 50 34" stroke="#000000" stroke-width="3" fill="none" stroke-linecap="round"/>'
            . '</g>',
            $cajaX,   // 1 rect x
            $cajaX,   // 2 rect y
            $caja,    // 3 width/height
            $radio,   // 4 rx
            $logoX,   // 5 translate x/y
            $escala   // 6 scale
        );
    }
}
