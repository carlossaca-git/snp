<?php

namespace App\Http\Controllers\Catalogos;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;


use App\Models\Catalogos\Ods;
use Illuminate\Routing\Controller;
use App\Models\Catalogos\MetaNacional;
use App\Models\Catalogos\ObjetivoNacional;


class MetaNacionalController extends Controller
{
    public function __construct()
    {
        //  Protección Base Nadie entra sin estar logueado
        $this->middleware('auth');

        //  Protección de LECTURA (Solo index y show)
        $this->middleware('permiso:metas_pnd.ver')->only(['index', 'show']);

        //  Protección de ESCRITURA (Crear, Editar, Borrar)
        $this->middleware('permiso:metas_pnd.gestionar')->only([
            'create',
            'store',
            'edit',
            'update',
            'destroy'
        ]);
    }
    public function index(Request $request)
    {
        $buscar = $request->input('busqueda');

        $query = MetaNacional::with([
            'ods',
            'objetivoNacional',
            'indicadoresNacionales'
        ]);

        //FILTROS DE BÚSQUEDA
        if ($buscar) {
            $estadoBusqueda = null;
            if (strtolower($buscar) == 'activo') $estadoBusqueda = 1;
            if (strtolower($buscar) == 'inactivo') $estadoBusqueda = 0;

            $query->where(function ($q) use ($buscar, $estadoBusqueda) {
                $q->where('nombre_meta', 'LIKE', "%{$buscar}%")
                    ->orWhere('codigo_meta', 'LIKE', "%{$buscar}%");

                if ($estadoBusqueda !== null) {
                    $q->orWhere('estado', $estadoBusqueda);
                }
            });
        }
        $metas = $query->orderBy('objetivo_nacional_id', 'asc')
            ->paginate(10)
            ->appends(['busqueda' => $buscar]);

        // DATOS AUXILIARES
        $objetivos = ObjetivoNacional::where('estado', 1)->get();
        $ods = Ods::all();

        return view('dashboard.configuracion.metas.index', compact('metas', 'objetivos', 'ods'));
    }
    /**
     * Metodo Show
     */
    public function show($id, Request $request)
    {
        // Cargamos la meta, el objetico, y los indicadores
        $meta = MetaNacional::with([
            'objetivoNacional',
            'indicadoresNacionales' => function ($query) {
                // Ordenamos los indicadores por importancia (Peso) de mayor a menor
                $query->orderBy('peso_oficial', 'desc');

                // Cargamos los proyectos dentro de cada indicador para saber cuántos son
                $query->withCount('proyectos');
            }
        ])->findOrFail($id);
        //Para obtener que proyectos se vinculan con esta meta
        $proyectos = $meta->indicadoresNacionales->flatMap(function ($ind) {
            return $ind->proyectos->map(function ($proyecto) use ($ind) {
                $proyecto->indicador_padre = $ind;
                return $proyecto;
            });
        });
        $busqueda = $request->get('busqueda');

        if ($busqueda) {
            // Filtramos la colección ignorando mayúsculas/minúsculas
            $proyectos = $proyectos->filter(function ($item) use ($busqueda) {
                // Buscamos coincidencia en Nombre o CUP
                return false !== stripos($item->nombre_proyecto, $busqueda) ||
                    false !== stripos($item->cup, $busqueda);
            });
        }
        $page = Paginator::resolveCurrentPage() ?: 1;
        $perPage = 10;

        $proyectosPaginados = new LengthAwarePaginator(
            $proyectos->forPage($page, $perPage),
            $proyectos->count(),
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => $request->query()
            ]
        );
        return view('dashboard.configuracion.metas.show', [
            'meta' => $meta,
            'proyectos' => $proyectosPaginados
        ]);
    }

    // metodo store, guarda los datos de la meta
    public function store(Request $request)
    {
        $request->validate([
            'objetivo_nacional_id' => 'required|exists:cat_objetivo_nacional,id_objetivo_nacional',
            'codigo_meta'          => 'required|string|unique:cat_meta_nacional,codigo_meta',
            'nombre_meta'          => 'required|string',
            'url_documento'        => 'nullable|url',
            'estado'               => 'required|boolean',
            'nombre_indicador'     => 'required|string|max:500',
            'unidad_medida'        => 'required|string',
            'linea_base'           => 'required',
            'meta_valor'           => 'required'
        ]);
        DB::beginTransaction();
        try {
            MetaNacional::create($request->all());
            DB::commit();
            return redirect()
                ->route('catalogos.metas.index')
                ->with('success', 'Meta Nacional registrada correctamente.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('catalogos.metas.index')
                ->with('error', 'No se pudo guardar la meta. Detalles: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'objetivo_nacional_id' => 'required|exists:cat_objetivo_nacional,id_objetivo_nacional',
            'codigo_meta'      => 'required|string|unique:cat_meta_nacional,codigo_meta,' . $id . ',id_meta_nacional',
            'nombre_meta' => 'required|string',
            'estado'      => 'required|boolean',
        ]);
        DB::beginTransaction();
        try {
            $meta = MetaNacional::findOrFail($id);
            $meta->update($request->all());
            DB::commit();
            return redirect()->route('catalogos.metas.index')
                ->with('success', 'La meta ' . $meta->codigo . ' se ha actualizado correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('catalogos.metas.index')
                ->with('error', 'No se pudo guardar el ODS. Detalles: ' . $e->getMessage());
        }
    }

    //Guardar vinculacio metas-ods
    public function vincularOds(Request $request)
    {
        $request->validate([
            'id_meta_nacional' => 'required|exists:cat_meta_nacional,id_meta_nacional',
            'ods_ids' => 'array'
        ]);

        $meta = MetaNacional::find($request->id_meta_nacional);

        // sync() se encarga de insertar en la tabla alineacion_metas_ods
        // y borrar lo que ya no esté seleccionado.
        $meta->ods()->sync($request->ods_ids);

        return back()->with('success', 'Alineación con ODS actualizada correctamente.');
    }

    // Metodo eliminar
    public function destroy($id)
    {
        $objetivo = MetaNacional::findOrFail($id);

        try {
            $objetivo->delete();
            return redirect()->route('catalogos.metas.index')
                ->with('success', 'Objetivo eliminado del catálogo.');
        } catch (\Exception $e) {
            return redirect()->route('catalogos.metas.index')
                ->with('error', 'No se puede eliminar porque tiene objetivos estratégicos asociados.');
        }
    }
}
