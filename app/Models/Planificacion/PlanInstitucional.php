<?php

namespace App\Models\Planificacion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class PlanInstitucional extends Model
{
use SoftDeletes;
use Auditable;

    protected $table = 'cat_planes_institucionales';
    protected $primaryKey = 'id_plan';

    protected $fillable = [
        'id_organizacion',
        'nombre_plan',
        'tipo_plan',
        'anio_inicio',
        'anio_fin',
        'estado'
    ];

    // Un Plan tiene muchos Objetivos Estratégicos
    public function objetivosEstrategicos()
    {
        return $this->hasMany(ObjetivoEstrategico::class, 'id_plan', 'id_plan');
    }
}
