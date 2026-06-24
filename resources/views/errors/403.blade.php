@extends('errors.layout')

@section('titulo', '403 — Acceso Denegado')

@slot('slot')
    <span class="emoji">🚫</span>
    <div class="code">403</div>
    <h1 class="titulo">Acceso Denegado</h1>
    <p class="mensaje">
        {{ $exception->getMessage() ?: 'No tenés permisos para acceder a esta sección. Si creés que es un error, contactá al administrador del club.' }}
    </p>
    <a href="{{ url('/dashboard') }}" class="btn">Ir al Panel</a>
    <a href="javascript:history.back()" class="btn btn-outline">Volver</a>
@endslot
