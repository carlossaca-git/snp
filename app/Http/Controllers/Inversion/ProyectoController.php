<?php

namespace App\Http\Controllers\Inversion;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

// Modelos de Configuración
use App\Models\Catalogos\Eje;
use App\Models\Catalogos\EjePnd;
use App\Models\Catalogos\ObjetivoNacional;
use App\Models\Institucional\OrganizacionEstatal;
use App\Models\Institucional\UnidadEjecutora;
use App\Models\Seguridad\User;

// Modelos de Inversión
use App\Models\Planificacion\Programa;
use App\Models\Inversion\ProyectoInversion;
use App\Models\Inversion\ProyectoLocalizacion;
use App\Models\Inversion\FuenteFinanciamiento;
use App\Models\Inversion\Financiamiento;
use Illuminate\Validation\Rules\Numeric;
use PhpParser\Builder\Function_;

//Modelos planificacion
use App\Models\Planificacion\ObjetivoEstrategico;

use function Symfony\Component\String\s;

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
            'objetivo',
            'unidadEjecutora',
            'marcoLogico'
        ]);

        //  Filtros de Seguridad
        if (!$user->tieneRol('SUPER_ADMIN')) {
            $query->where('id_organizacion', $user->id_organizacion);

            $unidades = UnidadEjecutora::where('id_organizacion', $user->id_organizacion)
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
    public function show($id)
    {
        //$proyecto = ProyectoInversion::findOrFail($id);
        // dd($proyecto->getAttributes());

        $proyecto = ProyectoInversion::with([
            'documentos',
            'objetivo.metasNacionales.objetivoNacional',
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
        $objetivos = ObjetivoEstrategico::where('id_organizacion', $idOrganizacion)
            ->where('estado', 1)
            ->with(['metasNacionales.ods'])
            ->get();
        // Filtramos solo los programas que pertenecen a esta organización
        $programas = Programa::where('id_organizacion', $idOrganizacion)
            ->orderBy('nombre_programa')
            ->get();

        // Filtramos solo las unidades de esta organización
        $unidades = UnidadEjecutora::where('id_organizacion', $idOrganizacion)
            ->where('estado', 1)
            ->get();

        $fuentes = FuenteFinanciamiento::all();
        $entidades = OrganizacionEstatal::orderBy('nom_organizacion')->get();
        return view('dashboard.inversion.proyectos.crear', compact(
            'objetivos',
            'programas',
            'unidades',
            'fuentes',
            'entidades'
        ));
    }
    /**
     * Almacena el proyecto y su alineación estratégica
     */
    public function store(Request $request)
    {
        // VALIDACIÓN
        $request->validate([
            'cup'                   => 'required|unique:tra_proyecto_inversion,cup',
            'documentos'            => 'nullable|array',
            'documentos.*'          => 'file|mimes:pdf|max:51200',
            'nombre_proyecto'       => 'required',
            'id_unidad_ejecutora'   => 'required|exists:cat_unidades_ejecutoras,id',
            'id_objetivo_estrategico' => 'required|exists:cat_objetivo_estrategico,id_objetivo_estrategico',
            'provincia'             => 'required',
            'monto_total_inversion' => 'required|numeric|min:0',
            'anio'                  => 'array',
            'monto_anio'            => 'array'
        ]);
        $user = $this->getUsuario();
        // VALIDACIÓN DE SUMAS

        //Si es Super Admin y envio una org la usamos Si no forzamos la del usuario
        $idOrganizacion = $user->id_organizacion;
        if ($user->tieneRol('SUPER_ADMIN') && $request->has('id_organizacion')) {
            $idOrganizacion = $request->id_organizacion;
        }
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
                'id_programa'           => $request->id_programa,
                'id_unidad_ejecutora'   => $request->id_unidad_ejecutora,
                'id_organizacion'       => Auth::user()->id_organizacion,
                'id_objetivo_estrategico' => $request->id_objetivo_estrategico,

                // Fechas y Montos
                'fecha_inicio_estimada' => $request->fecha_inicio_estimada,
                'fecha_fin_estimada'    => $request->fecha_fin_estimada,
                'duracion_meses'        => $request->duracion_meses,
                'monto_total_inversion' => $request->monto_total_inversion,

                // Estados
                'estado_dictamen'       => $request->estado_dictamen,
                'id_usuario_creacion'   => Auth::user()->id_usuario,
                'estado'                => 1
            ]);

            $idGenerado = $proyecto->id;

            // -------------------
            // GUARDAR DOCUMENTOS
            // --------------------
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

            // ---------------------------------------------------------
            //  GUARDAR UBICACIÓN
            // ---------------------------------------------------------
            DB::table('tra_proyecto_localizacion')->insert([
                'id_proyecto' => $idGenerado,
                'provincia'   => $request->provincia,
                'canton'      => $request->canton,
                'parroquia'   => $request->parroquia,
                'created_at'  => now()
            ]);

            // ---------------------------------------------------------
            //  GUARDAR FINANCIAMIENTO
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
            //dd($request->all());
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
    // En ProyectoController.php
    public function edit($id)
    {
        $idOrganizacion = Auth::user()->id_organizacion;
        $user = $this->getUsuario();

        // Buscamos el proyecto con sus relaciones (para llenar los inputs)
        $proyecto = ProyectoInversion::with(['localizacion', 'objetivo'])
            ->when(!$user->tieneRol('SUPER_ADMIN'), function ($q) use ($user) {
                return $q->where('id_organizacion', $user->id_organizacion);
            })
            ->findOrFail($id);
        //  Cargamos los catálogos filtrados tambien para mayor seguridad
        if (!$user->tieneRol('SUPER_ADMIN')) {
            $organizaciones = OrganizacionEstatal::where('id_organizacion', $user->id_organizacion)->get();
        } else {
            $organizaciones = OrganizacionEstatal::all();
        }

        $programas = Programa::where('id_organizacion', $idOrganizacion)->get();
        $fuentes = FuenteFinanciamiento::all();
        $unidades = UnidadEjecutora::where('id_organizacion', $idOrganizacion)->get();
        $objetivos = ObjetivoEstrategico::where('id_organizacion', $idOrganizacion)
            ->where('estado', 1)
            ->with(['metasNacionales.ods'])
            ->get();

        return view('dashboard.inversion.proyectos.editar', compact(
            'proyecto',
            'organizaciones',
            'objetivos',
            'programas',
            'fuentes',
            'unidades'

        ));
    }

    /**
     * Metodo update
     */
    //METODO UDAPTE DIVIDO EN FUNCIONES PARA MOJR MANTENIMIENTO

    public function update(Request $request, $id)
    {
        //dd($request->all());
        //  Validación (Idealmente esto iría en un FormRequest, pero lo dejamos aquí por ahora)
        $validado = $this->validarFormulario($request);

        //   Buscar proyecto
        $proyecto = $this->buscarProyectoSeguro($id);

        //   Si algo falla, nada se guarda
        DB::transaction(function () use ($request, $proyecto) {

            $this->actualizarDatosGenerales($proyecto, $request);

            $this->actualizarUbicacion($proyecto, $request);

            $this->procesarDocumentos($proyecto, $request);

            $this->actualizarFinanciamientos($proyecto, $request);
        });

        return redirect()->route('inversion.proyectos.index')
            ->with('success', 'Proyecto actualizado con éxito.');
    }
    private function validarFormulario(Request $request)
    {
        return $request->validate([
            'cup'                   => 'required',
            'nombre_proyecto'       => 'required',
            'id_objetivo_estrategico'  => 'required|numeric',
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
        ]);
    }
    private function buscarProyectoSeguro($id)
    {

        $user = $this->getUsuario();

        return ProyectoInversion::when(!$user->tieneRol('SUPER_ADMIN'), function ($q) use ($user) {
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
            'id_objetivo_estrategico' => $request->id_objetivo_estrategico,
            'estado_dictamen'         => $request->estado_dictamen,
            'objetivo_nacional'       => $request->id_objetivo_nacional,
            'estado'                  => $request->has('estado') ? $request->estado : 0,
        ];


        if ($this->getUsuario()->tieneRol('SUPER_ADMIN') && $request->has('id_organizacion')) {
            $datos['id_organizacion'] = $request->id_organizacion;
        }

        $proyecto->update($datos);
    }
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

    //////////////////////////
    //Funcion eliminar proyecto
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
