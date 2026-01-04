<?php

namespace App\Http\Controllers\Inversion;

use App\Http\Controllers\Controller;
use App\Models\Inversion\ProyectoInversion;
use App\Models\Inversion\ProyectoLocalizacion;
use App\Models\Inversion\Financiamiento;
use App\Models\Inversion\Programa;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProyectoInversionController extends Controller
{
    /**
     * Lista de Proyectos (Index)
     */
    public function index(Request $request)
    {
        // 1. Iniciamos la consulta base con la relación del programa
        $query = ProyectoInversion::with('programa');

        // 2. Aplicamos los filtros de búsqueda (Mantengo tu lógica)
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre_proyecto', 'like', "%{$search}%")
                    ->orWhere('cup', 'like', "%{$search}%");
            });
        }

        // 3. Filtro por Tipo de Inversión
        if ($request->filled('tipo')) {
            $query->where('tipo_inversion', $request->tipo);
        }

        // 4. NUEVO: Calculamos las estadísticas ANTES de paginar
        // Usamos clone para que los filtros de búsqueda también afecten a los totales
        $stats = [
            'total_proyectos' => (clone $query)->count(),
            'inversion_total' => (clone $query)->sum('monto_total_inversion'),
            'promedio_monto'  => (clone $query)->avg('monto_total_inversion') ?? 0,
        ];

        // 5. Paginación final
        $proyectos = $query->orderBy('created_at', 'desc')->paginate(10);

        // 6. Enviamos todo a la vista
        return view('inversion.proyectos.index', compact('proyectos', 'stats'));
    }

    /**
     * Formulario de Creación (Create)
     */
    public function create()
    {
        $programas = Programa::all();
        return view('inversion.proyectos.crear', compact('programas'));
    }

    /**
     * Guardar nuevo Proyecto (Store)
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_programa'           => 'required|exists:tra_programa,id',
            'cup'                   => 'nullable|unique:tra_proyecto_inversion,cup|max:30',
            'nombre_proyecto'       => 'required|string|max:255',
            'tipo_inversion'        => 'required|in:Obra,Bien,Servicio',
            'fecha_inicio_estimada' => 'required|date',
            'fecha_fin_estimada'    => 'required|date|after_or_equal:fecha_inicio_estimada',
            'monto_total_inversion' => 'required|numeric|min:0',
            'codigo_provincia'      => 'required',
            'codigo_canton'         => 'required',
            'fuente_financiamiento' => 'required',
            'monto_financiamiento'  => 'required|numeric|min:0',
            'anio_financiamiento'   => 'required|numeric',
        ]);

        if ((float)$request->monto_total_inversion != (float)$request->monto_financiamiento) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['monto_financiamiento' => 'Los montos deben coincidir exactamente.']);
        }
        DB::beginTransaction();
        try {
            $inicio = Carbon::parse($request->fecha_inicio_estimada);
            $fin = Carbon::parse($request->fecha_fin_estimada);
            $duracion = $inicio->diffInMonths($fin) + 1;

            $proyecto = ProyectoInversion::create([
                'id_programa'             => $request->id_programa,
                'cup'                     => $request->cup,
                'nombre_proyecto'         => $request->nombre_proyecto,
                'tipo_inversion'          => $request->tipo_inversion,
                'fecha_inicio_estimada'   => $request->fecha_inicio_estimada,
                'fecha_fin_estimada'      => $request->fecha_fin_estimada,
                'duracion_meses'          => $duracion,
                'descripcion_diagnostico' => $request->descripcion_diagnostico,
                'monto_total_inversion'   => $request->monto_total_inversion,
                'estado_dictamen'         => 'Solicitado',
            ]);

            ProyectoLocalizacion::create([
                'id_proyecto'      => $proyecto->id,
                'codigo_provincia' => $request->codigo_provincia,
                'codigo_canton'    => $request->codigo_canton,
                'codigo_parroquia' => $request->codigo_parroquia,
            ]);

            Financiamiento::create([
                'id_proyecto'           => $proyecto->id,
                'anio'                  => $request->anio_financiamiento,
                'fuente_financiamiento' => $request->fuente_financiamiento,
                'monto'                 => $request->monto_financiamiento,
            ]);

            DB::commit();
            return redirect()->route('inversion.proyectos.index')->with('success', 'Proyecto creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Ver Detalles (Show)
     */
    public function show($id)
    {
        $proyecto = ProyectoInversion::with(['programa', 'localizaciones', 'financiamientos'])->findOrFail($id);
        return view('inversion.proyectos.detalles', compact('proyecto'));
    }

    /**
     * Formulario de Edición (Edit)
     */
    public function edit($id)
    {
        $proyecto = ProyectoInversion::with(['localizaciones', 'financiamientos'])->findOrFail($id);
        $programas = Programa::all();
        return view('inversion.proyectos.editar', compact('proyecto', 'programas'));
    }

    /**
     * Actualizar Proyecto (Update)
     */
    public function update(Request $request, $id)
    {
        $proyecto = ProyectoInversion::findOrFail($id);

        $request->validate([
            'id_programa'           => 'required|exists:tra_programa,id',
            'cup'                   => 'nullable|max:30|unique:tra_proyecto_inversion,cup,' . $proyecto->id,
            'nombre_proyecto'       => 'required|string|max:255',
            'tipo_inversion'        => 'required|in:Obra,Bien,Servicio',
            'fecha_inicio_estimada' => 'required|date',
            'fecha_fin_estimada'    => 'required|date|after_or_equal:fecha_inicio_estimada',
            'monto_total_inversion' => 'required|numeric|min:0',
            'codigo_provincia'      => 'required',
            'codigo_canton'         => 'required',
            'fuente_financiamiento' => 'required',
            'monto_financiamiento'  => 'required|numeric|min:0',
        ]);

        if ($request->monto_total_inversion != $request->monto_financiamiento) {
            return redirect()->back()->withInput()->withErrors(['monto_financiamiento' => 'Los montos deben coincidir.']);
        }

        DB::beginTransaction();
        try {
            $inicio = Carbon::parse($request->fecha_inicio_estimada);
            $fin = Carbon::parse($request->fecha_fin_estimada);
            $duracion = $inicio->diffInMonths($fin) + 1;

            $proyecto->update([
                'id_programa'             => $request->id_programa,
                'cup'                     => $request->cup,
                'nombre_proyecto'         => $request->nombre_proyecto,
                'tipo_inversion'          => $request->tipo_inversion,
                'fecha_inicio_estimada'   => $request->fecha_inicio_estimada,
                'fecha_fin_estimada'      => $request->fecha_fin_estimada,
                'duracion_meses'          => $duracion,
                'descripcion_diagnostico' => $request->descripcion_diagnostico,
                'monto_total_inversion'   => $request->monto_total_inversion,
            ]);
            $localizacion = ProyectoLocalizacion::where('id_proyecto', $proyecto->id)->first();

            if ($localizacion) {
                // Si existe, actualizamos
                $localizacion->update([
                    'codigo_provincia' => $request->codigo_provincia,
                    'codigo_canton'    => $request->codigo_canton,
                    'codigo_parroquia' => $request->codigo_parroquia,
                ]);
            } else {
                // Si por algún motivo no existía, la creamos
                ProyectoLocalizacion::create([
                    'id_proyecto'      => $proyecto->id,
                    'codigo_provincia' => $request->codigo_provincia,
                    'codigo_canton'    => $request->codigo_canton,
                    'codigo_parroquia' => $request->codigo_parroquia,
                ]);
            }

            $proyecto->financiamientos()->updateOrCreate(
                ['id_proyecto' => $proyecto->id],
                [
                    'anio'                  => $request->anio_financiamiento,
                    'fuente_financiamiento' => $request->fuente_financiamiento,
                    'monto'                 => $request->monto_financiamiento,
                ]
            );

            DB::commit();
            return redirect()->route('inversion.proyectos.index')
                ->with('success', 'El proyecto actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $proyecto = ProyectoInversion::findOrFail($id);

            // 1. "Eliminar" la ubicación geográfica vinculada
            $proyecto->localizaciones()->delete();

            // 2. "Eliminar" los registros de financiamiento vinculados
            $proyecto->financiamientos()->delete();

            // 3. "Eliminar" el proyecto principal
            $proyecto->delete();

            DB::commit();
            return redirect()->route('inversion.proyectos.index')
                ->with('success', 'El proyecto y todos sus datos vinculados han sido eliminados.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al intentar eliminar: ' . $e->getMessage());
        }
    }
}
