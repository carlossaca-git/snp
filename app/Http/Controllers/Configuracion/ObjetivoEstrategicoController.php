<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\ObjetivoNacional;
use App\Models\Estrategico\ObjetivoEstrategico;
use App\Models\Configuracion\Pnd;
use Illuminate\Http\Request;

class ObjetivoEstrategicoController extends Controller
{
    /**
     * Muestra el listado de objetivos estratégicos institucionales.
     */
    public function index()
    {
        // Traemos los objetivos con su relación al PND para mostrar a qué alinean
        $objetivos = ObjetivoEstrategico::with('objetivoNacional')->get();

        return view('configuracion.objetivos-estrategicos.index', compact('objetivos'));
    }

    /**
     * Muestra el formulario para crear uno nuevo.
     */
    public function create()
    {
        // Necesitamos el listado de PND para el select de alineación
        $planes = ObjetivoNacional::orderBy('objetivo', 'asc')->get();

        return view('configuracion.objetivos-estrategicos.crear', compact('planes'));
    }

    /**
     * Guarda el nuevo objetivo estratégico.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_objetivo_nacional' => 'required|exists:cat_objetivo_nacional,id',
            'nombre'               => 'required|string|max:500',
            'descripcion'          => 'nullable|string',
            'codigo'               => 'nullable|string|max:20', // Ej: OEI-01
        ]);

        ObjetivoEstrategico::create($request->all());

        return redirect()->route('config.objetivos-estrategicos.index')
                         ->with('success', 'Objetivo Estratégico creado y alineado al PND.');
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit($id)
    {
        $objetivo = ObjetivoEstrategico::findOrFail($id);
        $planes = ObjetivoNacional::orderBy('objetivo', 'asc')->get();

        return view('configuracion.objetivos-estrategicos.editar', compact('objetivo', 'planes'));
    }

    /**
     * Actualiza el registro.
     */
    public function update(Request $request, $id)
    {
        $objetivo = ObjetivoEstrategico::findOrFail($id);

        $request->validate([
            'id_objetivo_nacional' => 'required|exists:cat_objetivo_nacional,id',
            'nombre'               => 'required|string|max:500',
        ]);

        $objetivo->update($request->all());

        return redirect()->route('config.objetivos-estrategicos.index')
                         ->with('success', 'Objetivo Estratégico actualizado correctamente.');
    }

    /**
     * Elimina el objetivo.
     */
    public function destroy($id)
    {
        $objetivo = ObjetivoEstrategico::findOrFail($id);

        // Antes de borrar, podrías validar si tiene alineaciones activas
        $objetivo->delete();

        return redirect()->route('config.objetivos-estrategicos.index')
                         ->with('success', 'Objetivo eliminado con éxito.');
    }
}
