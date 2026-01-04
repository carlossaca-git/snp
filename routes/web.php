<?php

use Illuminate\Support\Facades\Route;

// -----------------------------------------------------------------------------
// 1. IMPORTACIÓN DE CONTROLADORES
// -----------------------------------------------------------------------------
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

// Módulo Admin
use App\Http\Controllers\Admin\UserController;

// Módulo Estratégico
use App\Http\Controllers\Estrategico\OrganizacionController;
use App\Http\Controllers\Estrategico\AlineacionController;

// Módulo Inversión
use App\Http\Controllers\Inversion\ProgramaController;
use App\Http\Controllers\Inversion\ProyectoInversionController;

// Módulo Configuración (NUEVO)
use App\Http\Controllers\Configuracion\OdsController;
use App\Http\Controllers\Configuracion\ObjetivoEstrategicoController;
use App\Http\Controllers\Configuracion\ObjetivoNacionalController;

/*
|--------------------------------------------------------------------------
| 2. RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('inicio');

/*
|--------------------------------------------------------------------------
| 3. RUTAS PROTEGIDAS (AUTENTICACIÓN REQUERIDA)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // --- DASHBOARD / INICIO ---
    Route::get('/principal', [DashboardController::class, 'index'])
        ->name('dashboard');


    // --- PERFIL DE USUARIO (Breeze Standard) ---
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });


    /*
    |--------------------------------------------------------------------------
    | MÓDULO A: ADMINISTRACIÓN
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('administracion.')->group(function () {
        Route::resource('usuarios', UserController::class);
    });


    /*
    |--------------------------------------------------------------------------
    | MÓDULO B: PLANIFICACIÓN ESTRATÉGICA
    |--------------------------------------------------------------------------
    */
    Route::prefix('estrategico')->name('estrategico.')->group(function () {

        Route::resource('organizaciones', OrganizacionController::class);

        // API para Selects Dinámicos
        Route::get('/api/sectores/{id_macrosector}', [OrganizacionController::class, 'getSectores'])->name('api.sectores');
        Route::get('/api/subsectores/{id_sector}', [OrganizacionController::class, 'getSubsectores'])->name('api.subsectores');

        // Alineación (PND -> ODS)
        Route::prefix('alineacion')->name('alineacion.')->group(function () {
            Route::get('/{organizacion_id}/gestionar', [AlineacionController::class, 'index'])->name('gestionar');
            Route::post('/{organizacion_id}/guardar', [AlineacionController::class, 'store'])->name('guardar');
            Route::get('/organizacion/{id}/perfil', [AlineacionController::class, 'perfil'])->name('organizaciones.perfil');
            Route::put('/organizacion/{id}/perfil', [OrganizacionController::class, 'update'])->name('update-perfil');
            Route::post('/objetivos-estrategicos/ajax', [AlineacionController::class, 'storeObjetivoAjax'])->name('objetivos-estrategicos.store-ajax');
            Route::put('/alineacion/{id}/actualizar', [AlineacionController::class, 'update'])->name('actualizar');
            Route::delete('/alineacion/{id}/eliminar', [AlineacionController::class, 'destroy'])->name('eliminar');
        });
    });


    /*
    |--------------------------------------------------------------------------
    | MÓDULO C: GESTIÓN DE INVERSIÓN
    |--------------------------------------------------------------------------
    */
    Route::prefix('gestion')->name('inversion.')->group(function () {
        Route::resource('programas', ProgramaController::class);
        Route::resource('proyectos', ProyectoInversionController::class);

        // Ruta específica para update de proyectos si es necesaria fuera del resource
        Route::put('/proyectos/{id}', [ProyectoInversionController::class, 'update'])->name('proyectos.manual-update');
    });

    /*
    |--------------------------------------------------------------------------
    | MÓDULO D: CONFIGURACIÓN DE CATÁLOGOS ODS, PND, OEI
    | URL Base: /configuracion/...
    |--------------------------------------------------------------------------
    */
    Route::prefix('configuracion')->name('configuracion.')->group(function () {
        // Gestión de ODS (Objetivos de Desarrollo Sostenible)
        Route::resource('ods', OdsController::class);

        // Gestión de PND (Plan Nacional de Desarrollo / Objetivos Nacionales)
        Route::resource('pnd', ObjetivoNacionalController::class);

        // Gestión de Objetivos Estratégicos Institucionales
        Route::resource('objetivos-estrategicos', ObjetivoEstrategicoController::class);
    });
});

// Autenticación de Breeze
require __DIR__ . '/auth.php';
