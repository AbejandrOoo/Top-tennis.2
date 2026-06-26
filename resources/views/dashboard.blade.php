<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Panel</h1>
            <p class="text-green-200 text-sm mt-0.5">
                Hola, {{ Auth::user()->name }} · {{ ucfirst(Auth::user()->rol->value ?? 'usuario') }}
            </p>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if($esStaff)
            {{-- ===== Panel Admin / Recepción ===== --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
                @foreach([
                    ['Canchas', $stats['canchas'], $stats['canchas_operativas'].' operativas'],
                    ['Tarifas', $stats['tarifas'], 'configuradas'],
                    ['Horarios disponibles', $stats['horarios_disp'], 'para reservar'],
                    ['Reservas', $stats['reservas'], 'en total'],
                ] as [$lbl, $num, $sub])
                    <div class="card p-5">
                        <p class="text-3xl font-extrabold text-gray-900">{{ $num }}</p>
                        <p class="text-sm font-semibold text-gray-700 mt-1">{{ $lbl }}</p>
                        <p class="text-xs text-gray-400">{{ $sub }}</p>
                    </div>
                @endforeach

                <div class="card p-5 md:col-span-2" style="background:linear-gradient(135deg,#0d3d22,#14532d);">
                    <p class="text-green-200 text-xs uppercase tracking-wide">Ingresos confirmados</p>
                    <p class="text-3xl font-extrabold text-white mt-1">S/ {{ number_format($stats['ingresos'], 2) }}</p>
                    <a href="{{ route('reservas.index') }}" class="text-green-300 text-xs hover:underline mt-2 inline-block">Ver dashboard financiero →</a>
                </div>
            </div>

            <h2 class="text-sm font-bold text-green-700 uppercase tracking-wide mb-3">Gestión</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach([
                    ['Canchas', 'canchas.index', '🎾'],
                    ['Tarifas', 'tarifas.index', '💲'],
                    ['Horarios', 'horarios.index', '🗓️'],
                    ['Reservas', 'reservas.index', '🎫'],
                ] as [$lbl, $ruta, $icon])
                    <a href="{{ route($ruta) }}" class="card p-6 text-center hover:shadow-md transition-shadow">
                        <div class="text-3xl mb-2">{{ $icon }}</div>
                        <p class="font-bold text-gray-800">{{ $lbl }}</p>
                    </a>
                @endforeach
            </div>

        @else
            {{-- ===== Panel Cliente ===== --}}
            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="card p-6">
                    <p class="text-3xl font-extrabold text-green-700">{{ $stats['disponibles'] }}</p>
                    <p class="text-sm font-semibold text-gray-700 mt-1">Horarios disponibles</p>
                </div>
                <div class="card p-6">
                    <p class="text-3xl font-extrabold text-gray-900">{{ $stats['mis_reservas'] }}</p>
                    <p class="text-sm font-semibold text-gray-700 mt-1">Mis reservas</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('reservas.disponibles') }}" class="card p-8 text-center hover:shadow-md transition-shadow">
                    <div class="text-4xl mb-3">🎾</div>
                    <p class="font-extrabold text-gray-900 text-lg">Reservar una cancha</p>
                    <p class="text-sm text-gray-400 mt-1">Mira los horarios disponibles y paga con Yape o efectivo</p>
                </a>
                <a href="{{ route('reservas.index') }}" class="card p-8 text-center hover:shadow-md transition-shadow">
                    <div class="text-4xl mb-3">🎫</div>
                    <p class="font-extrabold text-gray-900 text-lg">Mis reservas</p>
                    <p class="text-sm text-gray-400 mt-1">Consulta tus tickets y el estado de tus pagos</p>
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
