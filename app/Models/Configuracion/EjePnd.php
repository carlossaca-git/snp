<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;

use App\Models\Configuracion\O;

class EjePnd extends Model
{
    protected $table = 'cat_eje_pnd';
    protected $primaryKey = 'id_eje';
    public $timestamps = false; // Asumo que no tienes created_at/updated_at en catálogos

    protected $fillable = ['nombre_eje', 'descripcion', 'periodo_inicio', 'periodo_fin'];

    // Relación: Un Eje tiene muchos Objetivos Nacionales
    public function objetivosNacionales()
    {
        return $this->hasMany(ObjetivoNacional::class, 'id_eje', 'id_eje');
    }
}
