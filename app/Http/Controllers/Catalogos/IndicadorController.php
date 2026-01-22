<?php

namespace App\Http\Controllers\Catalogos;

use Exception;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller;
use App\Models\Catalogos\Indicador;
use App\Models\Catalogos\MetaNacional;

use Illuminate\Http\Request;

class IndicadorController extends Controller
{
    public function __construct()
    {
        //  Protección Base Nadie entra sin estar logueado
        $this->middleware('auth');

        //  Protección de LECTURA solo index y show
        $this->middleware('permiso:indicadores.ver')->only(['index', 'show']);

        //  Protección de ESCRITURA Crear, Editar, Borrar
        $this->middleware('permiso:indicadores.gestionar')->only([
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
        $buscar = $request->input('busqueda');

        //  Iniciamos la consulta con la relación meta
        $query = Indicador::with('meta', 'ultimoAvance');

        // Aplicamos filtros de búsqueda AJAX compatible
        if ($buscar) {
            $estadoBusqueda = null;
            if (strtolower($buscar) == 'activo') $estadoBusqueda = 1;
            if (strtolower($buscar) == 'inactivo') $estadoBusqueda = 0;

            $query->where(function ($q) use ($buscar, $estadoBusqueda) {
                $q->where('nombre_indicador', 'LIKE', "%{$buscar}%")
                    ->orWhere('unidad_medida', 'LIKE', "%{$buscar}%")
                    ->orWhere('frecuencia', 'LIKE', "%{$buscar}%");

                if ($estadoBusqueda !== null) {
                    $q->orWhere('estado', $estadoBusqueda);
                }

                // También buscamos por el nombre de la Meta asociada
                $q->orWhereHas('meta', function ($metaQ) use ($buscar) {
                    $metaQ->where('nombre_meta', 'LIKE', "%{$buscar}%");
                });
            });
        }

        //  Ordenamos por ID descendente y paginamos
        $indicadores = $query->orderBy('id_indicador', 'desc')
            ->paginate(10)
            ->appends(['busqueda' => $buscar]);

        //  Metas activas para los Selects de los modales
        $metas = MetaNacional::where('estado', 1)->orderBy('nombre_meta')->get();

        return view('dashboard.configuracion.indicadores.index', compact('indicadores', 'metas'));
    }
    //Metodo stores
    public function store(Request $request)
    {

        $request->validate([
            'id_meta'            => 'required|exists:cat_meta_nacional,id_meta_nacional',
            'nombre_indicador'   => 'required|string',
            'linea_base'         => 'nullable|numeric',
            'anio_linea_base'    => 'nullable|integer|min:2000|max:2100',
            'meta_final'         => 'nullable|numeric',
            'unidad_medida'      => 'required|string',
            'frecuencia'         => 'required|string',
            'fuente_informacion' => 'nullable|string',
            'descripcion_indicador' => 'nullable|string',
            'estado'             => 'required|boolean',

        ], [
            'codigo_meta.unique' => 'Error: El código de meta ya existe en el sistema.'
        ]);
        DB::beginTransaction();
        try {

            Indicador::create($request->all());
            DB::commit();
            return redirect()
                ->route('catalogos.indicadores.index')
                ->with('success', 'Indicador creado correctamente.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('catalogos.metas.index')
                ->with('error', 'No se pudo guardar la meta. Detalles: ' . $e->getMessage());
        }
    }
    //Metodo Update
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_meta'          => 'required|exists:cat_meta_nacional,id_meta_nacional',
            'nombre_indicador' => 'required|string',
            'linea_base'       => 'nullable|numeric',
            'meta_final'       => 'nullable|numeric',
        ]);
        DB::beginTransaction();
        try {


            $indicador = Indicador::findOrFail($id);
            $indicador->update($request->all());
            DB::commit();
            return redirect()->route('catalogos.indicadores.index')
                ->with('success', 'El indicador ' . $indicador->codigo . ' se ha actualizado correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('catalogos.indicadores.index')
                ->with('error', 'No se pudo guardar el Indicador. Detalles: ' . $e->getMessage());
        }
    }

    //Metodo show para
    public function show($id)
    {
        // Buscamos el indicador con sus relaciones
        $indicador = Indicador::with(['meta', 'avances' => function ($query) {
            $query->orderBy('fecha_reporte', 'asc');
        }])->findOrFail($id);

        // Extraemos solo las fechas y los valores en listas separadas
        $fechasGrafico = $indicador->avances->map(function ($avance) {
            return date('d/m/Y', strtotime($avance->fecha_reporte));
        });

        $valoresGrafico = $indicador->avances->pluck('valor_logrado');

        // Agregamos la Línea Base al inicio del gráfico para que se vea el punto de partida
        $fechasGrafico->prepend('Línea Base (' . $indicador->anio_linea_base . ')');
        $valoresGrafico->prepend($indicador->linea_base);

        return view('dashboard.configuracion.indicadores.show', compact('indicador', 'fechasGrafico', 'valoresGrafico'));
    }
    public function destroy($id)
    {
        try {
            $indicador = Indicador::findOrFail($id);
            $indicador->delete();

            return redirect()->back()->with('success', 'El indicador ha sido eliminado correctamente.');
        } catch (\Exception $e) {
            // Esto protege el sistema si el indicador ya tiene "Avances" registrados (llave foránea)
            return redirect()->back()->with('error', 'No se puede eliminar el indicador porque tiene registros asociados.');
        }
    }

    //REPORTES PDf
    public function generarPdf($id)
    {
        // Buscamos los datos (igual que en el Kardex)
        $indicador = Indicador::with(['meta', 'avances' => function ($query) {
            $query->orderBy('fecha_reporte', 'asc');
        }])->findOrFail($id);

        // Cargamos la vista del PDF (Ojo: crearemos una vista especial 'limpia')
        $pdf = Pdf::loadView('dashboard.configuracion.indicadores.reportes.ficha', compact('indicador'));

        // Opciones de papel (Vertical/Horizontal)
        $pdf->setPaper('A4', 'portrait');

        // Descargar o Ver en navegador ('stream' es para ver, 'download' para bajar directo)
        return $pdf->stream('Ficha_Indicador_' . $indicador->codigo_indicador . '.pdf');
    }
}
