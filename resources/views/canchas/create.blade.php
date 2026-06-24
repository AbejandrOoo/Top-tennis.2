<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Nueva Cancha</h1>
            <p class="text-green-200 text-sm mt-0.5">
                <a href="{{ route('canchas.index') }}" class="hover:underline">Canchas</a> / Nueva
            </p>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="card p-8">
            <form method="POST" action="{{ route('canchas.store') }}">
                @csrf

                <div class="mb-5">
                    <label for="nombre" class="form-label">Nombre de la cancha</label>
                    <input id="nombre" name="nombre" type="text"
                           class="form-input {{ $errors->has('nombre') ? 'border-red-400' : '' }}"
                           value="{{ old('nombre') }}" placeholder="Ej: Cancha Central" required>
                    @error('nombre')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="mb-5">
                    <label for="tipo" class="form-label">Tipo de superficie</label>
                    <select id="tipo" name="tipo"
                            class="form-input {{ $errors->has('tipo') ? 'border-red-400' : '' }}">
                        <option value="">Seleccione...</option>
                        <option value="Arcilla"   {{ old('tipo') === 'Arcilla'   ? 'selected' : '' }}>Arcilla</option>
                        <option value="Sintética" {{ old('tipo') === 'Sintética' ? 'selected' : '' }}>Sintética</option>
                    </select>
                    @error('tipo')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="mb-8">
                    <label for="estado" class="form-label">Estado</label>
                    <select id="estado" name="estado" class="form-input">
                        <option value="Disponible"    {{ old('estado', 'Disponible') === 'Disponible'    ? 'selected' : '' }}>Disponible</option>
                        <option value="No Disponible" {{ old('estado') === 'No Disponible' ? 'selected' : '' }}>No Disponible</option>
                    </select>
                    @error('estado')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('canchas.index') }}" class="btn-outline-sm py-2 px-5">Cancelar</a>
                    <button type="submit" class="btn-primary py-2 px-6">Guardar Cancha</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
