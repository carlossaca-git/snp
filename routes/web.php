<?php

use Illuminate\Support\Facades\Route;

//  Dashboard y Perfil
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

//  Administración (Usuarios y Roles)
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RolController;
use App\Http\Controllers\Admin\AuditoriaController;

//  Catálogos y Normativa
use App\Http\Controllers\Catalogos\OdsController;
use App\Http\Controllers\Catalogos\ObjetivoNacionalController;
use App\Http\Controllers\Catalogos\EjeController;
use App\Http\Controllers\Catalogos\IndicadorController;
use App\Http\Controllers\Catalogos\MetaNacionalController;
use App\Http\Controllers\Catalogos\PlanNacionalController;
use App\Http\Controllers\Catalogos\AvanceMetaController;
use App\Http\Controllers\Catalogos\AvanceIndicadorController;
use App\Http\Controllers\DashboardController as ControllersDashboardController;
use App\Http\Controllers\DashboardController as HttpControllersDashboardController;
use App\Models\Catalogos\MetaNacional;

//  Institucional y Planificación
use App\Http\Controllers\Institucional\OrganizacionController;
use App\Http\Controllers\Planificacion\AlineacionController;
use App\Http\Controllers\Planificacion\ObjetivoEstrategicoController;

//  Inversión (Proyectos)
use App\Http\Controllers\Inversion\ProgramaController;
use App\Http\Controllers\Inversion\ProyectoController;
use App\Http\Controllers\Inversion\MarcoLogicoController;
use App\Http\Controllers\Planificacion\PlanInstitucionalController;

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
|           RUTAS PROTEGIDAS (Middleware Auth)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // ---  DASHBOARD Y PERFIL (Acceso para todos los logueados) ---
    Route::get('/principal', [DashboardController::class, 'index'])->name('dashboard');


    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // =========================================================================
    //           ADMINISTRACIÓN DEL SISTEMA
    // =========================================================================
    Route::middleware(['permiso:usuarios.gestionar'])->prefix('admin')->name('administracion.')->group(function () {

        Route::resource('usuarios', UserController::class);
        //Route::get('/perfil', [UserController::class, 'show'])->name('show');
        Route::resource('roles', RolController::class);


        // Auditoría (Permiso específico para auditar)
        Route::prefix('auditoria')->name('auditoria.')->controller(AuditoriaController::class)->group(function () {
            Route::get('/', 'index')->middleware('permiso:auditoria.ver')->name('index');
            Route::get('/logs', 'logs')->name('logs');
            Route::get('/{id}', 'show')->name('show');
        });
    });

    // =========================================================================
    //           NORMATIVA Y CATALOGOS
    // =========================================================================
    Route::middleware(['auth'])->prefix('catalogos')->name('catalogos.')->group(function () {

        Route::post('planes-nacionales/{plan}/activar', [PlanNacionalController::class, 'activar'])
            ->name('planes-nacionales.activar');

        // Estándar (index, create, store, show, edit, update)
        Route::resource('planes-nacionales', PlanNacionalController::class);
        // EJES
        Route::resource('ejes', EjeController::class);
        // OBJETIVOS
        Route::resource('objetivos', ObjetivoNacionalController::class);
        // ODS
        Route::resource('ods', OdsController::class);
        // METAS
        Route::post('metas/vincular-ods', [MetaNacionalController::class, 'vincularOds'])
            ->name('metas.vincular');
        //Actualizar avances de metas
        Route::post('metas/guardar-avance', [AvanceMetaController::class, 'store'])
            ->name('metas.avances.store');
        Route::post('metas/actualizar-avance', [MetaNacionalController::class, 'actualizarAvance'])
            ->name('metas.actualizar');


        // Estándar
        Route::resource('metas', MetaNacionalController::class);
        Route::resource('metas', MetaNacionalController::class)->except(['show']);
        //Obtener metas a partir de su id esto es posible por la relacion padre a hijo
        Route::get('api/obtener-metas/{id}', function ($id) {
            $metas = MetaNacional::where('id_objetivo_nacional', $id)
                ->select('id_meta_nacional', 'nombre_meta', 'codigo_meta')
                ->get();
            return response()->json($metas);
        })->name('api.obtener_metas');


        // INDICADORES
        Route::get('indicadores/{id}/kardex', [IndicadorController::class, 'show'])
            ->name('indicadores.kardex');
        // Estándar
        Route::resource('indicadores', IndicadorController::class);

        //Actualizar avances de indicadores
        Route::post('indicadores/guardar-avance', [AvanceIndicadorController::class, 'store'])
            ->name('indicadores.avances.store');
    });

    // =========================================================================
    //          ESTRATÉGICO:Institucional y Alineación
    // =========================================================================
    Route::prefix('institucional')->name('institucional.')->group(function () {

        // Gestión de Organizaciones (Solo Admin Institucional o Super Admin)
        Route::resource('organizaciones', OrganizacionController::class)
            ->middleware('permiso:organizacion.editar');

        // APIs JSON (Abiertas para que funcionen los selects dinámicos)
        Route::get('/api/sectores/{id_macrosector}', [OrganizacionController::class, 'getSectores'])
            ->name('api.sectores');
        Route::get('/api/subsectores/{id_sector}', [OrganizacionController::class, 'getSubsectores'])
            ->name('api.subsectores');
    });

    Route::prefix('estrategico')->name('estrategico.')->middleware(['permiso:planificacion.gestionar'])->group(function () {

        // Recurso estándar para Objetivos
        Route::resource('objetivos', ObjetivoEstrategicoController::class);
        Route::get('/planes/{idPlan}/objetivos', [ObjetivoEstrategicoController::class, 'index'])
            ->name('planificacion.objetivos.index');

        // GRUPO 1: Alineación
        Route::prefix('alineacion')->name('alineacion.')->controller(AlineacionController::class)->group(function () {
            Route::resource('general', AlineacionController::class);
            // Rutas personalizadas (Recomendado si usas URLs en español como 'gestionar')
            Route::get('/{organizacion_id}/gestionar', 'index')->name('gestionar');
            Route::post('/{organizacion_id}/guardar', 'store')->name('guardar');
            Route::put('/{id}/actualizar', 'update')->name('actualizar');
            Route::delete('/{id}/eliminar', 'destroy')->name('eliminar');

            // Rutas adicionales
            Route::post('/objetivos-ajax', 'storeObjetivoAjax')->name('objetivos-ajax');
            Route::get('/{id}', 'show')->name('show');
        });


        Route::prefix('planificacion')->name('planificacion.')->controller(PlanInstitucionalController::class)->group(function () {
            Route::resource('planes', PlanInstitucionalController::class);
            Route::put('/planes/{id}/cerrar', [PlanInstitucionalController::class, 'cerrarPlan'])
                ->name('planes.cerrar');
        });
    });

    // =========================================================================
    //           INVERSIÓN - PROYECTOS - GRANULARIDAD PURA
    // =========================================================================
    Route::prefix('inversion')->name('inversion.')->group(function () {

        Route::resource('programas', ProgramaController::class);

        // --- SUBMDULO DE PROYECTOS
        Route::prefix('proyectos')->name('proyectos.')->controller(ProyectoController::class)
            ->group(function () {

                //  (Todos los técnicos y jefes)
                Route::get('/', 'index')
                    ->middleware('permiso:proyectos.ver')
                    ->name('index');

                // Crear Solo Jefes
                Route::get('/create', 'create')->middleware('permiso:proyectos.crear')->name('create');
                Route::post('/', 'store')->middleware('permiso:proyectos.crear')->name('store');

                // Editar (Jefes y Técnicos designados)
                Route::get('/{id}/edit', 'edit')->middleware('permiso:proyectos.editar')->name('edit');
                Route::put('/{id}', 'update')->middleware('permiso:proyectos.editar')->name('update');

                //  Eliminar (Solo Jefes o Admins)
                Route::delete('/{id}', 'destroy')->middleware('permiso:proyectos.eliminar')->name('destroy');

                //  Auxiliares
                Route::get('/get-objetivos/{ejeId}', 'getObjetivos')
                    ->name('getObjetivos');
                Route::delete('/documentos/{id}', 'eliminarDocumento')
                    ->name('documentos.destroy');
                //Ver el perfil
                Route::get('/{id}', 'show')
                    ->middleware('permiso:proyectos.ver')
                    ->name('show');

                // Ruta para ver el tablero del Marco Lógico
                Route::get('/{id}/marco-logico', [MarcoLogicoController::class, 'index'])
                    ->name('marco-logico.index');
                // Rutas para guardar elementos (Propósito, Componentes, Actividades)
                Route::post('/marco-logico/guardar', [MarcoLogicoController::class, 'store'])
                    ->name('marco-logico.store');
                //Ruta para actualizar
                Route::put('/marco-logico/{id}', [MarcoLogicoController::class, 'update'])
                    ->name('marco-logico.update');
                //Ruta para reportar avance
                Route::post('/avance', [MarcoLogicoController::class, 'storeAvance'])
                    ->name('registrar-avance.store');
                //Ruta para eliminar
                Route::delete('/marco-logico/{id}', [MarcoLogicoController::class, 'destroy'])
                    ->name('marco-logico.destroy');
            });
    });

    // =========================================================================
    //                  REPORTES (Solo lectura)
    // =========================================================================
    Route::prefix('reportes')->name('reportes.')->middleware(['permiso:reportes.ver'])->group(function () {
        Route::get('/proyectos-general', [DashboardController::class, 'reporteGeneral'])
            ->name('proyectos.general');

        Route::get('/proyecto/{id}', [ProyectoController::class, 'generarReporte'])
            ->name('proyecto.individual');

        Route::get('/filtrar', [DashboardController::class, 'filtrarDatos'])->name('dashboard.filtrar');
        Route::get('/indicadores/{id}/pdf', [IndicadorController::class, 'generarPdf'])->name('catalogos.indicadores.pdf');
    });
});


require __DIR__ . '/auth.php';
