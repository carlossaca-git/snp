<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;

use App\Models\Catalogos\ObjetivoNacional;
use Illuminate\Database\Eloquent\SoftDeletes;

class EjePnd extends Model
{
    use SoftDeletes;
    protected $table = 'cat_eje_pnd';
    protected $primaryKey = 'id_eje';
    public $timestamps = false; // Asumo que no tienes created_at/updated_at en catálogos

    protected $fillable = [
        'id_plan_nacional',
        'nombre_eje',
        'descripcion',
        'estado',
        'url_documento',
        'periodo_inicio',
        'periodo_fin'
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
