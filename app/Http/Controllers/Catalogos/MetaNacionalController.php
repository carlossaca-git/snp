<?php

namespace App\Http\Controllers\Catalogos;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


use App\Models\Catalogos\Ods;
use App\Http\Controllers\Controller;
use App\Models\Catalogos\MetaNacional;
use App\Models\Catalogos\ObjetivoNacional;


class MetaNacionalController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('busqueda');

        // Iniciamos el QueryBuilder
        $query = MetaNacional::with(['ods', 'objetivoNacional']);

        //  Aplicamos filtros de búsqueda (si existen)
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

        //  Forzamos el orden y paginamos
        // Usamos el nombre de la tabla para evitar ambigüedad 'metas_nacionales.id_meta_nacional'
        $metas = $query->orderBy('id_meta_nacional', 'asc')
            ->paginate(10)
            ->appends(['busqueda' => $buscar]);

        //  Transformación de datos
        $metas->getCollection()->transform(function ($meta) {
            $meta->linea_base = (float) $meta->linea_base;
            $meta->meta_valor = (float) $meta->meta_valor;
            return $meta;
        });

        $objetivos = ObjetivoNacional::where('estado', 1)->get();
        $ods = Ods::all();

        return view('dashboard.configuracion.metas.index', compact('metas', 'objetivos', 'ods'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'id_objetivo_nacional' => 'required|exists:cat_objetivo_nacional,id_objetivo_nacional',
            'codigo_meta'      => 'nullable|max:50',
            'nombre_meta'      => 'required|string',
            'url_documento'    => 'nullable|url',
            'estado'           => 'required|boolean',
            'nombre_indicador' => 'required|string|max:500',
            'unidad_medida'    => 'required|string',
            'linea_base'       => 'required',
            'meta_valor'       => 'required'
        ]);
        DB::beginTransaction();
        try {
            MetaNacional::create($request->all());
            DB::commit();
            return redirect()
                ->route('catalogos.metas.index')
                ->with('success', 'ODS creado correctamente.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('catalogos.metas.index')
                ->with('error', 'No se pudo guardar la meta. Detalles: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_objetivo_nacional' => 'required|exists:cat_objetivo_nacional,id_objetivo_nacional',
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
    //Atualizar valor acctual
    public function actualizarAvance(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'id_meta_nacional'  => 'required|exists:cat_meta_nacional,id_meta_nacional',
            'valor_actual'      => 'required|numeric'
        ]);
        DB::beginTransaction();
        try {
            $meta = MetaNacional::findOrFail($request->id_meta_nacional);
            $meta->valor_actual = $request->valor_actual;
            $meta->save();
            DB::commit();
            return redirect()->route('catalogos.metas.index')
                ->with('success', '¡Avance de meta ' . $meta->codigo . ' La barra de progreso se ha recalculado.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('catalogos.metas.index')
                ->with('error', 'No se pudo guardar el ODS. Detalles: ' . $e->getMessage());
        }
    }
    //Guardar vinculacio metas-ods
    public function vincularOds(Request $request)
    {
        dd($request->all());
        $request->validate([
            'id_meta_nacional' => 'required|exists:cat_meta_nacional,id_meta_nacional',
            'ods_ids' => 'array' // Puede venir vacío si se quitan todas
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
            return redirect()->route('Configuracion.metas.index')
                ->with('success', 'Objetivo eliminado del catálogo.');
        } catch (\Exception $e) {
            return redirect()->route('configuracion.metas.index')
                ->with('error', 'No se puede eliminar porque tiene objetivos estratégicos asociados.');
        }
    }
}
