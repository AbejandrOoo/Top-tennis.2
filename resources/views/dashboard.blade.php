<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-white leading-tight">
                    Hola, {{ Auth::user()->name }} {{ Auth::user()->emoji_perfil ?? '👋' }}
                </h1>
                <p class="text-green-200 text-sm mt-0.5">Panel de control — Top Tennis Digital</p>
            </div>
            <a href="{{ route('horarios.create') }}" class="btn-primary">
                + Nueva reserva
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- Banner rol --}}
        @php
            $rol = Auth::user()->rol->value ?? '';
            $rolLabel = ['admin' => 'Administrador', 'recepcionista' => 'Recepcionista', 'cliente' => 'Cliente'][$rol] ?? ucfirst($rol);
            $rolColor = match($rol) {
                'admin'         => 'from-purple-600 to-purple-700',
                'recepcionista' => 'from-blue-600 to-blue-700',
                default         => 'from-green-600 to-green-700',
            };
        @endphp
        <div class="card p-5 flex items-center gap-4 border-l-4 border-green-400">
            <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white text-xl font-bold shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-gray-800 font-semibold">{{ Auth::user()->name }}</p>
                <p class="text-gray-500 text-sm">{{ Auth::user()->email }}</p>
            </div>
            <span class="ml-auto text-xs font-bold px-3 py-1 rounded-full
                {{ $rol === 'admin' ? 'bg-purple-100 text-purple-700' : ($rol === 'recepcionista' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                {{ $rolLabel }}
            </span>
        </div>

        {{-- Cards de acceso rápido --}}
        @if(in_array(Auth::user()->rol, [\App\Enums\Rol::Admin, \App\Enums\Rol::Recepcionista]))
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                <div class="card p-6 hover:shadow-md transition-shadow">
                    <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center mb-4">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" stroke="#15803d" stroke-width="2"/><line x1="12" y1="3" x2="12" y2="21" stroke="#15803d" stroke-width="1.5"/><line x1="3" y1="9" x2="21" y2="9" stroke="#15803d" stroke-width="1.5"/><line x1="3" y1="15" x2="21" y2="15" stroke="#15803d" stroke-width="1.5"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-1">Canchas</h3>
                    <p class="text-gray-400 text-sm mb-4">Administra las canchas del club.</p>
                    <div class="flex gap-2">
                        <a href="{{ route('canchas.index') }}" class="btn-primary text-xs py-1.5 px-4">Ver todas</a>
                        <a href="{{ route('canchas.create') }}" class="btn-outline-sm text-xs py-1.5 px-3">+ Nueva</a>
                    </div>
                </div>

                <div class="card p-6 hover:shadow-md transition-shadow">
                    <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center mb-4">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#15803d" stroke-width="2"/><path d="M12 8v4l3 3" stroke="#15803d" stroke-width="2" stroke-linecap="round"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-1">Tarifas</h3>
                    <p class="text-gray-400 text-sm mb-4">Gestiona los precios por turno.</p>
                    <div class="flex gap-2">
                        <a href="{{ route('tarifas.index') }}" class="btn-primary text-xs py-1.5 px-4">Ver todas</a>
                        <a href="{{ route('tarifas.create') }}" class="btn-outline-sm text-xs py-1.5 px-3">+ Nueva</a>
                    </div>
                </div>

                <div class="card p-6 hover:shadow-md transition-shadow">
                    <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center mb-4">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" stroke="#15803d" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="#15803d" stroke-width="2" stroke-linecap="round"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-1">Horarios</h3>
                    <p class="text-gray-400 text-sm mb-4">Gestiona todas las reservas.</p>
                    <div class="flex gap-2">
                        <a href="{{ route('horarios.index') }}" class="btn-primary text-xs py-1.5 px-4">Ver todas</a>
                        <a href="{{ route('horarios.create') }}" class="btn-outline-sm text-xs py-1.5 px-3">+ Nueva</a>
                    </div>
                </div>

            </div>
        @endif

        @if(Auth::user()->rol === \App\Enums\Rol::Cliente)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                <div class="card p-6 hover:shadow-md transition-shadow">
                    <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center mb-4">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" stroke="#15803d" stroke-width="2"/><line x1="12" y1="3" x2="12" y2="21" stroke="#15803d" stroke-width="1.5"/><line x1="3" y1="9" x2="21" y2="9" stroke="#15803d" stroke-width="1.5"/><line x1="3" y1="15" x2="21" y2="15" stroke="#15803d" stroke-width="1.5"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-1">Canchas disponibles</h3>
                    <p class="text-gray-400 text-sm mb-4">Consulta las canchas y sus tarifas.</p>
                    <a href="{{ route('canchas.index') }}" class="btn-primary text-xs py-1.5 px-4">Ver canchas</a>
                </div>

                <div class="card p-6 hover:shadow-md transition-shadow">
                    <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center mb-4">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#15803d" stroke-width="2"/><path d="M12 8v4l3 3" stroke="#15803d" stroke-width="2" stroke-linecap="round"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-1">Tarifas</h3>
                    <p class="text-gray-400 text-sm mb-4">Consulta los precios por turno.</p>
                    <a href="{{ route('tarifas.index') }}" class="btn-primary text-xs py-1.5 px-4">Ver tarifas</a>
                </div>

                <div class="card p-6 hover:shadow-md transition-shadow">
                    <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center mb-4">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" stroke="#15803d" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="#15803d" stroke-width="2" stroke-linecap="round"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-1">Mis Reservas</h3>
                    <p class="text-gray-400 text-sm mb-4">Consulta y gestiona tus horarios.</p>
                    <div class="flex gap-2">
                        <a href="{{ route('horarios.index') }}" class="btn-primary text-xs py-1.5 px-4">Ver reservas</a>
                        <a href="{{ route('horarios.create') }}" class="btn-outline-sm text-xs py-1.5 px-3">+ Nueva</a>
                    </div>
                </div>

            </div>
        @endif

    </div>
</x-app-layout>
