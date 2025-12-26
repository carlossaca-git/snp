<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rol; // Importamos el modelo

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $roles=Rol::all();
        $seccion = $request->query('seccion', 'inicio');
        // IMPORTANTE: Debes pasar 'seccion' a la vista
        return view('dashboard', compact('roles','seccion'));
    }
}
