<?php

namespace App\Models\Institucional;

use Illuminate\Database\Eloquent\Model;

// Importamos los modelos con los que se relaciona
use App\Models\Institucional\OrganizacionEstatal;
use App\Models\Inversion\ProyectoInversion;

class UnidadEjecutora extends Model
{

    protected $table = 'cat_unidades_ejecutoras';
    protected $primaryKey = 'id';


    protected $fillable = [
        'organizacion_id',
        'nombre_unidad',
        'codigo_unidad',
        'nombre_responsable',
        'estado'
    ];


    /**
     *
     * Una Unidad Ejecutora PERTENECE A una Organización Estatal.
     */
    public function organizacion()
    {
        return $this->belongsTo(OrganizacionEstatal::class, 'id_organizacion');
    }

    /**
     *
     * Una Unidad Ejecutora TIENE MUCHOS Proyectos de Inversión.
     */
    public function proyectos()
    {
        return $this->hasMany(ProyectoInversion::class, 'id_unidad_ejecutora');
    }
}
