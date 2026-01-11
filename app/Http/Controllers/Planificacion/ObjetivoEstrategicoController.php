<?php

namespace App\Http\Controllers\Planificacion;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Planificacion\ObjetivoEstrategico;
use App\Models\Catalogos\ObjetivoNacional;
use App\Models\Administracion\Unidad;
use App\Models\Planificacion\AlineacionEstrategica;


class ObjetivoEstrategicoController extends Controller
{
    /**
     * Muestra el listado de objetivos.
     */
    public function index()
    {   //Traemos la organizacion asociada al ausuario
        $idOrganizacion = Auth::user()->id_organizacion;
        // Traemos los objetivos con su padre (Nacional) para evitar consultas
        $objetivos = ObjetivoEstrategico::where('id_organizacion', $idOrganizacion)
            ->with('objetivosNacionales')
            ->orderBy('codigo', 'desc')
            ->paginate(10);

        return view('dashboard.estrategico.objestra.index', compact('objetivos', 'idOrganizacion'));
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create()
    {
        // Necesitamos la lista del PND para el Select
        $objetivosNacionales = ObjetivoNacional::where('estado', 1)->get();

        // Si tienes unidades, descomenta esto:
        // $unidades = Unidad::all();

        return view('dashboard.estrategico.objestra.crear', compact('objetivosNacionales'));
    }

    /**
     * Guarda el nuevo objetivo en la BD.
     */

    public function store(Request $request)
    {
        // 1. Validaciones
        $request->validate([
            'id_objetivo_nacional' => 'required|exists:cat_objetivo_nacional,id_objetivo_nacional',
            'codigo'               => 'required|unique:cat_objetivo_estrategico,codigo|max:20',
            'nombre'               => 'required|string|max:500',
            'descripcion'          => 'nullable|string',
            'tipo_objetivo'        => 'required|in:Estrategico,Tactico',
            'unidad_responsable_id' => 'required|string',
            'fecha_inicio'         => 'required|date',
            'fecha_fin'            => 'required|date|after_or_equal:fecha_inicio',
            'indicador'            => 'nullable|string|max:255',
            'linea_base'           => 'nullable|numeric',
            'meta'                 => 'nullable|numeric',

        ]);

        // 2. INICIAR TRANSDACCIÓN
        DB::beginTransaction();

        try {
            $idOrganizacion = Auth::user()->id_organizacion;

            if (!$idOrganizacion) {
                throw new \Exception('El usuario no está vinculado a ninguna organización.');
            }

            // 3. Crear el Objetivo
            $objetivo = ObjetivoEstrategico::create([
                'id_organizacion' => $idOrganizacion,
                'codigo'          => $request->codigo,
                'nombre'          => $request->nombre,
                'descripcion'     => $request->descripcion,
                'tipo_objetivo'   => $request->tipo_objetivo,
                'unidad_responsable_id' => $request->unidad_responsable_id,
                'indicador'       => $request->indicador,
                'linea_base'      => $request->linea_base,
                'meta'            => $request->meta,
                'fecha_inicio'    => $request->fecha_inicio,
                'fecha_fin'       => $request->fecha_fin,
                'estado'          => 1
            ]);

            // 4. Guardar la alineación (Usando la misma variable $objetivo)
            //$objetivo->objetivoNacional()->attach($request->id_objetivo_nacional);
            AlineacionEstrategica::create([
                'organizacion_id'         => $idOrganizacion,
                'objetivo_estrategico_id' => $objetivo->id_objetivo_estrategico, // El ID que acabas de crear
                'objetivo_nacional_id'    => $request->id_objetivo_nacional,      // El ID que viene del form
                // 'created_at' => now()
            ]);

            // 5. Confirmar cambios en BD
            DB::commit();

            return redirect()->route('estrategico.objetivos.index')
                ->with('success', 'El Objetivo Estratégico ha sido creado correctamente.');
        } catch (\Exception $e) {
            // 6. Revertir cambios si algo falla (IMPORTANTE)
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit($id)
    {
        $objetivo = ObjetivoEstrategico::with('objetivosNacionales')->findOrFail($id);
        if ($objetivo->id_organizacion != Auth::user()->id_organizacion) {
            // Si el ID de la org del objetivo no coincide con el del usuario...
            abort(403, 'Acceso No Autorizado'); // Cortamos el acceso.
        }

        $objetivosNacionales = ObjetivoNacional::where('estado', 1)->get();
        $alineacionActual = $objetivo->objetivosNacionales->first()->id_objetivo_nacional ?? null;
        return view('dashboard.estrategico.objestra.editar', compact('objetivo', 'objetivosNacionales', 'alineacionActual'));
    }

    /**
     * Actualiza el registro.
     */
    public function update(Request $request, $id)
    {
        // 1. Validamos TODO lo que viene del formulario
        $request->validate([
            'id_objetivo_nacional' => 'required', // Para la tabla intermedia
            // Validación única ignorando el actual
            'codigo' => 'required|max:20|unique:cat_objetivo_estrategico,codigo,' . $id . ',id_objetivo_estrategico',
            'nombre' => 'required|string|max:500',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
            // Los campos opcionales no necesitan 'required' pero sí validación de tipo si quieres
            'indicador'    => 'nullable|string',
            'meta'         => 'nullable|string',
            'linea_base'   => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $objetivo = ObjetivoEstrategico::findOrFail($id);

            // Seguridad: Verificar dueño
            if ($objetivo->id_organizacion != Auth::user()->id_organizacion) {
                abort(403, 'Acceso denegado');
            }

            $objetivo->update($request->except(['_token', '_method', 'id_objetivo_nacional']));

            // 3. ACTUALIZAR LA RELACIÓN (Tabla Intermedia)
            // Aquí tomamos ese ID que sacamos arriba y lo guardamos en la tabla puente.
            $objetivo->objetivosNacionales()->sync([
                $request->id_objetivo_nacional => ['organizacion_id' => Auth::user()->id_organizacion]
            ]);

            DB::commit(); // Guardar cambios

            return redirect()->route('estrategico.objetivos.index')
                ->with('success', 'Objetivo actualizado con éxito.');
        } catch (\Exception $e) {
            DB::rollBack(); // Si algo falla, deshacer todo
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Elimina el registro.
     */
    public function destroy($id)
    {
        try {
            $objetivo = ObjetivoEstrategico::findOrFail($id);

            // Opcional: Validar si ya tiene Programas o Metas hijas antes de borrar
            // if ($objetivo->programas()->count() > 0) {
            //    return back()->with('error', 'No se puede eliminar porque tiene Programas vinculados.');
            // }

            $objetivo->delete();

            return redirect()->route('estrategico.objetivos.index')
                ->with('success', 'Registro eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}
