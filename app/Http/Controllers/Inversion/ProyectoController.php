<?php

namespace App\Http\Controllers\Inversion;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

// Modelos de Configuración
use App\Models\Catalogos\Eje;
use App\Models\Catalogos\EjePnd;
use App\Models\Catalogos\ObjetivoNacional;
use App\Models\Institucional\OrganizacionEstatal;
use App\Models\Institucional\UnidadEjecutora;

// Modelos de Inversión
use App\Models\Planificacion\Programa;
use App\Models\Inversion\ProyectoInversion;
use App\Models\Inversion\ProyectoLocalizacion;
use App\Models\Inversion\FuenteFinanciamiento;
use App\Models\Inversion\Financiamiento;
use Illuminate\Validation\Rules\Numeric;

use function Symfony\Component\String\s;

class ProyectoController extends Controller
{
    /**
     * Muestra el Banco de Proyectos (Módulo Transaccional)
     */
    public function index(Request $request)
    {
        // Capturamos los datos del formulario
        $buscar = $request->input('buscar');
        $entidad = $request->input('entidad');

        // Consultamos con filtros condicionales
        $proyectos = ProyectoInversion::with(['organizacion', 'programa', 'objetivo', 'unidadEjecutora'])
            ->when($buscar, function ($query, $buscar) {
                return $query->where(function ($q) use ($buscar) {
                    $q->where('nombre_proyecto', 'LIKE', "%{$buscar}%")
                        ->orWhere('cup', 'LIKE', "%{$buscar}%");
                });
            })
            ->when($entidad, function ($query, $entidad) {
                return $query->where('id_unidad_ejecutora', $entidad);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Necesitamos las unidades para el select del filtro
        $unidades = UnidadEjecutora::orderBy('nombre_unidad')->get();

        return view('dashboard.inversion.proyectos.index', compact('proyectos', 'unidades'));
    }
    public function show($id)
    {
        //$proyecto = ProyectoInversion::findOrFail($id);
        // dd($proyecto->getAttributes());

        $proyecto = ProyectoInversion::with([
            'documentos',
            'objetivo.eje',
            'organizacion',
            'localizacion',
            'financiamientos.fuente',
            'unidadEjecutora'
        ])->findOrFail($id);
        return view('dashboard.inversion.proyectos.show', compact('proyecto'));
    }

    /**
     * Prepara los catálogos para la formulación
     */
    public function create()
    {
        $entidades = OrganizacionEstatal::all();
        $programas = Programa::all();
        $ejes = EjePnd::all();
        $fuentes = FuenteFinanciamiento::all();
        $unidades = UnidadEjecutora::All();

        return view('dashboard.inversion.proyectos.crear', compact('entidades', 'programas', 'ejes', 'fuentes', 'unidades'));
    }

    /**
     * Almacena el proyecto y su alineación estratégica
     */
    public function store(Request $request)
    {
        //dd($request->all());
        // 1. VALIDACIÓN
        $request->validate([
            'cup'                   => 'required|unique:tra_proyecto_inversion,cup',
            'documentos'            => 'nullable|array',
            'documentos.*'          => 'file|mimes:pdf|max:51200',
            'nombre_proyecto'       => 'required',
            'id_organizacion'       => 'required',
            'id_unidad_ejecutora'   => 'required|exists:cat_unidades_ejecutoras,id',
            'id_objetivo_nacional'  => 'required|numeric',
            'provincia'             => 'required',
            'monto_total_inversion' => 'required|numeric|min:0',
            'anio'                  => 'array',
            'monto_anio'            => 'array'
        ]);

        // 2. VALIDACIÓN DE SUMAS (Esto estaba bien)
        $totalProgramado = 0;
        if ($request->has('monto_anio')) {
            $totalProgramado = array_sum(array_map('floatval', $request->monto_anio));
        }

        if (abs($totalProgramado - $request->monto_total_inversion) > 0.01) {
            return back()
                ->withInput()
                ->with('error', 'Error: La suma del financiamiento no coincide con el Monto Total.');
        }

        try {
            DB::beginTransaction();

            // ---------------------------------------------------------
            // PASO 1: CREAR EL PROYECTO (PADRE)
            // ---------------------------------------------------------
            // Aquí NO guardamos nada de archivos todavía, porque
            // los archivos van en otra tabla y necesitan el ID de este proyecto.

            $proyecto = ProyectoInversion::create([
                'id_programa'             => $request->id_programa,
                'cup'                     => $request->cup,
                'nombre_proyecto'         => $request->nombre_proyecto,
                'id_unidad_ejecutora'     => $request->id_unidad_ejecutora,
                'descripcion_diagnostico' => $request->descripcion_diagnostico,
                'tipo_inversion'          => $request->tipo_inversion,
                'fecha_inicio_estimada'   => $request->fecha_inicio_estimada,
                'fecha_fin_estimada'      => $request->fecha_fin_estimada,
                'duracion_meses'          => $request->duracion_meses,
                'monto_total_inversion'   => $request->monto_total_inversion,
                'estado_dictamen'         => $request->estado_dictamen,
                'id_organizacion'         => $request->id_organizacion,
                'objetivo_nacional'       => $request->id_objetivo_nacional,
                'estado'                  => 1
            ]);

            $idGenerado = $proyecto->id;

            // ---------------------------------------------------------
            // PASO 2: GUARDAR DOCUMENTOS (HIJOS)
            // ---------------------------------------------------------
            if ($request->hasFile('documentos')) {
                foreach ($request->file('documentos') as $archivo) {
                    // A. Subir archivo al storage
                    $ruta = $archivo->store('proyectos/' . $idGenerado, 'public');
                    //dd($ruta);
                    // B. Insertar en la tabla 'documentos_proyectos'
                    // Usamos el modelo DocumentoProyecto o DB::table directo
                    DB::table('documentos_proyectos')->insert([
                        'id_proyecto'    => $idGenerado,
                        'nombre_archivo' => $archivo->getClientOriginalName(),
                        'url_archivo'    => $ruta,
                        'tipo_documento' => 'RESPALDO',
                        'extension'      => $archivo->getClientOriginalExtension(),
                        'created_at'     => now(),
                        'updated_at'     => now()
                    ]);
                }
            }

            // ---------------------------------------------------------
            // PASO 3: GUARDAR UBICACIÓN
            // ---------------------------------------------------------
            DB::table('tra_proyecto_localizacion')->insert([
                'id_proyecto' => $idGenerado,
                'provincia'   => $request->provincia,
                'canton'      => $request->canton,
                'parroquia'   => $request->parroquia,
                'created_at'  => now()
            ]);

            // ---------------------------------------------------------
            // PASO 4: GUARDAR FINANCIAMIENTO
            // ---------------------------------------------------------
            if ($request->has('anio')) {
                $datosFinanciamiento = [];
                foreach ($request->anio as $key => $anio) {
                    $monto = $request->monto_anio[$key] ?? 0;
                    if ($monto > 0) {
                        $datosFinanciamiento[] = [
                            'id_proyecto' => $idGenerado,
                            'anio'        => $anio,
                            'id_fuente'   => $request->id_fuente[$key],
                            'monto'       => $monto,
                            'created_at'  => now(),
                            'updated_at'  => now()
                        ];
                    }
                }
                if (!empty($datosFinanciamiento)) {
                    DB::table('tra_financiamiento')->insert($datosFinanciamiento);
                }
            }

            DB::commit();

            return redirect()->route('inversion.proyectos.index')
                ->with('success', 'Proyecto creado exitosamente.');
        } catch (\Exception $e) {

            DB::rollBack();
            //dd([
            //    'MENSAJE_ERROR' => $e->getMessage(),
            //    'ARCHIVO' => $e->getFile(),
            //    'LINEA' => $e->getLine()
            //]);
            // dd($e->getMessage()); // Descomenta solo para ver errores en pantalla azul
            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error al guardar: ' . $e->getMessage());
        }
    }

    /**
     * Editar proyecto
     */
    // En ProyectoController.php
    public function edit($id)
    {
        // 1. Buscamos el proyecto con sus relaciones (para llenar los inputs)
        // Usamos 'objetivo' en singular
        $proyecto = ProyectoInversion::with(['localizacion', 'objetivo'])->findOrFail($id);

        // 2. Cargamos los catálogos

        $organizaciones = OrganizacionEstatal::all();
        $objetivos = ObjetivoNacional::all();

        $programas = Programa::all();
        $fuentes = FuenteFinanciamiento::all();
        $ejes = EjePnd::all();
        $objetivos = [];

        //Buscar ejes a partir de objetivos
        if ($proyecto->objetivo) {
            $id_eje_actual = $proyecto->objetivo->id_eje;
            $objetivos = ObjetivoNacional::where('id_eje', $id_eje_actual)->get();
        } else {
            $objetivos = collect();
        }
        //dd($proyecto->localizacion);
        return view('dashboard.inversion.proyectos.editar', compact(
            'proyecto',
            'organizaciones',
            'objetivos',
            'programas',
            'fuentes',
            'ejes'
        ));
    }

    /**
     * Metodo update
     */
    public function update(Request $request, $id)
{
    // 1. VALIDACIÓN (Ajustada a los nombres de tu DD)
    $request->validate([
        'cup'                   => 'required',
        'nombre_proyecto'       => 'required',
        'id_organizacion'       => 'required',
        'id_objetivo_nacional'  => 'required|numeric', // Cambiado para coincidir con el DD
        'monto_total_inversion' => 'required',
        'provincia'             => 'required',
        'canton'                => 'required',
        'parroquia'             => 'required',
        'estado'                => 'nullable|in:0,1',
        'financiamientos'       => 'array',
        'financiamientos.*.anio' => 'required|integer|min:2020',
        'financiamientos.*.monto' => 'required|numeric|min:0',
        'financiamientos.*.id_fuente' => 'required',
        'nuevos_documentos.*'   => 'file|mimes:pdf,docx,jpg,png|max:4096', // Cambiado según tu DD
    ]);

    DB::transaction(function () use ($request, $id) {

        $proyecto = ProyectoInversion::findOrFail($id);

        // 2. ACTUALIZAR PROYECTO
        $proyecto->update([
            'id_programa'             => $request->id_programa,
            'cup'                     => $request->cup,
            'nombre_proyecto'         => $request->nombre_proyecto,
            'descripcion_diagnostico' => $request->descripcion_diagnostico,
            'tipo_inversion'          => $request->tipo_inversion,
            'fecha_inicio_estimada'   => $request->fecha_inicio_estimada,
            'fecha_fin_estimada'      => $request->fecha_fin_estimada,
            'duracion_meses'          => $request->duracion_meses,
            'monto_total_inversion'   => $request->monto_total_inversion,
            'estado_dictamen'         => $request->estado_dictamen,
            'id_organizacion'         => $request->id_organizacion,
            'objetivo_nacional'       => $request->id_objetivo_nacional, // Aquí usas el valor del DD
            'estado'                  => $request->has('estado') ? $request->estado : 0,
        ]);

        // 3. ACTUALIZAR UBICACIÓN
        DB::table('tra_proyecto_localizacion')->updateOrInsert(
            ['id_proyecto' => $proyecto->id],
            [
                'provincia'  => $request->provincia,
                'canton'     => $request->canton,
                'parroquia'  => $request->parroquia,
                'updated_at' => now()
            ]
        );

        // 4. PROCESAR DOCUMENTOS (Ajustado a "nuevos_documentos")
        if ($request->hasFile('nuevos_documentos')) {
            foreach ($request->file('nuevos_documentos') as $archivo) {
                $ruta = $archivo->store('proyectos/documentos', 'public');

                DB::table('documentos_proyectos')->insert([
                    'id_proyecto'    => $proyecto->id,
                    'nombre_archivo' => $archivo->getClientOriginalName(),
                    'url_archivo'    => $ruta,
                    'tipo_documento' => 'Actualización',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }

        // 5. ACTUALIZAR FINANCIAMIENTOS
        if ($request->has('financiamientos')) {
            foreach ($request->financiamientos as $data) {
                if (isset($data['_delete']) && $data['_delete'] == 1) {
                    if (isset($data['id'])) {
                        // Usamos DB si no estás seguro del nombre del modelo
                        DB::table('tra_proyecto_financiamiento')->where('id', $data['id'])->delete();
                    }
                    continue;
                }

                $proyecto->financiamientos()->updateOrCreate(
                    ['id' => $data['id'] ?? null],
                    [
                        'anio'      => $data['anio'],
                        'id_fuente' => $data['id_fuente'],
                        'monto'     => $data['monto']
                    ]
                );
            }
        }
    });

    return redirect()->route('inversion.proyectos.index')
        ->with('success', 'Proyecto actualizado con éxito.');
}
    /**
     * API para la carga dinámica de objetivos
     */
    // En ProyectoController.php
    public function getObjetivos($ejeId)
    {
        try {
            // Eliminamos el filtro de estado para traer todos los objetivos
            $objetivos = ObjetivoNacional::where('id_eje', $ejeId)
                ->get(['id_objetivo_nacional', 'descripcion_objetivo', 'estado']); // Traemos el estado para avisar al usuario

            return response()->json($objetivos);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    ///////////////////////////////////////
    // Generar reportes
    // IMPORTANTE:  'use' al inicio del archivo, antes de la clase


    public function generarReporte($id)
    {
        $proyecto = ProyectoInversion::with([
            'organizacion',
            'programa',
            'localizacion',
            'documentos',
            'eje',
            'objetivo',
            'unidadEjecutora',
            'financiamientos.fuente'
        ])->findOrFail($id);

        //Preparamos el logo de la institucion
        $logoPath = public_path('img/logo-institucion.png');
        $logoBase64 = null;

        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($logoData);
        }
        // Generamos la URL del QR (usamos una API confiable)
        $qrText = urlencode('CUP: ' . $proyecto->cup . ' | Total: $' . number_format($proyecto->monto_total_inversion, 2));
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={$qrText}";

        // DESCARGAMOS LA IMAGEN MANUALMENTE PARA EVITAR ERRORES DE SSL
        try {
            $context = stream_context_create([
                "ssl" => ["verify_peer" => false, "verify_peer_name" => false]
            ]);
            $imageData = file_get_contents($qrUrl, false, $context);
            $qrCodeBase64 = base64_encode($imageData);
        } catch (\Exception $e) {
            $qrCodeBase64 = null;
        }

        $pdf = Pdf::loadView('dashboard.inversion.proyectos.pdf', compact('proyecto', 'qrCodeBase64', 'logoBase64'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true
        ]);

        return $pdf->stream('Reporte_Proyecto_' . $proyecto->cup . '.pdf');
    }
    ////////////////////////////////
    //Proyecto eliminar documento

    public function eliminarDocumento($id)
    {
        $documento = DB::table('documentos_proyectos')->where('id', $id)->first();

        if ($documento) {
            // 1. Borrar archivo físico
            if (Storage::disk('public')->exists($documento->url_archivo)) {
                Storage::disk('public')->delete($documento->url_archivo);
            }

            // 2. Borrar registro de la BD
            DB::table('documentos_proyectos')->where('id', $id)->delete();

            // 3. CAMBIO CLAVE: Responder con JSON si es una petición AJAX
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Documento eliminado correctamente.'
                ]);
            }
            return back()->with('success', 'Documento eliminado correctamente.');
        }

        return response()->json(['success' => false, 'message' => 'No encontrado'], 404);
    }

    //////////////////////////
    //Funcion eliminar proyecto
    public function destroy($id)
    {
        // 1. Buscamos el proyecto
        $proyecto = ProyectoInversion::findOrFail($id);

        try {
            // 2. Opcional: Borrar hijos manualmente si tu BD no tiene "ON DELETE CASCADE"
            // Si configuraste bien tu BD, esto no es necesario, pero ponerlo no hace daño.
            $proyecto->financiamientos()->delete();
            $proyecto->localizacion()->delete();

            // 3. Borramos el proyecto principal
            $proyecto->delete();

            return redirect()->route('inversion.proyectos.index')
                ->with('success', 'Proyecto eliminado correctamente.');
        } catch (\Exception $e) {
            // Si hay un error (ej: el proyecto ya tiene facturas o trámites ligados)
            return redirect()->back()
                ->with('error', 'No se puede eliminar el proyecto: ' . $e->getMessage());
        }
    }
}
