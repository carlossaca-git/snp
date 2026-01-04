<?php

namespace App\Models\Inversion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Estrategico\OrganizacionEstatal;
use App\Models\Inversion\ProyectoInversios;

class Programa extends Model
{
    use SoftDeletes;

    protected $table = 'tra_programa';

    protected $fillable = [
        'id_plan',
        'id_organizacion',
        'codigo_programa',
        'nombre_programa'
    ];

    // Relación: Pertenece a un Plan
    public function plan()
    {
        return $this->belongsTo(PlanInversion::class, 'id_plan');
    }

    // Relación: Pertenece a una Organización
    public function organizacion()
    {
        return $this->belongsTo(OrganizacionEstatal::class, 'id_organizacion');
    }

    // Relación: Un Programa tiene muchos Proyectos

    public function proyectos()
    {
        return $this->hasMany(ProyectoInversion::class, 'id_programa');
    }
}
