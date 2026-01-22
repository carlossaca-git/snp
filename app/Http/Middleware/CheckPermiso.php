<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Seguridad\User;

class CheckPermiso
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next, $permisoRequerido)
    {
        // Si no está logueado,
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        // Verificamos si la instancia es correcta
        if (!($user instanceof User)) {
            // Si por alguna razón Auth::user() no es tu modelo, abortamos
            abort(500, 'Error: El usuario autenticado no es válido.');
        }
        //Verificar si es SUPER_ADMIN (Llave Maestra)
        if ($user instanceof('SUPER_ADMIN')) {
            return $next($request);
        }
        // usamos la la funcion tiene permiso que esta definida en User
        if (!$user->tienePermiso($permisoRequerido)) {

            abort(403, 'ACCESO DENEGADO: No tienes permiso para realizar esta acción.');
        }

        return $next($request);
    }
}
