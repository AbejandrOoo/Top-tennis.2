@php
    $rol = Auth::user()->rol->value ?? '';
@endphp

{{-- Hero admin --}}
<div class="relative bg-green-900 rounded-3xl overflow-hidden grid-pattern p-8 flex items-center justify-between min-h-[180px]">
    <div class="relative z-10">
        <p class="text-green-400 text-sm font-semibold tracking-wide mb-1">Panel de administración</p>
        <h1 class="text-white text-3xl font-black mb-1 leading-none">
            {{ Auth::user()->name }} {{ Auth::user()->emoji_perfil ?? '' }}
        </h1>
        <p class="text-green-200 text-sm">{{ ucfirst($rol) }} — Top Tennis Digital</p>
    </div>
    <div class="relative z-10 w-16 h-16 rounded-full bg-green-700 flex items-center justify-center text-white text-2xl font-black shrink-0 ml-6">
        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
    </div>
    <svg class="absolute inset-0 w-full h-full opacity-10" viewBox="0 0 600 180" preserveAspectRatio="xMidYMid slice" fill="none">
        <rect x="60" y="15" width="480" height="150" stroke="white" stroke-width="2"/>
        <line x1="300" y1="15" x2="300" y2="165" stroke="white" stroke-width="1.5"/>
        <line x1="60" y1="90" x2="540" y2="90" stroke="white" stroke-width="1.5"/>
        <rect x="150" y="15" width="300" height="150" stroke="white" stroke-width="1"/>
        <ellipse cx="300" cy="90" rx="25" ry="25" stroke="white" stroke-width="1.5"/>
    </svg>
</div>

{{-- Stats --}}
<div class="grid grid-cols-3 gap-4">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
        <div class="flex justify-center mb-2">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" class="stat-icon">
                <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                <line x1="12" y1="3" x2="12" y2="21" stroke="currentColor" stroke-width="1.5"/>
                <line x1="3" y1="9" x2="21" y2="9" stroke="currentColor" stroke-width="1.5"/>
                <line x1="3" y1="15" x2="21" y2="15" stroke="currentColor" stroke-width="1.5"/>
            </svg>
        </div>
        <p class="stat-num">{{ $canchasLibres }}</p>
        <p class="stat-lbl">Canchas disponibles</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
        <div class="flex justify-center mb-2">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" class="stat-icon">
                <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <p class="stat-num">{{ $reservasActivas }}</p>
        <p class="stat-lbl">Reservas activas</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
        <div class="flex justify-center mb-2">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" class="stat-icon">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                <path d="M12 8v4l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <p class="stat-num">{{ $horariosDisp }}</p>
        <p class="stat-lbl">Tarifas activas</p>
    </div>
</div>

{{-- Accesos rápidos --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
        <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center mb-4">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" stroke="#15803d" stroke-width="2"/><line x1="12" y1="3" x2="12" y2="21" stroke="#15803d" stroke-width="1.5"/><line x1="3" y1="9" x2="21" y2="9" stroke="#15803d" stroke-width="1.5"/><line x1="3" y1="15" x2="21" y2="15" stroke="#15803d" stroke-width="1.5"/></svg>
        </div>
        <h3 class="font-bold text-gray-900 mb-1">Canchas</h3>
        <p class="text-gray-400 text-sm mb-4">Administra las canchas del club.</p>
        <div class="flex gap-2">
            <a href="{{ route('canchas.index') }}" class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-4 py-1.5 rounded-xl transition-colors">Ver todas</a>
            <a href="{{ route('canchas.create') }}" class="border border-green-600 text-green-700 hover:bg-green-50 text-xs font-semibold px-3 py-1.5 rounded-xl transition-colors">+ Nueva</a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
        <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center mb-4">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#15803d" stroke-width="2"/><path d="M12 8v4l3 3" stroke="#15803d" stroke-width="2" stroke-linecap="round"/></svg>
        </div>
        <h3 class="font-bold text-gray-900 mb-1">Tarifas</h3>
        <p class="text-gray-400 text-sm mb-4">Gestiona los precios por turno.</p>
        <div class="flex gap-2">
            <a href="{{ route('tarifas.index') }}" class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-4 py-1.5 rounded-xl transition-colors">Ver todas</a>
            <a href="{{ route('tarifas.create') }}" class="border border-green-600 text-green-700 hover:bg-green-50 text-xs font-semibold px-3 py-1.5 rounded-xl transition-colors">+ Nueva</a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
        <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center mb-4">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" stroke="#15803d" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="#15803d" stroke-width="2" stroke-linecap="round"/></svg>
        </div>
        <h3 class="font-bold text-gray-900 mb-1">Horarios</h3>
        <p class="text-gray-400 text-sm mb-4">Gestiona todas las reservas.</p>
        <div class="flex gap-2">
            <a href="{{ route('horarios.index') }}" class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-4 py-1.5 rounded-xl transition-colors">Ver todas</a>
            <a href="{{ route('horarios.create') }}" class="border border-green-600 text-green-700 hover:bg-green-50 text-xs font-semibold px-3 py-1.5 rounded-xl transition-colors">+ Nueva</a>
        </div>
    </div>
</div>
