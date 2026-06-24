<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Cancha — Top Tennis Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { background: #f8faf8; font-family: 'Inter', system-ui, sans-serif; }
        .form-input {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 14px;
            color: #1a2e1a;
            background: #fff;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-input:focus { border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.12); }
        .form-input.error { border-color: #f87171; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
    </style>
</head>
<body>

{{-- Nav --}}
<nav style="background:#1a3d1a;" class="px-6 py-4 flex items-center justify-between shadow-lg">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-green-500 flex items-center justify-center shadow-inner">
            <svg width="20" height="20" viewBox="0 0 40 40" fill="none">
                <circle cx="20" cy="20" r="18" stroke="white" stroke-width="3"/>
                <path d="M8 20 Q14 10 20 20 Q26 30 32 20" stroke="white" stroke-width="2.5" stroke-linecap="round" fill="none"/>
            </svg>
        </div>
        <div>
            <p class="text-white font-black text-sm leading-tight tracking-wide">TOP TENNIS DIGITAL</p>
            <p class="text-green-300 text-xs leading-tight">Panel de Administración</p>
        </div>
    </a>
    <a href="{{ route('dashboard') }}"
       class="flex items-center gap-2 text-green-200 hover:text-white text-sm font-semibold transition-colors">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M19 12H5M5 12l7-7M5 12l7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Volver al Dashboard
    </a>
</nav>

<div class="max-w-2xl mx-auto px-4 py-10">

    {{-- Flash --}}
    @if(session('error') || $errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 text-red-600 text-sm font-medium px-4 py-3 rounded-2xl">
            {{ session('error') ?? $errors->first() }}
        </div>
    @endif

    {{-- Card --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Header de la card --}}
        <div class="flex items-center gap-4 px-8 py-6 border-b border-gray-100">
            <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0"
                 style="background:#f0fdf4; border:1.5px solid #bbf7d0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <path d="M12 5v14M5 12h14" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-black text-green-900">Nueva Cancha</h1>
                <p class="text-sm text-gray-400 mt-0.5">Completa los datos para registrar la cancha</p>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('canchas.store') }}" class="px-8 py-7 space-y-5">
            @csrf

            {{-- Nombre --}}
            <div>
                <label for="nombre" class="form-label">Nombre de la cancha</label>
                <input id="nombre" name="nombre" type="text"
                       class="form-input {{ $errors->has('nombre') ? 'error' : '' }}"
                       value="{{ old('nombre') }}" placeholder="Ej: Cancha Central" required>
                @error('nombre')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Superficie + Modalidad --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="tipo" class="form-label">Tipo de superficie</label>
                    <select id="tipo" name="tipo" class="form-input {{ $errors->has('tipo') ? 'error' : '' }}">
                        <option value="">Selecciona...</option>
                        @foreach(['Arcilla','Sintética','Hierba','Dura'] as $t)
                            <option value="{{ $t }}" {{ old('tipo') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                    @error('tipo')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="modalidad" class="form-label">Modalidad</label>
                    <select id="modalidad" name="modalidad" class="form-input">
                        <option value="Singles" {{ old('modalidad','Singles') === 'Singles' ? 'selected' : '' }}>Singles (1 vs 1)</option>
                        <option value="Dobles"  {{ old('modalidad') === 'Dobles'  ? 'selected' : '' }}>Dobles (2 vs 2)</option>
                    </select>
                </div>
            </div>

            {{-- Capacidad + Estado --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="capacidad" class="form-label">Capacidad (jugadores)</label>
                    <input id="capacidad" name="capacidad" type="number" min="1" max="8"
                           class="form-input {{ $errors->has('capacidad') ? 'error' : '' }}"
                           value="{{ old('capacidad', 2) }}">
                    @error('capacidad')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="estado" class="form-label">Estado inicial</label>
                    <select id="estado" name="estado" class="form-input">
                        <option value="Disponible"    {{ old('estado','Disponible') === 'Disponible'    ? 'selected' : '' }}>Disponible (Activa)</option>
                        <option value="Bloqueada"     {{ old('estado') === 'Bloqueada' ? 'selected' : '' }}>Bloqueada</option>
                        <option value="No Disponible" {{ old('estado') === 'No Disponible' ? 'selected' : '' }}>No Disponible (Mantenimiento)</option>
                    </select>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('dashboard') }}"
                   class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:border-gray-300 text-sm font-semibold transition-all">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-white text-sm font-bold transition-all"
                        style="background: linear-gradient(90deg,#4ade80,#22c55e); box-shadow:0 4px 14px rgba(34,197,94,.3);">
                    Guardar Cancha
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
