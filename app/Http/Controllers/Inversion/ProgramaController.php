<?php

namespace App\Http\Controllers\Inversion;

use App\Http\Controllers\Controller;
use App\Models\Planificacion\Programa;
use App\Models\Planificacion\ObjetivoEstrategico;
use App\Models\Inversion\FuenteFinanciamiento;
use App\Models\Planificacion\PlanInversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        $query = Programa::where('id_organizacion', $idOrganizacion)
            ->with(['objetivoE', 'fuenteFinanciamiento']);

        //  Lógica del Buscador
        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                //  Buscar por Nombre del Programa
                $q->where('nombre_programa', 'LIKE', "%{$busqueda}%")

                    //  Buscar por CUP o Código
                    ->orWhere('cup', 'LIKE', "%{$busqueda}%")

                    //  (Opcional) Buscar por el nombre del Objetivo Estratégico asociado
                    ->orWhereHas('objetivoE', function ($subQ) use ($busqueda) {
                        $subQ->where('nombre', 'LIKE', "%{$busqueda}%");
                    });
            });
        }

        //  Ordenamiento y Paginación
        $programas = $query->orderBy('anio_inicio', 'desc')
            ->paginate(10)
            ->appends(['busqueda' => $busqueda]); // Mantiene la búsqueda al cambiar de página

        //  Verificación auxiliar (tu código original)
        $tieneObjetivos = ObjetivoEstrategico::where('id_organizacion', $idOrganizacion)->exists();

        return view('dashboard.inversion.programas.index', compact('programas', 'tieneObjetivos'));
    }

    /**
     * Formulario de creación: enviamos los catálogos necesarios.
     */
    public function create()
    {
        $fuentes = FuenteFinanciamiento::all();
        $idOrganizacionUsuario = Auth::user()->id_organizacion;
        $objetivosEstrategicos = ObjetivoEstrategico::where('id_organizacion', $idOrganizacionUsuario)
            ->where('estado', 1)
            ->get();
        if ($objetivosEstrategicos->isEmpty()) {
            return redirect()->route('estrategico.objetivos.index')->with('Erro: Registre sus objetivos estrategicos');
        }

        return view('dashboard.inversion.programas.crear', compact('objetivosEstrategicos', 'fuentes'));
    }

    /**
     * Guardar el nuevo programa con validación técnica.
     */
    public function store(Request $request)
    {
        // VALIDACIÓN ROBUSTA
        $request->validate([
            'cup'               => 'required|unique:tra_programa,cup|max:20',
            'nombre_programa'   => 'required|string|max:255',
            'descripcion'       => 'nullable|string',
            'anio_inicio'       => 'required|integer|digits:4|min:2000|max:2100',
            'anio_fin'          => 'required|integer|digits:4|gte:anio_inicio',
            'monto_planificado' => 'required|numeric|min:0',
            'id_fuente'         => 'required|exists:cat_fuente_financiamiento,id_fuente',
            'id_objetivo_estrategico' => 'required|exists:cat_objetivo_estrategico,id_objetivo_estrategico',
            'cobertura'         => 'required|in:NACIONAL,ZONAL,PROVINCIAL,CANTONAL',
        ]);

        try {
            DB::beginTransaction();

            $programa = new Programa();


            $programa->cup = $request->cup;

            $programa->nombre_programa = $request->nombre_programa;
            $programa->descripcion = $request->descripcion;
            $programa->anio_inicio = $request->anio_inicio;
            $programa->anio_fin = $request->anio_fin;
            $programa->monto_planificado = $request->monto_planificado;
            $programa->id_fuente = $request->id_fuente;
            $programa->id_objetivo_estrategico = $request->id_objetivo_estrategico;
            $programa->cobertura = $request->cobertura;
            $programa->estado = 'POSTULADO';

            // Asignamos la organización
            $programa->id_organizacion = Auth::user()->id_organizacion;
            $programa->id_plan = $request->id_plan;

            $programa->save();

            DB::commit();

            return redirect()->route('inversion.programas.index')
                ->with('status', 'Programa registrado exitosamente en el PAI.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Formulario de edición.
     */
    public function edit($id)
    {
        $programa = Programa::findOrFail($id);
        $objetivosEstrategicos = ObjetivoEstrategico::all();
        $fuentes = FuenteFinanciamiento::all();

        return view('inversion.programas.edit', compact('programa', 'objetivosEstrategicos', 'fuentes'));
    }

    /**
     * Actualización con lógica de auditoría automática (vía Trait).
     */
    public function update(Request $request, $id)
    {
        $programa = Programa::findOrFail($id);

        $request->validate([
            'codigo_cup' => 'required|max:20|unique:tra_programa,codigo_cup,' . $id . ',id_programa',
            'nombre_programa' => 'required|string|max:255',
            'monto_planificado' => 'required|numeric|min:0',
            'id_fuente' => 'required|exists:cat_fuente_financiamiento,id_fuente',
        ]);

        $programa->update($request->all());

        return redirect()->route('inversion.programas.index')
            ->with('status', 'Programa actualizado correctamente.');
    }
}
