<?php

use Illuminate\Support\Facades\Route;

// 1. IMPORTACIÓN DE CONTROLADORES
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

// Módulo Administración
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AuditoriaController;
// (Si tienes controlador de roles, impórtalo aquí)

// Módulo Normativa (Catálogos)
use App\Http\Controllers\Catalogos\OdsController;
use App\Http\Controllers\Catalogos\ObjetivoNacionalController;
use App\Http\Controllers\Catalogos\EjeController;
use App\Http\Controllers\Catalogos\IndicadorController;
use App\Http\Controllers\Catalogos\MetaNacionalController;
use App\Http\Controllers\Catalogos\PlanNacionalController;

// Módulo Estratégico (Institucional y Planificación)
use App\Http\Controllers\Institucional\OrganizacionController;
use App\Http\Controllers\Planificacion\AlineacionController;
use App\Http\Controllers\Planificacion\ObjetivoEstrategicoController;

// Módulo Inversión
use App\Http\Controllers\Inversion\ProgramaController;
use App\Http\Controllers\Inversion\ProyectoController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('inicio');

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS (Middleware Auth)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // --- COMUNES: DASHBOARD Y PERFIL ---
    Route::get('/principal', [DashboardController::class, 'index'])->name('dashboard');

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // =========================================================================
    // 1. BLOQUE ADMINISTRACIÓN Y NORMATIVA
    // =========================================================================
    // Acceso principal: ADMIN_TI (El SuperAdmin entra por excepción en Middleware)
    Route::middleware(['rol:ADMIN_TI'])->group(function () {

        // 1.1 Gestión de Usuarios
        Route::prefix('admin')->name('administracion.')->group(function () {
            Route::resource('usuarios', UserController::class);
            // Route::resource('roles', RoleController::class); // Si implementas roles
        });

        // 1.2 Normativa Nacional (Catálogos PND/ODS)
        // Aunque los Técnicos los LEEN, los Admins los GESTIONAN (CRUD)
        Route::prefix('catalogos')->name('catalogos.')->group(function () {
            Route::resource('ejes', EjeController::class);
            Route::resource('pnd', ObjetivoNacionalController::class); // Objetivos Nacionales
            Route::resource('ods', OdsController::class);
            Route::resource('metas', MetaNacionalController::class);
            Route::resource('indicadores', IndicadorController::class);

            // Nota: 'except' destroy, porque acordamos no borrar físicamente
            Route::resource('planes-nacionales', PlanNacionalController::class)->except(['destroy']);

            // Ruta ESPECIAL para activar un plan (POST porque cambia estado)
            Route::post('planes-nacionales/{id}/activar', [PlanNacionalController::class, 'activar'])
                ->name('planes-nacionales.activar');
        });
        //
        Route::post('/catalogos/metas/actualizar-avance', [MetaNacionalController::class, 'actualizarAvance'])
            ->name('catalogos.metas.actualizar');
    });

    // =========================================================================
    // 2. BLOQUE ESTRATÉGICO (FASE 1)
    // =========================================================================
    // Acceso: TECNICO_PLAN
    Route::middleware(['rol:TECNICO_PLAN'])->group(function () {

        // 2.1 Institucional (Ficha de la Entidad)
        Route::prefix('institucional')->name('institucional.')->group(function () {
            Route::resource('organizaciones', OrganizacionController::class);

            // APIs para combos dependientes (Sectores/Subsectores)
            Route::get('/api/sectores/{id_macrosector}', [OrganizacionController::class, 'getSectores'])->name('api.sectores');
            Route::get('/api/subsectores/{id_sector}', [OrganizacionController::class, 'getSubsectores'])->name('api.subsectores');
        });

        // 2.2 Estrategia (Objetivos y Alineación)
        // Agrupamos bajo 'estrategico' para orden en la URL
        Route::prefix('estrategico')->name('estrategico.')->group(function () {

            // A. Objetivos Institucionales (OEI) - ¡MOVIDO AQUÍ SEGÚN SIDEBAR!
            Route::resource('objetivos', ObjetivoEstrategicoController::class);

            // B. Alineación Estratégica (PND <-> OEI <-> ODS)
            Route::prefix('alineacion')->name('alineacion.')->group(function () {

                // Ruta para procesar la vinculación de ODS con Metas
                Route::post('/catalogos/metas/vincular-ods', [MetaNacionalController::class, 'vincularOds'])
                    ->name('metas.vincular');
                // Vista Principal de Alineación
                Route::get('/{organizacion_id}/gestionar', [AlineacionController::class, 'index'])->name('gestionar');

                // Acciones
                Route::post('/{organizacion_id}/guardar', [AlineacionController::class, 'store'])->name('guardar');
                Route::post('/objetivos-ajax', [AlineacionController::class, 'storeObjetivoAjax'])->name('objetivos-ajax');
                Route::put('/{id}/actualizar', [AlineacionController::class, 'update'])->name('actualizar');
                Route::delete('/{id}/eliminar', [AlineacionController::class, 'destroy'])->name('eliminar');
            });
        });
    });

    // =========================================================================
    // 3. BLOQUE INVERSIÓN (FASE 2)
    // =========================================================================
    // Acceso: TECNICO_PLAN (Igual que arriba, pero separado por orden lógico)
    Route::middleware(['rol:TECNICO_PLAN'])->prefix('inversion')->name('inversion.')->group(function () {

        // 3.1 Cartera de Inversión
        // Route::resource('planes', PlanAnualController::class); // Si tuvieras controlador de planes
        Route::resource('programas', ProgramaController::class);
        Route::resource('proyectos', ProyectoController::class);
        // Route::resource('financiamiento', FinanciamientoController::class); // Si existiera

        // 3.2 Rutas Auxiliares
        Route::get('/proyectos/get-objetivos/{ejeId}', [ProyectoController::class, 'getObjetivos'])->name('proyectos.getObjetivos');
        Route::delete('/documentos/{id}', [ProyectoController::class, 'eliminarDocumento'])->name('documentos.destroy');
    });

    // =========================================================================
    // 4. BLOQUE CONTROL Y REPORTES
    // =========================================================================

    // 4.1 Auditoría (Solo AUDITOR)
    Route::middleware(['rol:AUDITOR'])->prefix('auditoria')->name('auditoria.')->group(function () {
        Route::controller(AuditoriaController::class)->group(function () {
            Route::get('/', 'index')->name('index');      // Dashboard Auditoría
            Route::get('/logs', 'logs')->name('logs');    // Listado de Logs (si tienes el método)
            Route::get('/{id}', 'show')->name('show');    // Detalle
        });
    });

    // 4.2 Reportes (Acceso más abierto o restringido según necesites)
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/proyectos-general', [DashboardController::class, 'reporteGeneral'])->name('proyectos.general');
        Route::get('/proyecto/{id}', [ProyectoController::class, 'generarReporte'])->name('proyecto.individual');
        Route::get('/filtrar', [DashboardController::class, 'filtrarDatos'])->name('dashboard.filtrar');
    });
});

require __DIR__ . '/auth.php';
