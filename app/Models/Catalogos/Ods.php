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



    /**
     * Relación Inversa: Muchos a Muchos
     * Que objetivos estratégicos apuntan al ODS 4
     */
    public function objetivosEstrategicos()
    {
        //return $this->belongsToMany(
        //    ObjetivoEstrategico::class,
        //    'piv_objetivo_ods',
        //    'id_ods',
        //    'id_objetivo_estrategico'
        //);
    }
}
