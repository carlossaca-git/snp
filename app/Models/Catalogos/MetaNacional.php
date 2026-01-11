<?php

namespace App\Models\Catalogos;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class MetaNacional extends Model
{
    use Auditable;
    use SoftDeletes;
    protected $table = 'cat_meta_nacional';
    protected $primaryKey = 'id_meta_nacional';
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'id_objetivo_nacional',
        'codigo_meta',
        'nombre_meta',
        'descripcion_meta',
        'url_documento',
        'nombre_indicador',
        'unidad_medida',
        'linea_base',
        'meta_valor',
        'valor_actual',
        'estado'
    ];

    /**
     * Relación Inversa: Pertenece a un Objetivo Nacional
     */
    public function objetivoNacional()
    {
        // Asegúrate que el segundo parámetro coincida con tu columna FK en la base de datos
        return $this->belongsTo(ObjetivoNacional::class, 'id_objetivo_nacional', 'id_objetivo_nacional');
    }

    /**
     * (Opcional)  mantener la tabla de Indicadores separada
     */

    public function indicadores()
    {
        return $this->hasMany(Indicador::class, 'id_meta_pnd', 'id_meta_pnd');
    }
    // app/Models/MetaPnd.php

    public function ods()
    {
        // Relación de muchos a muchos con el modelo Ods
        // Asegúrate de que los nombres de las llaves coincidan con tu tabla
        return $this->belongsToMany(Ods::class, 'alineacion_metas_ods', 'id_meta_nacional', 'id_ods');
    }
}
