<?php

namespace App\Http\Controllers\Catalogos;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Routing\Controller;
use App\Models\Catalogos\ObjetivoNacional;
use App\Models\Catalogos\EjePnd;
use App\Traits\Auditable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ObjetivoNacionalController extends Controller
{
    public function __construct()
    {
        //  Protección Base Nadie entra sin estar logueado
        $this->middleware('auth');

        //  Protección de LECTURA (Solo index y show)
        $this->middleware('permiso:objetivos.ver')->only(['index', 'show']);

        //  Protección de ESCRITURA (Crear, Editar, Borrar)
        $this->middleware('permiso:objetivos.gestionar')->only([
            'create',
            'store',
            'edit',
            'update',
            'destroy'
        ]);
    }

    /**
     * Muestra la lista de objetivos del PND.
     */
    /**
     * Muestra la lista de objetivos del PND.
     */
    public function index(Request $request)
    {
        // Capturamos el texto
        $busqueda = $request->input('busqueda');

        // Iniciamos la consulta cargando la relación 'eje'
        $query = ObjetivoNacional::with('eje');

        // Aplicamos filtros si hay búsqueda
        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                // Búsqueda directa en la tabla objetivos
                $q->where('codigo_objetivo', 'LIKE', "%{$busqueda}%")
                    ->orWhere('descripcion_objetivo', 'LIKE', "%{$busqueda}%")

                    // Búsqueda en la tabla relacionada (Eje)
                    ->orWhereHas('eje', function ($subQ) use ($busqueda) {
                        $subQ->where('nombre_eje', 'LIKE', "%{$busqueda}%");
                    });
            });
        }
        // Ordenamos por código para que salgan 1, 2, 3...
        $pnd = $query->orderBy('codigo_objetivo', 'asc')
            ->paginate(10)
            ->appends(['busqueda' => $busqueda]);

        // Cargamos la lista de ejes para el MODAL de crear/editar (sin filtrar)
        $relEje = EjePND::where('estado', 1)->get();

        return view('dashboard.configuracion.pnd.index', compact('pnd', 'relEje'));
    }

    /**
     * Formulario para crear un nuevo objetivo nacional (PND).
     */
    public function create()
    {
        // Si manejas ejes del PND (Social, Económico, etc.), pásalos a la vista
        // $ejes = EjePnd::all();
        // return view('configuracion.pnd.crear', compact('ejes'));

        return view('configuracion.pnd.crear');
    }

    /**
     * Guarda el objetivo en cat_objetivo_nacional.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo_objetivo'      => 'required|unique:cat_objetivo_nacional,codigo_objetivo',
            'descripcion_objetivo' => 'required',
            'id_eje'               => 'required|exists:cat_eje_pnd,id_eje',
            'periodo_inicio'       => 'required|integer',
            'periodo_fin'          => 'required|integer',
            'estado'               => 'required|in:1,0',
        ]);
        try {

            DB::beginTransaction();
            ObjetivoNacional::create($request->all());
            DB::commit();
            return redirect()->route('configuracion.pnd.index')
                ->with('success', 'Objetivo Nacional registrado exitosamente en el PND.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar objetivo: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error y no se guardó nada: ' . $e->getMessage());
        }
    }

    /**
     * Formulario de edición.
     */
    public function edit($id)
    {
        $objetivo = ObjetivoNacional::findOrFail($id);
        return view('configuracion.pnd.editar', compact('objetivo'));
    }

    /**
     * Actualiza el registro.
     */
    public function update(Request $request, $id)
    {
        //return dd($request->all());
        $objetivo = ObjetivoNacional::findOrFail($id);

        $request->validate([
            'codigo_objetivo'      => 'required|string|max:50',
            'descripcion_objetivo' => 'required|string',
            'id_eje'               => 'required|exists:cat_eje_pnd,id_eje',
            'periodo_inicio'       => 'nullable|integer|min:2000|max:2100',
            'periodo_fin'          => 'nullable|integer|min:2000|max:2100',
            'estado'               => 'required|in:1,0',
        ]);
        $pnd = ObjetivoNacional::findOrFail($id);
        $objetivo->update($request->all());

        return redirect()->route('configuracion.pnd.index')
            ->with('success', 'Objetivo Nacional actualizado correctamente.');
    }

    /**
     * Elimina un objetivo nacional.
     */
    public function destroy($id)
    {
        $objetivo = ObjetivoNacional::findOrFail($id);

        try {
            $objetivo->delete();
            return redirect()->route('Configuracion.pnd.index')
                ->with('success', 'Objetivo eliminado del catálogo.');
        } catch (\Exception $e) {
            return redirect()->route('configuracion.pnd.index')
                ->with('error', 'No se puede eliminar porque tiene objetivos estratégicos asociados.');
        }
    }
}
