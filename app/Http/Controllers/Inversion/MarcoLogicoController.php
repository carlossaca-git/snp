<?php

namespace App\Http\Controllers\Inversion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use App\Models\Inversion\ProyectoInversion;
use App\Models\Inversion\MarcoLogico;
use App\Models\Inversion\MetaAnual;
use App\Models\Inversion\IndicadorMarco;

class MarcoLogicoController extends Controller
{
    /**
     * Muestra el Tablero (Dashboard) del Marco Lógico
     */
    public function index($id)
    {
        $proyecto = ProyectoInversion::with([
            'proposito.indicador.metasAnuales',
            'componentes.actividades.indicador.metasAnuales',
            'componentes.indicador.metasAnuales'
        ])->findOrFail($id);
        $anioInicio = $proyecto->fecha_inicio_estimada ? Carbon::parse($proyecto->fecha_inicio_estimada)->year : date('Y');
        $anioFin    = $proyecto->fecha_fin_estimada ? Carbon::parse($proyecto->fecha_fin_estimada)->year : date('Y');

        $anios = range($anioInicio, $anioFin);
        $proposito = $proyecto->proposito;

        //OBTENER EL TECHO
        $techoPresupuestario = $proyecto->monto_total_inversion ?? 0;
        //CALCULAR LO PLANIFICADO
        $totalPlanificado = $proyecto->componentes->flatMap(function ($comp) {
            return $comp->actividades;
        })->sum('monto');
        //CALCULAR PORCENTAJE
        $porcentajeFinanciero = 0;
        if ($techoPresupuestario > 0) {
            $porcentajeFinanciero = ($totalPlanificado / $techoPresupuestario) * 100;
        }

        return view('dashboard.inversion.proyectos.marcologico.index', compact(
            'proyecto',
            'anios',
            'proposito',
            'techoPresupuestario',
            'totalPlanificado',
            'porcentajeFinanciero'
        ));
    }


    /**
     * Guarda un nuevo elemento (Propósito, Componente o Actividad)
     */
    public function store(Request $request)
    {
        // Validamos lo básico
        $request->validate([
            'proyecto_id' => 'required',
            'nivel'       => 'required'
        ]);

        DB::beginTransaction();

        try {
            // Si es PROPOSITO, buscamos si existe para no duplicar. Si es otro, creamos nuevo.
            if ($request->nivel === 'PROPOSITO') {
                $elemento = MarcoLogico::updateOrCreate(
                    ['proyecto_id' => $request->proyecto_id, 'nivel' => 'PROPOSITO'],
                    ['resumen_narrativo' => $request->resumen_narrativo, 'supuestos' => $request->supuestos]
                );
            } else {
                $elemento = new MarcoLogico();
                $elemento->proyecto_id       = $request->proyecto_id;
                $elemento->nivel             = $request->nivel;
                $elemento->padre_id          = $request->padre_id;
                $elemento->resumen_narrativo = $request->resumen_narrativo;
                $elemento->supuestos         = $request->supuestos;
                $elemento->monto             = $request->monto ?? 0;
                $elemento->ponderacion       = $request->ponderacion ?? 0;
                $elemento->fecha_inicio      = $request->fecha_inicio;
                $elemento->fecha_fin         = $request->fecha_fin;
                $elemento->save();
            }

            $indicador = IndicadorMarco::updateOrCreate(
                ['marco_logico_id' => $elemento->id_marco_logico],
                [
                    'descripcion'      => $request->descripcion_indicador,
                    'resumen_narrativo' => $request->nombre_indicador,
                    'unidad_medida'    => $request->unidad_medida,
                    'meta_total'       => array_sum($request->metas ?? []),
                    'medio_verificacion' => $request->medio_verificacion ?? 'Según informes técnicos'
                ]
            );
            // Borramos las viejas y ponemos las nuevas
            MetaAnual::where('indicador_id', $indicador->id_indicador)->delete();

            if ($request->has('metas')) {
                $indicador->metasAnuales()->delete();
                foreach ($request->metas as $anio => $valor) {
                    if ($valor) {
                        MetaAnual::create([
                            'indicador_id' => $indicador->id_indicador,
                            'anio'         => $anio,
                            'valor_meta'   => $valor,
                            'meta_ponderada' => $valor
                        ]);
                    }
                }
            }

            DB::commit();
            if ($request->nivel === 'ACTIVIDAD') {
                $this->actualizarAvanceProyecto($request->proyecto_id);
            }
            return back()->with('success', 'Guardado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar indicador: ' . $e->getMessage());
        }
    }

