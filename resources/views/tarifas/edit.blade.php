<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Editar Tarifa</h1>
            <p class="text-green-200 text-sm mt-0.5">
                <a href="{{ route('tarifas.index') }}" class="hover:underline">Tarifas</a> / Editar
            </p>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="card p-8">
            <form method="POST" action="{{ route('tarifas.update', $tarifa) }}">
                @csrf @method('PATCH')

                <div class="mb-5">
                    <label for="cancha_id" class="form-label">Cancha</label>
                    <select id="cancha_id" name="cancha_id" class="form-input">
                        @foreach($canchas as $cancha)
                            <option value="{{ $cancha->id }}"
                                {{ old('cancha_id', $tarifa->cancha_id) == $cancha->id ? 'selected' : '' }}>
                                {{ $cancha->nombre }} ({{ $cancha->tipo }})
                            </option>
                        @endforeach
                    </select>
                    @error('cancha_id')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="mb-5">
                    <label for="turno" class="form-label">Turno</label>
                    <select id="turno" name="turno" class="form-input">
                        @foreach(['Mañana', 'Tarde', 'Noche'] as $t)
                            <option value="{{ $t }}" {{ old('turno', $tarifa->turno) === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                    @error('turno')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="hora_inicio" class="form-label">Hora de inicio</label>
                        <input id="hora_inicio" name="hora_inicio" type="time"
                               class="form-input" value="{{ old('hora_inicio', $tarifa->hora_inicio) }}" required>
                        @error('hora_inicio')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="hora_fin" class="form-label">Hora de fin</label>
                        <input id="hora_fin" name="hora_fin" type="time"
                               class="form-input" value="{{ old('hora_fin', $tarifa->hora_fin) }}" required>
                        @error('hora_fin')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mb-5">
                    <label for="precio_hora" class="form-label">Precio por hora (S/.)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-semibold text-sm">S/.</span>
                        <input id="precio_hora" name="precio_hora" type="number"
                               step="0.01" min="0"
                               class="form-input pl-10"
                               value="{{ old('precio_hora', $tarifa->precio_hora) }}" required>
                    </div>
                    @error('precio_hora')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="mb-8">
                    <label for="estado" class="form-label">Estado</label>
                    <select id="estado" name="estado" class="form-input">
                        <option value="Activa"   {{ old('estado', $tarifa->estado) === 'Activa'   ? 'selected' : '' }}>Activa</option>
                        <option value="Inactiva" {{ old('estado', $tarifa->estado) === 'Inactiva' ? 'selected' : '' }}>Inactiva</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('tarifas.index') }}" class="btn-outline-sm py-2 px-5">Cancelar</a>
                    <button type="submit" class="btn-primary py-2 px-6">Actualizar Tarifa</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
