<?php

namespace App\Http\Controllers\Inversion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Exception;

use App\Http\Controllers\Controller;
use App\Models\Inversion\Programa;
use App\Models\Planificacion\ObjetivoEstrategico;
use App\Models\Inversion\FuenteFinanciamiento;
use App\Models\Inversion\PlanInversion;
use Illuminate\Support\Facades\Storage;


class ProgramaController extends Controller
{
    /**
     * Listado de programas con carga de relaciones para optimizar la tabla.
     */

    public function index(Request $request)
    {
        //  Datos básicos
        $idOrganizacion = Auth::user()->id_organizacion;
        $busqueda = trim($request->input('busqueda')); // Limpiamos espacios

        //  Construcción de la Consulta
        $query = Programa::query()
            ->whereHas('plan', function ($q) use ($idOrganizacion) {
                $q->where('organizacion_id', $idOrganizacion);
            })
            ->with(['plan', 'objetivoE', 'fuenteFinanciamiento', 'organizacion']);

        //  Lógica del Buscador
        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                //  Buscar por Nombre del Programa
                $q->where('nombre_programa', 'LIKE', "%{$busqueda}%")

                    //  Buscar por CUP o Código
                    ->orWhere('codigo_programa', 'LIKE', "%{$busqueda}%")

                    //  (Opcional) Buscar por el nombre del Objetivo Estratégico asociado
                    ->orWhereHas('objetivoE', function ($subQ) use ($busqueda) {
                        $subQ->where('nombre', 'LIKE', "%{$busqueda}%");
                    });
            });
        }

        //  Ordenamiento y Paginación
        $programas = $query->orderBy('plan_id', 'desc')
            ->paginate(10)
            ->appends(['busqueda' => $busqueda]);

        // Verificar si existen objetivos estratégicos registrados
        $tieneObjetivos = ObjetivoEstrategico::where('organizacion_id', $idOrganizacion)->exists();

        $fuentes = FuenteFinanciamiento::all();

        return view('dashboard.inversion.programas.index', compact('programas', 'tieneObjetivos', 'fuentes'));
    }
    /**
     * Vista show
     */
    public function show($id)
    {
        // Cargamos el programa con sus relaciones clave
        // 'proyectos' es vital para listar qué obras tiene este programa
        $programa = Programa::with(['plan', 'fuenteFinanciamiento', 'objetivoE', 'proyectos'])
            ->findOrFail($id);

        // Cálculos rápidos para la vista
        $saldo = $programa->monto_asignado - $programa->monto_planificado;
        $porcentajeUso = $programa->monto_asignado > 0
            ? ($programa->monto_planificado / $programa->monto_asignado) * 100
            : 0;

        return view('dashboard.inversion.programas.show', compact('programa', 'saldo', 'porcentajeUso'));
    }

    /**
     * Formulario de creación: enviamos los catálogos necesarios.
     */
    public function create()
    {
        $idOrgUsuario = Auth::user()->id_organizacion;

        // Validar Objetivos
        $objetivosEstrategicos = ObjetivoEstrategico::where('organizacion_id', $idOrgUsuario)
            ->where('estado', 1)
            ->get();

        if ($objetivosEstrategicos->isEmpty()) {
            return redirect()->route('estrategico.objetivos.index')
                ->with('error', 'Error: Registre sus objetivos estratégicos primero.');
        }
        //  Obtener el Plan de Inversión activo o en formulación
        $planInversion = PlanInversion::where('organizacion_id', $idOrgUsuario)
            ->whereIn('estado', ['FORMULACION', 'APROBADO']) // Sugerencia: Permitir ambos
            ->orderBy('anio', 'desc')
            ->first();

        // VALIDACIÓN CRÍTICA: Si no hay plan, no dejes entrar al formulario
        if (!$planInversion) {
            return redirect()->route('inversion.planes.index')
                ->with('error', 'No existe un Plan de Inversión activo/en formulación para crear programas.');
        }

        $fuentes = FuenteFinanciamiento::all();

        return view('dashboard.inversion.programas.create', compact('objetivosEstrategicos', 'fuentes', 'planInversion'));
    }

    /**
     * Guardar el nuevo programa con validación técnica.
     */
    public function store(Request $request)
    {

        // Obtener el Plan de Inversión asociado
        $planInversion = PlanInversion::where('id', $request->plan_id)
            ->where('organizacion_id', Auth::user()->id_organizacion)
            ->firstOrFail();
        // Obtener el año fiscal del plan
        $anio = $planInversion?->anio;
        $inicioFiscal = $anio . '-01-01';
        $finFiscal    = $anio . '-12-31';

        $request->validate([
            'codigo_programa'               => 'required|unique:tra_programa,codigo_programa|max:20',
            'nombre_programa'   => 'required|string|max:255',
            'descripcion'       => 'nullable|string',
            'monto_asignado'    => 'required|numeric|min:0',
            'monto_planificado' => 'required|numeric|min:0|lte:monto_asignado',
            'id_fuente'         => 'required|exists:cat_fuente_financiamiento,id_fuente',
            'id_objetivo_estrategico' => 'required|exists:cat_objetivo_estrategico,id_objetivo_estrategico',
            'cobertura'         => 'required|in:NACIONAL,ZONAL,PROVINCIAL,CANTONAL',
            'tipo_programa' => 'required|in:INVERSION,GASTO_CORRIENTE,CAPITAL_HUMANO',

            'fecha_inicio' => "required|date|after_or_equal:$inicioFiscal|before_or_equal:$finFiscal",
            'fecha_fin'    => "required|date|after:fecha_inicio|before_or_equal:$finFiscal",

            'sector' => 'required|in:SOCIAL,ECONOMICO,INFRAESTRUCTURA',
            'documento_habilitante' => 'nullable|file|mimes:pdf|max:10240',
        ], [
            'codigo_programa.unique' => 'Este código ya está en uso por otro programa.',
            'monto_planificado.lte' => 'El monto planificado no puede superar al monto asignado (Techo presupuestario).',
            'fecha_inicio.after_or_equal' => "La fecha de inicio debe ser posterior o igual al inicio del año fiscal del plan ($anio).",
            'fecha_inicio.before_or_equal' => "La fecha de inicio debe ser anterior o igual al fin del año fiscal del plan ($anio).",
            'fecha_fin.after' => "La fecha de fin debe ser posterior a la fecha de inicio.",
            'fecha_fin.before_or_equal' => "La fecha de fin debe ser anterior o igual al fin del año fiscal del plan ($finFiscal).",
        ]);

        try {
            DB::beginTransaction();

            $programa = new Programa();


            $programa->codigo_programa = $request->codigo_programa;

            $programa->nombre_programa = $request->nombre_programa;
            $programa->descripcion = $request->descripcion;
            $programa->fecha_inicio = $request->fecha_inicio;
            $programa->fecha_fin = $request->fecha_fin;
            $programa->monto_planificado = $request->monto_planificado;
            $programa->monto_asignado = $request->monto_asignado;
            $programa->fuente_id = $request->id_fuente;
            $programa->objetivo_estrategico_id = $request->id_objetivo_estrategico;
            $programa->cobertura = $request->cobertura;
            $programa->tipo_programa = $request->tipo_programa;
            $programa->sector = $request->sector;
            $programa->estado = 'POSTULADO';

            $programa->plan_id = $request->plan_id;
            // Lógica del archivo (si se subió uno)
            if ($request->hasFile('documento_habilitante')) {

                $file = $request->file('documento_habilitante');
                $nombreOriginal = $file->getClientOriginalName();
                // Lo guardamos en la carpeta 'programas' dentro del disco 'public'
                $path = $request->file('documento_habilitante')->store('documentos.programas', 'public');

                $programa->nombre_archivo = $nombreOriginal;
                $programa->url_documento = $path;
            }
            $programa->save();

            DB::commit();

            return redirect()->route('inversion.programas.index')
                ->with('status', 'Programa registrado exitosamente en el PAI.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Formulario de edición.
     */
    public function edit($id)
    {
        // Buscamos el programa
        $programa = Programa::with('plan')->findOrFail($id);

        // Cargamos los catálogos (Igual que en create)
        $idOrgUsuario = Auth::user()->id_organizacion;

        $fuentes = FuenteFinanciamiento::all();

        $objetivosEstrategicos = ObjetivoEstrategico::where('organizacion_id', $idOrgUsuario)
            ->where('estado', 1)
            ->get();

        // Retornamos la vista pasando todas las variables
        return view('dashboard.inversion.programas.edit', compact('programa', 'fuentes', 'objetivosEstrategicos'));
    }

    /**
     * Actualización con lógica de auditoría automática (vía Trait).
     */

    public function update(Request $request, $id)
    {
        // Buscamos programa y plan
        $programa = Programa::with('plan')->findOrFail($id);

        // Variables de fecha
        $anio = $programa->plan->anio;
        $inicioFiscal = $anio . '-01-01';
        $finFiscal    = $anio . '-12-31';

        // VALIDACIÓN
        $request->validate([
            'codigo_programa' => [
                'required',
                'max:20',
                Rule::unique('tra_programa', 'codigo_programa')->ignore($programa->id)
            ],
            'nombre_programa' => 'required|string|max:255',
            'descripcion'     => 'nullable|string',
            'monto_asignado'    => 'required|numeric|min:0',
            'monto_planificado' => 'required|numeric|min:0|lte:monto_asignado',
            'fuente_id'               => 'required|exists:cat_fuente_financiamiento,id_fuente',
            'objetivo_estrategico_id' => 'required|exists:cat_objetivo_estrategico,id_objetivo_estrategico',
            'sector'          => 'required|in:SOCIAL,ECONOMICO,INFRAESTRUCTURA',
            'fecha_inicio' => "required|date|after_or_equal:$inicioFiscal|before_or_equal:$finFiscal",
            'fecha_fin'    => "required|date|after:fecha_inicio|before_or_equal:$finFiscal",
            'cobertura'     => 'required|in:NACIONAL,ZONAL,PROVINCIAL,CANTONAL',
            'tipo_programa' => 'required|in:INVERSION,GASTO_CORRIENTE,CAPITAL_HUMANO',
            'estado'        => 'required|in:POSTULADO,APROBADO,SUSPENDIDO,CERRADO',
            'documento_habilitante' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $datos = $request->except(['documento_habilitante', '_token', '_method']);

        // LOGICA DE ESTADOS
        if ($request->estado == 'APROBADO' && $programa->estado != 'APROBADO') {
            if ($programa->monto_asignado <= 0) return back()->with('error', 'Falta monto asignado.');
            if (empty($request->sector)) return back()->with('error', 'Falta sector.');
        }
        if ($request->estado == 'CERRADO') {
            if ($programa->proyectos()->where('estado', 'EN_EJECUCION')->count() > 0) {
                return back()->with('error', 'Hay proyectos en ejecución.');
            }
        }
        if ($request->estado == 'POSTULADO' && $programa->estado == 'APROBADO') {
            if ($programa->proyectos()->exists()) return back()->with('error', 'Ya tiene proyectos vinculados.');
        }

        // PROCESAMIENTO DEL ARCHIVO
        if ($request->hasFile('documento_habilitante')) {

            $file = $request->file('documento_habilitante');
            if ($programa->url_documento) {
                Storage::disk('public')->delete($programa->url_documento);
            }

            //  GUARDADO: Guardamos el nuevo archivo
            $nombreOriginal = $file->getClientOriginalName();
            $path = $file->store('documentos/programas', 'public'); // Corregí la ruta a 'documentos/programas' (con barra)

            //ASIGNACIÓN: Agregamos las rutas al array $datos
            $datos['nombre_archivo'] = $nombreOriginal;
            $datos['url_documento']  = $path;
        }

        try {
            $programa->update($datos);

            return redirect()->route('inversion.programas.index')
                ->with('status', 'Programa actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage())->withInput();
        }
    }
    public function destroy($id)
    {
        try {
            $programa = Programa::findOrFail($id);

            //  Validar que no tenga proyectos asociados
            if ($programa->proyectos()->count() > 0) {
                return redirect()->route('inversion.programas.index')
                    ->with('error', 'No se puede eliminar: El programa tiene proyectos asociados. Elimine los proyectos primero.');
            }

            // Eliminar
            $programa->delete();

            // Redirigir con mensaje de éxito
            return redirect()->route('inversion.programas.index')
                ->with('eliminar', 'ok');
        } catch (\Exception $e) {
            return redirect()->route('inversion.programas.index')
                ->with('error', 'Ocurrió un error al intentar eliminar.');
        }
    }
}
