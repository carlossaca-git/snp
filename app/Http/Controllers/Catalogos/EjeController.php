<?php

namespace App\Http\Controllers\Catalogos;

use Illuminate\Routing\Controller;
use App\Models\Catalogos\EjePnd;
use App\Models\Catalogos\PlanNacional;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EjeController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth');

        //  Permiso de Lectura (Index y Show)
        $this->middleware('permiso:ejes.ver')->only(['index', 'show']);

        // Permiso de Escritura (Crear, Editar, Borrar)
        $this->middleware('permiso:ejes.gestionar')->only([
            'create',
            'store',
            'edit',
            'update',
            'destroy',
            'vistaAvance',
            'guardarAvance'
        ]);
    }
    /**
     * Muestra la lista de ejes con el conteo de sus objetivos relacionados.
     */

    public function index(Request $request)
    {
        //  Capturar texto de búsqueda
        $busqueda = $request->input('busqueda');
        $query = EjePnd::withCount('objetivosNacionales');
        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre_eje', 'LIKE', "%{$busqueda}%")
                    ->orWhere('descripcion', 'LIKE', "%{$busqueda}%")
                    ->orWhere('estado', 'LIKE', "%{$busqueda}%");
            });
        }
        $ejes = $query->orderBy('nombre_eje', 'desc')->get();
        $planActivo = PlanNacional::where('estado', 1)->first();

        return view('dashboard.configuracion.ejes.index', compact('ejes', 'planActivo'));
    }

    /**
     * Pasamos los datos al formulario
     */

    public function create()
    {
        $ejes = EjePnd::whereHas('plan', function ($q) {
            $q->activo();
        })->get();

        $planActivo = PlanNacional::activo()->first();

        // Si no hay plan no dejamos entrar al formulario
        if (!$planActivo) {
            return redirect()->route('planes-nacionales.index')
                ->with('error', 'Debe activar un Plan Nacional antes de crear Ejes.');
        }

        return view('dashboard.configuracion.ejes.crear', compact('planActivo', 'ejes'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        if (!$request->id_plan) {
            return back()->with('error', 'No se puede crear el eje: Falta el Plan Nacional Activo.');
        }
        // Validación
        $request->validate([
            'nombre_eje'     => 'required|unique:cat_eje_pnd,nombre_eje|max:250',
            'descripcion_eje' => 'nullable|string',
            'periodo_inicio' => 'required|integer|min:2000|max:2100',
            'periodo_fin'    => 'required|integer|gte:periodo_inicio|max:2100',
            'url_documento'  => 'nullable|url',
            'estado'         => 'required|boolean',
        ], [

            'periodo_fin.gte' => 'El año de fin no puede ser menor al de inicio.',
            'url_documento.url' => 'El formato del enlace no es válido.'
        ]);
        DB::beginTransaction();
        try {
            // Buscamos el primer plan que tenga estado ACTIVO
            $planActivo = PlanNacional::activo()->first();

            // Verificación de seguridad: Que pasa si no hay ningun plan activo
            if (!$planActivo) {
                return back()->with('error', 'Error crítico: No existe un Plan Nacional ACTIVO en el sistema. Contacte al administrador.');
            }



            $eje = new EjePnd();
            $eje->id_plan_nacional = $request->id_plan;
            $eje->nombre_eje      = $request->nombre_eje;
            $eje->descripcion     = $request->descripcion_eje;
            $eje->periodo_inicio  = $request->periodo_inicio;
            $eje->periodo_fin     = $request->periodo_fin;
            $eje->url_documento   = $request->url_documento;
            $eje->estado          = $request->estado;
            $eje->save();
            //  Redirección
            DB::commit();
            return redirect()->route('catalogos.ejes.index')
                ->with('success', 'El Eje Estratégico se ha registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
        }
        // Mensaje de error enviado a la vista
        return back()->with('error', 'Ocurrió un error al procesar la solicitud. Intente nuevamente.');
    }

    /**
     * Actualiza un eje existente.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre_eje'  => 'required|string|max:150|unique:cat_eje_pnd,nombre_eje,' . $id . ',id_eje',
            'descripcion' => 'nullable|string|max:500',
            'estado'      => 'required|in:0,1',
        ], [
            // Mensajes personalizados opcionales
            'nombre_eje.unique' => 'Ya existe otro eje con este nombre.',
            'nombre_eje.required' => 'El nombre del eje es obligatorio.'
        ]);
        Db::beginTransaction();
        try {
            //  Buscamos el registro
            $eje = EjePnd::findOrFail($id);

            //  Asignamos valores
            $eje->nombre_eje = $request->nombre_eje;
            $eje->descripcion = $request->descripcion_eje;
            $eje->estado = $request->estado;
            $eje->url_documento = $request->input('url_documento');

            //  Guardamos
            $eje->save();
            DB::commit();
            return redirect()->route('catalogos.ejes.index')
                ->with('success', 'Eje actualizado correctamente.');
        } catch (\Exception) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al procesar la solicitud. Intente nuevamente.');
        }
    }
    /**
     * Elimina un eje (Borrado Lógico si el modelo usa SoftDeletes).
     */
    public function destroy($id)
    {
        $eje = EjePnd::findOrFail($id);

        // Verificamos si tiene hijos antes de hacer nada
        if ($eje->objetivosNacionales()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar: Este eje tiene objetivos asociados. Primero debe eliminar o reasignar los objetivos.');
        }

        // Si no tiene hijos, procedemos a borrar
        $eje->delete();

        return redirect()->back()->with('success', 'Eje eliminado correctamente.');
    }
}
