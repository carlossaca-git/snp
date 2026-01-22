<?php

namespace App\Http\Controllers\Catalogos;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller;
use App\Models\Catalogos\Ods;
use Exception;
use Illuminate\Support\Facades\DB;

class OdsController extends Controller
{
    //Metodo de seguridad
    public function __construct()
    {
        //  Protección Base Nadie entra sin estar logueado
        $this->middleware('auth');

        //  Protección de LECTURA (Solo index y show)
        $this->middleware('permiso:ods.ver')->only(['index', 'show']);

        //  Protección de ESCRITURA (Crear, Editar, Borrar)
        $this->middleware('permiso:ods.gestionar')->only([
            'create',
            'store',
            'edit',
            'update',
            'destroy'
        ]);
    }
    //Metodo index

    public function index(Request $request)
    {
        $busqueda = $request->input('busqueda');

        $query = Ods::query();

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                    ->orWhere('descripcion', 'LIKE', "%{$busqueda}%")
                    ->orWhere('codigo', 'LIKE', "%{$busqueda}%")
                    ->orWhere('pilar', 'LIKE', "%{$busqueda}%");
            });
        }
        $ods = $query->orderBy('id_ods', 'asc')->get();

        return view('dashboard.configuracion.ods.index', compact('ods'));
    }
    /**
     * Funcion para nuevo ods
     */
    public function create()
    {
        return view('dashboard.configuracion.ods.crear');
    }

    /**
     * Funcion store ods
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo'        => 'required|string|max:20|unique:cat_ods,codigo',
            'nombre'        => 'required|string|max:255',
            'pilar'         => 'required|string|max:100',
            'descripcion'   => 'nullable|string',
            'estado'        => 'required|boolean',
            'color_hex'     => 'required|string|max:7'
        ], [
            'codigo.unique' => 'Ya existe un ODS registrado con el número ' . $request->codigo . '.',
        ]);
        DB::beginTransaction();
        try {
            $ods = new ODS();
            $ods->codigo      = 'ODS-' . $request->codigo;
            $ods->nombre      = $request->nombre;
            $ods->pilar       = $request->pilar;
            $ods->descripcion = $request->descripcion;
            $ods->estado      = $request->estado;
            $ods->color_hex   = $request->color_hex;
            $ods->save();
            DB::commit();

            return redirect()
                ->route('catalogos.ods.index')
                ->with('success', 'ODS creado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Retorna ERROR
            return redirect()->route('catalogos.ods.index')
                ->with('error', 'No se pudo guardar el ODS. Detalles: ' . $e->getMessage());
        }
    }
    /**
     * Funcion editar para enviar datos al vormulario
     */
    public function edit($id)
    {
        $ods_item = Ods::findOrFail($id);
        return view('dashboard.configuracion.ods.editar', compact('ods_item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'codigo'        => 'string',
            'nombre'        => 'string|max:255',
            'descripcion'   => 'string',
            'pilar'         => 'string|max:100',
            'estado'        => 'boolean',
            'color_hex'     => 'string|max:7',
        ]);
        try {
            $ods = Ods::findOrFail($id);
            if ($request->has('codigo')) {
                $ods->codigo = 'ODS-' . $request->codigo;
            }
            $ods->update($request->all());
            return redirect()->route('catalogos.ods.index')
                ->with('success', '¡Excelente! El ODS #' . $ods->codigo . ' se ha actualizado correctamente.');
        } catch (Exception $e) {
            return redirect()->route('catalogos.ods.index')
                ->with('error', 'No se pudo guardar el ODS. Detalles: ' . $e->getMessage());
        }
    }
    public function destroy($ods)
    {
        DB::beginTransaction();
        try {
            $ods = Ods::findOrFail($ods);
            $ods->delete();
            DB::commit();
            return redirect()->route('catalogos.ods.index')
                ->with('success', 'El ODS ha sido eliminado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('catalogos.ods.index')
                ->with('error', 'Ocurrió un error al eliminar: ' . $e->getMessage());
        }
    }
}
