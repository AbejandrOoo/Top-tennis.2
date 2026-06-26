<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Confirmación y Pago</h1>
            <p class="text-green-200 text-sm mt-0.5">Reserva #{{ $horario->id }} · {{ $horario->cancha->nombre ?? '—' }}</p>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ metodo: 'Yape/Plin' }">

        {{-- Resumen de la reserva --}}
        <div class="card p-6 mb-6">
            <h2 class="text-sm font-bold text-green-700 uppercase tracking-wide mb-4">Detalle de tu reserva</h2>
            <div class="grid grid-cols-2 gap-y-3 text-sm">
                <span class="text-gray-500">Cliente</span>
                <span class="font-semibold text-gray-800 text-right">{{ $horario->user->name ?? '—' }}</span>

                <span class="text-gray-500">Cancha</span>
                <span class="font-semibold text-gray-800 text-right">{{ $horario->cancha->nombre ?? '—' }}</span>

                <span class="text-gray-500">Fecha</span>
                <span class="font-semibold text-gray-800 text-right">{{ optional($horario->fecha)->format('d/m/Y') }}</span>

                <span class="text-gray-500">Horario</span>
                <span class="font-semibold text-gray-800 text-right">
                    {{ substr($horario->hora_inicio, 0, 5) }} – {{ substr($horario->hora_fin, 0, 5) }}
                </span>

                <span class="text-gray-500 pt-3 border-t mt-1">Monto a pagar</span>
                <span class="text-green-700 font-extrabold text-lg text-right pt-3 border-t mt-1">
                    S/ {{ number_format((float) $monto, 2) }}
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('pagos.procesar') }}">
            @csrf
            <input type="hidden" name="horario_id" value="{{ $horario->id }}">

            {{-- Selector de método de pago --}}
            <div class="card p-6 mb-6">
                <h2 class="text-sm font-bold text-green-700 uppercase tracking-wide mb-4">Método de pago</h2>

                <div class="grid grid-cols-2 gap-3 mb-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="metodo_pago" value="Yape/Plin" x-model="metodo" class="hidden">
                        <div class="border-2 rounded-xl p-4 text-center transition"
                             :class="metodo === 'Yape/Plin' ? 'border-green-500 bg-green-50' : 'border-gray-200'">
                            <div class="text-2xl mb-1">📱</div>
                            <div class="font-bold text-gray-800">Yape / Plin</div>
                            <div class="text-xs text-gray-500">Pago inmediato por QR</div>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="metodo_pago" value="Efectivo" x-model="metodo" class="hidden">
                        <div class="border-2 rounded-xl p-4 text-center transition"
                             :class="metodo === 'Efectivo' ? 'border-green-500 bg-green-50' : 'border-gray-200'">
                            <div class="text-2xl mb-1">💵</div>
                            <div class="font-bold text-gray-800">Efectivo</div>
                            <div class="text-xs text-gray-500">Pagas en recepción</div>
                        </div>
                    </label>
                </div>
                @error('metodo_pago')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Bloque Yape/Plin: QR + monto + número de operación --}}
            <div x-show="metodo === 'Yape/Plin'" x-cloak class="card p-6 mb-6 text-center">
                <p class="text-sm text-gray-600 mb-3">Escanea el QR con tu app de <strong>Yape</strong> o <strong>Plin</strong></p>
                <img src="{{ asset('images/yape.png') }}" alt="QR de pago Yape/Plin"
                     class="mx-auto w-56 h-56 object-contain rounded-xl border border-gray-200 bg-white">

                <div class="mt-4 inline-block bg-green-600 text-white font-extrabold px-5 py-2 rounded-full">
                    Monto a Yapear: S/ {{ number_format((float) $monto, 2) }}
                </div>

                <div class="mt-6 text-left">
                    <label for="numero_operacion" class="form-label">Número de operación <span class="font-normal text-gray-400">(de tu Yape/Plin)</span></label>
                    <input id="numero_operacion" name="numero_operacion" type="text" maxlength="50"
                           class="form-input {{ $errors->has('numero_operacion') ? 'border-red-400' : '' }}"
                           value="{{ old('numero_operacion') }}" placeholder="Ej: 01234567">
                    @error('numero_operacion')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Bloque Efectivo --}}
            <div x-show="metodo === 'Efectivo'" x-cloak class="card p-6 mb-6">
                <div class="flex items-start gap-3 bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <span class="text-xl">⚠</span>
                    <p class="text-sm text-yellow-800">
                        Tu reserva quedará <strong>confirmada con pago pendiente</strong>.
                        Acércate a recepción y paga <strong>S/ {{ number_format((float) $monto, 2) }}</strong> presentando tu ticket digital.
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('dashboard') }}" class="btn-outline-sm py-2 px-5">Cancelar</a>
                <button type="submit" class="btn-primary py-2 px-6">
                    <span x-show="metodo === 'Yape/Plin'">Confirmar pago</span>
                    <span x-show="metodo === 'Efectivo'" x-cloak>Confirmar reserva</span>
                </button>
            </div>
        </form>
    </div>

    <style>[x-cloak]{display:none!important}</style>
</x-app-layout>
