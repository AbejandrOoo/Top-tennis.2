<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Reservar una cancha</h1>
            <p class="text-green-200 text-sm mt-0.5">Horarios disponibles en canchas operativas</p>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        @include('partials.errores')

        @if($horarios->isEmpty())
            <div class="card p-16 text-center">
                <div class="text-5xl mb-4">🎾</div>
                <p class="text-gray-600 font-bold text-lg">No hay horarios disponibles por ahora.</p>
                <p class="text-gray-400 text-sm mt-1">Vuelve más tarde o consulta con recepción.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($horarios as $horario)
                    @php
                        $cancha   = $horario->cancha;
                        $tarifa   = $horario->tarifa;
                        $precio   = $tarifa?->precio ?? 0;
                        $sup      = $cancha?->tipo_superficie ?? '—';
                        $iconoSup = match($sup) {
                            'Arcilla'   => '🟤',
                            'Sintética' => '🟢',
                            'Hierba'    => '🌱',
                            'Dura'      => '⬜',
                            default     => '🎾',
                        };
                    @endphp

                    <div class="rounded-2xl overflow-hidden shadow-md bg-white flex flex-col hover:shadow-xl transition-shadow duration-300">

                        {{-- Imagen con badge de precio --}}
                        <div class="relative h-48 sm:h-52 overflow-hidden">
                            <img src="{{ $cancha?->imagenUrl() ?? asset('images/Arcilla.jpeg') }}"
                                 alt="{{ $cancha?->nombre ?? 'Cancha' }}"
                                 class="w-full h-full object-cover">

                            {{-- Overlay oscuro sutil en la base --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>

                            {{-- Badge precio --}}
                            <div class="absolute top-3 right-3">
                                <span class="bg-green-500 text-white text-sm font-extrabold px-3 py-1.5 rounded-full shadow-md">
                                    S/. {{ number_format($precio, 2) }}
                                </span>
                            </div>

                            {{-- Nombre de la cancha sobre la imagen --}}
                            <div class="absolute bottom-3 left-4 right-12">
                                <p class="text-white text-xs font-medium drop-shadow-sm">
                                    {{ $tarifa?->nombre_tarifa ?? 'Tarifa' }}
                                </p>
                            </div>
                        </div>

                        {{-- Contenido de la card --}}
                        <div class="flex flex-col flex-1 px-5 pt-4 pb-5 gap-3">

                            {{-- Nombre cancha --}}
                            <h3 class="text-xl font-extrabold text-gray-900 leading-tight">
                                {{ $cancha?->nombre ?? '—' }}
                            </h3>

                            {{-- Superficie + horario --}}
                            <div class="space-y-1 text-sm text-gray-500">
                                <p>
                                    {{ $iconoSup }} <span class="font-medium text-gray-700">{{ $sup }}</span>
                                    &nbsp;·&nbsp;
                                    💡 <span class="font-medium text-gray-700">Con iluminación</span>
                                </p>
                                <p>
                                    📅 <span class="font-medium text-gray-700">
                                        {{ optional($horario->hora_inicio)->format('d/m/Y') ?? '—' }}
                                    </span>
                                    &nbsp;·&nbsp;
                                    🕐 {{ optional($horario->hora_inicio)->format('H:i') ?? '—' }}
                                       – {{ optional($horario->hora_fin)->format('H:i') ?? '—' }}
                                </p>
                            </div>

                            <div class="flex-1"></div>

                            {{-- Botón RESERVAR AHORA --}}
                            <a href="{{ route('reservas.confirmar', $horario) }}"
                               class="block w-full text-center border-2 border-green-600 text-green-700 font-extrabold text-sm py-3 rounded-xl hover:bg-green-600 hover:text-white transition-all duration-200 tracking-wide">
                                RESERVAR AHORA &nbsp;→
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
