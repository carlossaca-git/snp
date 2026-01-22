<?php

namespace App\Http\Controllers\Catalogos;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Catalogos\AvanceIndicador;
use Exception;
use Illuminate\Http\Request;

class AvanceIndicadorController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'id_indicador'  => 'required|exists:cat_indicador,id_indicador',
            'valor_logrado' => 'required|numeric',
            'evidencia'     => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
            'observaciones' => 'nullable|string'
        ]);
        DB::beginTransaction();
        try {
            $dato = $request->except('evidencia');

            // Logica de subida de archivos
            if ($request->hasFile('evidencia')) {
                // Guardamos en: storage/app/public/evidencias/indicadores
                $path = $request->file('evidencia')->store('/documentos/indicadores/evidencias', 'public');
                $dato['evidencia_path'] = $path;
            }
            //Registramos el usuario que autenticado col los datos
            $dato['id_usuario_registro'] = Auth::id();
            //Registramos la fecha del avace
            $dato['fecha_reporte']=now()->format('Y-m-d');
            AvanceIndicador::create($dato);
            DB::commit();
            return redirect()->route('catalogos.indicadores.index')
                ->with('success', 'El avance  se ha actualizado correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('catalogos.indicadores.index')
                ->with('error', 'No se pudo guardar la meta. Detalles: ' . $e->getMessage());
        }
    }
}
