<?php

namespace App\Models\Inversion;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inversion\ProyectoInversion;
use Illuminate\Database\Eloquent\SoftDeletes;

class Financiamiento extends Model
{
    use SoftDeletes;
    protected $table = 'tra_financiamiento';

    protected $fillable = [
        'id_proyecto',
        'anio',
        'fuente_financiamiento',
        'monto'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    // Relación inversa: Un registro de financiamiento pertenece a un proyecto
    public function proyecto()
    {
        return $this->belongsTo(ProyectoInversion::class, 'id_proyecto');
    }
    // Relación: Un registro de financiamiento pertenece a una Fuente
    public function fuente()
    {
        return $this->belongsTo(FuenteFinanciamiento::class, 'id_fuente', 'id_fuente');
    }
}
