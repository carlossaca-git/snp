<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Solo importamos modelos para contar totales (KPIs)
use App\Models\User;
use App\Models\Estrategico\OrganizacionEstatal;
use App\Models\Inversion\Programa;

class DashboardController extends Controller
{
    /**
     * Muestra el Panel Principal (Resumen General)
     * URL: /dashboard
     */
    public function index()
    {
        // 1. Recopilamos estadísticas generales para las tarjetas del dashboard
        $kpis = [
            'total_usuarios'      => User::count(),
            'total_organizaciones'=> OrganizacionEstatal::count(),
            'programas_activos'   => Programa::count(), // Ajusta según tu lógica de 'activo'
            // 'alertas_auditoria' => ...
        ];

        // 2. Retornamos la vista limpia del resumen
        // Asegúrate de que tu vista 'dashboard.resumen' espere la variable $kpis
        return view('dashboard.resumen', compact('kpis'));

    }
}
