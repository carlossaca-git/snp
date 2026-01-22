<?php

namespace App\Http\Controllers\Planificacion;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Planificacion\AlineacionEstrategica;
use App\Models\Institucional\OrganizacionEstatal;
use App\Models\Planificacion\ObjetivoEstrategico;
use App\Models\Catalogos\MetaNacional;
use App\Models\Catalogos\Ods;



class AlineacionController extends Controller
{
    public function index(Request $request)
    {
        // Obtener la organización del usuario logueado
        $user = Auth::user();
        $organizacionId = $user->id_organizacion;

        // Validación de seguridad por si el usuario no tiene organización
        if (!$organizacionId) {
            return redirect()->back()->with('error', 'No tiene una organización asignada.');
        }

        $organizacion = OrganizacionEstatal::findOrFail($organizacionId);

        //  Capturar búsqueda (si existe)
        $busqueda = $request->input('busqueda');

        // Consulta de Alineaciones (Filtrada por la organización del usuario)
        $query = AlineacionEstrategica::where('organizacion_id', $organizacionId)
            ->with([
                'objetivoEstrategico',
                'metaNacional.ods',
                'usuario',
            ]);

        // Aplicar filtros de búsqueda
        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                // Buscar por nombre del Objetivo Estratégico
                $q->whereHas('objetivoEstrategico', function ($subQ) use ($busqueda) {
                    $subQ->where('nombre', 'LIKE', "%{$busqueda}%")
                        ->orWhere('codigo', 'LIKE', "%{$busqueda}%");
                })
                    // O buscar por Meta Nacional
                    ->orWhereHas('metaNacional', function ($subQ) use ($busqueda) {
                        $subQ->where('nombre_meta', 'LIKE', "%{$busqueda}%")
                            ->orWhere('codigo_meta', 'LIKE', "%{$busqueda}%");
                    });
            });
        }

        $alineaciones = $query->paginate(10)->appends(['busqueda' => $busqueda]);

        // Datos Auxiliares para Modales/Selects
        $ods = Ods::orderBy('id_ods', 'asc')->get();

        $metas = MetaNacional::with('ods')
            ->select('id_meta_nacional', 'nombre_meta', 'codigo_meta')
            ->orderBy('codigo_meta', 'asc')
            ->get();

        return view('dashboard.estrategico.alineacion.index', compact(
            'organizacion',
            'alineaciones',
            'metas',
            'ods'
        ));
    }

    public function show($id)
    {
        $alineacion = AlineacionEstrategica::with([
            'objetivoEstrategico',
            'metaNacional.ods',
            'usuario',
            'organizacion'
        ])->findOrFail($id);

        return view('dashboard.estrategico.alineacion.show', compact('alineacion'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'meta_nacional_id' => 'required|exists:cat_meta_nacional,id_meta_nacional',
        ]);

        $alineacion = AlineacionEstrategica::findOrFail($id);
        $alineacion->meta_nacional_id  = $request->input('meta_nacional_id');
        $alineacion->usuario_id = Auth::id();

        $alineacion->save();

        return back()->with('success', 'Alineación actualizada correctamente.');
    }
    // ELIMINAR
    public function destroy($idAlineacion)
    {
        // Buscas la fila en la tabla intermedia
        $alineacion = AlineacionEstrategica::findOrFail($idAlineacion);

        // La borras. El Objetivo y la Meta quedan vivos, solo se rompe el lazo.
        $alineacion->delete();

        return back()->with('success', 'El objetivo ha sido desvinculado correctamente.');
    }
}
