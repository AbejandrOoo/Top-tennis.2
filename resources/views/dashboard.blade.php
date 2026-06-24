<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Bienvenido, {{ Auth::user()->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Tarjeta de bienvenida con rol --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-gray-700">
                    Has iniciado sesión como
                    <span class="font-semibold text-indigo-600">{{ ucfirst(Auth::user()->rol->value) }}</span>.
                </p>
            </div>

            {{-- Panel de Admin y Recepcionista --}}
            @if(in_array(Auth::user()->rol, [\App\Enums\Rol::Admin, \App\Enums\Rol::Recepcionista]))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- Gestión de Canchas --}}
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Canchas</h3>
                        <p class="text-gray-500 text-sm mb-4">Administra las canchas del club.</p>
                        <div class="flex gap-3">
                            <a href="{{ route('canchas.index') }}"
                               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                                Ver canchas
                            </a>
                            <a href="{{ route('canchas.create') }}"
                               class="px-4 py-2 border border-indigo-600 text-indigo-600 text-sm rounded-md hover:bg-indigo-50">
                                + Nueva
                            </a>
                        </div>
                    </div>

                    {{-- Gestión de Tarifas --}}
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Tarifas</h3>
                        <p class="text-gray-500 text-sm mb-4">Administra los precios por cancha y turno.</p>
                        <div class="flex gap-3">
                            <a href="{{ route('tarifas.index') }}"
                               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                                Ver tarifas
                            </a>
                            <a href="{{ route('tarifas.create') }}"
                               class="px-4 py-2 border border-indigo-600 text-indigo-600 text-sm rounded-md hover:bg-indigo-50">
                                + Nueva
                            </a>
                        </div>
                    </div>

                    {{-- Gestión de Horarios --}}
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Horarios</h3>
                        <p class="text-gray-500 text-sm mb-4">Gestiona todas las reservas del club.</p>
                        <div class="flex gap-3">
                            <a href="{{ route('horarios.index') }}"
                               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                                Ver horarios
                            </a>
                            <a href="{{ route('horarios.create') }}"
                               class="px-4 py-2 border border-indigo-600 text-indigo-600 text-sm rounded-md hover:bg-indigo-50">
                                + Nueva
                            </a>
                        </div>
                    </div>

                </div>
            @endif

            {{-- Panel del Cliente --}}
            @if(Auth::user()->rol === \App\Enums\Rol::Cliente)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Canchas disponibles</h3>
                        <p class="text-gray-500 text-sm mb-4">Consulta las canchas y sus tarifas.</p>
                        <a href="{{ route('canchas.index') }}"
                           class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                            Ver canchas
                        </a>
                    </div>

                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Tarifas</h3>
                        <p class="text-gray-500 text-sm mb-4">Consulta los precios por turno y cancha.</p>
                        <a href="{{ route('tarifas.index') }}"
                           class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                            Ver tarifas
                        </a>
                    </div>

                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Mis Reservas</h3>
                        <p class="text-gray-500 text-sm mb-4">Consulta y gestiona tus horarios reservados.</p>
                        <div class="flex gap-3">
                            <a href="{{ route('horarios.index') }}"
                               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                                Ver reservas
                            </a>
                            <a href="{{ route('horarios.create') }}"
                               class="px-4 py-2 border border-indigo-600 text-indigo-600 text-sm rounded-md hover:bg-indigo-50">
                                + Nueva
                            </a>
                        </div>
                    </div>

                </div>
            @endif

        </div>
    </div>
</x-app-layout>
