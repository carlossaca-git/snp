<?php

namespace App\Http\Controllers\Inversion;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\faceades\Exception;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

// Modelos de Configuración
use App\Models\Catalogos\IndicadorNacional;
use App\Models\Catalogos\ObjetivoNacional;
use App\Models\Institucional\OrganizacionEstatal;
use App\Models\Institucional\UnidadEjecutora;
use App\Models\Seguridad\User;

// Modelos de Inversión
use App\Models\Inversion\Programa;
use App\Models\Inversion\ProyectoInversion;
use App\Models\Inversion\ProyectoLocalizacion;
use App\Models\Inversion\FuenteFinanciamiento;
use App\Models\Inversion\Financiamiento;
use Illuminate\Validation\Rules\Numeric;
use PhpParser\Builder\Function_;

//Modelos planificacion
use App\Models\Planificacion\ObjetivoEstrategico;


class ProyectoController extends Controller
{
    /**
     * Muestra el Banco de Proyectos (Módulo Transaccional)
     */
    public function index(Request $request)
    {
        //  Obtener usuario
        $user = $this->getUsuario();

        //  Query Base con Relaciones (Eager Loading)
        $query = ProyectoInversion::with([
            'organizacion',
            'programa',
            'objetivoEstrategico',
            'unidadEjecutora',
            'marcoLogico'
        ]);

        //  Filtros de Seguridad
        if (!method_exists($user, 'hasRole') || !$user->hasRole('SUPER_ADMIN')) {
            $query->where('organizacion_id', $user->id_organizacion);

            $unidades = UnidadEjecutora::where('organizacion_id', $user->id_organizacion)
                ->orderBy('nombre_unidad')
                ->get();
        } else {
            $unidades = UnidadEjecutora::orderBy('nombre_unidad')->get();
        }

        //  Filtros de Búsqueda
        $buscar = $request->input('buscar');
        $entidad = $request->input('entidad');

        $query->when($buscar, function ($q, $buscar) {
            return $q->where(function ($sub) use ($buscar) {
                $sub->where('nombre_proyecto', 'LIKE', "%{$buscar}%")
                    ->orWhere('cup', 'LIKE', "%{$buscar}%");
            });
        });

        $query->when($entidad, function ($q, $entidad) {
            return $q->where('id_unidad_ejecutora', $entidad);
        });

        //  Paginación
        $proyectos = $query->orderBy('created_at', 'desc')->paginate(15);

        $proyectos->getCollection()->transform(function ($proyecto) {

            $proyecto->calculo_avance = $proyecto->avance_real;
            return $proyecto;
        });

        return view('dashboard.inversion.proyectos.index', compact('proyectos', 'unidades'));
    }
    // Show proyectos
    public function show($id)
    {
        $proyecto = ProyectoInversion::with([
            'documentos',
            'objetivoEstrategico.metasNacionales.objetivoNacional.eje',
            'organizacion',
            'localizacion',
            'financiamientos.fuente',
            'unidadEjecutora',
            'marcoLogico'
        ])->findOrFail($id);
        return view('dashboard.inversion.proyectos.show', compact('proyecto'));
    }

