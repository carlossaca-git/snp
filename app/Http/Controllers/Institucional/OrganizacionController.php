<?php

namespace App\Http\Controllers\Institucional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use Illuminate\Validation\Rule;
use App\Models\Institucional\OrganizacionEstatal;
use App\Models\Catalogos\Macrosector;
use App\Models\Catalogos\Sector;
use App\Models\Catalogos\Subsector;
use App\Models\Institucional\TipoOrganizacion;
use Exception;

class OrganizacionController extends Controller
{
    /**
     * Listado de Organizaciones
     */
    public function index(Request $request)
    {
        $busqueda = trim($request->input('busqueda'));

        $organizaciones = OrganizacionEstatal::query()
            ->when($busqueda, function ($query) use ($busqueda) {
                return $query->where(function ($q) use ($busqueda) {
                    $q->where('nom_organizacion', 'LIKE', "%{$busqueda}%")
                        ->orWhere('ruc', 'LIKE', "%{$busqueda}%")
                        ->orWhere('siglas', 'LIKE', "%{$busqueda}%");
                });
            })
            ->orderBy('id_organizacion', 'DESC')
            ->paginate(10);

        $organizaciones->appends(['busqueda' => $busqueda]);

        return view('dashboard.estrategico.organizaciones.index', compact('organizaciones'));
    }
    /**
     * Formulario de Creación
     * Ahora enviamos Macrosectores en lugar de Subsectores
     */
    public function create()
    {
        $organizaciones = OrganizacionEstatal::orderBy('nom_organizacion', 'asc')->get();
        //  Tipos de organización
        $tipos = TipoOrganizacion::where('estado', 1)->get();

        // Macrosectores para el primer Select de la cascada
        $macrosectores = Macrosector::orderBy('nombre', 'asc')->get();

        return view(
            'dashboard.estrategico.organizaciones.crear',
            compact('tipos', 'macrosectores', 'organizaciones')
        );
    }

    public function edit($id)
    {
        $organizacion = OrganizacionEstatal::with('subsector.sector.macrosector', 'padre')->findOrFail($id);
        $macrosectores = Macrosector::all();
        $tipos = TipoOrganizacion::all();

        //Usamos optional para evitar errores si no hay subsector o sector.
        $macrosector_actual = optional(optional($organizacion->subsector)->sector)->id_macrosector;
        $sector_actual      = optional($organizacion->subsector)->id_sector;

        // Cargamos las listas dependientes si existen datos previos
        $sectores = [];
        $subsectores = [];
        $sector_actual = null;

        //Cargamos dependicias si existen
        if ($organizacion->subsector) {
            // Si tiene subsector cargamos sus hermanos
            $subsectores = Subsector::where('id_sector', $organizacion->subsector->id_sector)
                ->orderBy('nombre', 'asc')->get();

            if ($organizacion->subsector->sector) {
                $sector_actual = $organizacion->subsector->sector;
                // Si tiene sector cargamos los sectores hermanos
                $sectores = Sector::where('id_macrosector', $sector_actual->id_macrosector)
                    ->orderBy('nombre', 'asc')->get();
            }
        }
        //Traemos todas las organizaciones excepto la que estamos editando
        $organizaciones = OrganizacionEstatal::where('id_organizacion', '!=', $id)
            ->orderBy('nom_organizacion', 'asc')
            ->get();
        return view(
            'dashboard.estrategico.organizaciones.editar',
            compact(
                'organizacion',
                'organizaciones',
                'macrosectores',
                'sectores',
                'subsectores',
                'sector_actual',
                'tipos'
            )
        );
    }

    /**
     * --- FUNCIONES API PARA AJAX (Javscript) ---
     */

    // Obtener Sectores dado un Macrosector
    public function getSectores($id_macrosector)
    {
        $sectores = Sector::where('id_macrosector', $id_macrosector)
            //->where('estado', 'A')
            ->orderBy('nombre', 'asc')
            ->get(['id_sector', 'nombre']);

        return response()->json($sectores);
    }

    // Obtener Subsectores dado un Sector
    public function getSubsectores($id_sector)
    {
        $subsectores = Subsector::where('id_sector', $id_sector)
            //->where('estado', '1')
            ->orderBy('nombre', 'asc')
            ->get(['id_subsector', 'nombre']);

        return response()->json($subsectores);
    }

