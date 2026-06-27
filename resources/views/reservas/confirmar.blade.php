<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-green-300 text-xs font-semibold uppercase tracking-widest mb-0.5">Último paso</p>
            <h1 class="text-2xl font-black text-white">Confirmar y pagar</h1>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ metodo: 'Yape' }">

        @include('partials.errores')

        {{-- Resumen de la reserva --}}
        <div class="card p-6 mb-5">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#f0fdf4;">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Detalle de tu reserva</h2>
            </div>

            <div class="space-y-0">
                @php
                $detalles = [
                    ['Cliente',      auth()->user()?->name ?? '—'],
                    ['Cancha',       $horario->cancha?->nombre ?? '—'],
                    ['Tarifa',       $horario->tarifa?->nombre_tarifa ?? '—'],
                    ['Fecha',        optional($horario->hora_inicio)->format('d/m/Y') ?? '—'],
                    ['Horario',      (optional($horario->hora_inicio)->format('H:i') ?? '—').' – '.(optional($horario->hora_fin)->format('H:i') ?? '—')],
                ];
                @endphp

                @foreach($detalles as [$k, $v])
                    <div class="flex justify-between items-center py-3"
                         style="border-bottom: 1px solid #f0f2f0;">
                        <span class="text-sm text-gray-400 font-medium">{{ $k }}</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $v }}</span>
                    </div>
                @endforeach

                <div class="flex justify-between items-center pt-4 mt-1">
                    <span class="text-sm font-bold text-gray-700">Total a pagar</span>
                    <span class="text-2xl font-black" style="color:#15803d;">
                        S/ {{ number_format($horario->tarifa?->precio ?? 0, 2) }}
                    </span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('reservas.store') }}">
            @csrf
            <input type="hidden" name="horario_id" value="{{ $horario->id }}">

            {{-- Selector método de pago --}}
            <div class="card p-6 mb-5">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#eff6ff;">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Método de pago</h2>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="metodo_pago" value="Yape" x-model="metodo" class="hidden">
                        <div class="rounded-xl p-4 text-center transition-all duration-200"
                             :class="metodo === 'Yape'
                                 ? 'border-2 border-green-500 bg-green-50 shadow-sm'
                                 : 'border-2 border-gray-100 bg-gray-50 hover:border-gray-200'">
                            <div class="w-10 h-10 rounded-xl mx-auto mb-2.5 flex items-center justify-center"
                                 :class="metodo === 'Yape' ? 'bg-green-100' : 'bg-white'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     :stroke="metodo === 'Yape' ? '#15803d' : '#9ca3af'">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="font-bold text-sm" :class="metodo === 'Yape' ? 'text-green-800' : 'text-gray-700'">
                                Yape / Plin
                            </p>
                            <p class="text-xs mt-0.5" :class="metodo === 'Yape' ? 'text-green-600' : 'text-gray-400'">
                                Pago inmediato
                            </p>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="metodo_pago" value="Efectivo" x-model="metodo" class="hidden">
                        <div class="rounded-xl p-4 text-center transition-all duration-200"
                             :class="metodo === 'Efectivo'
                                 ? 'border-2 border-amber-400 bg-amber-50 shadow-sm'
                                 : 'border-2 border-gray-100 bg-gray-50 hover:border-gray-200'">
                            <div class="w-10 h-10 rounded-xl mx-auto mb-2.5 flex items-center justify-center"
                                 :class="metodo === 'Efectivo' ? 'bg-amber-100' : 'bg-white'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     :stroke="metodo === 'Efectivo' ? '#92400e' : '#9ca3af'">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <p class="font-bold text-sm" :class="metodo === 'Efectivo' ? 'text-amber-800' : 'text-gray-700'">
                                Efectivo
                            </p>
                            <p class="text-xs mt-0.5" :class="metodo === 'Efectivo' ? 'text-amber-600' : 'text-gray-400'">
                                Pagas en recepción
                            </p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Sección Yape --}}
            <div x-show="metodo === 'Yape'" x-cloak class="card p-6 mb-5">
                <p class="text-sm text-gray-500 text-center mb-4">
                    Escanea el código QR con tu app <strong class="text-gray-700">Yape</strong> o <strong class="text-gray-700">Plin</strong>
                </p>
                <div class="flex justify-center mb-4">
                    <div class="p-3 rounded-2xl border-2 border-gray-100 bg-white inline-block">
                        <img src="{{ asset('images/yape.png') }}" alt="QR Yape/Plin"
                             class="w-48 h-48 object-contain rounded-xl">
                    </div>
                </div>
                <div class="text-center mb-5">
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-bold text-white"
                          style="background:#15803d;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1"/>
                        </svg>
                        Yapear S/ {{ number_format($horario->tarifa?->precio ?? 0, 2) }}
                    </span>
                </div>
                <div>
                    <label for="numero_operacion" class="form-label">
                        Número de operación <span class="text-red-500">*</span>
                    </label>
                    <input id="numero_operacion" name="numero_operacion" type="text" maxlength="50"
                           class="form-input {{ $errors->has('numero_operacion') ? 'border-red-400' : '' }}"
                           value="{{ old('numero_operacion') }}"
                           placeholder="Ej: 01234567">
                    @error('numero_operacion')
                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- Sección Efectivo --}}
            <div x-show="metodo === 'Efectivo'" x-cloak class="mb-5">
                <div class="rounded-xl p-4 flex items-start gap-3"
                     style="background:#fffbeb; border:1px solid #fcd34d;">
                    <svg class="w-5 h-5 shrink-0 mt-0.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-bold text-amber-800">Reserva con pago pendiente</p>
                        <p class="text-sm text-amber-700 mt-0.5">
                            Paga <strong>S/ {{ number_format($horario->tarifa?->precio ?? 0, 2) }}</strong> en recepción presentando tu ticket.
                            Tienes hasta <strong>30 minutos antes</strong> del inicio para pagar o la reserva se cancelará.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-between gap-3 pt-2">
                <a href="{{ route('reservas.disponibles') }}" class="btn-outline-sm py-2.5 px-5">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver
                </a>
                <button type="submit" class="btn-primary py-2.5 px-7 text-base">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                    <span x-show="metodo === 'Yape'">Confirmar pago</span>
                    <span x-show="metodo === 'Efectivo'" x-cloak>Confirmar reserva</span>
                </button>
            </div>
        </form>
    </div>

    <style>[x-cloak]{display:none!important}</style>
</x-app-layout>
