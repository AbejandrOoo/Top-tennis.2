<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Top Tennis Digital — Reservá tu cancha en segundos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#22c55e',
                        'primary-dark': '#16a34a',
                        'primary-darker': '#15803d',
                        'court-dark': '#0f4c2a',
                        'court-mid': '#166534',
                        'court-light': '#1a7a3c',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: ui-sans-serif, system-ui, sans-serif; }

        /* Cancha de tenis SVG */
        .court-container {
            background: linear-gradient(160deg, #0f4c2a 0%, #166534 100%);
            border-radius: 1rem;
            position: relative;
            overflow: hidden;
            padding: 2.5rem 2rem;
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .court-lines {
            width: 100%;
            max-width: 340px;
            aspect-ratio: 16/9;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
            font-size: 0.8rem;
            font-family: monospace;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            font-weight: 500;
            letter-spacing: 0.01em;
        }
        .btn-primary {
            background: #22c55e;
            color: #fff;
            padding: 0.75rem 1.75rem;
            border-radius: 0.5rem;
            font-weight: 700;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn-primary:hover { background: #16a34a; }
        .btn-outline {
            background: #fff;
            color: #1f2937;
            border: 1.5px solid #d1d5db;
            padding: 0.75rem 1.75rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            transition: border-color 0.2s, background 0.2s;
        }
        .btn-outline:hover { border-color: #22c55e; background: #f0fdf4; }
        .feature-icon {
            width: 3rem;
            height: 3rem;
            background: #22c55e;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .floating-card {
            position: absolute;
            bottom: 1.5rem;
            right: 1rem;
            background: #fff;
            border-radius: 1rem;
            padding: 0.75rem 1rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.18);
            display: flex;
            align-items: center;
            gap: 0.6rem;
            min-width: 190px;
        }
        .floating-icon {
            width: 2.25rem;
            height: 2.25rem;
            background: #22c55e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            pointer-events: none;
            opacity: 0.25;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    {{-- ===== NAVBAR ===== --}}
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">

            {{-- Logo --}}
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full border-4 border-primary flex items-center justify-center">
                    <div class="w-3 h-3 rounded-full bg-primary"></div>
                </div>
                <span class="font-bold text-sm tracking-widest uppercase text-gray-900">Top Tennis Digital</span>
            </div>

            {{-- Acciones --}}
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="btn-primary text-sm py-2 px-5">
                        Ir al Panel
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="btn-outline text-sm py-2 px-5">
                        Iniciar Sesión
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="btn-primary text-sm py-2 px-5">
                            Registrarse Gratis
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    {{-- ===== HERO ===== --}}
    <section class="max-w-6xl mx-auto px-6 py-20 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

        {{-- Texto --}}
        <div>
            <span class="badge mb-5 block w-fit">Sistema de reservas deportivas</span>

            <h1 class="text-5xl font-extrabold leading-tight text-gray-900 mb-5">
                Reserva tu cancha<br>
                de tenis <span class="text-primary">en segundos</span>
            </h1>

            <p class="text-gray-500 text-lg mb-8 leading-relaxed">
                Gestiona tus turnos de manera fácil, rápida y segura.
                Canchas de arcilla y sintética disponibles para todos los turnos.
            </p>

            <div class="flex flex-wrap gap-3 mb-10">
                @auth
                    <a href="{{ route('horarios.create') }}" class="btn-primary">
                        Hacer una reserva &rsaquo;
                    </a>
                    <a href="{{ route('canchas.index') }}" class="btn-outline">
                        Ver canchas
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn-primary">
                        Crear cuenta gratis &rsaquo;
                    </a>
                    <a href="{{ route('login') }}" class="btn-outline">
                        Ya tengo cuenta
                    </a>
                @endauth
            </div>

            {{-- Stats --}}
            <div class="flex gap-10">
                <div>
                    <p class="text-3xl font-extrabold text-gray-900">3</p>
                    <p class="text-sm text-gray-400 mt-0.5">Canchas</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-gray-900">7d</p>
                    <p class="text-sm text-gray-400 mt-0.5">Por semana</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-gray-900">100%</p>
                    <p class="text-sm text-gray-400 mt-0.5">Online</p>
                </div>
            </div>
        </div>

        {{-- Ilustración cancha --}}
        <div class="relative">
            <div class="court-container shadow-2xl">

                {{-- Marca de agua texto --}}
                <div class="watermark">
                    <p class="text-white text-xs font-bold tracking-widest uppercase">Top Tennis Digital</p>
                    <p class="text-white text-[10px] tracking-widest uppercase">Reserva tu cancha en segundos</p>
                </div>

                {{-- SVG de la cancha --}}
                <svg class="court-lines" viewBox="0 0 340 190" fill="none" xmlns="http://www.w3.org/2000/svg">
                    {{-- Fondo de la cancha --}}
                    <rect x="10" y="10" width="320" height="170" fill="#1a7a3c" rx="2"/>
                    {{-- Borde exterior --}}
                    <rect x="10" y="10" width="320" height="170" stroke="white" stroke-width="2.5" fill="none"/>
                    {{-- Línea central vertical --}}
                    <line x1="170" y1="10" x2="170" y2="180" stroke="white" stroke-width="2"/>
                    {{-- Línea de servicio izquierda --}}
                    <line x1="70" y1="10" x2="70" y2="180" stroke="white" stroke-width="1.5"/>
                    {{-- Línea de servicio derecha --}}
                    <line x1="270" y1="10" x2="270" y2="180" stroke="white" stroke-width="1.5"/>
                    {{-- Línea horizontal central --}}
                    <line x1="70" y1="95" x2="270" y2="95" stroke="white" stroke-width="1.5"/>
                    {{-- Línea T izquierda --}}
                    <line x1="170" y1="45" x2="170" y2="145" stroke="white" stroke-width="1.5"/>
                    {{-- Pelota de tenis --}}
                    <circle cx="100" cy="95" r="9" fill="#a3e635"/>
                    <path d="M93 90 Q100 98 107 90" stroke="white" stroke-width="1.2" fill="none"/>
                    <path d="M93 100 Q100 92 107 100" stroke="white" stroke-width="1.2" fill="none"/>
                    {{-- Posts de la red --}}
                    <rect x="167" y="5" width="6" height="180" fill="#15803d"/>
                    <rect x="165" y="90" width="10" height="4" fill="#d1fae5"/>
                </svg>

                {{-- Floatcard reserva confirmada --}}
                <div class="floating-card">
                    <div class="floating-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M20 6L9 17l-5-5" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Reserva confirmada</p>
                        <p class="text-sm font-bold text-gray-900">Cancha A · 10:00</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ===== FEATURES ===== --}}
    <section class="bg-white py-20 px-6">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-14">
                <h2 class="text-3xl font-extrabold text-gray-900 mb-3">Todo lo que necesitás para jugar</h2>
                <p class="text-gray-400 text-base">Una plataforma completa de gestión de canchas deportivas</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="bg-gray-50 rounded-2xl p-6">
                    <div class="feature-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="4" width="18" height="18" rx="2" stroke="white" stroke-width="2"/>
                            <path d="M16 2v4M8 2v4M3 10h18" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Reservas en segundos</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Elegí cancha, turno y fecha. El sistema confirma disponibilidad en tiempo real.
                    </p>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6">
                    <div class="feature-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="9" stroke="white" stroke-width="2"/>
                            <path d="M12 7v5l3 3" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Disponibilidad en tiempo real</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Consultá qué canchas están libres antes de reservar, sin llamar a recepción.
                    </p>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6">
                    <div class="feature-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                            <path d="M12 2L3 7v5c0 5.25 3.75 10.15 9 11.25C17.25 22.15 21 17.25 21 12V7L12 2z" stroke="white" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Sistema seguro</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Control de acceso por roles: Admin, Recepcionista y Cliente con permisos diferenciados.
                    </p>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6">
                    <div class="feature-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                            <circle cx="9" cy="7" r="3" stroke="white" stroke-width="2"/>
                            <circle cx="15" cy="7" r="3" stroke="white" stroke-width="2"/>
                            <path d="M3 20c0-3.3 2.7-6 6-6h6c3.3 0 6 2.7 6 6" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Turnos Mañana, Tarde y Noche</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Tarifas diferenciadas por turno para que encuentres el horario ideal.
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- ===== SUPERFICIES ===== --}}
    <section class="max-w-6xl mx-auto px-6 py-20">
        <h2 class="text-2xl font-extrabold text-center text-gray-900 mb-10">Nuestras superficies</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-white border border-gray-100 rounded-2xl p-6 flex gap-4 items-start shadow-sm hover:shadow-md transition-shadow">
                <div class="feature-icon shrink-0">
                    <span class="text-white text-lg">🟤</span>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-gray-900 mb-1">Arcilla</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Superficie clásica ideal para juego de fondo de cancha.
                        Menor impacto en articulaciones y mayor agarre de la pelota.
                    </p>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-6 flex gap-4 items-start shadow-sm hover:shadow-md transition-shadow">
                <div class="feature-icon shrink-0">
                    <span class="text-white text-lg">🔵</span>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-gray-900 mb-1">Sintética</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Rápida y consistente en cualquier clima. Perfecta para
                        jugadores que buscan velocidad y un bote predecible.
                    </p>
                </div>
            </div>

        </div>
    </section>

    {{-- ===== CTA FINAL (solo visitantes) ===== --}}
    @guest
    <section class="bg-primary py-20 px-6">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-3xl font-extrabold text-white mb-3">¿Listo para jugar?</h2>
            <p class="text-green-100 mb-8 text-lg">
                Creá tu cuenta gratis y empezá a reservar en minutos.
            </p>
            <a href="{{ route('register') }}"
               class="inline-block bg-white text-primary-darker font-bold px-8 py-3 rounded-lg hover:bg-green-50 transition-colors">
                Registrarse gratis &rsaquo;
            </a>
        </div>
    </section>
    @endguest

    {{-- ===== FOOTER ===== --}}
    <footer class="bg-gray-900 text-gray-400 text-sm py-8 px-6 text-center">
        <div class="flex items-center justify-center gap-2 mb-2">
            <div class="w-6 h-6 rounded-full border-2 border-primary flex items-center justify-center">
                <div class="w-2 h-2 rounded-full bg-primary"></div>
            </div>
            <span class="font-bold text-white text-sm tracking-widest uppercase">Top Tennis Digital</span>
        </div>
        <p>© {{ date('Y') }} Top Tennis Digital. Todos los derechos reservados.</p>
    </footer>

</body>
</html>
