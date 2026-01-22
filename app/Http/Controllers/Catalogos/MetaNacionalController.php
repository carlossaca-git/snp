<?php

namespace App\Http\Controllers\Catalogos;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


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

        //  CARGA DE RELACIONES (Aquí agregamos la cadena mágica)
        // Mantenemos 'ods' y 'objetivoNacional' y agregamos la ruta hacia los proyectos
        // Asumiendo que 'objetivos' es la relación hacia ObjetivoEstrategico que definimos antes
        $query = MetaNacional::with([
            'ods',
            'objetivoNacional',
            'objetivos.proyectos.marcoLogico' // <--- AGREGADO: Para traer los datos de avance
        ]);

        //  FILTROS DE BÚSQUEDA (Tu código original intacto)
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

        // PAGINACIÓN Y ORDEN
        $metas = $query->orderBy('id_meta_nacional', 'asc')
            ->paginate(10)
            ->appends(['busqueda' => $buscar]);

        // TRANSFORMACIÓN DE DATOS (Aquí integramos el cálculo)
        $metas->getCollection()->transform(function ($meta) {
            // Conversiones originales
            $meta->linea_base = (float) $meta->linea_base;
            $meta->meta_valor = (float) $meta->meta_valor;



            // Obtenemos los objetivos estratégicos vinculados (o colección vacía si es null)
            $objetivosEstrategicos = $meta->objetivos ?? collect();

            //  Aplanamos para sacar todos los proyectos nietos en una sola lista
            $todosLosProyectos = $objetivosEstrategicos->flatMap(function ($obj) {
                return $obj->proyectos ?? collect();
            });

            // Calculamos el promedio ponderado usando el atributo del modelo
            if ($todosLosProyectos->isNotEmpty()) {
                $meta->avance_promedio = $todosLosProyectos->avg(function ($proy) {
                    return $proy->avance_real; // Llama a getAvanceRealAttribute
                });
            } else {
                $meta->avance_promedio = 0;
            }

            // Guardamos la lista de proyectos dentro del objeto Meta
            // Esto sirve para que el MODAL pueda recorrerlos con $meta->proyectos_calculados
            $meta->setRelation('proyectos_calculados', $todosLosProyectos);

            return $meta;
        });

        $objetivos = ObjetivoNacional::where('estado', 1)->get();
        $ods = Ods::all();

        return view('dashboard.configuracion.metas.index', compact('metas', 'objetivos', 'ods'));
    }

    public function show($id)
    {
        // CARGA DE DATOS
        // Buscamos la meta específica con toda su descendencia
        $meta = MetaNacional::with([
            'objetivoNacional',
            'ods',
            'objetivos.proyectos.marcoLogico'
        ])->findOrFail($id);

        // ---------------------------------------------------------
        // 2. CÁLCULO DEL RANKING DE OBJETIVOS (Lógica Nueva)
        // ---------------------------------------------------------
        // Usamos la colección de objetivos que ya cargamos arriba
        $objetivosCollection = $meta->objetivos ?? collect();

        // Transformamos cada objetivo para calcular su "nota" (avance ponderado)
        $objetivosCollection->transform(function ($obj) {
            $proyectosDelObj = $obj->proyectos ?? collect();

            // Sumamos toda la plata que maneja este objetivo
            $inversionTotal = $proyectosDelObj->sum('monto_total_inversion');

            // Calculamos la suma ponderada: (Avance * Dinero) de cada proyecto
            $sumaPonderada = 0;
            foreach ($proyectosDelObj as $proy) {
                $avance = $proy->avance_real ?? 0;
                $monto = $proy->monto_total_inversion ?? 0;
                $sumaPonderada += ($avance * $monto);
            }

            // Aplicamos la fórmula: Si hay dinero, dividimos. Si no, es 0.
            $obj->avance_ponderado = $inversionTotal > 0
                ? ($sumaPonderada / $inversionTotal)
                : 0;

            // Guardamos datos extra para la vista Gráfico y textos
            $obj->inversion_total = $inversionTotal;
            $obj->conteo_proyectos = $proyectosDelObj->count();

            return $obj;
        });

        // Ordenamos la colección El de mayor puntaje va primero
        $rankingObjetivos = $objetivosCollection->sortByDesc('avance_ponderado');
        // Extraemos todos los proyectos  en una lista plana para la tabla de abajo
        $proyectos = $objetivosCollection->flatMap(function ($obj) {
            return $obj->proyectos ?? collect();
        });

        // Calculamos el avance individual para la tabla
        $proyectos->transform(function ($proy) {
            $proy->calculo_avance = $proy->avance_real;
            return $proy;
        });

        // Calculamos el promedio global de la meta
        $promedioMeta = $proyectos->isNotEmpty() ? $proyectos->avg('calculo_avance') : 0;
        return view('dashboard.configuracion.metas.show', compact(
            'meta',
            'proyectos',
            'promedioMeta',
            'rankingObjetivos'
        ));
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_objetivo_nacional' => 'required|exists:cat_objetivo_nacional,id_objetivo_nacional',
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
                ->with('success', 'Meta creadq correctamente.');
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
            return redirect()->route('Configuracion.metas.index')
                ->with('success', 'Objetivo eliminado del catálogo.');
        } catch (\Exception $e) {
            return redirect()->route('configuracion.metas.index')
                ->with('error', 'No se puede eliminar porque tiene objetivos estratégicos asociados.');
        }
    }
}
