<?php

namespace App\Models\Inversion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inversion\IndicadorMarco;

class MetaAnual extends Model
{
    use Auditable;
    use SoftDeletes;
    use HasFactory;

    protected $table = 'tra_metas_anuales';
    protected $primaryKey = 'id_meta_anual';
    protected $fillable = [
        'indicador_id',
        'anio',
        'valor_meta',
        'meta_ponderada'
    ];

    protected $casts = [
        'anio' => 'integer',
        'valor_meta' => 'decimal:2',
        'meta_ponderada' => 'decimal:2',
    ];
    /**
     * Relación Inversa: Una meta anual pertenece a un Indicador del Marco Lógico.
     */
    public function indicador()
    {
        return $this->belongsTo(IndicadorMarco::class, 'indicador_id', 'id_indicador');
    }
}
