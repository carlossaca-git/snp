<?php

namespace App\Models\Institucional;

use Illuminate\Database\Eloquent\Model;

// Importamos los modelos con los que se relaciona
use App\Models\Institucional\OrganizacionEstatal;
use App\Models\Inversion\ProyectoInversion;

class UnidadEjecutora extends Model
{
    // 1. Nombre de la tabla en la Base de Datos
    protected $table = 'cat_unidades_ejecutoras';

    // 2. Campos que permitimos llenar (Mass Assignment)
    protected $fillable = [
        'id_organizacion', // La llave foránea del "Papá"
        'nombre_unidad',
        'codigo_interno',
        'activo'
    ];

    // --- RELACIONES ---

    /**
     * Relación HACIA ARRIBA (Padre)
     * Una Unidad Ejecutora PERTENECE A una Organización Estatal.
     */
    public function organizacion()
    {
        return $this->belongsTo(OrganizacionEstatal::class, 'id_organizacion');
    }

    /**
     * Relación HACIA ABAJO (Hijos)
     * Una Unidad Ejecutora TIENE MUCHOS Proyectos de Inversión.
     */
    public function proyectos()
    {
        return $this->hasMany(ProyectoInversion::class, 'id_unidad_ejecutora');
    }
}
