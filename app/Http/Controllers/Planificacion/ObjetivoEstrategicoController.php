<?php

namespace App\Http\Controllers\Planificacion;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Planificacion\ObjetivoEstrategico;
use App\Models\Catalogos\ObjetivoNacional;
use App\Models\Catalogos\MetaNacional;
use App\Models\Planificacion\AlineacionEstrategica;
use App\Models\Planificacion\PlanInstitucional;

class ObjetivoEstrategicoController extends Controller
{
    /**
     * Muestra el listado de objetivos.
     */

    public function index(Request $request)
    {
        // Obtener datos básicos
        $idOrganizacion = Auth::user()->id_organizacion;
        $busqueda = $request->input('busqueda');
        //  Construcción de la Consulta Base
        $query = ObjetivoEstrategico::where('id_organizacion', $idOrganizacion)
            ->with([
                'metasNacionales.objetivoNacional',
                'proyectos.marcoLogico'
            ]);

        //  APLICAR FILTRO DE BÚSQUEDA (Si existe)
        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                // Buscamos por nombre, código o descripción
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                    ->orWhere('codigo', 'LIKE', "%{$busqueda}%")
                    ->orWhere('descripcion', 'LIKE', "%{$busqueda}%");
            });
        }

        //  Ordenamiento y Paginación
        $objetivos = $query->orderBy('codigo', 'desc')
            ->paginate(10)
            ->appends(['busqueda' => $busqueda]);
        // INYECCIÓN DE CÁLCULOS
        $objetivos->getCollection()->transform(function ($objetivo) {
            $proyectos = $objetivo->proyectos ?? collect();

            if ($proyectos->isNotEmpty()) {
                $objetivo->avance_promedio = $proyectos->avg(function ($proy) {
                    return $proy->avance_real;
                });
                $objetivo->total_inversion = $proyectos->sum('monto_total_inversion');
            } else {
                $objetivo->avance_promedio = 0;
                $objetivo->total_inversion = 0;
            }

            return $objetivo;
        });

        return view('dashboard.estrategico.objestra.index', compact('objetivos', 'idOrganizacion'));
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create()
    {   //Obtener la organización del usuario
        $idOrganizacion = Auth::user()->id_organizacion;
        // Necesitamos la lista del PND para el Select
        $objetivosNacionales = ObjetivoNacional::where('estado', 1)->get();
        $objetivosEst = new ObjetivoEstrategico();
        $planVigente = PlanInstitucional::where('id_organizacion', $idOrganizacion)
            ->where('estado', 'VIGENTE')
            ->first();
        if (!$planVigente) {
            return redirect()->route('estrategico.planificacion.planes.index')
                ->with('error', 'Debe crear un Plan Institucional VIGENTE antes de registrar objetivos.');
        }
        return view('dashboard.estrategico.objestra.crear', compact('objetivosNacionales', 'objetivosEst', 'planVigente'));
    }
    /**
     * Metodo edit
     */
    public function edit($id)
    {
        $objetivo = ObjetivoEstrategico::with('metasNacionales.objetivoNacional')->findOrFail($id);
        if ($objetivo->id_organizacion != Auth::user()->id_organizacion) {
            // Si el ID de la org del objetivo no coincide con el del usuario impedimos el acceso
            abort(403, 'Acceso No Autorizado');
        }
        //Lista para llenar el select filtro
        $objetivosNacionales = ObjetivoNacional::where('estado', 1)->get();
        //Buscamos la primera meta vinculada
        $metaVinculada = $objetivo->metasNacionales->first();
        //Obtenemos ;as vinculaciones pero usamos null safe por si no hay vinculacion todabia
        $alineacionActual = $metaVinculada?->objetivoNacional->first()->id_objetivo_nacional ?? null;
        //Obtenemos el Id del objetivo
        return view('dashboard.estrategico.objestra.editar', compact(
            'objetivo',
            'objetivosNacionales',
            'alineacionActual',
            'metaVinculada'
        ));
    }

    /**
     * Guarda el nuevo objetivo en la BD.
     */

    public function show($id)
    {
        // Carga profunda: Eje <- Obj. Nacional <- Meta Nacional <- Objetivo Estratégico
        $objetivo = ObjetivoEstrategico::with([
            'metasNacionales.objetivoNacional.eje',
            'proyectos.marcoLogico',
            'organizacion'
        ])->findOrFail($id);

        // ... (El resto del código de cálculos se mantiene igual) ...
        $proyectos = $objetivo->proyectos ?? collect();
        $proyectos->transform(function ($proy) {
            $proy->calculo_avance = $proy->avance_real;
            return $proy;
        });
        $promedioAvance = $proyectos->isNotEmpty() ? $proyectos->avg('calculo_avance') : 0;
        $totalInversion = $proyectos->sum('monto_total_inversion');

        return view('dashboard.estrategico.objestra.show', compact('objetivo', 'proyectos', 'promedioAvance', 'totalInversion'));
    }
    public function store(Request $request)
{
    //  Validaciones
    $request->validate([
        'id_meta_nacional'      => 'required|exists:cat_meta_nacional,id_meta_nacional',
        'codigo'                => 'required|unique:cat_objetivo_estrategico,codigo|max:20',
        'nombre'                => 'required|string|max:500',
        'descripcion'           => 'nullable|string',
        'tipo_objetivo'         => 'required|in:Estrategico,Tactico',
        'unidad_responsable_id' => 'required|string',
        'fecha_inicio'          => 'required|date',
        'fecha_fin'             => 'required|date|after_or_equal:fecha_inicio',
        'indicador'             => 'nullable|string|max:255',
        'linea_base'            => 'nullable|numeric',
        'meta'                  => 'nullable|numeric',
    ]);

    //  INICIAR TRANSACCIÓN
    DB::beginTransaction();

    try {
        $idOrganizacion = Auth::user()->id_organizacion;

        if (!$idOrganizacion) {
            throw new \Exception('El usuario no está vinculado a ninguna organización.');
        }

        // Obtener el Plan Vigente (Vital para la vinculación)
        $planVigente = PlanInstitucional::where('id_organizacion', $idOrganizacion)
                        ->where('estado', 'VIGENTE')
                        ->first();

        // Validamos manualmente para dar un mensaje claro si no existe
        if (!$planVigente) {
            throw new \Exception('No existe un Plan Institucional VIGENTE. Por favor registre uno en el módulo de Planificación antes de crear objetivos.');
        }

        // Crear el Objetivo (CORREGIDO)
        $objetivo = ObjetivoEstrategico::create([
            'id_organizacion'       => $idOrganizacion,
            'id_plan'               => $planVigente->id_plan,
            'codigo'                => $request->codigo,
            'nombre'                => $request->nombre,
            'descripcion'           => $request->descripcion,
            'tipo_objetivo'         => $request->tipo_objetivo,
            'unidad_responsable_id' => $request->unidad_responsable_id,
            'indicador'             => $request->indicador,
            'linea_base'            => $request->linea_base,
            'meta'                  => $request->meta,
            'fecha_inicio'          => $request->fecha_inicio,
            'fecha_fin'             => $request->fecha_fin,
            'estado'                => 1
        ]);

        // Crear la Alineación (El puente con el Plan Nacional)
        AlineacionEstrategica::create([
            'organizacion_id'         => $idOrganizacion,
            'objetivo_estrategico_id' => $objetivo->id_objetivo_estrategico,
            'meta_nacional_id'        => $request->id_meta_nacional,
            'created_at'              => now(),
        ]);

        // Confirmar cambios
        DB::commit();

        return redirect()->route('estrategico.objetivos.index')
            ->with('success', 'El Objetivo Estratégico ha sido creado y vinculado al Plan Vigente.');

    } catch (\Exception $e) {
        // Revertir cambios si algo falla
        DB::rollBack();

        return back()->withInput()
            ->with('error', 'Error al guardar: ' . $e->getMessage());
    }
}
    /**
     * Actualiza el registro.
     */
    public function update(Request $request, $id)
    {
        // Validamos todo lo que viene del formulario
        $request->validate([
            'codigo'       => 'required|max:20|unique:cat_objetivo_estrategico,codigo,' . $id . ',id_objetivo_estrategico',
            'nombre'       => 'required|string|max:500',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
            'indicador'    => 'nullable|string',
            'meta'         => 'nullable|string',
            'linea_base'   => 'nullable|string',
            'id_meta_nacional' => 'required|exists:cat_meta_nacional,id_meta_nacional',
        ]);

        DB::beginTransaction();

        try {
            $objetivo = ObjetivoEstrategico::findOrFail($id);

            // Verificar dueño
            if ($objetivo->id_organizacion != Auth::user()->id_organizacion) {
                abort(403, 'Acceso denegado');
            }

            $objetivo->update($request->except([
                '_token',
                '_method',
                'id_objetivo_nacional'
            ]));

            //  Actualizar la relacion
            // Aqui tomamos ese id que sacamos arriba y lo guardamos en la tabla intermedia
            $objetivo->metasNacionales()->sync([
                $request->id_meta_nacional => [
                    'organizacion_id' => Auth::user()->id_organizacion
                ]
            ]);

            DB::commit();
            return redirect()->route('estrategico.objetivos.index')
                ->with('success', 'Objetivo actualizado con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Elimina el registro.
     */
    public function destroy($id)
    {
        try {
            $objetivo = ObjetivoEstrategico::findOrFail($id);
            $objetivo->delete();

            return redirect()->route('estrategico.objetivos.index')
                ->with('success', 'Registro eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}
