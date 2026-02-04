<?php

namespace App\Models\Catalogos;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Planificacion\AlineacionEstrategica;
use App\Models\Planificacion\ObjetivoEstrategico;


class MetaNacional extends Model
{
    use Auditable;
    use SoftDeletes;
    protected $table = 'cat_meta_nacional';
    protected $primaryKey = 'id_meta_nacional';
    public $incrementing = true;

    protected $fillable = [
        'objetivo_nacional_id',
        'codigo_meta',
        'nombre_meta',
        'descripcion_meta',
        'url_documento',
        'nombre_indicador',
        'unidad_medida',
        'linea_base',
        'meta_valor',
        'valor_actual',
        'estado'
    ];

    /**
     * Relación Inversa: Pertenece a un Objetivo Nacional
     */
    public function objetivoNacional()
    {
        // Asegúrate que el segundo parámetro coincida con tu columna FK en la base de datos
        return $this->belongsTo(
            ObjetivoNacional::class,
            'objetivo_nacional_id',
            'id_objetivo_nacional'
        );
    }
    // Una meta tiene muchos indicadores
    public function indicadoresNacionales()
    {
        return $this->hasMany(
            IndicadorNacional::class,
            'meta_nacional_id',
            'id_meta_nacional'
        );
    }
    // app/Models/MetaPnd.php

    public function ods()
    {
        // Relación de muchos a muchos con el modelo Ods
        // Asegúrate de que los nombres de las llaves coincidan con tu tabla
        return $this->belongsToMany(
            Ods::class,
            'alineacion_metas_ods',
            'id_meta_nacional',
            'id_ods'
        );
    }
    public function avances()
    {
        // Una meta tiene muchos avances
        return $this->hasMany(
            AvanceMeta::class,
            'id_meta_nacional',
            'id_meta_nacional'
        );
    }
    public function objetivos()
    {
        return $this->belongsToMany(
            ObjetivoEstrategico::class,
            'alineacion_estrategica',
            'meta_nacional_id',
            'objetivo_estrategico_id',
            'id_meta_nacional',
            'id_objetivo_estrategico'
        );
    }
    public function alineacion()
    {
        return $this->hasMany(
            AlineacionEstrategica::class,
            'meta_nacional_id',
            'id_meta_nacional'
        );
    }

    //Accessor: Suma de los aportes ponderados de sus indicadores
    public function getAvanceActualAttribute()
    {
        $indicadores = $this->indicadoresNacionales;

        if ($indicadores->isEmpty()) return 0;

        $avanceMeta = 0;

        foreach ($indicadores as $indicador) {
            $avanceIndicador = $indicador->porcentaje_cumplimiento;

            $pesoIndicador = $indicador->peso_oficial;

            $aporte = ($avanceIndicador * $pesoIndicador) / 100;

            $avanceMeta += $aporte;
        }

        return round($avanceMeta, 2);
    }
}
