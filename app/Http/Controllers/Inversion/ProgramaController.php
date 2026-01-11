<?php

namespace App\Http\Controllers\Inversion;

use App\Http\Controllers\Controller;
use App\Models\Planificacion\Programa;
use App\Models\Planificacion\ObjetivoEstrategico;
use App\Models\Inversion\FuenteFinanciamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProgramaController extends Controller
{
    /**
     * Listado de programas con carga de relaciones para optimizar la tabla.
     */
    public function index()
    {
        // Cargamos relaciones para evitar el error N+1 en la tabla
        $programas = Programa::with(['objetivoE', 'fuenteFinanciamiento'])
            ->orderBy('anio_inicio', 'desc')
            ->paginate(10);
            //Preguntamos si teiene objetivos estrategicos
        $tieneObjetivos = ObjetivoEstrategico::count() > 0;
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
        $request->validate([
            'codigo_cup'            => 'required|unique:tra_programa,codigo_cup|max:20',
            'nombre_programa'       => 'required|string|max:255',
            'anio_inicio'           => 'required|integer|min:2020',
            'anio_fin'              => 'required|integer|gte:anio_inicio',
            'monto_planificado'     => 'required|numeric|min:0',
            'id_fuente'             => 'required|exists:cat_fuente_financiamiento,id_fuente',
            'id_objetivo_estrategico' => 'required|exists:tra_objetivo_estrategico,id_objetivo_estrategico',
            'cobertura'             => 'required|in:NACIONAL,ZONAL,PROVINCIAL,CANTONAL',
        ]);

        try {
            DB::beginTransaction();

            $programa = new Programa();
            $programa->codigo_cup = $request->codigo_cup;
            $programa->nombre_programa = $request->nombre_programa;
            $programa->descripcion = $request->descripcion;
            $programa->anio_inicio = $request->anio_inicio;
            $programa->anio_fin = $request->anio_fin;
            $programa->monto_planificado = $request->monto_planificado;
            $programa->id_fuente = $request->id_fuente;
            $programa->id_objetivo_estrategico = $request->id_objetivo_estrategico;
            $programa->cobertura = $request->cobertura;
            $programa->estado = 'POSTULADO'; // Estado inicial por defecto

            // Asignamos la organización del usuario que crea el programa
            $programa->id_organizacion = Auth::user()->id_organizacion;

            $programa->save();

            DB::commit();
            return redirect()->route('inversion.programas.index')
                ->with('status', 'Programa registrado exitosamente en el PAI.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar el programa: ' . $e->getMessage())->withInput();
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
