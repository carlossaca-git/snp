<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inversion\ProyectoInversion;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index()
    {
        // --- 1. TARJETAS SUPERIORES (KPIs) ---
        $totalProyectos = ProyectoInversion::count();
        $montoTotal = ProyectoInversion::sum('monto_total_inversion');
        $proyectosFavorables = ProyectoInversion::where('estado_dictamen', 'FAVORABLE')->count();
        $proyectosPendientes = ProyectoInversion::where('estado_dictamen', '!=', 'FAVORABLE')
            ->orWhereNull('estado_dictamen')->count();

        // --- 2. GRÁFICO DE BARRAS: INVERSIÓN POR EJE (CORREGIDO CON JOINS) ---
        // Explicación: Proyecto -> se une con Objetivo -> se une con Eje
        $inversionPorEje = DB::table('tra_proyecto_inversion as p')
            ->join('cat_objetivo_nacional as o', 'p.objetivo_nacional', '=', 'o.id_objetivo_nacional') // Verifica nombre de tabla y FK
            ->join('cat_eje_pnd as e', 'o.id_eje', '=', 'e.id_eje') // Verifica nombre de tabla y PK
            ->select('e.nombre_eje', DB::raw('SUM(p.monto_total_inversion) as total'))
            ->whereNull('p.deleted_at') // Solo si usas SoftDeletes
            ->groupBy('e.nombre_eje')
            ->get();

        // --- 3. GRÁFICO CIRCULAR: ESTADO DE DICTÁMENES (NUEVO) ---
        $estadosDictamen = ProyectoInversion::select('estado_dictamen', DB::raw('count(*) as total'))
            ->groupBy('estado_dictamen')
            ->get();

        // --- 4. ÚLTIMOS PROYECTOS ---
        $ultimosProyectos = ProyectoInversion::with('organizacion')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.resumen', compact(
            'totalProyectos',
            'montoTotal',
            'proyectosFavorables',
            'proyectosPendientes',
            'inversionPorEje',
            'estadosDictamen',
            'ultimosProyectos'
        ));
    }
    // Obligatorio importar carbon
    public function filtrarDatos(Request $request)
    {
        $rango = $request->input('rango', 'anio'); // Por defecto 'anio'

        // 1. Definir fechas de inicio y fin
        $inicio = now()->startOfYear();
        $fin = now()->endOfYear();

        if ($rango === 'semana') {
            $inicio = now()->startOfWeek();
            $fin = now()->endOfWeek();
        } elseif ($rango === 'mes') {
            $inicio = now()->startOfMonth();
            $fin = now()->endOfMonth();
        }

        // Consulta Base con Filtro de Fecha
        // Usamos 'created_at' o tu campo de fecha del proyecto 'fecha_inicio_estimada'
        $query = ProyectoInversion::whereBetween('created_at', [$inicio, $fin]);
        //dd($rango, $inicio->toDateTimeString(), $fin->toDateTimeString());
        // Recalcular KPIs
        $totalProyectos = (clone $query)->count();
        $montoTotal = (clone $query)->sum('monto_total_inversion');
        $proyectosFavorables = (clone $query)->where('estado_dictamen', 'FAVORABLE')->count();
        $proyectosPendientes = (clone $query)->where(function ($q) {
            $q->where('estado_dictamen', '!=', 'FAVORABLE')->orWhereNull('estado_dictamen');
        })->count();

        // 4. Recalcular Gráfico Ejes (Con Join)
        $inversionPorEje = DB::table('tra_proyecto_inversion as p')
            ->join('cat_objetivo_nacional as o', 'p.objetivo_nacional', '=', 'o.id_objetivo_nacional')
            ->join('cat_eje_pnd as e', 'o.id_eje', '=', 'e.id_eje')
            ->select('e.nombre_eje', DB::raw('SUM(p.monto_total_inversion) as total'))
            ->whereBetween('p.created_at', [$inicio, $fin]) // Filtro aquí también
            //->whereNull('p.deleted_at')
            ->groupBy('e.nombre_eje')
            ->get();

        // 5. Recalcular Gráfico Estados
        $estadosDictamen = (clone $query)
            ->select('estado_dictamen', DB::raw('count(*) as total'))
            ->groupBy('estado_dictamen')
            ->get();

        return response()->json([
            'kpis' => [
                'total' => $totalProyectos,
                'solicitado' => '$ ' . number_format($montoTotal / 1000000, 2) . ' M',
                'favorables' => $proyectosFavorables,
                'pendientes' => $proyectosPendientes
            ],
            'graficos' => [
                'ejesLabels' => $inversionPorEje->pluck('nombre_eje'),
                'ejesData'   => $inversionPorEje->pluck('total'),
                'estadosData' => $estadosDictamen->pluck('total'), // El orden de colores se mantiene en JS
                'estadosLabels' => $estadosDictamen->pluck('estado_dictamen') // Para asegurar coincidencia
            ]
        ]);
    }


    // Asegúrate de importar la clase PDF arriba


public function reporteGeneral(Request $request)
{

    // 1. Reutilizamos la lógica de fechas (Copia esto igual que en tu filtrarDatos)
    $rango = $request->input('rango', 'anio');
    $inicio = now()->startOfYear();
    $fin = now()->endOfYear();

    if ($rango === 'semana') {
        $inicio = now()->startOfWeek();
        $fin = now()->endOfWeek();
    } elseif ($rango === 'mes') {
        $inicio = now()->startOfMonth();
        $fin = now()->endOfMonth();
    }

    // 2. Obtenemos los datos para la tabla del PDF
    $proyectos = ProyectoInversion::with(['organizacion', 'objetivo.nombre_eje']) // Eager Loading para optimizar
        ->whereBetween('created_at', [$inicio, $fin])
        ->orderBy('created_at', 'desc')
        ->get();

    // 3. Generamos el PDF usando una vista Blade
    $pdf = Pdf::loadView('dashboard.reportes.proyectos_pdf', [
        'proyectos' => $proyectos,
        'rango' => ucfirst($rango),
        'fecha' => now()->format('d/m/Y H:i')
    ]);

    // Opción A: Descargar directo
    //return $pdf->download('Reporte_Proyectos_SIPEIP.pdf');
return $pdf->stream();
    // Opción B: Ver en el navegador (Stream) - Útil para probar el diseño
    // return $pdf->stream();
}
}
