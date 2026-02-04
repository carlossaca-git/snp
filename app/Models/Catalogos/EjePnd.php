<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

use App\Models\Catalogos\ObjetivoNacional;
use Illuminate\Database\Eloquent\SoftDeletes;

class EjePnd extends Model
{
    use Auditable;
    use SoftDeletes;
    protected $table = 'cat_eje_pnd';
    protected $primaryKey = 'id_eje';


    protected $fillable = [
        'id_plan',
        'nombre_eje',
        'descripcion',
        'estado',
        'url_documento'
    ];

    // Relación: Un Eje tiene muchos Objetivos Nacionales
    public function objetivosNacionales()
    {
        return $this->hasMany(ObjetivoNacional::class, 'id_eje', 'id_eje');
    }
    public function plan()
    {
        return $this->belongsTo(PlanNacional::class, 'id_plan', 'id_plan');
    }
}
