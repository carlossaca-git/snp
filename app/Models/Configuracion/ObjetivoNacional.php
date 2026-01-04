<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;
use App\Models\Estrategico\ObjetivoEstrategico;
use Http\Controllers\Configuracion\ObjetivoEstrategicoController;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObjetivoNacional extends Model
{
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
    public function relEje()
    {
        return $this->belongsTo(EjePnd::class, 'id_eje', 'id_eje');
    }

    // Relación: Tiene muchas Metas
    public function metaPND()
    {
        return $this->hasMany(MetaPnd::class, 'meta_pnd_id', 'id_meta_pnd');
    }

    // Relación: Tiene muchos Objetivos Estratégicos
    public function objetivosEstrategicos()
    {
        return $this->hasMany(ObjetivoEstrategico::class, 'id_objetivo_nacional', 'id_objetivo_nacional');
    }
    //Objetivo nacional
    public function objetivoNacional()
    {
        return $this->belongsTo(ObjetivoNacional::class, 'objetivo_nacional_id', 'id_objetivo_nacional');
    }

    public function ods()
    {
        // Esto asume que ObjetivoEstrategico tiene la relación 'ods' definida
       // return $this->hasManyThrough(
       //     Ods::class,
       //     ObjetivoEstrategico::class,
       //     'id_objetivo_nacional',    // FK en ObjetivoEstrategico
       //     'id_ods',                  // Esto requeriría una lógica de pivote más compleja
       //     'id_objetivo_nacional',    // Local key en ObjetivoNacional
       //     'id_objetivo_estrategico'  // Local key en ObjetivoEstrategico
        //);
        return $this->belongsTo(Ods::class, 'ods_ods', 'id_ods');
    }
}
