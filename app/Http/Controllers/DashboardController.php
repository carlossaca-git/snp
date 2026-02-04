<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Catalogos\PlanNacionalController;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Inversion\ProyectoInversion;
use Illuminate\Support\Facades\DB;
use App\Models\Catalogos\Indicador;
use App\Models\Catalogos\MetaNacional;
use Exception;

class DashboardController extends Controller
{
    public function index()
    {
        $idOrganizacion = Auth::user()->id_organizacion;

        // KPIs DE PROYECTOS

        $proyectosQuery = ProyectoInversion::where('organizacion_id', $idOrganizacion);

        $totalProyectos = $proyectosQuery->count();
        $montoTotal = $proyectosQuery->sum('monto_total_inversion');
        $proyectosFavorables = (clone $proyectosQuery)->where('estado_dictamen', 'FAVORABLE')->count();
        $proyectosPendientes = (clone $proyectosQuery)->whereIn('estado_dictamen', ['PENDIENTE', 'REVISION', null])->count();

        $ultimosProyectos = ProyectoInversion::where('organizacion_id', $idOrganizacion)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // METAS NACIONALES

        // Obtenemos las Metas Nacionales que tu organización ha seleccionado en la tabla de alineaciones
        $metasNacionales = MetaNacional::whereHas('alineacion', function ($q) use ($idOrganizacion) {
            // Entramos a la tabla intermedia 'alineacion_estrategica'
            // Y verificamos que el Objetivo Estratégico asociado sea de MI organización
            $q->whereHas('objetivoEstrategico', function ($q2) use ($idOrganizacion) {
                $q2->where('organizacion_id', $idOrganizacion);
            });
        })
            ->with('objetivoNacional') // Cargamos el padre para ver info si hace falta
            ->get();

        $totalIndicadores = $metasNacionales->count();
        $indicadoresCriticos = 0;
        $sumaPromedios = 0;
        $metasConProyectos = 0;

        // Recorremos las metas para calcular promedios y proyectos asociados
        $metasNacionales->transform(function ($meta) use ($idOrganizacion, &$indicadoresCriticos, &$sumaPromedios, &$metasConProyectos) {

            // BUSCAMOS LOS PROYECTOS QUE CONTRIBUYEN A ESTA META ESPECÍFICA
            $proyectos = ProyectoInversion::where('organizacion_id', $idOrganizacion)
                ->whereHas('programa.objetivoE.alineacion', function ($q) use ($meta) {
                    $q->where('meta_nacional_id', $meta->id_meta_nacional);
                })
                ->select('id', 'nombre_proyecto', 'cup', 'monto_total_inversion', 'estado_dictamen')
                ->get();

            // Cálculo del promedio de avance físico para esta meta
            $promedioMeta = $proyectos->avg('avance_fisico_real') ?? 0;

            // Guardamos datos en el objeto para la vista
            $meta->avance_promedio = $promedioMeta;
            $meta->proyectos = $proyectos;

            // Semáforo para contadores
            if ($promedioMeta < 40 && $proyectos->count() > 0) {
                $indicadoresCriticos++;
            }

            // Acumuladores para el promedio global
            if ($proyectos->count() > 0) {
                $sumaPromedios += $promedioMeta;
                $metasConProyectos++;
            }

            return $meta;
        });

        //  Promedio Global del Dashboard (Promedio de las Metas activas)
        $promedioGlobal = $metasConProyectos > 0
            ? round($sumaPromedios / $metasConProyectos, 2)
            : 0;

        //  GRÁFICO INVERSIÓN POR EJE

        $inversionPorEje = DB::table('tra_proyecto_inversion as p')
            ->join('tra_programa as prog', 'p.programa_id', '=', 'prog.id')
            ->join('cat_objetivo_estrategico as oe', 'prog.objetivo_estrategico_id', '=', 'oe.id_objetivo_estrategico')

            ->join('alineacion_estrategica as ae', 'oe.id_objetivo_estrategico', '=', 'ae.objetivo_estrategico_id')

            ->join('cat_meta_nacional as mn', 'ae.meta_nacional_id', '=', 'mn.id_meta_nacional')
            ->join('cat_objetivo_nacional as onac', 'mn.objetivo_nacional_id', '=', 'onac.id_objetivo_nacional')
            ->join('cat_eje_pnd as eje', 'onac.id_eje', '=', 'eje.id_eje')
            ->where('p.organizacion_id', $idOrganizacion)
            ->select('eje.nombre_eje', DB::raw('SUM(p.monto_total_inversion) as total'))
            ->groupBy('eje.nombre_eje')
            ->get();

        // GRÁFICO ESTADOS DE DICTAMEN
        $estadosDictamen = ProyectoInversion::where('organizacion_id', $idOrganizacion)
            ->select('estado_dictamen', DB::raw('count(*) as total'))
            ->groupBy('estado_dictamen')
            ->get();

        return view('dashboard.resumen', compact(
            'totalProyectos',
            'montoTotal',
            'proyectosFavorables',
            'proyectosPendientes',
            'ultimosProyectos',
            'metasNacionales',
            'totalIndicadores',
            'indicadoresCriticos',
            'promedioGlobal',
            'inversionPorEje',
            'estadosDictamen'
        ));
    }
    // FIltrar datos AJAX
    public function filtrarDatos(Request $request)
    {
        try {
            $idOrganizacion = Auth::user()->id_organizacion;
            $rango = $request->input('rango', 'anio');

            // DEFINIR FECHAS
            $inicio = now()->startOfYear();
            $fin = now()->endOfYear();

            if ($rango === 'semana') {
                $inicio = now()->startOfWeek();
                $fin = now()->endOfWeek();
            } elseif ($rango === 'mes') {
                $inicio = now()->startOfMonth();
                $fin = now()->endOfMonth();
            }

            //  CONSULTA BASE (Filtro de Organización)
            $query = ProyectoInversion::where('organizacion_id', $idOrganizacion)
                ->whereBetween('created_at', [$inicio, $fin]);

            //  RECALCULAR KPIs
            $totalProyectos = (clone $query)->count();
            //  Monto Total Solicitado
            $montoTotal = (clone $query)->sum('monto_total_inversion') ?? 0;

            $proyectosFavorables = (clone $query)->where('estado_dictamen', 'FAVORABLE')->count();

            $proyectosPendientes = (clone $query)->where(function ($q) {
                $q->where('estado_dictamen', '!=', 'FAVORABLE')
                    ->orWhereNull('estado_dictamen');
            })->count();

            //  PROMEDIO GLOBAL
            // Calculamos el promedio del avance físico real de los proyectos filtrados
            $promedioGlobal = (clone $query)->count() > 0
                ? round((clone $query)->avg('avance_fisico_real'), 1)
                : 0;

            //  GRÁFICO INVERSIÓN POR EJE
            $inversionPorEje = DB::table('tra_proyecto_inversion as p')
                ->join('tra_programa as prog', 'p.programa_id', '=', 'prog.id')
                ->join('cat_objetivo_estrategico as oe', 'prog.objetivo_estrategico_id', '=', 'oe.id_objetivo_estrategico')
                ->join('alineacion_estrategica as ae', 'oe.id_objetivo_estrategico', '=', 'ae.objetivo_estrategico_id')
                ->join('cat_meta_nacional as mn', 'ae.meta_nacional_id', '=', 'mn.id_meta_nacional')
                ->join('cat_objetivo_nacional as onac', 'mn.objetivo_nacional_id', '=', 'onac.id_objetivo_nacional')
                ->join('cat_eje_pnd as e', 'onac.id_eje', '=', 'e.id_eje')
                // Aplicar filtros de organización y fechas
                ->select('e.nombre_eje', DB::raw('SUM(p.monto_total_inversion) as total'))

                ->where('p.organizacion_id', $idOrganizacion)
                ->whereBetween('p.created_at', [$inicio, $fin])

                ->groupBy('e.nombre_eje')
                ->get();

            //  GRÁFICO ESTADOS
            $estadosDictamen = (clone $query)
                ->select('estado_dictamen', DB::raw('count(*) as total'))
                ->groupBy('estado_dictamen')
                ->get();

            //  RESPUESTA JSON
            return response()->json([
                'kpis' => [
                    'total' => $totalProyectos,
                    'solicitado' => number_format($montoTotal / 1000000, ) ,
                    'favorables' => $proyectosFavorables,
                    'pendientes' => $proyectosPendientes,
                    'promedioGlobal' => $promedioGlobal
                ],
                'graficos' => [
                    'ejesLabels' => $inversionPorEje->pluck('nombre_eje'),
                    'ejesData'   => $inversionPorEje->pluck('total'),
                    'estadosLabels' => $estadosDictamen->map(fn($item) => $item->estado_dictamen ?? 'SIN ESTADO'),
                    'estadosData' => $estadosDictamen->pluck('total')
                ]
            ]);
        } catch (\Exception $e) {
            // En caso de error, devolver mensaje adecuado
            return response()->json([
                'error' => true,
                'mensaje' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }

    public function dashboardEstrategico()
    {
        // Traemos las Metas Nacionales que tienen proyectos vinculados
        $metasNacionales = MetaNacional::with('proyectos')->get()->map(function ($meta) {

            // Calculamos el avance promedio de la META basado en sus proyectos
            if ($meta->proyectos->count() > 0) {
                $meta->avance_promedio = $meta->proyectos->avg('avance_fisico_real');
                $meta->total_inversion = $meta->proyectos->sum('monto_total_inversion');
            } else {
                $meta->avance_promedio = 0;
                $meta->total_inversion = 0;
            }

            return $meta;
        });

        return view('dashboard.estrategico', compact('metasNacionales'));
    }
    // Reportes pdf
    public function reporteGeneral(Request $request)
    {
        // SEGURIDAD
        $idOrganizacion = Auth::user()->id_organizacion;

        // FECHAS
        $rango = $request->input('rango', 'anio');
        $inicio = Carbon::now()->startOfYear();
        $fin = Carbon::now()->endOfYear();

        if ($rango === 'semana') {
            $inicio = Carbon::now()->startOfWeek();
            $fin = Carbon::now()->endOfWeek();
        } elseif ($rango === 'mes') {
            $inicio = Carbon::now()->startOfMonth();
            $fin = Carbon::now()->endOfMonth();
        }

        // CONSULTA PROYECTOS (Eloquent)
        $query = ProyectoInversion::where('organizacion_id', $idOrganizacion)
            ->whereBetween('created_at', [$inicio, $fin]);

        // Cargamos relaciones para el listado general
        $proyectos = (clone $query)->with([
            'programa.objetivoE',
            'organizacion'
        ])->get();

        //  KPIS
        $montoTotal = $proyectos->sum('monto_total_inversion');

        $kpis = [
            'total'      => $proyectos->count(),
            'monto'      => $montoTotal,
            'favorables' => (clone $query)->where('estado_dictamen', 'FAVORABLE')->count(),
            'pendientes' => (clone $query)->where(function ($q) {
                $q->where('estado_dictamen', '!=', 'FAVORABLE')
                    ->orWhereNull('estado_dictamen');
            })->count(),
        ];

        // PROMEDIO GLOBAL
        $promedioGlobal = $proyectos->count() > 0
            ? round($proyectos->avg('avance_fisico_real'), 1)
            : 0;

        // INVERSIÓN DETALLADA (SQL MANUAL)
        $inversionDetallada = DB::table('tra_proyecto_inversion as p')
            ->join('tra_programa as prog', 'p.programa_id', '=', 'prog.id')
            ->join('cat_objetivo_estrategico as oe', 'prog.objetivo_estrategico_id', '=', 'oe.id_objetivo_estrategico')

            // PUENTE ALINEACIÓN
            ->join('alineacion_estrategica as ae', 'oe.id_objetivo_estrategico', '=', 'ae.objetivo_estrategico_id')
            ->join('cat_meta_nacional as mn', 'ae.meta_nacional_id', '=', 'mn.id_meta_nacional')

            // Hacia arriba (Plan Nacional)
            ->join('cat_objetivo_nacional as onac', 'mn.objetivo_nacional_id', '=', 'onac.id_objetivo_nacional')
            ->join('cat_eje_pnd as e', 'onac.id_eje', '=', 'e.id_eje')

            // ODS  Left Join para no perder datos si no hay ODS
            ->leftJoin('alineacion_metas_ods as amo', 'mn.id_meta_nacional', '=', 'amo.id_meta_nacional')
            ->leftJoin('cat_ods as ods', 'amo.id_ods', '=', 'ods.id_ods')

            ->select(
                'e.nombre_eje',
                'onac.descripcion_objetivo as objetivo_nacional',
                'mn.codigo_meta',
                'mn.nombre_meta',
                'ods.nombre as nombre_ods',
                'ods.codigo as codigo_ods',
                DB::raw('SUM(p.monto_total_inversion) as total')
            )
            ->where('p.organizacion_id', $idOrganizacion)
            ->whereBetween('p.created_at', [$inicio, $fin])

            // Agrupamiento
            ->groupBy(
                'e.nombre_eje',
                'onac.descripcion_objetivo',
                'mn.codigo_meta',
                'mn.nombre_meta',
                'ods.nombre',
                'ods.codigo'
            )
            // ORDENAMIENTO FINAL
            ->orderBy('e.nombre_eje')
            ->orderBy('onac.descripcion_objetivo')
            ->get();

        // AGRUPAMIENTO PARA LA VISTA (Árbol)
        $datosAgrupados = $inversionDetallada->groupBy(['nombre_eje', 'objetivo_nacional']);

        // ESTADOS DICTAMEN
        $estadosDictamen = (clone $query)
            ->select('estado_dictamen', DB::raw('count(*) as total'))
            ->groupBy('estado_dictamen')
            ->get();

        // GENERAR PDF
        $pdf = Pdf::loadView('dashboard.reportes.general_pdf', compact(
            'proyectos',
            'kpis',
            'promedioGlobal',
            'datosAgrupados',
            'montoTotal',
            'estadosDictamen',
            'inicio',
            'fin',
            'rango'
        ));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('general_pdf.pdf');
    }
}