    /**
     * Prepara los catálogos para la formulación
     */
    public function create()
    {
        $idOrganizacion = Auth::user()->id_organizacion;

        // Objetivos
        $objetivosEstr = ObjetivoEstrategico::where('organizacion_id', $idOrganizacion)
            ->where('estado', 1)
            ->with(['metasNacionales.ods'])
            ->get();

        $programas = Programa::whereHas('plan', function ($query) use ($idOrganizacion) {
            $query->where('organizacion_id', $idOrganizacion);
            // $query->where('anio', date('Y'));
        })
            ->with('plan')
            ->where('estado', 'APROBADO')
            ->orderBy('nombre_programa')
            ->get();

        // Unidades Ejecutoras
        $unidades = UnidadEjecutora::where('organizacion_id', $idOrganizacion)
            ->where('estado', 1)
            ->get();

        $fuentes   = FuenteFinanciamiento::all();
        $entidades = OrganizacionEstatal::orderBy('nom_organizacion')->get();
        $indicadoresDisponibles = IndicadorNacional::all();

        return view('dashboard.inversion.proyectos.create', compact(
            'objetivosEstr',
            'programas',
            'unidades',
            'fuentes',
            'entidades',
            'indicadoresDisponibles'
        ));
    }
    /**
     * Almacena el proyecto y su alineación estratégica
     */
    public function store(Request $request)
    {
        //dd($request->all());
        // VALIDACIÓN
        $request->validate([
            'cup'                   => 'required|unique:tra_proyecto_inversion,cup',
            'documentos'            => 'nullable|array',
            'documentos.*'          => 'file|mimes:pdf|max:51200',
            'nombre_proyecto'       => 'required',
            'id_unidad_ejecutora'   => 'required|exists:cat_unidades_ejecutoras,id',
            'objetivo_estrategico_id' => 'required|exists:cat_objetivo_estrategico,id_objetivo_estrategico',
            'provincia'             => 'required',
            'monto_total_inversion' => 'required|numeric|min:0',
            'anio'                  => 'array',
            'monto_anio'            => 'array',
            //indicadores
            'indicadores'           => 'nullable|array',
            'indicadores.*'         => 'exists:cat_indicadores_nacionales,id_indicador',
            //Contribuciones
            'contribuciones'        => 'nullable|array',
            'contribuciones.*'      => 'nullable|numeric|min:0|max:100',
        ]);

        // VALIDACIÓN DE SUMAS

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

            $proyecto = ProyectoInversion::create([
                'cup'                   => $request->cup,
                'nombre_proyecto'       => $request->nombre_proyecto,
                'descripcion_diagnostico' => $request->descripcion_diagnostico,
                'tipo_inversion'        => $request->tipo_inversion,

                // Relaciones
                'programa_id'           => $request->id_programa,
                'unidad_ejecutora_id'   => $request->id_unidad_ejecutora,
                'organizacion_id'       => Auth::user()->id_organizacion,
                'objetivo_estrategico_id' => $request->objetivo_estrategico_id,

                // Fechas y Montos
                'fecha_inicio_estimada' => $request->fecha_inicio_estimada,
                'fecha_fin_estimada'    => $request->fecha_fin_estimada,
                'duracion_meses'        => $request->duracion_meses,
                'monto_total_inversion' => $request->monto_total_inversion,

                // Estados
                'estado_dictamen'       => 'Pendiente',
                'usuario_creacion_id'   => Auth::user()->id_usuario,
                'estado'                => 1
            ]);

            $idGenerado = $proyecto->id;

            // GUARDAR DOCUMENTOS

            if ($request->hasFile('documentos')) {
                foreach ($request->file('documentos') as $archivo) {
                    // A. Subir archivo al storage
                    $ruta = $archivo->store('proyectos/' . $idGenerado, 'public');

                    //Insertar en la tabla 'documentos_proyectos'
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
            //  GUARDAR UBICACIÓN
            DB::table('tra_proyecto_localizacion')->insert([
                'id_proyecto' => $idGenerado,
                'provincia'   => $request->provincia,
                'canton'      => $request->canton,
                'parroquia'   => $request->parroquia,
                'created_at'  => now()
            ]);

            //  GUARDAR FINANCIAMIENTO
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

            //Guaradar indicadores
            if ($request->has('indicadores')) {
                $datosSync = [];

                // Obtenemos el array de contribuciones
                $contribuciones = $request->input('contribuciones', []);

                foreach ($request->indicadores as $idIndicador) {
                    // Usamos intval($idIndicador) para buscar la clave correcta en el array
                    $peso = isset($contribuciones[$idIndicador]) && is_numeric($contribuciones[$idIndicador])
                        ? $contribuciones[$idIndicador]
                        : 0;

                    // 2. Construimos el array para sync
                    $datosSync[$idIndicador] = [
                        'contribucion_proyecto' => $peso
                    ];
                }

                //  Guardamos todo de una sola vez en la tabla proyecto_indicador
                $proyecto->indicadoresNacionales()->sync($datosSync);
            }
            DB::commit();

            return redirect()->route('inversion.proyectos.marco-logico.index', $proyecto->id)
                ->with('success', 'Proyecto creado exitosamente.');
        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error al guardar: ' . $e->getMessage());
        }
    }

    /**
     * Editar proyecto
     */
    public function edit($id)
    {
        $user = $this->getUsuario();
        //  Datos del usuario actual
        $idOrganizacion = Auth::user()->id_organizacion;

        $proyecto = ProyectoInversion::with(['programa.plan', 'objetivoEstrategico', 'unidadEjecutora'])
            ->when(!$user->hasRole('SUPER_ADMIN'), function ($q) use ($idOrganizacion) {
                return $q->whereHas('programa.plan', function ($qPlan) use ($idOrganizacion) {
                    $qPlan->where('organizacion_id', $idOrganizacion);
                });
            })
            ->findOrFail($id);
        $programas = Programa::whereHas('plan', function ($q) use ($idOrganizacion) {
            $q->where('organizacion_id', $idOrganizacion);
        })
            ->with('plan')
            ->orderBy('nombre_programa')
            ->get();

        //  Objetivos Estratégicos
        $objetivosEstr = ObjetivoEstrategico::where('organizacion_id', $idOrganizacion)
            ->where('estado', 1)
            ->with(['metasNacionales.ods'])
            ->get();

        // Unidades Ejecutoras
        $unidades = UnidadEjecutora::where('organizacion_id', $idOrganizacion)
            ->where('estado', 1)
            ->get();

        // Fuentes y Organizaciones
        $fuentes = FuenteFinanciamiento::all();

        // Rol administradir
        if ($user->hasRole('SUPER_ADMIN')) {
            $organizaciones = OrganizacionEstatal::all();
        } else {
            // Si es usuario normal solo ve su propia organización en el select
            $organizaciones = OrganizacionEstatal::where('id_organizacion', $idOrganizacion)->get();
        }

        return view('dashboard.inversion.proyectos.edit', compact(
            'proyecto',
            'organizaciones',
            'objetivosEstr',
            'programas',
            'fuentes',
            'unidades'
        ));
    }

    /**
     * Metodo update
     */
    //METODO UDAPTE DIVIDO EN FUNCIONES PARA MOJoR MANTENIMIENTO

    public function update(Request $request, $id)
    {

        //  Validación (Idealmente esto iría en un FormRequest, pero lo dejamos aquí por ahora)
        $validado = $this->validarFormulario($request);

        //   Buscar proyecto
        $proyecto = $this->buscarProyectoSeguro($id);

        //   Si algo falla, nada se guarda
        DB::transaction(function () use ($request, $proyecto) {

            $this->actualizarDatosGenerales($proyecto, $request);

            $this->actualizarIndicadores($proyecto, $request);

            $this->actualizarUbicacion($proyecto, $request);

            $this->procesarDocumentos($proyecto, $request);

            $this->actualizarFinanciamientos($proyecto, $request);
        });

        return redirect()->route('inversion.proyectos.index')
            ->with('success', 'Proyecto actualizado con éxito.');
    }
    private function validarFormulario(Request $request)
    {
        //dd($request->all());
        return $request->validate([
            'cup'                   => 'required',
            'nombre_proyecto'       => 'required',
            'objetivo_estrategico_id'  => 'exists:cat_objetivo_estrategico,id_objetivo_estrategico',
            'unidad_ejecutora_id'   => 'exists:cat_unidades_ejecutoras,id',
            'monto_total_inversion' => 'required',
            'provincia'             => 'required',
            'canton'                => 'required',
            'parroquia'             => 'required',
            'estado'                => 'nullable|in:0,1',
            'financiamientos'       => 'array',
            'financiamientos.*.anio'      => 'required|integer|min:2020',
            'financiamientos.*.monto'     => 'required|numeric|min:0',
            'financiamientos.*.id_fuente' => 'required',
            'nuevos_documentos.*'         => 'file|mimes:pdf,docx,jpg,png|max:4096',
            'indicadores'                 => 'required|array|min:1',
            'indicadores.*'               => 'exists:cat_indicadores_nacionales,id_indicador',
            'contribuciones'              => 'nullable|array',
            'contribuciones.*'            => 'nullable|numeric|min:0|max:100',
        ]);
    }
    private function buscarProyectoSeguro($id)
    {

        $user = $this->getUsuario();

        return ProyectoInversion::when(!$user->hasRole('SUPER_ADMIN'), function ($q) use ($user) {
            return $q->where('id_organizacion', $user->id_organizacion);
        })
            ->findOrFail($id);
    }
    private function actualizarDatosGenerales($proyecto, Request $request)
    {
        // Preparamos los datos básicos
        $datos = [
            'id_programa'             => $request->id_programa,
            'cup'                     => $request->cup,
            'nombre_proyecto'         => $request->nombre_proyecto,
            'descripcion_diagnostico' => $request->descripcion_diagnostico,
            'tipo_inversion'          => $request->tipo_inversion,
            'fecha_inicio_estimada'   => $request->fecha_inicio_estimada,
            'fecha_fin_estimada'      => $request->fecha_fin_estimada,
            'duracion_meses'          => $request->duracion_meses,
            'monto_total_inversion'   => $request->monto_total_inversion,
            'objetivo_estrategico_id' => $request->objetivo_estrategico_id,
            'unidad_ejecutora_id'     => $request->unidad_ejecutora_id,
            'objetivo_nacional'       => $request->id_objetivo_nacional,
            'estado'                  => $request->has('estado') ? $request->estado : 0,
        ];


        if ($this->getUsuario()->hasRole('SUPER_ADMIN') && $request->has('id_organizacion')) {
            $datos['id_organizacion'] = $request->id_organizacion;
        }
        //Actualizar dictame
        if ($proyecto->estado_dictamen == 'Rechazado' || $proyecto->estadodictamen == 'Corregir') {
            $proyecto->estado_dictamen = 'Pendiente';
        }
        // Actualizamos
        $proyecto->update($datos);
    }
    /**
     * Atualizar indicadores
     */
    private function actualizarIndicadores($proyecto, Request $request)
    {
        //
        if ($request->has('indicadores')) {
            $datosSync = [];
            $contribuciones = $request->input('contribuciones', []);

            foreach ($request->indicadores as $idIndicador) {
                // Convertimos a int para evitar errores de índice string/number
                $id = intval($idIndicador);

                $peso = isset($contribuciones[$id]) && is_numeric($contribuciones[$id])
                    ? $contribuciones[$id]
                    : 0;

                $datosSync[$id] = ['contribucion_proyecto' => $peso];
            }
            // sync borra lo viejo y pone lo nuevo
            $proyecto->indicadoresNacionales()->sync($datosSync);
        }
    }
    /**
     * Actualizar ubicacion
     */
    private function actualizarUbicacion($proyecto, Request $request)
    {
        // Usamos updateOrInsert para ser más eficientes
        DB::table('tra_proyecto_localizacion')->updateOrInsert(
            ['id_proyecto' => $proyecto->id],
            [
                'provincia'  => $request->provincia,
                'canton'     => $request->canton,
                'parroquia'  => $request->parroquia,
                'updated_at' => now()
            ]
        );
    }
    private function procesarDocumentos($proyecto, Request $request)
    {
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
    }
    private function actualizarFinanciamientos($proyecto, Request $request)
    {
        if (!$request->has('financiamientos')) return;

        foreach ($request->financiamientos as $data) {
            //  Eliminar registro
            if (isset($data['_delete']) && $data['_delete'] == 1) {
                if (isset($data['id'])) {
                    // Aseguramos que solo borre financiamientos de ESTE proyecto
                    $proyecto->financiamientos()->where('id', $data['id'])->delete();
                }
                continue;
            }

            // Crear o Actualizar
            $proyecto->financiamientos()->updateOrCreate(
                ['id' => $data['id'] ?? null], // Si tiene ID busca, si no, crea
                [
                    'anio'      => $data['anio'],
                    'id_fuente' => $data['id_fuente'],
                    'monto'     => $data['monto']
                ]
            );
        }
    }
    /**
     * API para la carga dinámica de objetivos
     */
    public function getObjetivos($ejeId)
    {
        try {
            // Eliminamos el filtro de estado para traer todos los objetivos
            $objetivos = ObjetivoNacional::where('id_eje', $ejeId)
                ->get(['id_objetivo_nacional', 'descripcion_objetivo', 'estado']);

            return response()->json($objetivos);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    //Generar reporte PDF del proyecto
    public function generarReporte($id)
    {
        // CARGA DE DATOS DEL PROYECTO
        // Quitamos 'eje' y 'objetivo' directos y cargamos la cadena completa
        $proyecto = ProyectoInversion::with([
            'organizacion',
            'localizacion',
            'documentos',
            'unidadEjecutora',
            'financiamientos.fuente',
            'programa.objetivoE.alineacion.metaNacional.objetivoNacional.eje'
        ])->findOrFail($id);

        // LOGO EN BASE64
        $logoPath = public_path('img/logo-institucion.png');
        $logoBase64 = null;

        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($logoData);
        }

        //  CÓDIGO QR EN BASE64
        $qrText = urlencode('CUP: ' . $proyecto->cup . ' | Total: $' . number_format($proyecto->monto_total_inversion, 2));
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={$qrText}";

        try {
            // Contexto SSL para evitar errores en servidores locales o sin certificados
            $context = stream_context_create([
                "ssl" => ["verify_peer" => false, "verify_peer_name" => false]
            ]);
            $imageData = file_get_contents($qrUrl, false, $context);
            $qrCodeBase64 = base64_encode($imageData);
        } catch (\Exception $e) {
            $qrCodeBase64 = null;
        }

        //  GENERAR PDF
        $pdf = Pdf::loadView('dashboard.inversion.proyectos.proyecto_pdf', compact('proyecto', 'qrCodeBase64', 'logoBase64'));

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
            // Borrar archivo físico
            if (Storage::disk('public')->exists($documento->url_archivo)) {
                Storage::disk('public')->delete($documento->url_archivo);
            }

            // Borrar registro de la BD
            DB::table('documentos_proyectos')->where('id', $id)->delete();

            // CAMBIO CLAVE: Responder con JSON si es una petición AJAX
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

    /**
     * Actualizar dictamen
     */
    public function updateDictamen(Request $request, $id)
    {
        // Lógica de validación condicional
        // Si el botón presionado NO es Aprobar entonces la observación es requerida
        $reglas = [
            'nuevo_dictamen' => 'required|in:Aprobar,Rechazar,Corregir,Pendiente',
            'observaciones' => 'nullable|string|max:1000',
        ];

        // Validación: Obligar a comentar si rechazan
        if ($request->nuevo_dictamen === 'Rechazar' || $request->nuevo_dictamen === 'Corregir') {
            $reglas['observaciones'] = 'required|string|min:10|max:1000';
        }

        $request->validate($reglas, [
            'observaciones.required' => 'Es obligatorio indicar el motivo para rechazar o pedir correcciones.'
        ]);

        // Procesar
        $proyecto = ProyectoInversion::findOrFail($id);

        // Mapeo de estados
        $estadoMap = [
            'Aprobar' => 'Aprobado',
            'Rechazar' => 'Rechazado',
            'Corregir' => 'Corregir',
            'Pendiente' => 'Pendiente'
        ];
        $nuevoEstado = $estadoMap[$request->nuevo_dictamen] ?? $request->nuevo_dictamen;

        // Guardar cambios
        $proyecto->estado_dictamen = $nuevoEstado;

        // Si aprueban podríamos limpiar las observaciones viejas para que quede limpio
        if ($nuevoEstado == 'Aprobado') {
            $proyecto->observaciones = $request->observaciones ?? "Aprobado, puede proceder a la ejecución.";
            // O puedes guardar "Aprobado sin observaciones"
        } else {
            $proyecto->observaciones = $request->observaciones;
        }

        $proyecto->save();

        return redirect()->route('inversion.proyectos.show', $id)
            ->with('success', "Proyecto actualizado a estado:  $nuevoEstado .");
    }

    // Obtener el arbol completo de alineacion

    public function getArbolAlineacion($idObjetivo)
    {
        try {
            if (!$idObjetivo) {
                throw new \Exception("ID de objetivo no recibido");
            }

            // Intentar la consulta
            $objetivo = ObjetivoEstrategico::with([
                'metasNacionales.indicadoresNacionales'
            ])->findOrFail($idObjetivo);

            // Devolver éxito
            return response()->json($objetivo->metasNacionales);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine()
            ], 500);
        }
    }
     /**
     *  Funcion eliminar proyecto
     */

    public function destroy($id)
    {
        // Buscamos el proyecto
        $proyecto = ProyectoInversion::findOrFail($id);

        try {

            // Si configuraste bien tu BD, esto no es necesario, pero ponerlo no hace daño.
            $proyecto->financiamientos()->delete();
            $proyecto->localizacion()->delete();

            //  Borramos el proyecto principal
            $proyecto->delete();

            return redirect()->route('inversion.proyectos.index')
                ->with('success', 'Proyecto eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el proyecto: ' . $e->getMessage());
        }
    }
    private function getUsuario(): User
    {
        /** @var User */
        return Auth::user();
    }

}
