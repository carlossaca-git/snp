<?php

namespace App\Models\Catalogos;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

use App\Models\Catalogos\MetaNacional;
use App\Models\Inversion\ProyectoInversion;
use App\Models\Catalogos\AvanceIndicador;

class IndicadorNacional extends Model
{
    //
    use HasFactory;
    use Auditable;
    use SoftDeletes;
    protected $table = 'cat_indicadores_nacionales';
    protected $primaryKey = 'id_indicador';
    protected $fillable = [
        'id_meta',
        'nombre_indicador',
        'metodo_calculo',
        'linea_base',
        'anio_linea_base',
        'meta_final',
        'unidad_medida',
        'frecuencia',
        'fuente_informacion',
        'descripcion_indicador',
        'estado'
    ];

    // Relación: Un indicador pertenece a una meta
    public function metaNacional()
    {
        return $this->belongsTo(
            MetaNacional::class,
            'meta_nacional_id',
            'id_meta_nacional'
        );
    }
    //
    public function ultimoAvance()
    {
        //
        return $this->hasOne(
            AvanceIndicador::class,
            'id_indicador'
        )->latestOfMany('fecha_reporte');
    }
    public function avances()
    {
        return $this->hasMany(
            AvanceIndicador::class,
            'id_indicador'
        );
    }
    public function proyectos()
    {
        return $this->belongsToMany(
            ProyectoInversion::class,
            'tra_proyecto_indicador',
            'indicador_nacional_id',
            'proyecto_id'
        )->withPivot('contribucion_proyecto');
    }
    //Accessor: Suma de los aportes ponderados de sus proyectos
    public function getPorcentajeCumplimientoAttribute()
    {
        if ($this->proyectos->isEmpty()) {
            return 0;
        }
        //Inicializar la variable
        $acumulado = 0;

        foreach ($this->proyectos as $proyecto) {

            $avanceReal = $proyecto->avance_fisico_real ?? 0;

            $pesoAsignado = $proyecto->pivot->contribucion_proyecto ?? 0;
            $aporte = ($avanceReal * $pesoAsignado) / 100;
            $acumulado += $aporte;
        }
        // Retornamos el total
        return $acumulado;
    }

    // Accessor: obtener valor actual del de avance entre la linea base y meta final
    public function getValorActualAbsolutoAttribute()
    {
        // Validacin que existan valores
        if (!is_numeric($this->meta_final) || !is_numeric($this->linea_base)) {
            return $this->linea_base ?? 0;
        }
        // Calculamos la BRECHA
        $brecha = $this->meta_final - $this->linea_base;
        // Traemos el porcentaje que ya calculaste con los proyectos
        $porcentajeLogrado = $this->porcentaje_cumplimiento;
        // Total de la brecha cubierta
        $avanceEnUnidades = ($brecha * $porcentajeLogrado) / 100;
        // Sumamos a la base original
        $valorFinal = $this->linea_base + $avanceEnUnidades;
        // Retornamos redondeado (ajusta decimales según tu necesidad)
        return $valorFinal;
    }
    public function getPrecisionAttribute()
    {
        $unidad = Str::lower($this->unidad_medida);

        return match (true) {

            Str::contains($unidad, ['persona', 'habitante', 'alumno', 'escuela', 'hospital', 'vivienda', 'hogar']) => 0,

            Str::contains($unidad, ['%', 'porcentaje']) => 1,
            Str::contains($unidad, ['dolar', 'peso', 'inversion', 'tasa', 'índice', 'km', 'tonelada']) => 2,

            Str::contains($unidad, ['gases', 'particulas', 'micro']) => 3,

            default => 2,
        };
    }
}
