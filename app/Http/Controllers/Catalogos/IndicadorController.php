<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Indicador;
use App\Models\Catalogos\MetaNacional;
use Illuminate\Http\Request;

class IndicadorController extends Controller
{
    public function index()
    {
        // Traemos indicadores con su meta padre
        $indicadores = Indicador::with('meta')->get();
        // Solo metas activas para el formulario
        $metas = MetaNacional::where('estado', 1)->get();

        return view('dashboard.configuracion.indicadores.index', compact('indicadores', 'metas'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'id_meta'            => 'required|exists:cat_meta_nacional,id_meta',
            'nombre_indicador'   => 'required|string',
            'linea_base'         => 'nullable|numeric',
            'anio_linea_base'    => 'nullable|integer|min:2000|max:2100',
            'meta_final'         => 'nullable|numeric',
            'unidad_medida'      => 'required|string',
            'frecuencia'         => 'required|string',
            'fuente_informacion' => 'nullable|string',
            'descripcion_indicador' => 'nullable|string',
            'estado'             => 'required|boolean',
        ]);

        Indicador::create($request->all());

        return redirect()->back()->with('success', 'Indicador registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_meta'          => 'required|exists:cat_meta_nacional,id_meta',
            'nombre_indicador' => 'required|string',
            'linea_base'       => 'nullable|numeric',
            'meta_final'       => 'nullable|numeric',
        ]);

        $indicador = Indicador::findOrFail($id);
        $indicador->update($request->all());

        return redirect()->back()->with('success', 'Indicador actualizado con éxito.');
    }
    public function destroy($id)
    {
        try {
            $indicador = Indicador::findOrFail($id);
            $indicador->delete();

            return redirect()->back()->with('success', 'El indicador ha sido eliminado correctamente.');
        } catch (\Exception $e) {
            // Esto protege el sistema si el indicador ya tiene "Avances" registrados (llave foránea)
            return redirect()->back()->with('error', 'No se puede eliminar el indicador porque tiene registros asociados.');
        }
    }
}
