<?php

namespace App\Models\Inversion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Catalogos\Actividad;
use App\Models\Seguridad\User;
use App\Traits\Auditable;

class AvanceActividad extends Model
{
    use HasFactory, SoftDeletes;
    use Auditable;
    // Nombre de la tabla (opcional si sigue la convención en plural)
    protected $table = 'tra_avances_actividad';
    protected $primaryKey = 'id_avance';


    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'marco_logico_id',
        'fecha_reporte',
        'avance_reportado',
        'avance_total_acumulado',
        'observacion',
        'usuario_id',
    ];


    /**
     * Relación con el Usuario que registró el avance.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
