<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;
use App\Models\Estrategico\ObjetivoEstrategico;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ods extends Model
{
    use Auditable;
    use SoftDeletes;
    // Nombre de la tabla en base de datos
    protected $table = 'cat_ods';

    // Llave primaria
    protected $primaryKey = 'id_ods';
    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'pilar',
        'estado',
        'color_hex'
    ];
    //Relacion: muchos ods pertenecen a muchas metas
    public function metasNacionales()
    {
        return $this->belongsToMany(
            MetaNacional::class,
            'alineacion_metas_ods',
            'id_ods',
            'id_meta_nacional',
            'id_ods',
            'id_meta_nacional'
        );
    }
    //Calculo de contribucion
    public function getAvancePromedioAttribute()
    {
        if ($this->metasNacionales->isEmpty()) {
            return 0;
        }
        $sumaAvances = 0;
        foreach ($this->metasNacionales as $meta) {
            $sumaAvances += $meta->avance_actual;
        }
        return $sumaAvances / $this->metasNacionales->count();
    }
}
