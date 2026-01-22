<?php

namespace App\Http\Controllers\Planificacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Planificacion\PlanInstitucional;
use Illuminate\Support\Facades\Auth;

class PlanInstitucionalController extends Controller
{

    // Muestra la lista de planes institucionales
    public function index()
    {
        $idOrganizacion = Auth::user()->id_organizacion;

        // Buscamos el Plan VIGENTE (Solo debe haber uno)
        $planVigente = PlanInstitucional::where('id_organizacion', $idOrganizacion)
            ->where('estado', 'VIGENTE')
            ->withCount('objetivosEstrategicos') // Contamos cuántos objetivos tiene
            ->first();

        // Buscamos el historial (Los que no son vigentes)
        $historial = PlanInstitucional::where('id_organizacion', $idOrganizacion)
            ->where('estado', '!=', 'VIGENTE')
            ->orderBy('anio_fin', 'desc')
            ->get();

        return view('dashboard.estrategico.planes.index', compact('planVigente', 'historial'));
    }
    public function show($id)
    {
        $idOrganizacion = Auth::user()->id_organizacion;

        // Buscamos el plan con sus objetivos y la alineación a la meta nacional
        $plan = PlanInstitucional::where('id_organizacion', $idOrganizacion)
            ->where('id_plan', $id)
            ->with(['objetivosEstrategicos.alineacion.metaNacional'])
            ->firstOrFail();

        return view('dashboard.estrategico.planes.show', compact('plan'));
    }
    // Muestra la lista de planes institucionales
    public function create()
    {
        return view('dashboard.estrategico.planes.crear');
    }


    // Guarda los datos
    public function store(Request $request)
    {
        // Validación
        $request->validate([
            'nombre_plan' => 'required|string|max:255',
            'tipo_plan'   => 'required|in:PDOT,PEI,PEDI,SECTORIAL',
            'anio_inicio' => 'required|digits:4|integer|min:2000',
            'anio_fin'    => 'required|digits:4|integer|gte:anio_inicio',
            'descripcion' => 'nullable|string'
        ]);

        // Obtener Organización del Usuario
        $idOrganizacion = Auth::user()->id_organizacion;

        //  Si creamos uno nuevo VIGENTE, pasamos los anteriores a HISTORICO
        // Esto asegura que solo haya un plan activo a la vez.
        PlanInstitucional::where('id_organizacion', $idOrganizacion)
            ->where('estado', 'VIGENTE')
            ->update(['estado' => 'HISTORICO']);

        // Crear el Plan
        $plan = new PlanInstitucional();
        $plan->id_organizacion = $idOrganizacion;
        $plan->nombre_plan = $request->nombre_plan;
        $plan->tipo_plan = $request->tipo_plan;
        $plan->anio_inicio = $request->anio_inicio;
        $plan->anio_fin = $request->anio_fin;
        $plan->estado = 'VIGENTE';
        $plan->descripcion = $request->descripcion;
        $plan->save();

        // Redireccionar a la carga de Objetivos Siguiente paso lógico
        return redirect()->route('estrategico.planificacion.planes.index', $plan->id_plan)
            ->with('success', 'Plan Institucional registrado correctamente. Ahora defina sus Objetivos.');
    }
    // Muestra el formulario con los datos cargados
    public function edit($id)
    {
        $idOrganizacion = Auth::user()->id_organizacion;

        // Buscamos el plan asegurando que pertenezca a la organización del usuario
        $plan = PlanInstitucional::where('id_organizacion', $idOrganizacion)
            ->where('id_plan', $id)
            ->firstOrFail();

        return view('dashboard.estrategico.planes.editar', compact('plan'));
    }

    // Procesa los cambios
    public function update(Request $request, $id)
    {
        $idOrganizacion = Auth::user()->id_organizacion;

        //  Validación (Igual que en store)
        $request->validate([
            'nombre_plan' => 'required|string|max:255',
            'tipo_plan'   => 'required|in:PDOT,PEI,PEDI,SECTORIAL',
            'anio_inicio' => 'required|digits:4|integer|min:2000',
            'anio_fin'    => 'required|digits:4|integer|gte:anio_inicio',
            'descripcion' => 'nullable|string'
        ]);

        //  Buscar el plan con seguridad
        $plan = PlanInstitucional::where('id_organizacion', $idOrganizacion)
            ->where('id_plan', $id)
            ->firstOrFail();

        //  Actualizar datos
        $plan->nombre_plan = $request->nombre_plan;
        $plan->tipo_plan   = $request->tipo_plan;
        $plan->anio_inicio = $request->anio_inicio;
        $plan->anio_fin    = $request->anio_fin;
        $plan->descripcion = $request->descripcion;
        // El estado se cambia cerrando el plan o creando uno nuevo
        $plan->save();

        return redirect()->route('estrategico.planificacion.planes.index')
            ->with('success', 'El Plan Institucional se actualizó correctamente.');
    }
    public function cerrarPlan($id)
    {
        $plan = PlanInstitucional::findOrFail($id);

        // Lógica de seguridad: Solo cerrar si está vigente
        if ($plan->estado !== 'VIGENTE') {
            return back()->with('error', 'Solo se pueden cerrar planes vigentes.');
        }

        // Cambiamos el estado
        $plan->estado = 'CERRADO';

        $plan->save();

        return back()->with('success', 'El Plan ha sido cerrado y archivado en el histórico correctamente.');
    }
}
