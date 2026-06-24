<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Editar Cancha</h1>
            <p class="text-green-200 text-sm mt-0.5">
                <a href="{{ route('canchas.index') }}" class="hover:underline">Canchas</a> / {{ $cancha->nombre }}
            </p>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="card p-8">
            <form method="POST" action="{{ route('canchas.update', $cancha) }}">
                @csrf @method('PATCH')

                <div class="mb-5">
                    <label for="nombre" class="form-label">Nombre de la cancha</label>
                    <input id="nombre" name="nombre" type="text"
                           class="form-input {{ $errors->has('nombre') ? 'border-red-400' : '' }}"
                           value="{{ old('nombre', $cancha->nombre) }}" required>
                    @error('nombre')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="tipo" class="form-label">Tipo de superficie</label>
                        <select id="tipo" name="tipo" class="form-input">
                            @foreach(['Arcilla','Sintética','Hierba','Dura'] as $t)
                                <option value="{{ $t }}" {{ old('tipo', $cancha->tipo) === $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="modalidad" class="form-label">Modalidad</label>
                        <select id="modalidad" name="modalidad" class="form-input">
                            <option value="Singles" {{ old('modalidad', $cancha->modalidad) === 'Singles' ? 'selected' : '' }}>Singles</option>
                            <option value="Dobles"  {{ old('modalidad', $cancha->modalidad) === 'Dobles'  ? 'selected' : '' }}>Dobles</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div>
                        <label for="capacidad" class="form-label">Capacidad (jugadores)</label>
                        <input id="capacidad" name="capacidad" type="number" min="1" max="8"
                               class="form-input" value="{{ old('capacidad', $cancha->capacidad) }}">
                    </div>
                    <div>
                        <label for="estado" class="form-label">Estado</label>
                        <select id="estado" name="estado" class="form-input">
                            <option value="Disponible"    {{ old('estado', $cancha->estado) === 'Disponible'    ? 'selected' : '' }}>Disponible</option>
                            <option value="No Disponible" {{ old('estado', $cancha->estado) === 'No Disponible' ? 'selected' : '' }}>No Disponible</option>
                            <option value="Bloqueada"     {{ old('estado', $cancha->estado) === 'Bloqueada'     ? 'selected' : '' }}>Bloqueada</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('dashboard') }}" class="btn-outline-sm py-2 px-5">Cancelar</a>
                    <button type="submit" class="btn-primary py-2 px-6">Actualizar Cancha</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
