<?php

namespace App\Http\Controllers\Institucional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Importante para manejar archivos

use Illuminate\Validation\Rule;
use App\Models\Institucional\OrganizacionEstatal;
use App\Models\Catalogos\Macrosector;
use App\Models\Catalogos\Sector;
use App\Models\Catalogos\Subsector;
use App\Models\Institucional\TipoOrganizacion;

class OrganizacionController extends Controller
{
    /**
     * Listado de Organizaciones
     */
    public function index(Request $request)
    {
        // 1. Limpiamos espacios en blanco por si acaso
        $busqueda = trim($request->get('busqueda'));

        // 2. Consulta BLINDADA
        $organizaciones = OrganizacionEstatal::query()
            ->when($busqueda, function ($query) use ($busqueda) { // <--- ¡IMPORTANTE EL 'use'!

                // Usamos un grupo (paréntesis) para que el OR no rompa otras reglas
                return $query->where(function ($q) use ($busqueda) { // <--- ¡OTRA VEZ 'use'!
                    $q->where('nom_organizacion', 'LIKE', "%{$busqueda}%")
                        ->orWhere('ruc', 'LIKE', "%{$busqueda}%")
                        ->orWhere('siglas', 'LIKE', "%{$busqueda}%");
                });
            })
            ->orderBy('id_organizacion', 'DESC')
            ->paginate(10); // Asegúrate de que este número sea igual al de tu vista

        // 3. Mantener el texto en la URL al pasar de página
        $organizaciones->appends(['busqueda' => $busqueda]);

        return view('dashboard.estrategico.organizaciones.index', compact('organizaciones'));
    }
    /**
     * Formulario de Creación
     * CAMBIO: Ahora enviamos Macrosectores en lugar de Subsectores
     */
    public function create()
    {
        // 1. Tipos de organización
        $tipos = TipoOrganizacion::where('estado', 1)->get();

        // 2. Macrosectores (Para el primer Select de la cascada)
        // Usamos 'nom_macrosector' o 'nombre' según tu BD. Asumo 'nombre' por la charla anterior.
        $macrosectores = Macrosector::orderBy('nombre', 'asc')->get();

        return view('dashboard.estrategico.organizaciones.crear', compact('tipos', 'macrosectores'));
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
        // 1. VALIDACIÓN
        $validated = $request->validate([
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
            'id_subsector'     => 'required|exists:cat_subsector,id_subsector',
            'id_tipo_org'      => 'required|exists:cat_tipo_organizacion,id_tipo_org',
            'nivel_gobierno'   => 'required|string',
            'web'              => 'nullable|url',
            'email'            => 'nullable|email',
            // NUEVO: Validación del Logo (Máx 10MB)
            'logo'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        // 2. CREAR INSTANCIA
        $organizacion = new OrganizacionEstatal();

        // Asignación de campos
        $organizacion->nom_organizacion = $request->nom_organizacion;
        $organizacion->siglas           = $request->siglas;
        $organizacion->ruc              = $request->ruc;
        $organizacion->id_subsector     = $request->id_subsector;
        $organizacion->id_tipo_org      = $request->id_tipo_org;
        $organizacion->nivel_gobierno   = $request->nivel_gobierno;
        $organizacion->mision           = $request->mision;
        $organizacion->vision           = $request->vision;
        $organizacion->web              = $request->web;
        $organizacion->email            = $request->email;
        $organizacion->telefono         = $request->telefono;
        $organizacion->estado           = '1'; // Por defecto Activo


        // Procesamos la imagen antes de guardar
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $organizacion->logo = $path;
        }

        // 4. GUARDAR EN BD
        $organizacion->save();

        return redirect()->route('institucional.organizaciones.index')
            ->with('status', 'La Institución se ha registrado exitosamente.');
    }
    public function show($id)
    {
        // 1. CARGA PROFUNDA
        // cargamos la organizacion y sus objetivos
        // cruza por la tabla de alineación y tráeme los nacionales, y de paso sus ODS".
        $organizacion = OrganizacionEstatal::with([
            'tipo',
            'subsector.sector',
            // Aquí ocurre la magia del puente:
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

        // 3. PREPARAR ARRAYS PLANOS PARA JAVASCRIPT
        $labels = [];
        $valores = [];
        $colores = [];

        foreach ($conteoOds as $id => $cantidad) {
            $labels[]  = $infoOds[$id]['label'];
            $valores[] = $cantidad;
            $colores[] = $infoOds[$id]['color'];
        }

        // 4. RETORNAR VISTA
        return view('dashboard.estrategico.organizaciones.show', compact('organizacion', 'labels', 'valores', 'colores'));
    }
    /**
     * Actualizar perfil de la Organización
     **/
    public function update(Request $request, $id)
    {
        // 1. Buscar la organización
        $organizacion = OrganizacionEstatal::findOrFail($id);

        // 2. Validación (Ahora más permisiva: 10MB)
        $request->validate([
            'nom_organizacion' => 'required|string|max:255',
            'siglas'           => 'nullable|string|max:50',
            'estado'           => 'required|in:A,I,1,0',
            'mision'           => 'nullable|string',
            'vision'           => 'nullable|string',
            'email'            => 'nullable|email',
            'telefono'         => 'nullable|string',
            'logo'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        // 3. Preparar datos básicos
        $data = $request->except(['logo', '_token', '_method']);

        // 4. Manejo del Archivo (El código que ya sabemos que funciona)
        if ($request->hasFile('logo')) {

            // A. Borrar logo anterior si existe
            if ($organizacion->logo && Storage::disk('public')->exists($organizacion->logo)) {
                Storage::disk('public')->delete($organizacion->logo);
            }

            // B. Guardar el nuevo
            $path = $request->file('logo')->store('logos', 'public');

            // C. Asignar la ruta para guardar en BD
            $data['logo'] = $path;
        }

        // 5. Actualizar en Base de Datos
        $organizacion->update($data);

        // 6. Volver con mensaje
        return redirect()->back()->with('status', 'Perfil y logo actualizados correctamente.');
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
