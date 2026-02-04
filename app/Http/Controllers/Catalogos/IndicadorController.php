<?php

namespace App\Http\Controllers\Catalogos;

use Exception;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Routing\Controller;
use App\Models\Catalogos\IndicadorNacional;
use App\Models\Catalogos\MetaNacional;

use Illuminate\Http\Request;

class IndicadorController extends Controller
{
    public function __construct()
    {
        //  Protección Base Nadie entra sin estar logueado
        $this->middleware('auth');

        //  Protección de LECTURA solo index y show
        $this->middleware('permiso:indicadores.ver')->only(['index', 'show']);

        //  Protección de ESCRITURA Crear, Editar, Borrar
        $this->middleware('permiso:indicadores.gestionar')->only([
            'create',
            'store',
            'edit',
            'update',
            'destroy'
        ]);
    }
    //Metodo index
    public function index(Request $request)
    {
        $buscar = $request->input('busqueda');

        //  Iniciamos la consulta con la relación meta
        $query = IndicadorNacional::with('metaNacional', 'ultimoAvance');

        // Aplicamos filtros de búsqueda AJAX compatible
        if ($buscar) {
            $estadoBusqueda = null;
            if (strtolower($buscar) == 'activo') $estadoBusqueda = 1;
            if (strtolower($buscar) == 'inactivo') $estadoBusqueda = 0;

            $query->where(function ($q) use ($buscar, $estadoBusqueda) {
                $q->where('nombre_indicador', 'LIKE', "%{$buscar}%")
                    ->orWhere('unidad_medida', 'LIKE', "%{$buscar}%")
                    ->orWhere('frecuencia', 'LIKE', "%{$buscar}%");

                if ($estadoBusqueda !== null) {
                    $q->orWhere('estado', $estadoBusqueda);
                }

                // También buscamos por el nombre de la Meta asociada
                $q->orWhereHas('meta', function ($metaQ) use ($buscar) {
                    $metaQ->where('nombre_meta', 'LIKE', "%{$buscar}%");
                });
            });
        }

        //  Ordenamos por ID descendente y paginamos
        $indicadores = $query->orderBy('id_indicador', 'desc')
            ->paginate(10)
            ->appends(['busqueda' => $buscar]);

        //  Metas activas para los Selects de los modales
        $metas = MetaNacional::where('estado', 1)->orderBy('nombre_meta')->get();

        return view('dashboard.configuracion.indicadores.index', compact('indicadores', 'metas'));
    }
    //Metodo stores
    public function store(Request $request)
    {

        $request->validate([
            'id_meta'            => 'required|exists:cat_meta_nacional,id_meta_nacional',
            'nombre_indicador'   => 'required|string',
            'linea_base'         => 'nullable|numeric',
            'anio_linea_base'    => 'nullable|integer|min:2000|max:2100',
            'meta_final'         => 'nullable|numeric',
            'unidad_medida'      => 'required|string',
            'frecuencia'         => 'required|string',
            'fuente_informacion' => 'nullable|string',
            'descripcion_indicador' => 'nullable|string',
            'estado'             => 'required|boolean',

        ], [
            'codigo_meta.unique' => 'Error: El código de meta ya existe en el sistema.'
        ]);
        DB::beginTransaction();
        try {

            IndicadorNacional::create($request->all());
            DB::commit();
            return redirect()
                ->route('catalogos.indicadores.index')
                ->with('success', 'Indicador creado correctamente.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('catalogos.metas.index')
                ->with('error', 'No se pudo guardar la meta. Detalles: ' . $e->getMessage());
        }
    }
    //Metodo Update
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_meta'          => 'required|exists:cat_meta_nacional,id_meta_nacional',
            'nombre_indicador' => 'required|string',
            'linea_base'       => 'nullable|numeric',
            'meta_final'       => 'nullable|numeric',
        ]);
        DB::beginTransaction();
        try {


            $indicador = IndicadorNacional::findOrFail($id);
            $indicador->update($request->all());
            DB::commit();
            return redirect()->route('catalogos.indicadores.index')
                ->with('success', 'El indicador ' . $indicador->codigo . ' se ha actualizado correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('catalogos.indicadores.index')
                ->with('error', 'No se pudo guardar el Indicador. Detalles: ' . $e->getMessage());
        }
    }

    //Metodo show para

    public function show($id)
    {

        $indicador = IndicadorNacional::with(['proyectos' => function ($q) {
            $q->withPivot('contribucion_proyecto');
            $q->with(['marcoLogico' => function ($qAct) {
                $qAct->whereIn('nivel', ['ACTIVIDAD', 'Actividad']);
                $qAct->with('historialAvances');
            }]);
        }])->findOrFail($id);

        // CONFIGURACIÓN DEL GRÁFICO
        $labels = [];
        $dataHistorica = [];

        // Recorremos los últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $fechaCorte = Carbon::now()->subMonths($i)->endOfMonth();
            $labels[] = $fechaCorte->format('M Y');

            $acumuladoIndicadorMes = 0;

            // Recorremos Proyectos del Indicador
            foreach ($indicador->proyectos as $proyecto) {
                // Reconstruimos el valor del proyecto sumando sus actividades en el pasado

                $avanceProyectoEnFecha = 0;
                $actividades = $proyecto->marcoLogico;

                if ($actividades->isNotEmpty()) {
                    $sumaPonderadaActividades = 0;
                    $totalPesoActividades = $actividades->sum('ponderacion');

                    foreach ($actividades as $actividad) {
                        // Buscamos el último reporte de ESTA actividad antes de la fecha corte
                        $ultimoHistorial = $actividad->historialAvances
                            ->where('fecha_reporte', '<=', $fechaCorte)
                            ->sortByDesc('fecha_reporte')
                            ->first();

                        // Si hubo reporte, usamos ese valor. Si no, 0.
                        $valActividad = $ultimoHistorial ? $ultimoHistorial->avance_total_acumulado : 0;

                        // Suma ponderada (Valor * PesoActividad)
                        $sumaPonderadaActividades += ($valActividad * $actividad->ponderacion);
                    }

                    // Calculamos el promedio ponderado del proyecto en ese mes
                    if ($totalPesoActividades > 0) {
                        $avanceProyectoEnFecha = $sumaPonderadaActividades / $totalPesoActividades;
                    }
                }
                //  Aporte al Indicador
                $pesoProyecto = $proyecto->pivot->contribucion_proyecto;

                //  (AvanceProyectoReconstruido * PesoProyecto) / 100
                $acumuladoIndicadorMes += ($avanceProyectoEnFecha * $pesoProyecto) / 100;
            }

            $dataHistorica[] = round($acumuladoIndicadorMes, 2);
        }
        //
        // Puedba de
        $chartLabels = json_encode($labels);
        $chartData = json_encode($dataHistorica);
        $proyectos = $indicador->proyectos()
            ->withPivot('contribucion_proyecto')
            ->orderBy('id', 'desc')
            ->paginate(10);
        return view('dashboard.configuracion.indicadores.show', compact('indicador', 'chartLabels', 'chartData', 'proyectos'));
    }
    //Metodo destroy
    public function destroy($id)
    {
        try {
            $indicador = IndicadorNacional::findOrFail($id);
            $indicador->delete();

            return redirect()->back()->with('success', 'El indicador ha sido eliminado correctamente.');
        } catch (\Exception $e) {
            // Esto protege el sistema si el indicador ya tiene "Avances" registrados (llave foránea)
            return redirect()->back()->with('error', 'No se puede eliminar el indicador porque tiene registros asociados.');
        }
    }

    //REPORTES PDf
    public function generarPdf($id)
    {
        // Buscamos los datos (igual que en el Kardex)
        $indicador = IndicadorNacional::with(['metaNacional', 'avances' => function ($query) {
            $query->orderBy('fecha_reporte', 'asc');
        }])->findOrFail($id);

        // Cargamos la vista del PDF (Ojo: crearemos una vista especial 'limpia')
        $pdf = Pdf::loadView('dashboard.configuracion.indicadores.reportes.ficha', compact('indicador'));

        // Opciones de papel (Vertical/Horizontal)
        $pdf->setPaper('A4', 'portrait');

        // Descargar o Ver en navegador ('stream' es para ver, 'download' para bajar directo)
        return $pdf->stream('Ficha_Indicador_' . $indicador->codigo_indicador . '.pdf');
    }
}