    /**
     * Guardar Organización
     */
    public function store(Request $request)
    {
        //dd($request->all());
        //VALIDACIÓN
        $request->validate([
            'nom_organizacion' => 'required|string|max:255',
            'siglas'           => 'nullable|string|max:20',
            'telefono'         => 'nullable|string|max:20',
            'mision'           => 'nullable|string',
            'vision'           => 'nullable|string',
            'ruc' => [
                'required',
                'digits:13',
                Rule::unique('cat_organizacion_estatal')->whereNull('deleted_at')
            ],
            'id_padre'         => 'nullable|exists:cat_organizacion_estatal,id_organizacion',
            'id_subsector'     => 'required|exists:cat_subsector,id_subsector',
            'id_tipo_org'      => 'required|exists:cat_tipo_organizacion,id_tipo_org',
            'nivel_gobierno'   => 'required|string',
            'web'              => 'nullable|url',
            'email'            => 'nullable|email',
            'logo'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);
        DB::beginTransaction();
        try {
            //  CREAR INSTANCIA
            $organizacion = new OrganizacionEstatal();

            // Asignación de campos
            $organizacion->nom_organizacion = $request->nom_organizacion;
            $organizacion->siglas           = $request->siglas;
            $organizacion->ruc              = $request->ruc;
            $organizacion->id_subsector     = $request->id_subsector;
            $organizacion->id_padre         = $request->id_padre;
            $organizacion->id_tipo_org      = $request->id_tipo_org;
            $organizacion->nivel_gobierno   = $request->nivel_gobierno;
            $organizacion->mision           = $request->mision;
            $organizacion->vision           = $request->vision;
            $organizacion->web              = $request->web;
            $organizacion->email            = $request->email;
            $organizacion->telefono         = $request->telefono;
            $organizacion->estado           = '1';


            // Procesamos la imagen antes de guardar
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('logos', 'public');
                $organizacion->logo = $path;
            }
            // GUARDAR EN BD
            $organizacion->save();

            DB::commit();
            return redirect()
                ->route('institucional.organizaciones.index')
                ->with('success', 'La Institución se ha registrado exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('institucional.organizaciones.index')
                ->with('error', 'No se pudo registrar la organizacion. Detalles: ' . $e->getMessage());
        }
    }
    public function show($id)
    {
        // 1. CARGA PROFUNDA
        // cargamos la organizacion y sus objetivos
        //
        $organizacion = OrganizacionEstatal::with([
            'tipo',
            'subsector.sector',
            'objetivos.objetivosNacionales.ods'
        ])->findOrFail($id);


        // 2. PROCESAMIENTO DE DATOS PARA EL GRÁFICO
        $conteoOds = [];
        $infoOds = [];

        // Recorremos TUS objetivos estratégicos
        foreach ($organizacion->objetivos as $estrategico) {

            // Gracias a 'belongsToMany' y la tabla 'alineacion_estrategica',
            // Laravel ya nos da la lista de nacionales conectados aquí:
            foreach ($estrategico->objetivosNacionales as $nacional) {

                // Si el objetivo nacional tiene un ODS asociado...
                if ($nacional->ods) {
                    // Usamos el ID del ODS como clave para agrupar
                    $idOds = $nacional->ods->id_objetivo_desarrollo; // O el nombre de tu PK

                    // Si es la primera vez que vemos este ODS, inicializamos su contador
                    if (!isset($conteoOds[$idOds])) {
                        $conteoOds[$idOds] = 0;
                        // Guardamos el color y nombre una sola vez
                        $infoOds[$idOds] = [
                            'label' => 'ODS ' . $nacional->ods->numero,
                            'color' => $nacional->ods->color_hex
                        ];
                    }

                    // Sumamos +1 al contador de este ODS
                    $conteoOds[$idOds]++;
                }
            }
        }

        // PREPARAR ARRAYS PLANOS PARA JAVASCRIPT
        $labels = [];
        $valores = [];
        $colores = [];

        foreach ($conteoOds as $id => $cantidad) {
            $labels[]  = $infoOds[$id]['label'];
            $valores[] = $cantidad;
            $colores[] = $infoOds[$id]['color'];
        }

        // RETORNAR A LA VISTA
        return view('dashboard.estrategico.organizaciones.show', compact('organizacion', 'labels', 'valores', 'colores'));
    }
    /**
     * Actualizar perfil de la Organización
     **/
    public function update(Request $request, $id)
    {
        //dd($request->all());
        // Buscar la organización
        $organizacion = OrganizacionEstatal::findOrFail($id);

        // Validación (Ahora más permisiva: 10MB)
        $request->validate([
            'nom_organizacion' => 'required|string|max:255',
            'siglas'           => 'nullable|string|max:50',
            'estado'           => 'required|in:A,I,1,0',
            'mision'           => 'nullable|string',
            'vision'           => 'nullable|string',
            'email'            => 'nullable|email',
            Rule::unique('cat_organizacion_estatal', 'ruc')
                ->ignore($id, 'id_organizacion')
                ->whereNull('deleted_at'),
            'telefono'         => 'nullable|string',
            'id_tipo_org'      => 'nullable|exists:cat_tipo_organizacion,id_tipo_org',
            'nivel_gobierno'   => 'nullable|string',
            'logo'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'id_subsector'     => 'required|exists:cat_subsector,id_subsector',
            'id_padre'         => 'required|exists:cat_organizacion_estatal,id_organizacion',
            'estado'           => 'required|boolean',

        ]);
        DB::beginTransaction();
        try {

            // Preparar datos básicos
            $data = $request->except([
                'logo',
                '_token',
                '_method',
                'id_macrosector',
                'id_sector'

            ]);
            $data['id_subsector'] = $request->input('id_subsector');

            // Si usas padre:
            if ($request->has('id_padre')) {
                $data['id_padre'] = $request->input('id_padre');
            }
            // Manejo del Archivo (El código que ya sabemos que funciona)
            if ($request->hasFile('logo')) {

                // Borrar logo anterior si existe
                if ($organizacion->logo && Storage::disk('public')->exists($organizacion->logo)) {
                    Storage::disk('public')->delete($organizacion->logo);
                }
                // Guardar el nuevo
                $path = $request->file('logo')->store('logos', 'public');

                // Asignar la ruta para guardar en BD
                $data['logo'] = $path;
            }

            // Actualizar en Base de Datos
            $organizacion->update($data);
            DB::commit();
            return redirect()
                ->route('institucional.organizaciones.index')
                ->with('success', 'La Institución se ha registrado exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('institucional.organizaciones.index')
                ->with('error', 'No se pudo registrar la organizacion. Detalles: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar Organización
     */
    public function destroy($id)
    {
        // Usamos id o el nombre de tu llave primaria si es diferente
        $organizacion = OrganizacionEstatal::findOrFail($id);

        // Soft delete o borrado físico (depende de tu modelo)
        $organizacion->delete();

        return redirect()->route('estrategico.organizaciones.index')
            ->with('status', 'La entidad ha sido eliminada correctamente.');
    }
}
