<?php

namespace App\Models\Inversion;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inversion\MetaAnual;
use App\Models\Inversion\MarcoLogico;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndicadorMarco extends Model
{
    use Auditable;
    use SoftDeletes;

    protected $table = 'tra_indicadores_marco';
    protected $primaryKey = 'id_indicador';
    protected $guarded = [];

    protected $fillable = [
        'marco_logico_id',
        'descripcion',
        'unidad_medida',
        'linea_base',
        'meta_total',
        'ponderacion',
        'medio_verificacion'
    ];

    public function metasAnuales()
    {
        return $this->hasMany(
            MetaAnual::class,
            'indicador_id',
            'id_indicador'
        );
    }
}
