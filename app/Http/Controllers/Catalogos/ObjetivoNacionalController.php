<?php

namespace App\Http\Controllers\Catalogos;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\ObjetivoNacional;
use App\Models\Catalogos\EjePnd;
use App\Traits\Auditable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ObjetivoNacionalController extends Controller
{

    use Auditable;
    use HasFactory;
    use SoftDeletes;

 /**
     * Muestra la lista de objetivos del PND.
     */ /**
     * Muestra la lista de objetivos del PND.
     */
    public function index()
    {
        // Traemos los objetivos nacionales.
        $pnd = ObjetivoNacional::with('eje')->get();
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
