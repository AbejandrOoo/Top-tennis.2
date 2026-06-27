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
                        primary:      '#4ade80',
                        'court-dark': '#0d3d22',
                        'court-mid':  '#14532d',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: ui-sans-serif, system-ui, sans-serif; }
        .btn-green {
            background: linear-gradient(135deg, #4ade80, #22c55e);
            color: #14532d; font-weight: 700; padding: .75rem 1.75rem;
            border-radius: 9999px; display: inline-flex; align-items: center; gap: .4rem;
            text-decoration: none; transition: opacity .2s;
        }
        .btn-green:hover { opacity: .88; }
        .btn-outline {
            background: #fff; color: #1f2937; border: 1.5px solid #d1d5db;
            font-weight: 600; padding: .75rem 1.75rem; border-radius: 9999px;
            display: inline-flex; align-items: center; text-decoration: none; transition: border-color .2s;
        }
        .btn-outline:hover { border-color: #4ade80; }
        .badge {
            display: inline-flex; align-items: center; background: #dcfce7; color: #15803d;
            border: 1px solid #bbf7d0; font-size: .78rem; font-family: monospace;
            padding: .3rem .85rem; border-radius: 9999px; font-weight: 500;
        }
        .court-wrap {
            background: linear-gradient(160deg, #0d3d22 0%, #14532d 100%);
            border-radius: 1rem; position: relative; overflow: hidden;
            padding: 2.5rem 2rem; min-height: 280px;
            display: flex; align-items: center; justify-content: center;
        }
        .court-lines { width: 100%; max-width: 340px; aspect-ratio: 16/9; }
        .watermark {
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            text-align: center; pointer-events: none; opacity: .2;
        }
        .feat-card {
            background: #fff; border-radius: 1.25rem; padding: 1.75rem;
            border: 1px solid #f3f4f6; box-shadow: 0 2px 8px rgba(0,0,0,.04);
        }
        .feat-icon {
            width: 3rem; height: 3rem; background: #14532d; border-radius: .75rem;
            display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;
        }
        .how-section {
            background: #0d3d22;
            background-image: linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 32px 32px;
        }
        .step-num {
            width: 3.5rem; height: 3.5rem; background: #4ade80; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; font-weight: 800; color: #14532d; margin: 0 auto 1rem;
        }
        .court-card { background: #fff; border-radius: 1rem; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,.07); }
        .court-card-img { height: 130px; position: relative; display: flex; align-items: center; justify-content: center; }
        .court-card-img svg { width: 90%; height: 90%; }
        .court-type-badge {
            position: absolute; top: .6rem; left: 50%; transform: translateX(-50%);
            background: rgba(0,0,0,.45); color: #fff;
            font-size: .7rem; font-weight: 600; padding: .2rem .65rem;
            border-radius: 9999px; backdrop-filter: blur(4px);
        }
        .cta-card {
            background: #fff; border-radius: 1.5rem; padding: 3rem 2rem; text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,.06); max-width: 700px; margin: 0 auto;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

{{-- NAVBAR --}}
<nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            {{-- Logo pelota de tenis --}}
            <svg width="34" height="34" viewBox="0 0 56 56" fill="none">
                <circle cx="28" cy="28" r="26" fill="#dcfce7" stroke="#22c55e" stroke-width="3"/>
                <path d="M6 22 Q28 13 50 22" stroke="#22c55e" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                <path d="M6 34 Q28 43 50 34" stroke="#22c55e" stroke-width="2.5" fill="none" stroke-linecap="round"/>
            </svg>
            <span class="font-bold text-sm tracking-widest uppercase text-gray-900">Top Tennis Digital</span>
        </div>
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-green text-sm">Ir al Panel</a>
            @else
                <a href="{{ route('login') }}" class="btn-outline text-sm">Iniciar Sesión</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-green text-sm">Registrarse Gratis</a>
                @endif
            @endauth
        </div>
    </div>
</nav>

{{-- HERO --}}
<section class="max-w-6xl mx-auto px-6 py-20 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
    <div>
        <span class="badge mb-5 block w-fit">Sistema de reservas deportivas</span>
        <h1 class="text-5xl font-extrabold leading-tight text-gray-900 mb-5">
            Reserva tu cancha<br>
            de tenis <span class="text-green-500">en segundos</span>
        </h1>
        <p class="text-gray-500 text-lg mb-8 leading-relaxed">
            Gestiona tus turnos de manera fácil, rápida y segura.
            Canchas de arcilla y sintética disponibles para todos los turnos.
        </p>
        <div class="flex flex-wrap gap-3 mb-10">
            @auth
                @php $esStaff = in_array(auth()->user()->rol->value, ['Admin','Recepcionista']); @endphp
                <a href="{{ $esStaff ? route('dashboard') : route('reservas.disponibles') }}" class="btn-green">
                    {{ $esStaff ? 'Panel de administración' : 'Reservar ahora' }} &rsaquo;
                </a>
                <a href="{{ route('dashboard') }}" class="btn-outline">Mi Panel</a>
            @else
                <a href="{{ route('register') }}" class="btn-green">Crear cuenta gratis &rsaquo;</a>
                <a href="{{ route('login') }}" class="btn-outline">Ya tengo cuenta</a>
            @endauth
        </div>
        <div class="flex gap-10">
            <div><p class="text-3xl font-extrabold text-gray-900">3</p><p class="text-sm text-gray-400 mt-0.5">Canchas</p></div>
            <div><p class="text-3xl font-extrabold text-gray-900">7d</p><p class="text-sm text-gray-400 mt-0.5">Por semana</p></div>
            <div><p class="text-3xl font-extrabold text-gray-900">100%</p><p class="text-sm text-gray-400 mt-0.5">Online</p></div>
        </div>
    </div>

    <div class="relative">
        <div class="court-wrap shadow-2xl">
            <div class="watermark">
                <p class="text-white text-xs font-bold tracking-widest uppercase">Top Tennis Digital</p>
                <p class="text-white text-[10px] tracking-widest uppercase">Reserva tu cancha en segundos</p>
            </div>
            <svg class="court-lines" viewBox="0 0 340 190" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="10" y="10" width="320" height="170" fill="#1a7a3c" rx="2"/>
                <rect x="10" y="10" width="320" height="170" stroke="white" stroke-width="2.5" fill="none"/>
                <line x1="170" y1="10" x2="170" y2="180" stroke="white" stroke-width="2"/>
                <line x1="70"  y1="10" x2="70"  y2="180" stroke="white" stroke-width="1.5"/>
                <line x1="270" y1="10" x2="270" y2="180" stroke="white" stroke-width="1.5"/>
                <line x1="70"  y1="95" x2="270" y2="95"  stroke="white" stroke-width="1.5"/>
                <line x1="170" y1="45" x2="170" y2="145" stroke="white" stroke-width="1.5"/>
                <circle cx="100" cy="95" r="9" fill="#a3e635"/>
                <path d="M93 90 Q100 98 107 90" stroke="white" stroke-width="1.2" fill="none"/>
                <path d="M93 100 Q100 92 107 100" stroke="white" stroke-width="1.2" fill="none"/>
                <rect x="167" y="5" width="6" height="180" fill="#15803d"/>
                <rect x="165" y="90" width="10" height="4" fill="#d1fae5"/>
            </svg>
        </div>
    </div>
</section>

{{-- FEATURES --}}
<section class="bg-white py-20 px-6">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-14">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-3">Todo lo que necesitas para jugar</h2>
            <p class="text-gray-400 text-base">Una plataforma completa de gestión de canchas deportivas</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="feat-card">
                <div class="feat-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" stroke="white" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="white" stroke-width="2" stroke-linecap="round"/></svg></div>
                <h3 class="font-bold text-gray-900 mb-1.5">Reservas en segundos</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Elige fecha, horario y cancha en pocos pasos. Sin llamadas ni esperas.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="white" stroke-width="2"/><path d="M12 7v5l3 3" stroke="white" stroke-width="2" stroke-linecap="round"/></svg></div>
                <h3 class="font-bold text-gray-900 mb-1.5">Disponibilidad en tiempo real</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Consulta qué canchas están libres al instante, todos los días.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7v5c0 5.25 3.75 10.15 9 11.25C17.25 22.15 21 17.25 21 12V7L12 2z" stroke="white" stroke-width="2" stroke-linejoin="round"/><path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                <h3 class="font-bold text-gray-900 mb-1.5">Pago seguro</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Paga con Yape o en efectivo en recepción. Tu reserva queda confirmada al instante.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="7" r="3" stroke="white" stroke-width="2"/><circle cx="15" cy="7" r="3" stroke="white" stroke-width="2"/><path d="M3 20c0-3.3 2.7-6 6-6h6c3.3 0 6 2.7 6 6" stroke="white" stroke-width="2" stroke-linecap="round"/></svg></div>
                <h3 class="font-bold text-gray-900 mb-1.5">Singles y Dobles</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Canchas habilitadas para juego individual y en pareja. Elige la que necesitas.</p>
            </div>
        </div>
    </div>
</section>

{{-- CÓMO FUNCIONA --}}
<section class="how-section py-20 px-6">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl font-extrabold text-white mb-2">¿Cómo funciona?</h2>
        <p class="text-green-300 mb-14 text-base">Reserva tu cancha en 3 pasos simples</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-12">
            <div class="text-center">
                <div class="step-num">1</div>
                <h3 class="font-bold text-white text-lg mb-2">Regístrate gratis</h3>
                <p class="text-green-200 text-sm leading-relaxed">Crea tu cuenta en menos de un minuto con tu correo y nombre.</p>
            </div>
            <div class="text-center">
                <div class="step-num">2</div>
                <h3 class="font-bold text-white text-lg mb-2">Elige tu cancha</h3>
                <p class="text-green-200 text-sm leading-relaxed">Selecciona la fecha, el horario disponible y el tipo de cancha.</p>
            </div>
            <div class="text-center">
                <div class="step-num">3</div>
                <h3 class="font-bold text-white text-lg mb-2">Confirma y juega</h3>
                <p class="text-green-200 text-sm leading-relaxed">Confirma con efectivo en recepción. ¡Listo para jugar!</p>
            </div>
        </div>
        @auth
            @php $esStaff = $esStaff ?? in_array(auth()->user()->rol->value, ['Admin','Recepcionista']); @endphp
            <a href="{{ $esStaff ? route('dashboard') : route('reservas.disponibles') }}" class="btn-green text-base px-8 py-3">Comenzar ahora &rsaquo;</a>
        @else
            <a href="{{ route('register') }}" class="btn-green text-base px-8 py-3">Comenzar ahora &rsaquo;</a>
        @endauth
    </div>
</section>

{{-- NUESTRAS CANCHAS --}}
<section class="py-20 px-6 bg-gray-50">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Nuestras Canchas</h2>
            <p class="text-gray-400">Canchas disponibles con diferentes superficies</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="court-card">
                <div class="court-card-img" style="background: linear-gradient(135deg, #c2410c, #ea580c);">
                    <svg viewBox="0 0 200 120" fill="none"><rect x="10" y="8" width="180" height="104" fill="rgba(255,255,255,.1)" rx="1"/><rect x="10" y="8" width="180" height="104" stroke="white" stroke-width="2" fill="none"/><line x1="100" y1="8" x2="100" y2="112" stroke="white" stroke-width="1.5"/><line x1="45" y1="8" x2="45" y2="112" stroke="white" stroke-width="1"/><line x1="155" y1="8" x2="155" y2="112" stroke="white" stroke-width="1"/><line x1="45" y1="60" x2="155" y2="60" stroke="white" stroke-width="1"/><circle cx="60" cy="60" r="5" fill="#fde68a"/></svg>
                    <span class="court-type-badge">Arcilla</span>
                </div>
                <div class="p-4">
                    <p class="font-bold text-gray-900">Cancha Central</p>
                    <p class="text-sm text-gray-400 mt-0.5">Singles · Superficie de arcilla</p>
                </div>
            </div>
            <div class="court-card">
                <div class="court-card-img" style="background: linear-gradient(135deg, #1d4ed8, #3b82f6);">
                    <svg viewBox="0 0 200 120" fill="none"><rect x="10" y="8" width="180" height="104" fill="rgba(255,255,255,.1)" rx="1"/><rect x="10" y="8" width="180" height="104" stroke="white" stroke-width="2" fill="none"/><line x1="100" y1="8" x2="100" y2="112" stroke="white" stroke-width="1.5"/><line x1="45" y1="8" x2="45" y2="112" stroke="white" stroke-width="1"/><line x1="155" y1="8" x2="155" y2="112" stroke="white" stroke-width="1"/><line x1="45" y1="60" x2="155" y2="60" stroke="white" stroke-width="1"/><circle cx="60" cy="60" r="5" fill="#fde68a"/></svg>
                    <span class="court-type-badge">Sintética</span>
                </div>
                <div class="p-4">
                    <p class="font-bold text-gray-900">Cancha Norte</p>
                    <p class="text-sm text-gray-400 mt-0.5">Dobles · Superficie sintética</p>
                </div>
            </div>
            <div class="court-card">
                <div class="court-card-img" style="background: linear-gradient(135deg, #166534, #15803d);">
                    <svg viewBox="0 0 200 120" fill="none"><rect x="10" y="8" width="180" height="104" fill="rgba(255,255,255,.1)" rx="1"/><rect x="10" y="8" width="180" height="104" stroke="white" stroke-width="2" fill="none"/><line x1="100" y1="8" x2="100" y2="112" stroke="white" stroke-width="1.5"/><line x1="45" y1="8" x2="45" y2="112" stroke="white" stroke-width="1"/><line x1="155" y1="8" x2="155" y2="112" stroke="white" stroke-width="1"/><line x1="45" y1="60" x2="155" y2="60" stroke="white" stroke-width="1"/><circle cx="60" cy="60" r="5" fill="#fde68a"/></svg>
                    <span class="court-type-badge">Arcilla</span>
                </div>
                <div class="p-4">
                    <p class="font-bold text-gray-900">Cancha Sur</p>
                    <p class="text-sm text-gray-400 mt-0.5">Singles · <span class="text-red-500 text-xs font-medium">No disponible</span></p>
                </div>
            </div>
        </div>
        <div class="text-center mt-8">
            @auth
                @php $esStaff = $esStaff ?? in_array(auth()->user()->rol->value, ['Admin','Recepcionista']); @endphp
                <a href="{{ $esStaff ? route('canchas.index') : route('reservas.disponibles') }}" class="btn-outline inline-flex">
                    {{ $esStaff ? 'Gestionar canchas' : 'Ver horarios disponibles' }}
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-outline inline-flex">Ver horarios disponibles</a>
            @endauth
        </div>
    </div>
</section>

{{-- CTA FINAL --}}
<section class="bg-gray-100 py-20 px-6">
    <div class="cta-card">
        <div class="flex justify-center gap-1 text-green-400 text-2xl mb-4">★ ★ ★ ★ ★</div>
        <h2 class="text-3xl font-extrabold text-gray-900 mb-3">Empieza a jugar hoy</h2>
        <p class="text-gray-500 mb-8">Regístrate gratis y reserva tu primera cancha en menos de 2 minutos</p>
        <div class="flex flex-wrap justify-center gap-3">
            @auth
                @php $esStaff = $esStaff ?? in_array(auth()->user()->rol->value, ['Admin','Recepcionista']); @endphp
                <a href="{{ $esStaff ? route('dashboard') : route('reservas.disponibles') }}" class="btn-green text-base">
                    {{ $esStaff ? 'Panel de administración' : 'Reservar ahora' }} &rsaquo;
                </a>
                <a href="{{ route('dashboard') }}" class="btn-outline text-base">Mi Panel</a>
            @else
                <a href="{{ route('register') }}" class="btn-green text-base">Registrarme gratis &rsaquo;</a>
                <a href="{{ route('login') }}" class="btn-outline text-base">Iniciar Sesión</a>
            @endauth
        </div>
    </div>
</section>

{{-- FOOTER --}}
<footer class="bg-gray-900 text-gray-400 text-sm py-8 px-6 text-center">
    <div class="flex items-center justify-center gap-2 mb-2">
        <svg width="22" height="22" viewBox="0 0 56 56" fill="none">
            <circle cx="28" cy="28" r="26" fill="none" stroke="#4ade80" stroke-width="3"/>
            <path d="M6 22 Q28 13 50 22" stroke="#4ade80" stroke-width="2.5" fill="none" stroke-linecap="round"/>
            <path d="M6 34 Q28 43 50 34" stroke="#4ade80" stroke-width="2.5" fill="none" stroke-linecap="round"/>
        </svg>
        <span class="font-bold text-white text-sm tracking-widest uppercase">Top Tennis Digital</span>
    </div>
    <p>© {{ date('Y') }} Top Tennis Digital. Todos los derechos reservados.</p>
</footer>

</body>
</html>
