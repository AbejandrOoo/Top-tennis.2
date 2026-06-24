@extends('errors.layout')

@section('titulo', '429 — Demasiados intentos')

@slot('slot')
    <span class="emoji">🛑</span>
    <div class="code">429</div>
    <h1 class="titulo">Demasiados intentos</h1>
    <p class="mensaje">
        Realizaste demasiadas solicitudes en poco tiempo.
        Por seguridad, el sistema limitó temporalmente tu acceso.
        Espera unos minutos e intenta de nuevo.
    </p>
    <a href="{{ url('/') }}" class="btn">Ir al Inicio</a>
@endslot
