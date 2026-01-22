<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use App\Models\Seguridad\User;
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
        Gate::before(function (User $user, $ability) {
            // Si el usuario tiene el rol de 'admin', siempre devuelve 'true'

            if ($user->hasRole('SUPER_ADMIN')){
                return true;
            }
        });
        // Definimos una "Puerta" llamada 'admin-access'
        Gate::define('admin-access', function (User $user) {
            // Verifica si en la tabla seg_rol existe el nombre 'Administrador'\
           return $user->roles->contains('name', 'SUPER_ADMIN');

        });
        Paginator::useBootstrapFive();



    }

}
