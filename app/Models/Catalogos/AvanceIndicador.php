<?php

namespace App\Models\Catalogos;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Inversion\MarcoLogico;

class AvanceIndicador extends Model
{
    use HasFactory, SoftDeletes;
    use Auditable;

    protected $table = 'tra_indicador_avances';
    protected $primaryKey = 'id_avance';

    protected $fillable = [
        'id_indicador',
        'fecha_reporte',
        'valor_logrado',
        'evidencia_path',
        'observaciones',
        'id_usuario_registro'
    ];

    public function indicador()
    {
        return $this->belongsTo(IndicadorNacional::class, 'id_indicador');
    }
    public function marcoLogico()
    {
        return $this->belongsTo(MarcoLogico::class, 'marco_logico_id', 'id_marco_logico');
    }
}
