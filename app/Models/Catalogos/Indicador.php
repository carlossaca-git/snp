<?php

namespace App\Models\Catalogos;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Indicador extends Model
{
    use SoftDeletes;
    use Auditable;
    protected $table = 'cat_indicador';
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
    public function meta()
    {
        return $this->belongsTo(MetaNacional::class, 'id_meta', 'id_meta_nacional');
    }
    //
    public function ultimoAvance()
    {
        //
        return $this->hasOne(AvanceIndicador::class, 'id_indicador')->latestOfMany('fecha_reporte');
    }
    public function avances()
    {
        return $this->hasMany(AvanceIndicador::class, 'id_indicador');
    }
}
