<?php

namespace App\Models\Inversion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Institucional\OrganizacionEstatal;
use App\Models\Inversion\ProyectoInversion;
use App\Models\Inversion\FuenteFinanciamiento;
use App\Models\Planificacion\ObjetivoEstrategico;

use App\Traits\Auditable;

class Programa extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $table = 'tra_programa';
    protected $primaryKey = 'id';

    protected $fillable = [
        'plan_id',
        'codigo_cup',
        'nombre_programa',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'monto_planificado',
        'fuente_id',
        'objetivo_estrategico_id',
        'cobertura',
        'estado',
        'nombre_archivo',
        'url_documento',
        'sector',
        'monto_asignado',
        'total_devengado',
        'estado'
    ];

    // Relación: Pertenece a un Plan
    public function plan()
    {
        return $this->belongsTo(PlanInversion::class, 'plan_id');
    }

    // Relación: Pertenece a una Organización
    public function organizacion()
    {
        return $this->belongsTo(OrganizacionEstatal::class, 'id_organizacion');
    }

    // Relación: Un Programa tiene muchos Proyectos

    public function proyectos()
    {
        return $this->hasMany(ProyectoInversion::class, 'programa_id');
    }
    public function objetivoE()
    {
        return $this->belongsTo(ObjetivoEstrategico::class, 'objetivo_estrategico_id', 'id_objetivo_estrategico');
    }
    public function fuenteFinanciamiento()
    {
        // El programa pertenece a una fuente del catálogo
        return $this->belongsTo(FuenteFinanciamiento::class, 'fuente_id');
    }
    // Accesor para obtener el avance físico real del programa
    public function getAvanceFisicoRealAttribute()
    {
        // Calculamos el avance físico real como el promedio de los proyectos asociados
        $proyectos = $this->proyectos;
        if ($proyectos->isEmpty()) {
            return 0;
        }
        $totalAvance = $proyectos->sum('avance_fisico_real');
        return round($totalAvance / $proyectos->count(), 2);
    }
    // Accesor para obtener el total devengado del programa
    public function getTotalDevengadoAttribute()
    {
        // Calculamos el total devengado sumando los montos devengados de los proyectos asociados
        $proyectos = $this->proyectos;
        if ($proyectos->isEmpty()) {
            return 0;
        }
        return ($this->total_devengado / $this->monto_asignado) * 100;
    }
}
