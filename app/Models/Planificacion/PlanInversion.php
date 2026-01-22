<?php
namespace App\Models\Planificacion;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanInversion extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $table = 'tra_plan_inversion';

    protected $fillable = [
        'nombre_plan',
        'anio_fiscal',
        'estado'
    ];

    // Relación: Un Plan tiene muchos Programas
    public function programas()
    {
        return $this->hasMany(Programa::class, 'id_plan');
    }
}
