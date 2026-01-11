<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seguridad\Auditoria;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)    {

        $query = Auditoria::with('usuario')->orderBy('fecha_hora', 'desc');

        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        $logs = $query->paginate(15);

        return view('dashboard.admin.auditoria.index', compact('logs'));
    }

    public function show($id)
    {
        $auditoria = Auditoria::with('usuario')->findOrFail($id);
        return view('dashboard.admin.auditoria.show', compact('auditoria'));
    }
}
