<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Ticket de Reserva</h1>
            <p class="text-green-200 text-sm mt-0.5">Comprobante digital · {{ $reserva->codigo_validacion }}</p>
        </div>
    </x-slot>

    <div class="max-w-md mx-auto px-4 py-8">

        <div class="card overflow-hidden">
            {{-- Cabecera con logo oficial --}}
            <div class="text-center py-7" style="background: linear-gradient(135deg,#0d3d22,#14532d);">
                <div class="mx-auto w-20 h-20 rounded-full bg-green-400 flex items-center justify-center mb-3">
                    <svg width="56" height="56" viewBox="0 0 56 56" fill="none">
                        <circle cx="28" cy="28" r="26" fill="#dcfce7" stroke="#15803d" stroke-width="3"/>
                        <path d="M6 22 Q28 13 50 22" stroke="#15803d" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                        <path d="M6 34 Q28 43 50 34" stroke="#15803d" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                    </svg>
                </div>
                <h2 class="text-white font-extrabold text-2xl tracking-wide">TOP TENNIS</h2>
                <p class="text-green-200 text-xs mt-0.5">Ticket de Reserva</p>
            </div>

            <div class="text-center py-3 border-b border-dashed border-gray-200">
                @if($reserva->estado_pago === 'aprobado')
                    <span class="badge badge-green">✓ PAGADO</span>
                @elseif($reserva->estado_pago === 'anulada')
                    <span class="badge badge-red">✕ ANULADA</span>
                @else
                    <span class="badge badge-yellow">⏳ PAGO PENDIENTE</span>
                @endif
            </div>

            @if($reserva->estado_pago === 'pendiente' && $reserva->metodo_pago === 'Efectivo')
                <div class="mx-5 mt-4 p-3 rounded-xl bg-red-50 border border-red-300">
                    <p class="text-xs font-bold text-red-700 leading-snug">
                        ⚠ PAGO PENDIENTE: Tiene hasta 30 minutos antes de su hora de inicio
                        ({{ optional($reserva->expira_at)->format('d/m/Y H:i') }}) para pagar presencialmente,
                        de lo contrario su reserva será anulada.
                    </p>
                </div>
            @elseif($reserva->estado_pago === 'anulada')
                <div class="mx-5 mt-4 p-3 rounded-xl bg-red-50 border border-red-300">
                    <p class="text-xs font-bold text-red-700">
                        Esta reserva fue anulada por falta de pago. El horario fue liberado.
                    </p>
                </div>
            @endif

            <div class="text-center py-5">
                <p class="text-xs text-gray-400 uppercase tracking-widest">Código de validación</p>
                <p class="text-3xl font-extrabold text-green-700 tracking-wider">{{ $reserva->codigo_validacion }}</p>
            </div>

            <div class="flex justify-center pb-5">
                <div class="p-2 border border-gray-200 rounded-xl bg-white" style="width:240px;">
                    {!! $qrSvg !!}
                </div>
            </div>

            <div class="px-7 pb-6 text-sm">
                @foreach([
                    ['Cliente', $reserva->user->name ?? '—'],
                    ['Cancha', $reserva->horario->cancha->nombre ?? '—'],
                    ['Fecha', $reserva->horario->hora_inicio->format('d/m/Y')],
                    ['Horario', $reserva->horario->hora_inicio->format('H:i').' – '.$reserva->horario->hora_fin->format('H:i')],
                    ['Método de pago', $reserva->metodo_pago],
                ] as [$k, $v])
                    <div class="flex justify-between py-2 border-t border-gray-100">
                        <span class="text-gray-500">{{ $k }}</span>
                        <span class="font-semibold text-gray-800">{{ $v }}</span>
                    </div>
                @endforeach
                @if($reserva->numero_operacion)
                    <div class="flex justify-between py-2 border-t border-gray-100">
                        <span class="text-gray-500">N° operación</span>
                        <span class="font-semibold text-gray-800">{{ $reserva->numero_operacion }}</span>
                    </div>
                @endif
                <div class="flex justify-between py-2 border-t border-gray-100">
                    <span class="text-gray-500">Monto</span>
                    <span class="font-extrabold text-green-700 text-base">S/ {{ number_format($reserva->horario->tarifa->precio ?? 0, 2) }}</span>
                </div>
            </div>

            <div class="bg-green-50 text-center px-6 py-4 border-t border-dashed border-gray-200">
                <p class="text-xs text-green-800 font-medium">
                    Muestre este ticket digital en la recepción del establecimiento.
                </p>
            </div>
        </div>

        <div class="flex justify-center gap-3 mt-6">
            <a href="{{ route('reservas.ticket.pdf', $reserva) }}" class="btn-primary py-2 px-6">⬇ Descargar PDF</a>
            <a href="{{ route('reservas.index') }}" class="btn-outline-sm py-2 px-5">Mis reservas</a>
        </div>
    </div>
</x-app-layout>
