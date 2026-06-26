{{-- Formulario compartido de Tarifa. Nunca expone el id. --}}
<div class="mb-5">
    <label for="nombre_tarifa" class="form-label">Nombre de la tarifa</label>
    <input id="nombre_tarifa" name="nombre_tarifa" type="text"
           class="form-input {{ $errors->has('nombre_tarifa') ? 'border-red-400' : '' }}"
           value="{{ old('nombre_tarifa', $tarifa->nombre_tarifa ?? '') }}"
           placeholder="Ej: Tarifa Mañana" required>
    @error('nombre_tarifa')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
</div>

<div class="mb-6">
    <label for="precio" class="form-label">Precio (S/)</label>
    <input id="precio" name="precio" type="number" step="0.01" min="0"
           class="form-input {{ $errors->has('precio') ? 'border-red-400' : '' }}"
           value="{{ old('precio', $tarifa->precio ?? '') }}" required>
    @error('precio')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
</div>
