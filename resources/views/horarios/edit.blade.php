<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Editar Reserva</h1>
            <p class="text-green-200 text-sm mt-0.5">
                <a href="{{ route('horarios.index') }}" class="hover:underline">Horarios</a>
                / {{ $horario->cancha->nombre ?? '' }} · {{ $horario->fecha->format('d/m/Y') }}
            </p>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Badge estado actual --}}
        @php
            $badgeEstado = ['Reservado'=>'badge-yellow','Confirmado'=>'badge-green','Cancelado'=>'badge-red','Completado'=>'badge-gray'][$horario->estado] ?? 'badge-gray';
        @endphp
        <div class="flex items-center gap-2 mb-4">
            <span class="text-sm text-gray-500">Estado actual:</span>
            <span class="badge {{ $badgeEstado }}">{{ $horario->estado }}</span>
        </div>

        <div class="card p-8" x-data="horarioForm({{ $tarifas->toJson() }})">
            <form method="POST" action="{{ route('horarios.update', $horario) }}">
                @csrf
                @method('PATCH')

                {{-- Cancha --}}
                <div class="mb-5">
                    <label for="cancha_id" class="form-label">Cancha</label>
                    <select id="cancha_id" name="cancha_id"
                            x-model="canchaSeleccionada"
                            @change="filtrarTarifas()"
                            class="form-input {{ $errors->has('cancha_id') ? 'border-red-400' : '' }}">
                        @foreach($canchas as $cancha)
                            <option value="{{ $cancha->id }}"
                                {{ old('cancha_id', $horario->cancha_id) == $cancha->id ? 'selected' : '' }}>
                                {{ $cancha->nombre }} ({{ $cancha->tipo }})
                            </option>
                        @endforeach
                    </select>
                    @error('cancha_id')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Tarifa --}}
                <div class="mb-5">
                    <label for="tarifa_id" class="form-label">Tarifa / Turno</label>
                    <select id="tarifa_id" name="tarifa_id"
                            x-model="tarifaSeleccionada"
                            @change="aplicarHorarioTarifa()"
                            class="form-input {{ $errors->has('tarifa_id') ? 'border-red-400' : '' }}">
                        <template x-for="tarifa in tarifasFiltradas" :key="tarifa.id">
                            <option :value="tarifa.id"
                                    :selected="tarifa.id == tarifaSeleccionada"
                                    x-text="tarifa.turno + ' — S/. ' + parseFloat(tarifa.precio_hora).toFixed(2) + '/h (' + tarifa.hora_inicio + ' a ' + tarifa.hora_fin + ')'">
                            </option>
                        </template>
                    </select>
                    @error('tarifa_id')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Fecha --}}
                <div class="mb-5">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input id="fecha" name="fecha" type="date"
                           class="form-input {{ $errors->has('fecha') ? 'border-red-400' : '' }}"
                           value="{{ old('fecha', $horario->fecha->format('Y-m-d')) }}" required>
                    @error('fecha')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Horario --}}
                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="hora_inicio" class="form-label">Hora de inicio</label>
                        <input id="hora_inicio" name="hora_inicio" type="time"
                               class="form-input {{ $errors->has('hora_inicio') ? 'border-red-400' : '' }}"
                               x-model="horaInicio" required>
                        @error('hora_inicio')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="hora_fin" class="form-label">Hora de fin</label>
                        <input id="hora_fin" name="hora_fin" type="time"
                               class="form-input {{ $errors->has('hora_fin') ? 'border-red-400' : '' }}"
                               x-model="horaFin" required>
                        @error('hora_fin')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Precio informativo --}}
                <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <p class="text-sm text-green-700 font-semibold">
                        Precio por hora:
                        <span class="text-green-900 text-base"
                              x-text="'S/. ' + (tarifaActual ? parseFloat(tarifaActual.precio_hora).toFixed(2) : '{{ number_format($horario->tarifa->precio_hora ?? 0, 2) }}')">
                        </span>
                    </p>
                </div>

                {{-- Estado (solo Admin / Recepcionista) --}}
                @if(in_array(auth()->user()->rol, [\App\Enums\Rol::Admin, \App\Enums\Rol::Recepcionista]))
                    <div class="mb-5">
                        <label for="estado" class="form-label">Estado</label>
                        <select id="estado" name="estado"
                                class="form-input {{ $errors->has('estado') ? 'border-red-400' : '' }}">
                            @foreach(['Reservado', 'Confirmado', 'Cancelado', 'Completado'] as $est)
                                <option value="{{ $est }}"
                                    {{ old('estado', $horario->estado) === $est ? 'selected' : '' }}>
                                    {{ $est }}
                                </option>
                            @endforeach
                        </select>
                        @error('estado')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                @else
                    <input type="hidden" name="estado" value="{{ $horario->estado }}">
                @endif

                {{-- Notas --}}
                <div class="mb-8">
                    <label for="notas" class="form-label">Notas <span class="font-normal text-gray-400">(opcional)</span></label>
                    <textarea id="notas" name="notas" rows="3"
                              class="form-input {{ $errors->has('notas') ? 'border-red-400' : '' }}"
                              maxlength="500"
                              placeholder="Ej: clase particular, partido amistoso...">{{ old('notas', $horario->notas) }}</textarea>
                    @error('notas')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('horarios.index') }}" class="btn-outline-sm py-2 px-5">Cancelar</a>
                    <button type="submit" class="btn-primary py-2 px-6">Actualizar Reserva</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function horarioForm(todasLasTarifas) {
            return {
                canchaSeleccionada: '{{ old('cancha_id', $horario->cancha_id) }}',
                tarifaSeleccionada: '{{ old('tarifa_id', $horario->tarifa_id) }}',
                tarifasFiltradas: [],
                tarifaActual: null,
                horaInicio: '{{ old('hora_inicio', $horario->hora_inicio) }}',
                horaFin:    '{{ old('hora_fin', $horario->hora_fin) }}',

                init() {
                    this.filtrarTarifas();
                    this.tarifaActual = todasLasTarifas.find(t => t.id == this.tarifaSeleccionada) || null;
                },

                filtrarTarifas() {
                    this.tarifasFiltradas = todasLasTarifas.filter(
                        t => t.cancha_id == this.canchaSeleccionada
                    );
                },

                aplicarHorarioTarifa() {
                    const tarifa = todasLasTarifas.find(t => t.id == this.tarifaSeleccionada);
                    if (tarifa) {
                        this.tarifaActual = tarifa;
                        this.horaInicio   = tarifa.hora_inicio;
                        this.horaFin      = tarifa.hora_fin;
                    }
                },
            };
        }
    </script>
</x-app-layout>
