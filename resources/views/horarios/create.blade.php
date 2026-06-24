<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nueva Reserva de Horario
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                {{--
                    Alpine.js maneja el filtrado de tarifas según la cancha seleccionada.
                    $tarifasJson es un JSON de todas las tarifas activas con su cancha_id.
                --}}
                <form method="POST" action="{{ route('horarios.store') }}"
                      x-data="horarioForm({{ $tarifas->toJson() }})">
                    @csrf

                    <!-- Cancha -->
                    <div class="mb-4">
                        <x-input-label for="cancha_id" value="Cancha" />
                        <select id="cancha_id" name="cancha_id"
                                x-model="canchaSeleccionada"
                                @change="filtrarTarifas()"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Seleccione una cancha...</option>
                            @foreach($canchas as $cancha)
                                <option value="{{ $cancha->id }}"
                                    {{ old('cancha_id') == $cancha->id ? 'selected' : '' }}>
                                    {{ $cancha->nombre }} ({{ $cancha->tipo }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('cancha_id')" class="mt-2" />
                    </div>

                    <!-- Tarifa (filtrada según cancha) -->
                    <div class="mb-4">
                        <x-input-label for="tarifa_id" value="Tarifa / Turno" />
                        <select id="tarifa_id" name="tarifa_id"
                                x-model="tarifaSeleccionada"
                                @change="aplicarHorarioTarifa()"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                :disabled="!canchaSeleccionada">
                            <option value="">Seleccione una tarifa...</option>
                            <template x-for="tarifa in tarifasFiltradas" :key="tarifa.id">
                                <option :value="tarifa.id"
                                        :selected="tarifa.id == {{ old('tarifa_id', 0) }}">
                                    <span x-text="tarifa.turno + ' — $' + parseFloat(tarifa.precio_hora).toFixed(2) + '/h (' + tarifa.hora_inicio + ' a ' + tarifa.hora_fin + ')'"></span>
                                </option>
                            </template>
                        </select>
                        <p x-show="!canchaSeleccionada" class="mt-1 text-xs text-gray-400">Seleccione primero una cancha.</p>
                        <x-input-error :messages="$errors->get('tarifa_id')" class="mt-2" />
                    </div>

                    <!-- Fecha -->
                    <div class="mb-4">
                        <x-input-label for="fecha" value="Fecha" />
                        <x-text-input id="fecha" name="fecha" type="date"
                                      class="block mt-1 w-full"
                                      min="{{ date('Y-m-d') }}"
                                      :value="old('fecha')" required />
                        <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                    </div>

                    <!-- Horario (se pre-rellena desde la tarifa) -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-input-label for="hora_inicio" value="Hora de inicio" />
                            <x-text-input id="hora_inicio" name="hora_inicio" type="time"
                                          class="block mt-1 w-full"
                                          x-model="horaInicio"
                                          :value="old('hora_inicio')" required />
                            <x-input-error :messages="$errors->get('hora_inicio')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="hora_fin" value="Hora de fin" />
                            <x-text-input id="hora_fin" name="hora_fin" type="time"
                                          class="block mt-1 w-full"
                                          x-model="horaFin"
                                          :value="old('hora_fin')" required />
                            <x-input-error :messages="$errors->get('hora_fin')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Notas -->
                    <div class="mb-6">
                        <x-input-label for="notas" value="Notas (opcional)" />
                        <textarea id="notas" name="notas" rows="3"
                                  class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                  maxlength="500">{{ old('notas') }}</textarea>
                        <x-input-error :messages="$errors->get('notas')" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('horarios.index') }}"
                           class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancelar
                        </a>
                        <x-primary-button>Guardar Reserva</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function horarioForm(todasLasTarifas) {
            return {
                canchaSeleccionada: '{{ old('cancha_id', '') }}',
                tarifaSeleccionada: '{{ old('tarifa_id', '') }}',
                tarifasFiltradas: [],
                horaInicio: '{{ old('hora_inicio', '') }}',
                horaFin: '{{ old('hora_fin', '') }}',

                init() {
                    // Si venimos de un error de validación, restaurar el filtro
                    if (this.canchaSeleccionada) {
                        this.filtrarTarifas();
                    }
                },

                filtrarTarifas() {
                    this.tarifasFiltradas = todasLasTarifas.filter(
                        t => t.cancha_id == this.canchaSeleccionada
                    );
                    this.tarifaSeleccionada = '';
                    this.horaInicio = '';
                    this.horaFin = '';
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
