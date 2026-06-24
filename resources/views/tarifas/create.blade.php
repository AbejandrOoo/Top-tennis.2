<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Nueva Tarifa</h1>
            <p class="text-green-200 text-sm mt-0.5">
                <a href="{{ route('tarifas.index') }}" class="hover:underline">Tarifas</a> / Nueva
            </p>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="card p-8">
            <form method="POST" action="{{ route('tarifas.store') }}">
                @csrf

                <div class="mb-5">
                    <label for="cancha_id" class="form-label">Cancha</label>
                    <select id="cancha_id" name="cancha_id"
                            class="form-input {{ $errors->has('cancha_id') ? 'border-red-400' : '' }}">
                        <option value="">Seleccione una cancha...</option>
                        @foreach($canchas as $cancha)
                            <option value="{{ $cancha->id }}" {{ old('cancha_id') == $cancha->id ? 'selected' : '' }}>
                                {{ $cancha->nombre }} ({{ $cancha->tipo }})
                            </option>
                        @endforeach
                    </select>
                    @error('cancha_id')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="mb-5">
                    <label for="turno" class="form-label">Turno</label>
                    <select id="turno" name="turno"
                            class="form-input {{ $errors->has('turno') ? 'border-red-400' : '' }}">
                        <option value="">Seleccione...</option>
                        @foreach(['Mañana', 'Tarde', 'Noche'] as $turno)
                            <option value="{{ $turno }}" {{ old('turno') === $turno ? 'selected' : '' }}>{{ $turno }}</option>
                        @endforeach
                    </select>
                    @error('turno')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="hora_inicio" class="form-label">Hora de inicio</label>
                        <input id="hora_inicio" name="hora_inicio" type="time"
                               class="form-input {{ $errors->has('hora_inicio') ? 'border-red-400' : '' }}"
                               value="{{ old('hora_inicio') }}" required>
                        @error('hora_inicio')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="hora_fin" class="form-label">Hora de fin</label>
                        <input id="hora_fin" name="hora_fin" type="time"
                               class="form-input {{ $errors->has('hora_fin') ? 'border-red-400' : '' }}"
                               value="{{ old('hora_fin') }}" required>
                        @error('hora_fin')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mb-5">
                    <label for="precio_hora" class="form-label">Precio por hora (S/.)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-semibold text-sm">S/.</span>
                        <input id="precio_hora" name="precio_hora" type="number"
                               step="0.01" min="0"
                               class="form-input pl-10 {{ $errors->has('precio_hora') ? 'border-red-400' : '' }}"
                               value="{{ old('precio_hora') }}" placeholder="0.00" required>
                    </div>
                    @error('precio_hora')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="mb-8">
                    <label for="estado" class="form-label">Estado</label>
                    <select id="estado" name="estado" class="form-input">
                        <option value="Activa"   {{ old('estado', 'Activa') === 'Activa'   ? 'selected' : '' }}>Activa</option>
                        <option value="Inactiva" {{ old('estado') === 'Inactiva' ? 'selected' : '' }}>Inactiva</option>
                    </select>
                    @error('estado')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('tarifas.index') }}" class="btn-outline-sm py-2 px-5">Cancelar</a>
                    <button type="submit" class="btn-primary py-2 px-6">Guardar Tarifa</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
