@extends('errors.layout')

@section('titulo', '419 — Sesión expirada')

@slot('slot')
    <span class="emoji">⏱️</span>
    <div class="code">419</div>
    <h1 class="titulo">Tu sesión expiró</h1>
    <p class="mensaje">
        El formulario que intentaste enviar expiró por inactividad.
        Por seguridad, cada formulario tiene un tiempo límite.
        Vuelve atrás, recarga la página e inténtalo de nuevo.
    </p>
    <a href="javascript:history.back()" class="btn">Volver al formulario</a>
    <a href="{{ url('/') }}" class="btn btn-outline">Ir al Inicio</a>
@endslot
