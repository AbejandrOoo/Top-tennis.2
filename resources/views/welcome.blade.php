<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Top Tennis — Club de Tenis</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    {{-- Navbar --}}
    <nav class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <span class="text-xl font-bold text-indigo-700 tracking-tight">🎾 Top Tennis</span>
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                        Ir al Panel
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="text-sm text-gray-600 hover:text-indigo-600">
                        Iniciar sesión
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                            Registrarse
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="bg-indigo-700 text-white py-24 px-6 text-center">
        <h1 class="text-4xl font-bold mb-4">Reservá tu cancha en segundos</h1>
        <p class="text-indigo-200 text-lg mb-8 max-w-xl mx-auto">
            Top Tennis es el sistema de reservas del club. Consultá disponibilidad,
            elegí tu turno y asegurá tu horario sin llamadas ni filas.
        </p>
        <div class="flex justify-center gap-4 flex-wrap">
            @auth
                <a href="{{ route('horarios.create') }}"
                   class="px-6 py-3 bg-white text-indigo-700 font-semibold rounded-md hover:bg-indigo-50">
                    Hacer una reserva
                </a>
                <a href="{{ route('canchas.index') }}"
                   class="px-6 py-3 border border-white text-white rounded-md hover:bg-indigo-600">
                    Ver canchas
                </a>
            @else
                <a href="{{ route('register') }}"
                   class="px-6 py-3 bg-white text-indigo-700 font-semibold rounded-md hover:bg-indigo-50">
                    Crear cuenta gratis
                </a>
                <a href="{{ route('login') }}"
                   class="px-6 py-3 border border-white text-white rounded-md hover:bg-indigo-600">
                    Iniciar sesión
                </a>
            @endauth
        </div>
    </section>

    {{-- Características --}}
    <section class="py-20 px-6 max-w-6xl mx-auto">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-12">¿Qué podés hacer en el club?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="text-4xl mb-4">🏟️</div>
                <h3 class="font-semibold text-lg mb-2">Canchas disponibles</h3>
                <p class="text-gray-500 text-sm">
                    Contamos con canchas de arcilla y sintética. Consultá el estado
                    en tiempo real antes de reservar.
                </p>
                <a href="{{ route('canchas.index') }}" class="mt-4 inline-block text-indigo-600 text-sm hover:underline">
                    Ver canchas →
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="text-4xl mb-4">💰</div>
                <h3 class="font-semibold text-lg mb-2">Tarifas por turno</h3>
                <p class="text-gray-500 text-sm">
                    Precios diferenciados por mañana, tarde y noche. Encontrá
                    el horario que mejor se adapte a tu bolsillo.
                </p>
                <a href="{{ route('tarifas.index') }}" class="mt-4 inline-block text-indigo-600 text-sm hover:underline">
                    Ver tarifas →
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="text-4xl mb-4">📅</div>
                <h3 class="font-semibold text-lg mb-2">Reservá tu horario</h3>
                <p class="text-gray-500 text-sm">
                    Elegí cancha, turno y fecha. El sistema verifica disponibilidad
                    automáticamente para evitar conflictos.
                </p>
                @auth
                    <a href="{{ route('horarios.create') }}" class="mt-4 inline-block text-indigo-600 text-sm hover:underline">
                        Reservar ahora →
                    </a>
                @else
                    <a href="{{ route('register') }}" class="mt-4 inline-block text-indigo-600 text-sm hover:underline">
                        Registrate para reservar →
                    </a>
                @endauth
            </div>

        </div>
    </section>

    {{-- Tipos de superficie --}}
    <section class="bg-white py-16 px-6">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-10">Nuestras superficies</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="border border-gray-200 rounded-lg p-6 flex gap-4 items-start">
                    <span class="text-3xl">🟤</span>
                    <div>
                        <h3 class="font-semibold text-lg mb-1">Arcilla</h3>
                        <p class="text-gray-500 text-sm">
                            Superficie clásica ideal para juego de fondo de cancha.
                            Menor impacto en articulaciones y mayor agarre de la pelota.
                        </p>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-6 flex gap-4 items-start">
                    <span class="text-3xl">🔵</span>
                    <div>
                        <h3 class="font-semibold text-lg mb-1">Sintética</h3>
                        <p class="text-gray-500 text-sm">
                            Rápida y consistente en cualquier clima. Perfecta para
                            jugadores que buscan velocidad y un bote predecible.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- CTA final solo para visitantes --}}
    @guest
    <section class="bg-indigo-700 text-white py-16 px-6 text-center">
        <h2 class="text-2xl font-bold mb-3">¿Listo para jugar?</h2>
        <p class="text-indigo-200 mb-6">Creá tu cuenta y empezá a reservar en minutos.</p>
        <a href="{{ route('register') }}"
           class="px-8 py-3 bg-white text-indigo-700 font-semibold rounded-md hover:bg-indigo-50">
            Registrarse gratis
        </a>
    </section>
    @endguest

    {{-- Footer --}}
    <footer class="bg-gray-800 text-gray-400 text-sm text-center py-6">
        © {{ date('Y') }} Top Tennis. Todos los derechos reservados.
    </footer>

</body>
</html>
