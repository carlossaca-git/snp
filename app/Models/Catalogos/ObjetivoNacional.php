<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;
use App\Models\Catalogos\MetaNacional;
use App\Models\Planificacion\ObjetivoEstrategico;
use App\Traits\Auditable;
use Http\Controllers\Configuracion\ObjetivoEstrategicoController;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObjetivoNacional extends Model
{
    use Auditable;
    use SoftDeletes;
    protected $table = 'cat_objetivo_nacional';
    protected $primaryKey = 'id_objetivo_nacional';

    // Si en SQL pusiste created_at/updated_at déjalo en true, si no, false
    public $timestamps = true;


    protected $fillable = [
        'id_eje',
        'codigo_objetivo',
        'descripcion_objetivo',
        'estado',
        'ods_id',
        'meta_pnd_id'
    ];

    // Relación Inversa: Pertenece a un Eje
    public function eje()
    {
        return $this->belongsTo(EjePnd::class,
            'id_eje',
            'id_eje'
            );
    }

    // Relación: Tiene muchas Metas
    public function metaPND()
    {
        return $this->hasMany(MetaNacional::class, 'meta_pnd_id', 'id_meta_pnd');
    }

    // Relación: Tiene muchos Objetivos Estratégicos
    public function objetivosEstrategicos()
    {
        return $this->hasMany(ObjetivoEstrategico::class,
        'id_objetivo_nacional',
        'id_objetivo_nacional');
    }
    //Objetivo nacional
    public function objetivosNacionales()
    {
        return $this->belongsTo(ObjetivoNacional::class,
        'objetivo_nacional_id',
        'id_objetivo_nacional');
    }

    public function ods()
    {
        return $this->belongsTo(Ods::class, 'ods_ods', 'id_ods');
    }
}
