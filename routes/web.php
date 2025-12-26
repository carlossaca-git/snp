<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

// Ruta que direge a la pagina inicio
Route::get('/', function () {
    return view('welcome');
})->name('inicio');

/*
|--------------------------------------------------------------------------
| 2. RUTAS PROTEGIDAS (Requieren Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard Principal
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Gestión del Perfil (Breeze) utilizando el motor de controladores agrupados
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | 3. MÓDULO ADMINISTRATIVO (Gestión de Usuarios)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {

        Route::controller(UserController::class)->group(function () {
            // Listado de usuarios
            Route::get('/usuarios', 'index')->name('users.index');

            // Creación de usuarios
            Route::get('/usuarios/crear', 'create')->name('users.create');
            Route::post('/usuarios/guardar', 'store')->name('users.store');

            // Edición de usuarios
            Route::get('/usuarios/{user}/editar', 'edit')->name('users.edit');
            Route::put('/usuarios/{user}', 'update')->name('users.update');

            // Eliminación de usuarios
            Route::delete('/usuarios/{user}', 'destroy')->name('users.destroy');
        });

    });
});

/*
|--------------------------------------------------------------------------
| 4. RUTAS DE AUTENTICACIÓN (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
