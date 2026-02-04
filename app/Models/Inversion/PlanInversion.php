<?php

namespace App\Models\Inversion;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Inversion\Programa;
use App\Models\Institucional\OrganizacionEstatal;
use Carbon\Carbon;

class PlanInversion extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $table = 'tra_plan_inversion';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'organizacion_id',
        'anio',
        'monto_total',
        'descripcion',
        'ruta_documento',
        'nombre_archivo',
        'numero_resolucion',
        'version',
        'estado'
    ];

    // Relación: Un Plan tiene muchos Programas
    public function programas()
    {
        return $this->hasMany(Programa::class, 'plan_id');
    }
    // Relación: Un Plan pertenece a una Organización Estatal
    public function organizacion()
    {
        return $this->belongsTo(
            OrganizacionEstatal::class,
            'organizacion_id',
            'id_organizacion'
        );
    }
    // Accesor para obtener la fecha de inicio del año fiscal
    public function getFechaInicioFiscalAttribute()
    {
        return Carbon::createFromDate($this->anio, 1, 1);
    }
    // Accesor para obtener la fecha de fin del año fiscal
    public function getFechaFinFiscalAttribute()
    {
        return Carbon::createFromDate($this->anio, 12, 31);
    }
}
