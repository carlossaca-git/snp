<?php

namespace App\Http\Controllers\Institucional;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;


use App\Models\Institucional\UnidadEjecutora;


class UnidadEjecutoraController extends Controller
{
    public function index()
    {
        $idOrg = Auth::user()->id_organizacion;

        // FILTRO DE SEGURIDAD: Solo unidades de MI organización
        $unidades = UnidadEjecutora::where('organizacion_id', $idOrg)
            ->orderBy('nombre_unidad', 'asc')
            ->get();

        return view('dashboard.estrategico.organizaciones.unidades.index', compact('unidades'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'nombre_unidad' => 'required|string|max:150',
            'codigo_presupuestario' => 'nullable|string|max:20',
            'nombre_responsable' => 'nullable|string|max:100',
        ]);
        try {
            DB::beginTransaction();

            // Creación manual para forzar el id_organizacion
            UnidadEjecutora::create([
                'organizacion_id' => Auth::user()->id_organizacion,
                'nombre_unidad' => $request->nombre_unidad,
                'codigo_unidad' => $request->codigo_unidad,
                'nombre_responsable' => $request->nombre_responsable,
                'estado' => 1
            ]);
            DB::commit();
            return back()->with('status', 'Unidad Ejecutora creada correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar la unidad: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        // Buscamos y aseguramos que pertenezca la organizacion del usuario logeado
        $unidad = UnidadEjecutora::where('organizacion_id', Auth::user()->id_organizacion)
            ->findOrFail($id);

        $request->validate([
            'nombre_unidad' => 'required|string|max:150',
            'codigo_unidad' => 'nullable|string|max:20',
            'nombre_responsable' => 'nullable|string|max:100',
            'estado' => 'required|boolean'
        ]);
        try {
            DB::beginTransaction();
            $unidad->update($request->all());
            DB::commit();
            return back()->with('status', 'Unidad actualizada correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la unidad: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $unidad = UnidadEjecutora::where('id_organizacion', Auth::user()->id_organizacion)
            ->findOrFail($id);

        // VALIDACIÓN DE INTEGRIDAD: No borrar si ya tiene proyectos
        if ($unidad->proyectos()->exists()) {
            return back()->with('error', 'No se puede eliminar: Esta unidad tiene proyectos asignados. Inactívela en su lugar.');
        }

        $unidad->delete();

        return back()->with('eliminar', 'ok');
    }
}
