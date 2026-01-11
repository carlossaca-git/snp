<?php

namespace App\Models\Inversion;

use App\Models\Catalogos\EjePnd;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Importaciones correctas según tus módulos
use App\Models\Planificacion\Programa;
use App\Models\Inversion\ProyectoLocalizacion;
use App\Models\Inversion\Financiamiento;
use App\Models\Institucional\OrganizacionEstatal;
use App\Models\Catalogos\ObjetivoNacional; // Módulo 1
use App\Models\Institucional\UnidadEjecutora;
use App\Traits\Auditable;

class ProyectoInversion extends Model
{   use Auditable;
    use HasFactory, SoftDeletes;

    protected $table = 'tra_proyecto_inversion';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_programa',
        'cup',
        'nombre_proyecto',
        'id_unidad_ejecutora',
        'descripcion_diagnostico',
        'tipo_inversion',
        'fecha_inicio_estimada',
        'fecha_fin_estimada',
        'duracion_meses',
        'monto_total_inversion',
        'estado_dictamen',
        'id_organizacion',
        'objetivo_nacional',
        'provincia',
        'canton',
        'parroquia',
        'estado'
    ];
    protected $casts = [
        'fecha_inicio_estimada' => 'date',
        'fecha_fin_estimada'    => 'date',
        'monto_total_inversion' => 'decimal:2',
    ];

    /**
     * RELACIONES DIRECTAS
     */

    // Un Proyecto pertenece a una Organización (Ministerio/GAD)
    public function organizacion()
    {
        return $this->belongsTo(OrganizacionEstatal::class, 'id_organizacion', 'id_organizacion');
    }

    // Un Proyecto se alinea a un Objetivo Nacional
    public function objetivo()
    {
        return $this->belongsTo(ObjetivoNacional::class, 'objetivo_nacional', 'id_objetivo_nacional');
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'id_programa');
    }

    public function localizacion()
    {
        return $this->hasOne(ProyectoLocalizacion::class, 'id_proyecto', 'id');
    }
    // Relación: Un proyecto tiene muchos registros de financiamiento (uno por cada año)
    public function financiamientos()
    {
        return $this->hasMany(Financiamiento::class, 'id_proyecto', 'id');
    }
    public function eje()
    {
        // Sintaxis: belongsTo(Modelo, 'FK_en_esta_tabla', 'PK_en_la_otra_tabla')
        return $this->belongsTo(EjePnd::class, 'id_eje', 'id_eje');
    }
    public function unidadEjecutora()
    {
        // Modelo Padre
        // La columna en ESTA tabla que sirve de unión
        return $this->belongsTo(UnidadEjecutora::class, 'id_unidad_ejecutora');
    }
    public function documentos()
{
    return $this->hasMany(DocumentoProyecto::class, 'id_proyecto');
}
}
