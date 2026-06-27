<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Confirmación y Pago</h1>
            <p class="text-green-200 text-sm mt-0.5">{{ $horario->cancha->nombre }} · {{ $horario->hora_inicio->format('d/m/Y H:i') }}</p>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ metodo: 'Yape' }">

        @include('partials.errores')

        {{-- Resumen --}}
        <div class="card p-6 mb-6">
            <h2 class="text-sm font-bold text-green-700 uppercase tracking-wide mb-4">Detalle de tu reserva</h2>
            <div class="grid grid-cols-2 gap-y-3 text-sm">
                <span class="text-gray-500">Cliente</span>
                <span class="font-semibold text-gray-800 text-right">{{ auth()->user()?->name ?? '—' }}</span>
                <span class="text-gray-500">Cancha</span>
                <span class="font-semibold text-gray-800 text-right">{{ $horario->cancha?->nombre ?? '—' }}</span>
                <span class="text-gray-500">Tarifa</span>
                <span class="font-semibold text-gray-800 text-right">{{ $horario->tarifa?->nombre_tarifa ?? '—' }}</span>
                <span class="text-gray-500">Fecha y hora</span>
                <span class="font-semibold text-gray-800 text-right">
                    {{ optional($horario->hora_inicio)->format('d/m/Y') ?? '—' }} · {{ optional($horario->hora_inicio)->format('H:i') ?? '—' }}–{{ optional($horario->hora_fin)->format('H:i') ?? '—' }}
                </span>
                <span class="text-gray-500 pt-3 border-t mt-1">Monto a pagar</span>
                <span class="text-green-700 font-extrabold text-lg text-right pt-3 border-t mt-1">
                    S/ {{ number_format($horario->tarifa?->precio ?? 0, 2) }}
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('reservas.store') }}">
            @csrf
            <input type="hidden" name="horario_id" value="{{ $horario->id }}">

            {{-- Método de pago --}}
            <div class="card p-6 mb-6">
                <h2 class="text-sm font-bold text-green-700 uppercase tracking-wide mb-4">Método de pago</h2>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="metodo_pago" value="Yape" x-model="metodo" class="hidden">
                        <div class="border-2 rounded-xl p-4 text-center transition"
                             :class="metodo === 'Yape' ? 'border-green-500 bg-green-50' : 'border-gray-200'">
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
            </div>

            {{-- Yape: QR + monto + operación --}}
            <div x-show="metodo === 'Yape'" x-cloak class="card p-6 mb-6 text-center">
                <p class="text-sm text-gray-600 mb-3">Escanea el QR con tu app de <strong>Yape</strong> o <strong>Plin</strong></p>
                <img src="{{ asset('images/yape.png') }}" alt="QR Yape/Plin"
                     class="mx-auto w-56 h-56 object-contain rounded-xl border border-gray-200 bg-white">
                <div class="mt-4 inline-block bg-green-600 text-white font-extrabold px-5 py-2 rounded-full">
                    Monto a Yapear: S/ {{ number_format($horario->tarifa?->precio ?? 0, 2) }}
                </div>
                <div class="mt-6 text-left">
                    <label for="numero_operacion" class="form-label">Número de operación</label>
                    <input id="numero_operacion" name="numero_operacion" type="text" maxlength="50"
                           class="form-input {{ $errors->has('numero_operacion') ? 'border-red-400' : '' }}"
                           value="{{ old('numero_operacion') }}" placeholder="Ej: 01234567">
                    @error('numero_operacion')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Efectivo --}}
            <div x-show="metodo === 'Efectivo'" x-cloak class="card p-6 mb-6">
                <div class="flex items-start gap-3 bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <span class="text-xl">⚠</span>
                    <p class="text-sm text-yellow-800">
                        Tu reserva quedará <strong>confirmada con pago pendiente</strong>.
                        Paga <strong>S/ {{ number_format($horario->tarifa?->precio ?? 0, 2) }}</strong> en recepción presentando tu ticket.
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('reservas.disponibles') }}" class="btn-outline-sm py-2 px-5">Cancelar</a>
                <button type="submit" class="btn-primary py-2 px-6">
                    <span x-show="metodo === 'Yape'">Confirmar pago</span>
                    <span x-show="metodo === 'Efectivo'" x-cloak>Confirmar reserva</span>
                </button>
            </div>
        </form>
    </div>

    <style>[x-cloak]{display:none!important}</style>
</x-app-layout>
