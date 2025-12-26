<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Si el usuario no está logueado o no pasa la Gate 'admin-access'
        if (!auth()->check() || !Gate::allows('admin-access')) {
            abort(403, 'Acceso denegado. Se requieren permisos de Administrador.');
        }

        return $next($request);
    }
}
