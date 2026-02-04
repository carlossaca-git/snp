<?php

namespace App\Http\Controllers\Planificacion;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Planificacion\ObjetivoEstrategico;
use App\Models\Catalogos\ObjetivoNacional;
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
        $query = ObjetivoEstrategico::where('organizacion_id', $idOrganizacion)
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
        $objetivoEstr = $query->orderBy('codigo', 'desc')
            ->paginate(10)
            ->appends(['busqueda' => $busqueda]);
        // INYECCIÓN DE CÁLCULOS
        $objetivoEstr->getCollection()->transform(function ($objetivosEstr) {
            $proyectos = $objetivosEstr->proyectos ?? collect();

            if ($proyectos->isNotEmpty()) {
                $objetivosEstr->avance_promedio = $proyectos->avg(function ($proy) {
                    return $proy->avance_real;
                });
                $objetivosEstr->total_inversion = $proyectos->sum('monto_total_inversion');
            } else {
                $objetivosEstr->avance_promedio = 0;
                $objetivosEstr->total_inversion = 0;
            }

            return $objetivosEstr;
        });

        return view('dashboard.estrategico.objestra.index', compact('objetivoEstr', 'idOrganizacion'));
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create()
    {   //Obtener la organización del usuario
        $idOrganizacion = Auth::user()->id_organizacion;

        $alineacionPND = ObjetivoNacional::with('metasNacionales')->get();
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
        return view('dashboard.estrategico.objestra.create', compact(
            'objetivosNacionales',
            'objetivosEst',
            'planVigente',
            'alineacionPND'
        ));
    }
    /**
     * Metodo edit
     */
    public function edit($id)
    {
        $alineacionPND = ObjetivoNacional::with('metasNacionales')->get();
        $objetivoEstr = ObjetivoEstrategico::with('metasNacionales.objetivoNacional')->findOrFail($id);
        if ($objetivoEstr->organizacion_id != Auth::user()->id_organizacion) {
            // Si el ID de la org del objetivo no coincide con el del usuario impedimos el acceso
            abort(403, 'Acceso No Autorizado');
        }
        //Lista para llenar el select filtro
        $objetivosNacionales = ObjetivoNacional::where('estado', 1)->get();
        //Buscamos la primera meta vinculada
        $metaVinculada = $objetivoEstr->metasNacionales->first();
        $metasSeleccionadas = $objetivoEstr->metasNacionales->pluck('id_meta_nacional')->toArray();
        return view('dashboard.estrategico.objestra.edit', compact(
            'objetivoEstr',
            'objetivosNacionales',
            'metaVinculada',
            'alineacionPND',
            'metasSeleccionadas'
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
        $usuario = Auth::user()->id_usuario;
        //  Validaciones
        $request->validate([
            'metas_id'              => 'required|array|min:1',
            'metas_id.*'            => 'exists:cat_meta_nacional,id_meta_nacional',
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
            'documento_respaldo'    => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ], [
            'metas_id.required' => 'Debe seleccionar al menos una Meta Nacional.',
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
            //Documentos
            if ($request->hasFile('documento_respaldo')) {
                $file = $request->file('documento_respaldo');

                // Guardar en: storage/app/public/objetivos/respaldos
                $path = $file->store('objetivos/respaldos', 'public');
            }

            // Crear el Objetivo
            $objetivo = ObjetivoEstrategico::create([
                'organizacion_id'       => $idOrganizacion,
                'plan_id'               => $planVigente->id_plan,
                'usuario_id'            => $usuario,
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
                'url_documento'         => $path,
                'nombre_archivo'        => $file->getClientOriginalName(),
                'estado'                => 1
            ]);

            // Guardamos la alineacion estrategica
            $objetivo->metasNacionales()->sync($request->metas_id);

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
            'metas_id'     => 'required|array|min:1',
            'metas_id.*'   => 'exists:cat_meta_nacional,id_meta_nacional',
            'codigo'       => 'required|max:20|unique:cat_objetivo_estrategico,codigo,' . $id . ',id_objetivo_estrategico',
            'nombre'       => 'required|string|max:500',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
            'indicador'    => 'nullable|string',
            'meta'         => 'nullable|string',
            'linea_base'   => 'nullable|string',
            'documento_respaldo' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ], [
            'metas_id.required' => 'Debe seleccionar al menos una Meta Nacional.',
        ]);

        DB::beginTransaction();

        try {
            $objetivoEstr = ObjetivoEstrategico::findOrFail($id);

            // Verificar dueño
            if ($objetivoEstr->organizacion_id != Auth::user()->id_organizacion) {
                abort(403, 'Acceso denegado');
            }

            $objetivoEstr->update($request->except([
                '_token',
                '_method',
                'metas_id',
                'documento_respaldo'
            ]));

            // Lógica del Archivo (Si agregaste el input de archivo al edit)
            if ($request->hasFile('documento_respaldo')) {
                // Borrar anterior si existe (Opcional, buena práctica)
                if ($objetivoEstr->url_documento) {
                    Storage::disk('public')->delete($objetivoEstr->url_documento);
                }

                $file = $request->file('documento_respaldo');
                $path = $file->store('objetivos/respaldos', 'public');

                $objetivoEstr->url_documento = $path;
                $objetivoEstr->nombre_archivo = $file->getClientOriginalName();
            }

            //Guardamos el objetivo
            $objetivoEstr->save();

            // Guardamos la alineacion
            $objetivoEstr->metasNacionales()->sync($request->metas_id);

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
            DB::beginTransaction();

            $objetivo = ObjetivoEstrategico::findOrFail($id);

            // Verificar seguridad
            if ($objetivo->organizacion_id != Auth::user()->id_organizacion) {
                abort(403, 'No tienes permiso para eliminar este objetivo.');
            }

            // Verificar dependencias
            // Si este objetivo ya tiene proyectos no se permite borrarlos
            if ($objetivo->proyectos()->count() > 0) {
                return back()->with('error', 'No se puede eliminar: Este objetivo tiene Proyectos asociados. Elimine los proyectos primero.');
            }

            // Limpieza de alineaciones
            $objetivo->metasNacionales()->detach();

            // LIMPIEZA DE ARCHIVOS
            if ($objetivo->url_documento && Storage::disk('public')->exists($objetivo->url_documento)) {
                Storage::disk('public')->delete($objetivo->url_documento);
            }

            // eliminar el objetivo
            $objetivo->delete();

            DB::commit();

            return redirect()->route('estrategico.objetivos.index')
                ->with('success', 'Objetivo eliminado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}
