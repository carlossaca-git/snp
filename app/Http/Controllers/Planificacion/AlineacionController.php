<?php

namespace App\Http\Controllers\Planificacion;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Planificacion\AlineacionEstrategica;
use App\Models\Institucional\OrganizacionEstatal;
use App\Models\Planificacion\ObjetivoEstrategico;
use App\Models\Catalogos\ObjetivoNacional;
use App\Models\Catalogos\Ods;
use App\Models\Catalogos\MetaNacional;


class AlineacionController extends Controller
{
    // MUESTRA LA PANTALLA
    public function index($organizacionId)
    {

        $organizacion = OrganizacionEstatal::findOrFail($organizacionId);

        // 1. CATÁLOGOS
        $objetivosNacionales = ObjetivoNacional::with('ods')->get();
        $metasNacionales     = MetaNacional::all();
        $ods                 = Ods::orderBy('codigo', 'asc')->get();

        // 2. DATA PRINCIPAL (La que recorres en la tabla HTML)
        // CORRECCIÓN CLAVE: Aquí agregamos el 'with' para que los objetivos traigan sus datos.
        $objetivosEstrategicos = ObjetivoEstrategico::where('id_organizacion', $organizacionId)
            ->with([
                // Cargamos la alineación, y DENTRO de ella, sus relaciones (Nested Eager Loading)
                'alineacion.metaPnd',
                'alineacion.objetivoNacional',
                'alineacion.ods'
            ])
            ->get();

        // 3. (Opcional) Si necesitas la lista plana de alineaciones para gráficos o conteos
        // Si no la usas en la vista, podrías borrar esta consulta para ahorrar memoria.
        $alineaciones = AlineacionEstrategica::where('organizacion_id', $organizacionId)
            ->with(['objetivoEstrategico', 'objetivoNacional', 'ods', 'metaPnd'])
            ->get();
dd($alineaciones->toArray());
        return view('dashboard.estrategico.alineacion.gestionar', compact(
            'organizacion',
            'objetivosNacionales',
            'metasNacionales',
            'objetivosEstrategicos',
            'alineaciones',
            'ods'
        ));
    }

