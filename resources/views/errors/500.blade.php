@extends('errors.layout')

@section('titulo', '500 — Error del servidor')

@slot('slot')
    <span class="emoji">⚙️</span>
    <div class="code">500</div>
    <h1 class="titulo">Error interno del servidor</h1>
    <p class="mensaje">
        Algo salió mal de nuestro lado. El error fue registrado automáticamente
        y el equipo técnico será notificado. Intenta de nuevo en unos minutos.
    </p>
    <a href="{{ url('/') }}" class="btn">Ir al Inicio</a>
    <a href="javascript:history.back()" class="btn btn-outline">Volver</a>
@endslot
