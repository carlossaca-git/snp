<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Esta es la línea mágica que debes agregar o corregir:
        $middleware->redirectTo(
            guests: '/login',
            users: '/principal', // <-- Asegúrate de que apunte a tu nueva ruta
        );
        $middleware->alias([
            // 'admin' es el nombre que usas en el archivo de rutas (web.php)
            'admin' => \App\Http\Middleware\CheckAdmin::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
