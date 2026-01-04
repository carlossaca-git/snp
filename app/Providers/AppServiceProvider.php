<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Gate::before(function (User $user, string $ability) {
            // Si el usuario tiene el rol de 'admin', siempre devuelve 'true' (Acceso Total)

            if ($user->rol === 'SUPERADMIN') {
                return true;
            }
        });
        // Definimos una "Puerta" llamada 'admin-access'
        Gate::define('admin-access', function (User $user) {
            // Usamos el método que creamos en el modelo User anteriormente
            // Verifica si en su tabla seg_rol existe el nombre 'Administrador'\
           return $user->roles->contains('nombre_rol', 'SUPERADMIN');

        });
        Paginator::useBootstrap();



    }

}
