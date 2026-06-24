<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Horario — {{ $horario->cancha->nombre ?? '' }} ({{ $horario->fecha->format('d/m/Y') }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('horarios.update', $horario) }}"
                      x-data="horarioForm({{ $tarifas->toJson() }})">
                    @csrf
                    @method('PATCH')

                    <!-- Cancha -->
                    <div class="mb-4">
                        <x-input-label for="cancha_id" value="Cancha" />
                        <select id="cancha_id" name="cancha_id"
                                x-model="canchaSeleccionada"
                                @change="filtrarTarifas()"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($canchas as $cancha)
                                <option value="{{ $cancha->id }}"
                                    {{ old('cancha_id', $horario->cancha_id) == $cancha->id ? 'selected' : '' }}>
                                    {{ $cancha->nombre }} ({{ $cancha->tipo }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('cancha_id')" class="mt-2" />
                    </div>

                    <!-- Tarifa -->
                    <div class="mb-4">
                        <x-input-label for="tarifa_id" value="Tarifa / Turno" />
                        <select id="tarifa_id" name="tarifa_id"
                                x-model="tarifaSeleccionada"
                                @change="aplicarHorarioTarifa()"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <template x-for="tarifa in tarifasFiltradas" :key="tarifa.id">
                                <option :value="tarifa.id"
                                        :selected="tarifa.id == tarifaSeleccionada">
                                    <span x-text="tarifa.turno + ' — $' + parseFloat(tarifa.precio_hora).toFixed(2) + '/h (' + tarifa.hora_inicio + ' a ' + tarifa.hora_fin + ')'"></span>
                                </option>
                            </template>
                        </select>
                        <x-input-error :messages="$errors->get('tarifa_id')" class="mt-2" />
                    </div>

                    <!-- Fecha -->
                    <div class="mb-4">
                        <x-input-label for="fecha" value="Fecha" />
                        <x-text-input id="fecha" name="fecha" type="date"
                                      class="block mt-1 w-full"
                                      :value="old('fecha', $horario->fecha->format('Y-m-d'))" required />
                        <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                    </div>

                    <!-- Horario -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-input-label for="hora_inicio" value="Hora de inicio" />
                            <x-text-input id="hora_inicio" name="hora_inicio" type="time"
                                          class="block mt-1 w-full"
                                          x-model="horaInicio"
                                          :value="old('hora_inicio', $horario->hora_inicio)" required />
                            <x-input-error :messages="$errors->get('hora_inicio')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="hora_fin" value="Hora de fin" />
                            <x-text-input id="hora_fin" name="hora_fin" type="time"
                                          class="block mt-1 w-full"
                                          x-model="horaFin"
                                          :value="old('hora_fin', $horario->hora_fin)" required />
                            <x-input-error :messages="$errors->get('hora_fin')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Estado (solo Admin y Recepcionista pueden cambiarlo) -->
                    @if(in_array(auth()->user()->rol, [\App\Enums\Rol::Admin, \App\Enums\Rol::Recepcionista]))
                    <div class="mb-4">
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach(['Reservado', 'Confirmado', 'Cancelado', 'Completado'] as $estado)
                                <option value="{{ $estado }}"
                                    {{ old('estado', $horario->estado) === $estado ? 'selected' : '' }}>
                                    {{ $estado }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                    </div>
                    @else
                        {{-- Los clientes no pueden cambiar el estado; enviamos el actual --}}
                        <input type="hidden" name="estado" value="{{ $horario->estado }}">
                    @endif

                    <!-- Notas -->
                    <div class="mb-6">
                        <x-input-label for="notas" value="Notas (opcional)" />
                        <textarea id="notas" name="notas" rows="3"
                                  class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                  maxlength="500">{{ old('notas', $horario->notas) }}</textarea>
                        <x-input-error :messages="$errors->get('notas')" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('horarios.index') }}"
                           class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancelar
                        </a>
                        <x-primary-button>Actualizar Reserva</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function horarioForm(todasLasTarifas) {
            return {
                canchaSeleccionada: '{{ old('cancha_id', $horario->cancha_id) }}',
                tarifaSeleccionada: '{{ old('tarifa_id', $horario->tarifa_id) }}',
                tarifasFiltradas: [],
                horaInicio: '{{ old('hora_inicio', $horario->hora_inicio) }}',
                horaFin: '{{ old('hora_fin', $horario->hora_fin) }}',

                init() {
                    this.filtrarTarifas();
                },

                filtrarTarifas() {
                    this.tarifasFiltradas = todasLasTarifas.filter(
                        t => t.cancha_id == this.canchaSeleccionada
                    );
                },

                aplicarHorarioTarifa() {
                    const tarifa = todasLasTarifas.find(t => t.id == this.tarifaSeleccionada);
                    if (tarifa) {
                        this.horaInicio = tarifa.hora_inicio;
                        this.horaFin    = tarifa.hora_fin;
                    }
                },
            };
        }
    </script>
</x-app-layout>
