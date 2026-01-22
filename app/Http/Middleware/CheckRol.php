<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRol
{
    public function handle(Request $request, Closure $next, string $rol)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('login');
        }
        if ($request->user()->tieneRol('SUPER_ADMIN')) {
        return $next($request);
    }

        // Usar el método tieneRol() que creamos en el modelo User
        if (!$request->user()->tieneRol($rol)) {
            // Si no tiene el rol, lanzamos un error 403 (Prohibido)
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
