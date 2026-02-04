<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seguridad\Auditoria;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {

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
    public function exportarPdf(Request $request)
    {
        $query = Auditoria::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }
        if ($request->filled('modulo')) {
            $query->where('auditable_type', 'LIKE', "%{$request->modulo}%");
        }
        $logs = $query->limit(500)->get();

        // GENERAR PDF
        $pdf = Pdf::loadView('dashboard.auditoria.pdf', compact('logs'));


        $pdf->setPaper('a4', 'portrait');

        // Mostrar
        return $pdf->stream('reporte_auditoria_' . date('Y-m-d_His') . '.pdf');
    }
}