    //UPDATE
    public function update(Request $request, $id)
    {
        //  Buscar el elemento
        $elemento = MarcoLogico::findOrFail($id);

        //  Actualizar datos básicos
        $elemento->resumen_narrativo = $request->resumen_narrativo;
        $elemento->supuestos         = $request->supuestos;
        $elemento->fecha_inicio      = $request->fecha_inicio;
        $elemento->fecha_fin         = $request->fecha_fin;
        $elemento->monto             = $request->monto ?? 0;
        $elemento->ponderacion       = $request->ponderacion ?? 0;
        $elemento->save();

        //  Actualizar Indicador (Si enviaron datos de indicador)
        if ($request->filled('descripcion_indicador')) {
            $indicador = IndicadorMarco::updateOrCreate(
                ['marco_logico_id' => $elemento->id_marco_logico],
                [
                    'descripcion'        => $request->descripcion_indicador,
                    'unidad_medida'      => $request->unidad_medida,
                    'medio_verificacion' => $request->medio_verificacion
                ]
            );

            //  Actualizar Metas Anuales
            if ($request->has('metas')) {
                $indicador->metasAnuales()->delete();
                foreach ($request->metas as $anio => $valor) {
                    if ($valor !== null && $valor !== '') {
                        MetaAnual::create([
                            'indicador_id' => $indicador->id_indicador,
                            'anio'         => $anio,
                            'valor_meta'   => $valor
                        ]);
                    }
                }
            }
        }

        return back()->with('success', 'Actualizado correctamente');
    }
    // Método store para guardar avances
    public function storeAvance(Request $request)
    {
        // Validar
        $request->validate([
            'marco_logico_id' => 'required|exists:tra_marco_logico,id_marco_logico',
            'avance_total_acumulado' => 'required|numeric|min:0|max:100',
            'fecha_reporte' => 'required|date'
        ]);

        //  Buscar la Actividad
        $actividad = MarcoLogico::findOrFail($request->marco_logico_id);
        try {
            //  Crear el registro en el Historial
            DB::table('tra_avances_actividad')->insert([
                'marco_logico_id' => $actividad->id_marco_logico,
                'fecha_reporte'   => $request->fecha_reporte,
                'avance_total_acumulado' => $request->avance_total_acumulado,
                // Calculamos cuánto avanzó solo en este reporte
                'avance_reportado' => $request->avance_total_acumulado - $actividad->avance_actual,
                'observacion'     => $request->observacion,
                'created_at'      => now(),
                'updated_at'      => now()
            ]);

            //  ACTUALIZAR LA ACTIVIDAD
            $actividad->avance_actual = $request->avance_total_acumulado;
            $actividad->save();

            //  ACTUALIZAR EL PROYECTO
            $this->actualizarAvanceProyecto($actividad->proyecto_id);

            return back()->with('success', 'Avance reportado correctamente. El proyecto se ha recalculado.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo actualizar el avance.');
        }
    }
    /**
     * Recalcula el avance físico del proyecto basándose en el promedio de sus actividades.
     */
    /**
     * Recalcula el avance físico del proyecto basándose en el promedio de sus actividades.
     */
    private function actualizarAvanceProyecto($idProyecto)
    {
        try {
            //OBTENER EL PROMEDIO DE AVANCE DE LAS ACTIVIDADES
            $promedio = MarcoLogico::where('proyecto_id', $idProyecto)
                ->where(function ($q) {
                    $q->where('nivel', 'ACTIVIDAD')
                      ->orWhere('nivel', 'Actividad')
                      ->orWhere('nivel', 'actividad');
                })
                ->avg('avance_actual');
            // Redondear a 2 decimales y manejar nulls
            $avanceFinal = $promedio ? round($promedio, 2) : 0.00;
            //ACTUALIZAR EL PROYECTO
            $affected = DB::table('tra_proyecto_inversion')
                ->where('id', $idProyecto)
                ->update(['avance_fisico_real' => $avanceFinal]);

            // Log para verificar si se actualizó algo
            if ($affected === 0) {
                Log::warning("No se actualizó el proyecto ID $idProyecto. Puede que el ID no exista o el valor ya era el mismo.");
            }

        } catch (\Exception $e) {
            Log::error("Error actualizando avance proyecto $idProyecto: " . $e->getMessage());
        }
    }
    /**
     * Eliminar un elemento
     */
    public function destroy($id)
    {
        try {
            $elemento = MarcoLogico::findOrFail($id);
            $idProyecto = $elemento->proyecto_id;
            $nivel = $elemento->nivel;
            // Contamos si tiene hijos
            $conteoHijos = MarcoLogico::where('padre_id', $id)->count();

            $elemento->delete();
            // Si es componente, eliminamos sus actividades hijas
            if ($nivel === 'ACTIVIDAD' || $nivel === 'actividad') {
                $this->actualizarAvanceProyecto($idProyecto);
            }
            if ($conteoHijos > 0) {
                return back()->with('warning', 'Se eliminó el componente y todas sus actividades vinculadas.');
            }

            return back()->with('success', 'Eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo eliminar el elemento.');
        }
    }
}
