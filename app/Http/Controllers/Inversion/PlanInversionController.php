<?php

namespace App\Http\Controllers\Inversion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

use App\Models\Inversion\PlanInversion;
use Illuminate\Support\Facades\DB;

class PlanInversionController extends Controller
{
    public function index()
    {
        // Traemos los planes de la institución del usuario logueado
        // Si eres superadmin, podrías quitar el where.
        $idorganizacion = Auth::user()->id_organizacion;
        //dd($idorganizacion);
        $planes = PlanInversion::where('organizacion_id', $idorganizacion)
            ->orderBy('anio', 'desc')
            ->get();
        $planes->loadCount('programas');

        return view('dashboard.inversion.planes.index', compact('planes'));
    }
    // Formulario de creación
    public function create()
    {
        return view('dashboard.inversion.planes.crear');
    }
    public function store(Request $request)
    {

        // Obtener ID de la organización
        $organizacion_id = Auth::user()->id_organizacion;

        // Validación
        $request->validate([
            'nombre' => 'required|string|max:200',
            'monto_total' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:1000',
            'numero_resolucion' => 'nullable|string|max:100',
            'documento_soporte' => 'nullable|file|mimes:pdf|max:5120',
            'anio' => [
                'required',
                'integer',
                'digits:4',
                Rule::unique('tra_plan_inversion')->where(function ($query) use ($organizacion_id) {
                    return $query->where('organizacion_id', $organizacion_id);
                })
            ],
        ], [
            'anio.unique' => 'Ya existe un Plan de Inversión registrado para este año.'
        ]);
        try {
            DB::beginTransaction();


            // Preparar los datos base
            $datosParaGuardar = [
                'organizacion_id' => $organizacion_id,
                'nombre'          => $request->nombre,
                'anio'            => $request->anio,
                'monto_total'     => $request->monto_total,
                'descripcion'     => $request->descripcion,
                'numero_resolucion' => $request->numero_resolucion,
                'version'         => 1,
                'estado'          => 'FORMULACION',
                'ruta_documento' => null,
            ];

            // Lógica del archivo (si se subió uno)
            if ($request->hasFile('documento_soporte')) {
                $file = $request->file('documento_soporte');
                // El archivo se sube y guarda la ruta
                $path = $request->file('documento_soporte')->store('documentos/planes_institucionales', 'public');
                $nombreArchivo = $request->file('documento_soporte')->getClientOriginalName();

                // Asignamos la ruta al array
                $datosParaGuardar['ruta_documento'] = $path;
                // Asignamos el nombre original del archivo
                $datosParaGuardar['nombre_archivo'] = $nombreArchivo;
            }

            // UN SOLO GUARDADO FINAL
            PlanInversion::create($datosParaGuardar);
            DB::commit();
            return back()->with('success', 'Plan Anual creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el Plan: ' . $e->getMessage())->withInput();
        }
    }

    // Formulario de edición
    public function edit($id)
    {
        $plan = PlanInversion::findOrFail($id);
        return view('dashboard.inversion.planes.editar', compact('plan'));
    }
    // Actualizar el plan
    public function update(Request $request, $id)
    {
        $plan = PlanInversion::findOrFail($id);
        $organizacion_id = Auth::user()->id_organizacion;
        // VALIDACIÓN
        $request->validate([
            'nombre'            => 'required|string|max:200',
            'monto_total'       => 'required|numeric',
            'estado'            => 'required|in:FORMULACION,APROBADO,EJECUCION,CERRADO',
            'descripcion'       => 'nullable|string|max:1000',
            'documento_soporte' => 'nullable|file|mimes:pdf|max:5120',
            'anio' => [
                'required',
                'integer',
                Rule::unique('tra_plan_inversion')
                    ->where('organizacion_id', $organizacion_id)
                    ->ignore($plan->id)
            ],
        ]);
        try {
            DB::beginTransaction();


            //PREPARAR DATOS (Tomamos solo los campos de texto primero)
            $datosParaActualizar = [
                'nombre'      => $request->nombre,
                'monto_total' => $request->monto_total,
                'estado'      => $request->estado,
                'descripcion' => $request->descripcion,
            ];
            if($request->has('es_reforma') && $request->es_reforma == 'si'){
                // Si es una reforma, incrementamos la versión
                $datosParaActualizar['version'] = $plan->version + 1;
                //
                $datosParaActualizar['descripcion'] = $datosParaActualizar['descripcion']."(Reformado)";
            }

            // LÓGICA DEL ARCHIVO (Solo si subieron uno nuevo)
            if ($request->hasFile('documento_soporte')) {

                //Borrar el archivo viejo si existe
                if ($plan->ruta_documento) {
                    Storage::disk('public')->delete($plan->ruta_documento);
                }
                $file = $request->file('documento_soporte');
                //Subir el nuevo y agregar la ruta al array de datos
                $path = $request->file('documento_soporte')->store('documentos/planes_institucionales', 'public');
                $nombreArchivo = $file->getClientOriginalName();
                $datosParaActualizar['ruta_documento'] = $path;
                $datosParaActualizar['nombre_archivo'] = $nombreArchivo;
            }

            // UN SOLO UPDATE FINAL
            $plan->update($datosParaActualizar);
            DB::commit();
            return back()->with('success', 'Plan actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el Plan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $plan = PlanInversion::findOrFail($id);

            // Verificar si el plan tiene programas asociados
            if ($plan->programas()->count() > 0) {
                return back()->with('error', 'No se puede eliminar el Plan porque tiene Programas asociados.');
            }

            // Borrar el archivo asociado si existe
            if ($plan->ruta_documento) {
                Storage::disk('public')->delete($plan->ruta_documento);
            }

            // Eliminar el plan
            $plan->delete();

            DB::commit();
            return back()->with('success', 'Plan eliminado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar el Plan: ' . $e->getMessage());
        }
    }
}
