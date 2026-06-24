@extends('errors.layout')

@section('titulo', '403 — Acceso Denegado')

@slot('slot')
    <span class="emoji">🚫</span>
    <div class="code">403</div>
    <h1 class="titulo">Acceso Denegado</h1>
    <p class="mensaje">
        {{ $exception->getMessage() ?: 'No tienes permisos para acceder a esta sección. Si crees que es un error, contacta al administrador del club.' }}
    </p>
    <a href="{{ url('/dashboard') }}" class="btn">Ir al Panel</a>
    <a href="javascript:history.back()" class="btn btn-outline">Volver</a>
@endslot
