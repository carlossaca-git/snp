<?php

namespace App\Models\Inversion;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inversion\IndicadorMarco;
use App\Models\Inversion\ProyectoInversion;
use App\Models\Inversion\AvanceActividad;

class MarcoLogico extends Model
{

    use SoftDeletes;
    use Auditable;

    protected $table = 'tra_marco_logico';
    protected $primaryKey = 'id_marco_logico';
    protected $guarded = [];

    // Usamos hasOne porque asumimos que solo tiene 1 principal para simplificar
    public function indicador()
    {
        return $this->hasOne(IndicadorMarco::class, 'marco_logico_id', 'id_marco_logico');
    }

    // Relación para hijos (Actividades)
    public function actividades()
    {
        return $this->hasMany(MarcoLogico::class, 'padre_id', 'id_marco_logico')
            ->where('nivel', 'ACTIVIDAD');
    }
    public function proyecto()
    {
        return $this->belongsTo(ProyectoInversion::class, 'proyecto_id', 'id_proyecto');
    }
    //
    public function historialAvances()
    {
        // Ajusta los nombres si tu modelo de avance se llama diferente (ej: AvanceActividad)
        // y asegúrate que la tabla sea 'tra_avances_actividad'
        return $this->hasMany(AvanceActividad::class, 'marco_logico_id', 'id_marco_logico');
    }
}
