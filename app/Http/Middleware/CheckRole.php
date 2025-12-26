<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $roleName)
    {
        // 1. Verificamos si el usuario está logueado
        if (!Auth::check()) {
            return redirect('login');
        }

        // 2. Buscamos dinámicamente si el usuario tiene el rol pasado por parámetro
        // Esto consulta tu tabla seg_rol a través de la relación
        $hasRole = Auth::user()->roles()->where('nombre_rol', $roleName)->exists();

        if (!$hasRole) {
            abort(403, 'Tu perfil no tiene acceso a esta sección.');
        }

        return $next($request);
    }
}

