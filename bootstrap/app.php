<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\CheckRol;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    // Aquí es donde unificamos todo el middleware
    ->withMiddleware(function (Middleware $middleware) {

        // 1. Configuramos las redirecciones
        $middleware->redirectTo(
            guests: '/login',
            users: '/principal',
        );

        // 2. Registramos TODOS los alias aquí mismo
        $middleware->alias([
            'admin' => \App\Http\Middleware\CheckAdmin::class,
            'rol'   => \App\Http\Middleware\CheckRol::class, // <-- Agregamos esta línea
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
