<?php

namespace App\Http\Controllers\Catalogos;

use App\Models\Catalogos\AvanceMeta;
use App\Models\Catalogos\MetaNacional;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class AvanceMetaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Usamos el permiso de gestionar, o uno específico si creaste 'metas.avances'
        $this->middleware('permiso:metas_pnd.gestionar');
    }

    /**
     * Guarda el nuevo avance (Actualiza el valor actual de la Meta)
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_meta_nacional' => 'required|exists:cat_meta_nacional,id_meta_nacional',
            'valor_actual'     => 'required|numeric',
            'evidencia'        => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ]);

        DB::beginTransaction();
        try {
            //Guardar archivo si existe
            $rutaEvidencia = null;
            if ($request->hasFile('evidencia')) {
                $rutaEvidencia = $request->file('evidencia')->store('avances/metas', 'public');
            }

            // Crear el registro en el HISTORIAL (Usando el nuevo Modelo)
            // Usamos la relación ->avances() para que asigne el ID automáticamente
            AvanceMeta::create([
                'id_meta_nacional' => $request->id_meta_nacional,
                'id_usuario'    => Auth::id(),
                'valor'         => $request->valor_actual,
                'fecha_reporte' => now()->format('Y-m-d'),
                'evidencia'     => $rutaEvidencia,
                'observaciones' => $request->observaciones ?? null
            ]);
            // Actualizar el valor actual en el padre
            $meta = MetaNacional::findOrFail($request->id_meta_nacional);
            $meta->valor_actual = $request->valor_actual;
            $meta->save();

            DB::commit();

            return redirect()->route('catalogos.metas.index')
                ->with('success', 'Avance registrado e historial actualizado.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