    // GUARDA
    public function store(Request $request, $organizacionId)
    {
        //dd("¡SI ENTRÓ AL CONTROLADOR!", $request->all());
        // 1. VALIDACIÓN (Fundamental para seguridad)
        $request->validate([
            'objetivo_estrategico_id' => 'required',
            'objetivo_nacional_id'    => 'required',
            'ods_id'                  => 'required',
            'id_meta_pnd'             => 'required', // Valida que el select dinámico no venga vacío
        ]);

        // 2. GUARDAR

        AlineacionEstrategica::create([
            // Lado izquierdo: Nombres EXACTOS de columnas en BD
            // Lado derecho: Nombres de los inputs del formulario (name="")
            'organizacion_id'         => $organizacionId,
            'objetivo_estrategico_id' => $request->objetivo_estrategico_id,
            'objetivo_nacional_id'    => $request->objetivo_nacional_id,
            'ods_id'                  => $request->ods_id,
            'meta_pnd_id'             => $request->id_meta_pnd,

        ]);

        return redirect()->back()->with('success', 'Alineación guardada correctamente con Meta y ODS.');
    }
    // Agrega esto dentro de tu clase AlineacionController
    public function storeObjetivoAjax(Request $request)
    {
        // Bloque de seguridad para atrapar errores fatales
        try {
            // 1. Validar
            $request->validate([
                'id_organizacion' => 'required',
                // Validamos que el código no se repita SOLO para esta organización
                'codigo' => [
                    'required',
                    'string',
                    'max:20',
                    \Illuminate\Validation\Rule::unique('cat_objetivo_estrategico', 'codigo')
                        ->where('id_organizacion', $request->id_organizacion)
                ],
                'nombre' => 'required|string|max:500',
            ], [
                'codigo.required' => 'El código es obligatorio.',
                'codigo.unique'   => 'Este código ya está registrado para su institución.',
                'nombre.required' => 'La descripción del objetivo es necesaria.',
            ]);

            // 2. Crear
            $nuevoObjetivo = ObjetivoEstrategico::create([
                'id_organizacion' => $request->id_organizacion,
                'codigo'          => $request->codigo,
                'nombre'          => $request->nombre,
                'indicador'       => $request->indicador,
                'fecha_inicio'    => $request->fecha_inicio,
                'fecha_fin'       => $request->fecha_fin,
                'estado'          => 1
            ]);

            // 3. Responder Éxito
            return response()->json([
                'success'     => true,
                'id'          => $nuevoObjetivo->id_objetivo_estrategico,
                'codigo'      => $nuevoObjetivo->codigo,
                'descripcion' => substr($nuevoObjetivo->nombre, 0, 60) . '...',
                'data' => $nuevoObjetivo
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si falla la validación (campos vacíos), devolvemos los errores normales
            return response()->json([
                'success' => false,
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // AQUÍ ESTÁ LA MAGIA: Si falla la Base de Datos
            return response()->json([
                'success' => false,
                'message' => 'Error Fatal: ' . $e->getMessage()
            ], 500);
        }
    }
    public function perfil($id)
    {
        $organizacion = OrganizacionEstatal::findOrFail($id);

        // 1. Traemos todo junto
        // Asegúrate de que 'ods' es el nombre correcto de la relación en tu modelo AlineacionEstrategica
        $objetivos = ObjetivoEstrategico::where('id_organizacion', $id)
            ->with(['alineacion.metaPnd', 'alineacion.ods'])
            ->get();

        // 2. Filtramos solo los que tienen alineación
        $alineaciones = $objetivos->map(fn($obj) => $obj->alineacion)->filter();

        // 3. Agrupamos y Preparamos
        // CORRECCIÓN AQUÍ: Usamos 'ods_id' que es lo que mostró tu base de datos
        $datosGrafico = $alineaciones->groupBy('ods_id')->map(function ($group) {

            // Obtenemos el objeto ODS desde la relación
            $ods = $group->first()->ods;

            // Validación de seguridad por si el ODS no se cargó
            if (!$ods) {
                return [
                    'label' => 'Sin ODS',
                    'valor' => $group->count(),
                    'color' => '#cccccc'
                ];
            }

            // Lógica del nombre
            $nombre = $ods->nombre_corto ?? $ods->nombre ?? ('ODS ' . $ods->numero);

            return [
                'label' => 'ODS ' . ($ods->numero ?? '?') . ': ' . Str::limit($nombre, 20),
                'valor' => $group->count(),
                'color' => $ods->color_hex ?? '#cccccc' // Asegúrate que tu tabla ODS tenga 'color_hex'
            ];
        })->values();

        // 4. Retornamos
        return view('dashboard.estrategico.organizaciones.perfils', [
            'organizacion' => $organizacion,
            'objetivos'    => $objetivos,
            'alineaciones' => $alineaciones,
            'labels'       => $datosGrafico->pluck('label'),
            'valores'      => $datosGrafico->pluck('valor'),
            'colores'      => $datosGrafico->pluck('color')
        ]);
    }



    public function update(Request $request, $id)
    {
        // 1. Validar
        $request->validate([
            'objetivo_estrategico_id' => 'required',
            'objetivo_nacional_id'    => 'required',
            'ods_id'                  => 'required',
            'id_meta_pnd'             => 'required',
        ]);

        // 2. Buscar registro
        $alineacion = AlineacionEstrategica::findOrFail($id);

        // 3. Actualizar datos
        $alineacion->update([

            'objetivo_estrategico_id' => $request->objetivo_estrategico_id,
            'objetivo_nacional_id'    => $request->objetivo_nacional_id,
            'ods_id'                  => $request->ods_id,
            'meta_pnd_id'             => $request->id_meta_pnd,
        ]);

        return redirect()->back()->with('status', '¡Alineación actualizada correctamente!');
    }
    // ELIMINA
    public function destroy($id)
    {
        // 1. Buscar la alineación
        $alineacion = AlineacionEstrategica::findOrFail($id);

        // 2. Eliminarla
        $alineacion->delete();

        // 3. Regresar con mensaje
        return redirect()->back()->with('status', '¡Alineación eliminada correctamente!');
    }
}
