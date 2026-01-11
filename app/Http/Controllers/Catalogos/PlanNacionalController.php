<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\PlanNacional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlanNacionalController extends Controller
{
    // 1. LISTAR LOS PLANES
    public function index()
    {
        // Ordenamos por fecha de inicio descendente (lo más nuevo primero)

        $planes = PlanNacional::orderBy('periodo_inicio', 'desc')->get();
        return view('dashboard.configuracion.plannacional.index', compact('planes'));
    }

    // 2. MOSTRAR FORMULARIO
    public function create()
    {
        return view('dashboard.configuracion.plannacional.crear');
    }

    // 3. GUARDAR (LOGICA DE NEGOCIO)
    public function store(Request $request)
    {
        // 1. La validación va ANTES de la transacción.
        // Si falla aquí, Laravel redirige automáticamente y no abrimos conexión innecesaria.
        $request->validate([
            'nombre' => 'required|string|max:255',
            'periodo_inicio' => 'required|integer|min:2000|max:2100',
            'periodo_fin' => 'required|integer|gt:periodo_inicio',
            'registro_oficial' => 'nullable|string|max:255'
        ]);

        // 2. Iniciamos la Transacción
        DB::beginTransaction();

        try {
            // Intentamos crear el registro
            $plan = PlanNacional::create([
                'nombre' => $request->nombre,
                'periodo_inicio' => $request->periodo_inicio,
                'periodo_fin' => $request->periodo_fin,
                'registro_oficial' => $request->registro_oficial,
                'estado' => 'INACTIVO' // Siempre nace inactivo por seguridad
            ]);

            // Si llegamos aquí, todo salió bien. Confirmamos cambios en la BD.
            DB::commit();

            return redirect()->route('planes-nacionales.index')
                ->with('success', 'Plan Nacional registrado exitosamente.');
        } catch (\Exception $e) {
            // 3. Si algo falló, deshacemos TODO (Rollback)
            DB::rollBack();

            // Registramos el error técnico en el archivo laravel.log (storage/logs)
            Log::error('Error creando Plan Nacional: ' . $e->getMessage());

            // Devolvemos al usuario al formulario con un mensaje amigable
            return back()
                ->withInput() // Mantiene lo que el usuario escribió
                ->with('error', 'Ocurrió un error al guardar el plan. Por favor intente nuevamente.');
        }
    }
    // ... métodos anteriores (index, create, store, activar) ...

    // 5. FORMULARIO DE EDICIÓN
    public function edit($id)
    {
        $plan = PlanNacional::findOrFail($id);
        return view('dashboard.configuracion.plannacional.editar', compact('plan'));
    }

    // 6. ACTUALIZAR DATOS
    public function update(Request $request, $id)
    {
        // Validación
        $request->validate([
            'nombre' => 'required|string|max:255',
            'periodo_inicio' => 'required|integer|min:1990|max:2100',
            'periodo_fin' => 'required|integer|gt:periodo_inicio|max:2100',
            'registro_oficial' => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();

        try {
            // Buscamos el plan. Si no existe, findOrFail lanza excepción y salta al catch
            $plan = PlanNacional::findOrFail($id);

            // Actualizamos
            $plan->update([
                'nombre' => $request->nombre,
                'periodo_inicio' => $request->periodo_inicio,
                'periodo_fin' => $request->periodo_fin,
                'registro_oficial' => $request->registro_oficial,
            ]);

            DB::commit();

            return redirect()->route('catalogos.planes-nacionales.index')
                ->with('success', 'Información actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error actualizando Plan ID ' . $id . ': ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'No se pudieron guardar los cambios.');
        }
    }

    // 4. EL SWITCH "REY DE LA COLINA"
    public function activar($id)
    {
        try {
            DB::transaction(function () use ($id) {
                // A. Apagar TODOS los planes
                PlanNacional::query()->update(['estado' => 'INACTIVO']);

                // B. Encender SOLO el seleccionado
                $plan = PlanNacional::findOrFail($id);
                $plan->estado = 'ACTIVO';
                $plan->save();
            });

            return back()->with('success', 'El sistema ahora opera bajo el: ' . PlanNacional::find($id)->nombre);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar el plan activo.');
        }
    }
}
