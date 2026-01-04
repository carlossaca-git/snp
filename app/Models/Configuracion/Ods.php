<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;
use App\Models\Estrategico\ObjetivoEstrategico;

class Ods extends Model
{
    // Nombre de la tabla en base de datos
    protected $table = 'cat_ods';

    // Llave primaria
    protected $primaryKey = 'id_ods';

    // Campos que se pueden llenar masivamente
    protected $fillable = ['numero', 'nombre_corto', 'descripcion', 'color_hex'];

    public $timestamps = false; // Generalmente los catálogos fijos no necesitan timestamps

    /**
     * Relación Inversa: Muchos a Muchos
     * Permite saber: "¿Qué objetivos estratégicos apuntan al ODS 4?"
     */
    public function objetivosEstrategicos()
    {
        //return $this->belongsToMany(
        //    ObjetivoEstrategico::class,
        //    'piv_objetivo_ods',        // Nombre de la tabla intermedia
        //    'id_ods',                  // FK de ESTE modelo en la pivote
        //    'id_objetivo_estrategico'  // FK del OTRO modelo en la pivote
        //);
    }
}
