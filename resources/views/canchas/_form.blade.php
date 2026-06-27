{{-- Formulario compartido de Cancha. FK por nombre, nunca el id.
     El estado de mantenimiento se gestiona mediante el modal dedicado. --}}
@php $cancha = $cancha ?? null; @endphp

{{-- Nombre --}}
<div class="mb-5">
    <label for="nombre" class="form-label">Nombre de la cancha</label>
    <input id="nombre" name="nombre" type="text" maxlength="100" placeholder="Ej. Cancha Central"
           class="form-input {{ $errors->has('nombre') ? 'border-red-400' : '' }}"
           value="{{ old('nombre', $cancha?->nombre) }}" required>
    @error('nombre')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
</div>

{{-- Superficie --}}
<div class="mb-5">
    <label for="tipo_superficie" class="form-label">Tipo de superficie</label>
    <select id="tipo_superficie" name="tipo_superficie"
            class="form-input {{ $errors->has('tipo_superficie') ? 'border-red-400' : '' }}">
        <option value="">Seleccione una superficie...</option>
        @foreach(\App\Models\Cancha::SUPERFICIES as $sup)
            <option value="{{ $sup }}"
                {{ old('tipo_superficie', $cancha?->tipo_superficie) === $sup ? 'selected' : '' }}>
                {{ $sup }}
            </option>
        @endforeach
    </select>
    @error('tipo_superficie')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
</div>

{{-- Modalidad --}}
<div class="mb-5">
    <label for="modalidad" class="form-label">Modalidad</label>
    <select id="modalidad" name="modalidad"
            class="form-input {{ $errors->has('modalidad') ? 'border-red-400' : '' }}">
        <option value="">Seleccione una modalidad...</option>
        @foreach(\App\Models\Cancha::MODALIDADES as $mod)
            <option value="{{ $mod }}"
                {{ old('modalidad', $cancha?->modalidad) === $mod ? 'selected' : '' }}>
                {{ $mod }}
            </option>
        @endforeach
    </select>
    @error('modalidad')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
</div>

{{-- Iluminación --}}
<div class="mb-6">
    <label class="form-label">Iluminación</label>
    @php
        $ilumVal = old('iluminacion', $cancha !== null ? ($cancha->iluminacion ? '1' : '0') : '1');
    @endphp
    <div class="flex gap-6 mt-2">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="iluminacion" value="1"
                   {{ $ilumVal === '1' ? 'checked' : '' }}
                   class="text-green-600 focus:ring-green-500 w-4 h-4">
            <span class="text-sm font-medium text-gray-700">Con iluminación</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="iluminacion" value="0"
                   {{ $ilumVal === '0' ? 'checked' : '' }}
                   class="text-green-600 focus:ring-green-500 w-4 h-4">
            <span class="text-sm font-medium text-gray-700">Sin iluminación</span>
        </label>
    </div>
    @error('iluminacion')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
</div>

{{-- Imagen --}}
<div class="mb-5" x-data="{ img: '{{ old('imagen', $cancha?->imagen ?? '') }}' }">
    <label class="form-label">Imagen de la cancha</label>
    <p class="text-xs text-gray-400 mb-3">Opcional — si no se elige, se usará la imagen por defecto del tipo de superficie.</p>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach(\App\Models\Cancha::IMAGENES as $sup => $archivo)
            <label class="cursor-pointer">
                <input type="radio" name="imagen" value="{{ $archivo }}"
                       x-model="img" class="hidden">
                <div class="rounded-xl overflow-hidden border-2 transition-all"
                     :class="img === '{{ $archivo }}' ? 'border-green-500 ring-2 ring-green-300' : 'border-gray-200'">
                    <img src="{{ asset('images/' . $archivo) }}" alt="{{ $sup }}"
                         class="w-full h-24 object-cover">
                    <div class="px-2 py-1.5 text-center text-xs font-semibold"
                         :class="img === '{{ $archivo }}' ? 'bg-green-50 text-green-700' : 'bg-gray-50 text-gray-600'">
                        {{ $sup }}
                    </div>
                </div>
            </label>
        @endforeach
    </div>
    <button type="button" @click="img = ''"
            x-show="img !== ''"
            class="mt-2 text-xs text-gray-400 hover:text-red-500 underline transition-colors">
        Quitar selección (usar predeterminada)
    </button>
    @error('imagen')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
</div>
