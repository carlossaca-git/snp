<?php

use App\Http\Middleware\CheckPermiso;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\CheckRol;
use App\Http\Middleware\CheckAdmin;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    // Aquí es donde unificamos todo el middleware
    ->withMiddleware(function (Middleware $middleware) {

        //  Configuramos las redirecciones
        $middleware->redirectTo(
            guests: '/login',
            users: '/principal',
        );

        // Registramos TODOS los alias aquí mismo
        $middleware->alias([
            //'admin' => CheckAdmin::class,
            //'rol'   => CheckRol::class,
            //'permiso' => CheckPermiso::class,
            'rol' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permiso' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
