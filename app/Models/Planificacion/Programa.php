<?php

namespace App\Models\Planificacion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Institucional\OrganizacionEstatal;
use App\Models\Inversion\ProyectoInversion;
use App\Models\Inversion\FuenteFinanciamiento;

class Programa extends Model
{
    use SoftDeletes;

    protected $table = 'tra_programa';
    protected $primaryKey = 'id';

    protected $fillable = [
        'codigo_cup',
        'nombre_programa',
        'descripcion',
        'anio_inicio',
        'anio_fin',
        'monto_planificado',
        'id_fuente',
        'id_objetivo_estrategico',
        'cobertura',
        'estado',
        'id_organizacion'
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
    public function objetivoE()
    {
        return $this->belongsTo(ObjetivoEstrategico::class, 'id_objetivo_estrategico', 'id_objetivo_estrategico');
    }
    public function fuenteFinanciamiento()
    {
        // El programa pertenece a una fuente del catálogo
        return $this->belongsTo(FuenteFinanciamiento::class, 'id_fuente');
    }
}
