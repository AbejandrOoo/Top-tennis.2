@extends('errors.layout')

@section('titulo', '404 — Página no encontrada')

@slot('slot')
    <span class="emoji">🔍</span>
    <div class="code">404</div>
    <h1 class="titulo">Página no encontrada</h1>
    <p class="mensaje">
        El recurso que buscás no existe, fue eliminado o la URL está mal escrita.
        Revisá el enlace e intentá de nuevo.
    </p>
    <a href="{{ url('/') }}" class="btn">Ir al Inicio</a>
    @auth
        <a href="{{ url('/dashboard') }}" class="btn btn-outline">Ir al Panel</a>
    @endauth
@endslot
