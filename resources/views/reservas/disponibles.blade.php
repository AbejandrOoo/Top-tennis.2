<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Reservar una cancha</h1>
            <p class="text-green-200 text-sm mt-0.5">Horarios disponibles en canchas operativas</p>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @include('partials.errores')

        @if($horarios->isEmpty())
            <div class="card p-12 text-center">
                <div class="text-4xl mb-3">🎾</div>
                <p class="text-gray-500 font-semibold">No hay horarios disponibles por ahora.</p>
                <p class="text-gray-400 text-sm mt-1">Vuelve más tarde o consulta con recepción.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($horarios as $horario)
                    <div class="card p-5 flex flex-col">
                        <div class="flex items-center justify-between mb-2">
                            <p class="font-bold text-gray-900">{{ $horario->cancha?->nombre ?? '—' }}</p>
                            <span class="badge badge-green">Disponible</span>
                        </div>
                        <p class="text-sm text-gray-500">{{ $horario->cancha?->tipo_superficie ?? '—' }} · {{ $horario->tarifa?->nombre_tarifa ?? '—' }}</p>
                        <p class="text-sm text-gray-700 mt-2 font-medium">
                            📅 {{ optional($horario->hora_inicio)->format('d/m/Y') ?? '—' }}
                            · 🕐 {{ optional($horario->hora_inicio)->format('H:i') ?? '—' }} – {{ optional($horario->hora_fin)->format('H:i') ?? '—' }}
                        </p>
                        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                            <span class="text-green-700 font-extrabold text-lg">S/ {{ number_format($horario->tarifa?->precio ?? 0, 2) }}</span>
                            <a href="{{ route('reservas.confirmar', $horario) }}" class="btn-primary">Reservar</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
