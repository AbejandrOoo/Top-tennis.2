<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Ticket de Reserva</h1>
            <p class="text-green-200 text-sm mt-0.5">Comprobante digital · {{ $pago->codigo_validacion }}</p>
        </div>
    </x-slot>

    <div class="max-w-md mx-auto px-4 py-8">

        {{-- Ticket --}}
        <div class="card overflow-hidden">
            {{-- Cabecera verde con el logo oficial (pelota de tenis) --}}
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

            {{-- Estado del pago --}}
            <div class="text-center py-3 border-b border-dashed border-gray-200">
                @if($pago->estado === 'Pagado')
                    <span class="badge badge-green">✓ PAGADO</span>
                @else
                    <span class="badge badge-yellow">⏳ PENDIENTE EN RECEPCIÓN</span>
                @endif
            </div>

            {{-- Código de validación --}}
            <div class="text-center py-5">
                <p class="text-xs text-gray-400 uppercase tracking-widest">Código de validación</p>
                <p class="text-3xl font-extrabold text-green-700 tracking-wider">{{ $pago->codigo_validacion }}</p>
            </div>

            {{-- QR --}}
            <div class="flex justify-center pb-5">
                <div class="p-2 border border-gray-200 rounded-xl bg-white" style="width:240px;">
                    {!! $qrSvg !!}
                </div>
            </div>

            {{-- Datos --}}
            <div class="px-7 pb-6 text-sm">
                <div class="flex justify-between py-2 border-t border-gray-100">
                    <span class="text-gray-500">Cliente</span>
                    <span class="font-semibold text-gray-800">{{ $pago->horario->user->name ?? '—' }}</span>
                </div>
                <div class="flex justify-between py-2 border-t border-gray-100">
                    <span class="text-gray-500">Cancha</span>
                    <span class="font-semibold text-gray-800">{{ $pago->horario->cancha->nombre ?? '—' }}</span>
                </div>
                <div class="flex justify-between py-2 border-t border-gray-100">
                    <span class="text-gray-500">Fecha</span>
                    <span class="font-semibold text-gray-800">{{ optional($pago->horario->fecha)->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between py-2 border-t border-gray-100">
                    <span class="text-gray-500">Horario</span>
                    <span class="font-semibold text-gray-800">
                        {{ substr($pago->horario->hora_inicio, 0, 5) }} – {{ substr($pago->horario->hora_fin, 0, 5) }}
                    </span>
                </div>
                <div class="flex justify-between py-2 border-t border-gray-100">
                    <span class="text-gray-500">Método de pago</span>
                    <span class="font-semibold text-gray-800">{{ $pago->metodo_pago }}</span>
                </div>
                @if($pago->numero_operacion)
                    <div class="flex justify-between py-2 border-t border-gray-100">
                        <span class="text-gray-500">N° operación</span>
                        <span class="font-semibold text-gray-800">{{ $pago->numero_operacion }}</span>
                    </div>
                @endif
                <div class="flex justify-between py-2 border-t border-gray-100">
                    <span class="text-gray-500">Monto</span>
                    <span class="font-extrabold text-green-700 text-base">S/ {{ number_format((float) $pago->monto, 2) }}</span>
                </div>
            </div>

            {{-- Pie --}}
            <div class="bg-green-50 text-center px-6 py-4 border-t border-dashed border-gray-200">
                <p class="text-xs text-green-800 font-medium">
                    Muestre este ticket digital en la recepción del establecimiento.
                </p>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="flex justify-center gap-3 mt-6">
            <a href="{{ route('pagos.ticket.pdf', $pago) }}" class="btn-primary py-2 px-6">⬇ Descargar PDF</a>
            <a href="{{ route('dashboard') }}" class="btn-outline-sm py-2 px-5">Volver al inicio</a>
        </div>
    </div>
</x-app-layout>
