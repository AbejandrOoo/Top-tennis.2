<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; }
        .ticket { width: 100%; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .head { background: #14532d; text-align: center; padding: 18px 16px; }
        .head h1 { color: #fff; font-size: 22px; margin-top: 6px; }
        .head p { color: #bbf7d0; font-size: 10px; margin-top: 2px; }
        .head .ball { width: 56px; height: 56px; }
        .estado { text-align: center; padding: 8px; border-bottom: 1px dashed #d1d5db; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .badge-green { background: #dcfce7; color: #15803d; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .aviso-rojo { margin: 10px 16px 0; padding: 8px 10px; border: 1px solid #fca5a5;
                      background: #fef2f2; border-radius: 6px; }
        .aviso-rojo p { font-size: 10px; color: #b91c1c; font-weight: bold; line-height: 1.3; }
        .codigo { text-align: center; padding: 14px; }
        .codigo .label { font-size: 9px; color: #9ca3af; letter-spacing: 2px; text-transform: uppercase; }
        .codigo .val { font-size: 26px; font-weight: bold; color: #15803d; letter-spacing: 2px; }
        .qr { text-align: center; padding: 0 0 14px; }
        .qr-box { display: inline-block; padding: 6px; border: 1px solid #e5e7eb; border-radius: 8px; }
        .datos { padding: 0 24px 14px; }
        .row { padding: 6px 0; border-top: 1px solid #f3f4f6; }
        .row .k { color: #6b7280; }
        .row .v { color: #1f2937; font-weight: bold; float: right; }
        .monto .v { color: #15803d; font-size: 15px; }
        .foot { background: #f0fdf4; text-align: center; padding: 14px 24px; border-top: 1px dashed #d1d5db; }
        .foot p { font-size: 10px; color: #166534; }
        .clear { clear: both; }
    </style>
</head>
<body>
    @php
        $ballSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 56 56">'
            . '<circle cx="28" cy="28" r="26" fill="#dcfce7" stroke="#15803d" stroke-width="3"/>'
            . '<path d="M6 22 Q28 13 50 22" stroke="#15803d" stroke-width="2.5" fill="none" stroke-linecap="round"/>'
            . '<path d="M6 34 Q28 43 50 34" stroke="#15803d" stroke-width="2.5" fill="none" stroke-linecap="round"/>'
            . '</svg>';
    @endphp
    <div class="ticket">
        <div class="head">
            <img class="ball" src="data:image/svg+xml;base64,{{ base64_encode($ballSvg) }}" alt="">
            <h1>TOP TENNIS</h1>
            <p>Ticket de Reserva</p>
        </div>

        <div class="estado">
            @if($reserva->estado_pago === 'aprobado')
                <span class="badge badge-green">PAGADO</span>
            @elseif($reserva->estado_pago === 'anulada')
                <span class="badge badge-red">ANULADA</span>
            @else
                <span class="badge badge-yellow">PAGO PENDIENTE</span>
            @endif
        </div>

        @if($reserva->estado_pago === 'pendiente' && $reserva->metodo_pago === 'Efectivo')
            <div class="aviso-rojo">
                <p>PAGO PENDIENTE: Tiene hasta 30 minutos antes de su hora de inicio ({{ optional($reserva->expira_at)->format('d/m/Y H:i') }}) para pagar presencialmente, de lo contrario su reserva sera anulada.</p>
            </div>
        @endif

        <div class="codigo">
            <div class="label">Codigo de validacion</div>
            <div class="val">{{ $reserva->codigo_validacion }}</div>
        </div>

        <div class="qr">
            <div class="qr-box">
                <img src="data:image/svg+xml;base64,{{ base64_encode($qrSvg) }}" width="180" height="180" alt="QR">
            </div>
        </div>

        <div class="datos">
            <div class="row"><span class="k">Cliente</span><span class="v">{{ $reserva->user->name ?? '—' }}</span><div class="clear"></div></div>
            <div class="row"><span class="k">Cancha</span><span class="v">{{ $reserva->horario->cancha->nombre ?? '—' }}</span><div class="clear"></div></div>
            <div class="row"><span class="k">Fecha</span><span class="v">{{ $reserva->horario->hora_inicio->format('d/m/Y') }}</span><div class="clear"></div></div>
            <div class="row"><span class="k">Horario</span><span class="v">{{ $reserva->horario->hora_inicio->format('H:i') }} - {{ $reserva->horario->hora_fin->format('H:i') }}</span><div class="clear"></div></div>
            <div class="row"><span class="k">Metodo de pago</span><span class="v">{{ $reserva->metodo_pago }}</span><div class="clear"></div></div>
            @if($reserva->numero_operacion)
                <div class="row"><span class="k">N° operacion</span><span class="v">{{ $reserva->numero_operacion }}</span><div class="clear"></div></div>
            @endif
            <div class="row monto"><span class="k">Monto</span><span class="v">S/ {{ number_format($reserva->horario->tarifa->precio ?? 0, 2) }}</span><div class="clear"></div></div>
        </div>

        <div class="foot">
            <p>Muestre este ticket digital en la recepcion del establecimiento.</p>
        </div>
    </div>
</body>
</html>
