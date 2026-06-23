<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        // Compara el valor string de nuestro Enum con los autorizados en la ruta
        if (in_array(request()->user()->rol->value, $roles)) {
            return $next($request);
        }

        abort(403, 'Acceso Denegado: Esta área es exclusiva para el personal del club.');
    }
}