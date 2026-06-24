<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Tarifa
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('tarifas.update', $tarifa) }}">
                    @csrf
                    @method('PATCH')

                    <!-- Cancha -->
                    <div class="mb-4">
                        <x-input-label for="cancha_id" value="Cancha" />
                        <select id="cancha_id" name="cancha_id"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($canchas as $cancha)
                                <option value="{{ $cancha->id }}"
                                    {{ old('cancha_id', $tarifa->cancha_id) == $cancha->id ? 'selected' : '' }}>
                                    {{ $cancha->nombre }} ({{ $cancha->tipo }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('cancha_id')" class="mt-2" />
                    </div>

                    <!-- Turno -->
                    <div class="mb-4">
                        <x-input-label for="turno" value="Turno" />
                        <select id="turno" name="turno"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach(['Mañana', 'Tarde', 'Noche'] as $t)
                                <option value="{{ $t }}" {{ old('turno', $tarifa->turno) === $t ? 'selected' : '' }}>
                                    {{ $t }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('turno')" class="mt-2" />
                    </div>

                    <!-- Horario -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-input-label for="hora_inicio" value="Hora de inicio" />
                            <x-text-input id="hora_inicio" name="hora_inicio" type="time"
                                          class="block mt-1 w-full"
                                          :value="old('hora_inicio', $tarifa->hora_inicio)" required />
                            <x-input-error :messages="$errors->get('hora_inicio')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="hora_fin" value="Hora de fin" />
                            <x-text-input id="hora_fin" name="hora_fin" type="time"
                                          class="block mt-1 w-full"
                                          :value="old('hora_fin', $tarifa->hora_fin)" required />
                            <x-input-error :messages="$errors->get('hora_fin')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Precio -->
                    <div class="mb-4">
                        <x-input-label for="precio_hora" value="Precio por hora ($)" />
                        <x-text-input id="precio_hora" name="precio_hora" type="number"
                                      step="0.01" min="0"
                                      class="block mt-1 w-full"
                                      :value="old('precio_hora', $tarifa->precio_hora)" required />
                        <x-input-error :messages="$errors->get('precio_hora')" class="mt-2" />
                    </div>

                    <!-- Estado -->
                    <div class="mb-6">
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Activa"   {{ old('estado', $tarifa->estado) === 'Activa'   ? 'selected' : '' }}>Activa</option>
                            <option value="Inactiva" {{ old('estado', $tarifa->estado) === 'Inactiva' ? 'selected' : '' }}>Inactiva</option>
                        </select>
                        <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('tarifas.index') }}"
                           class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancelar
                        </a>
                        <x-primary-button>Actualizar Tarifa</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
