<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Cancha: {{ $cancha->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('canchas.update', $cancha) }}">
                    @csrf
                    @method('PATCH')

                    <!-- Nombre -->
                    <div class="mb-4">
                        <x-input-label for="nombre" value="Nombre de la cancha" />
                        <x-text-input id="nombre" name="nombre" type="text"
                                      class="block mt-1 w-full"
                                      :value="old('nombre', $cancha->nombre)" required />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <!-- Tipo -->
                    <div class="mb-4">
                        <x-input-label for="tipo" value="Tipo de superficie" />
                        <select id="tipo" name="tipo"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Arcilla"   {{ old('tipo', $cancha->tipo) === 'Arcilla'   ? 'selected' : '' }}>Arcilla</option>
                            <option value="Sintética" {{ old('tipo', $cancha->tipo) === 'Sintética' ? 'selected' : '' }}>Sintética</option>
                        </select>
                        <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                    </div>

                    <!-- Estado -->
                    <div class="mb-6">
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Disponible"    {{ old('estado', $cancha->estado) === 'Disponible'    ? 'selected' : '' }}>Disponible</option>
                            <option value="No Disponible" {{ old('estado', $cancha->estado) === 'No Disponible' ? 'selected' : '' }}>No Disponible</option>
                        </select>
                        <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('canchas.index') }}"
                           class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancelar
                        </a>
                        <x-primary-button>Actualizar Cancha</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
