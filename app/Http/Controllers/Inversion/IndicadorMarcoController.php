<?php

namespace App\Http\Controllers\Inversion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Inversion\IndicadorMarco;
use App\Models\Inversion\MetaAnual;

class IndicadorMarcoController extends Controller
{
    /**
     * Actualiza la definición del indicador y sus metas programadas.
     * Se usa cuando le das "Editar" específicamente al indicador.
     */
    public function update(Request $request, $id)
    {
        // Validaciones
        $request->validate([
            'nombre_indicador' => 'required|string|max:255',
            'unidad_medida'    => 'required|string|max:100',
            'formula_calculo'    => 'nullable|string',
            'medio_verificacion' => 'nullable|string',
            'metas'              => 'nullable|array'
        ]);

        DB::beginTransaction();

        try {
            $indicador = IndicadorMarco::findOrFail($id);

            //  Actualizar datos básicos
            $indicador->update([
                'nombre_indicador'   => $request->nombre_indicador,
                'unidad_medida'      => $request->unidad_medida,
                'formula_calculo'    => $request->formula_calculo,
                'medio_verificacion' => $request->medio_verificacion,
                'linea_base'         => $request->linea_base,
            ]);

            // Actualizar o Crear Metas Anuales
            if ($request->has('metas')) {
                foreach ($request->metas as $anio => $valor) {
                    // Usamos updateOrCreate para no borrar datos de ejecución si ya existieran
                    MetaAnual::updateOrCreate(
                        [
                            'indicador_id' => $indicador->id_indicador_marco,
                            'anio'         => $anio
                        ],
                        [
                            'valor_meta' => $valor
                        ]
                    );
                }
            }

            DB::commit();
            return back()->with('success', 'Indicador actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar indicador: ' . $e->getMessage());
        }
    }

    /**
     *
     * Registra el avance real (Lo que realmente se logro).
     */
    public function reportarAvance(Request $request, $id)
    {
        $request->validate([
            'anio'            => 'required|integer',
            'valor_ejecutado' => 'required|numeric'
        ]);

        try {
            // Buscamos la meta de ese año específico
            $meta = MetaAnual::where('indicador_id', $id)
                             ->where('anio', $request->anio)
                             ->firstOrFail();

            // Guardamos lo que se logró en la realidad
            $meta->valor_ejecutado = $request->valor_ejecutado;

            $meta->save();

            return response()->json(['success' => true, 'message' => 'Avance reportado.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al reportar avance.'], 500);
        }
    }
}
