<?php

namespace App\Models\Institucional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Planificacion\AlineacionEstrategica;
use App\Models\Planificacion\ObjetivoEstrategico;

use App\Models\Inversion\ProyectoInversion;
use App\Models\Inversion\Programa;
use App\Models\Inversion\PlanInversion;


use App\Models\Institucional\TipoOrganizacion;
use App\Models\Catalogos\Subsector;
use App\Traits\Auditable;
use Symfony\Component\Mime\Email;

class OrganizacionEstatal extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;

    // Configuración de Tabla
    protected $table = 'cat_organizacion_estatal';
    protected $primaryKey = 'id_organizacion';
    public $incrementing = true;

    // Fechas (Para que funcione SoftDeletes)
    protected $dates = ['deleted_at'];

    // Asignación Masiva
    protected $fillable = [
        'id_tipo_org',
        'id_subsector',
        'id_padre',
        'codigo_oficial',
        'nom_organizacion',
        'nivel_gobierno',
        'siglas',
        'estado',
        'mision',
        'vision',
        'telefono',
        'logo',
        'web',
        'email',
        'ruc'
    ];

    /**
     * RELACIONES
     */

    // Relación: Una Organización pertenece a un Tipo
    public function tipo(): BelongsTo
    {
        // Asegúrate de que el modelo TipoOrganizacion exista en esa ruta
        return $this->belongsTo(TipoOrganizacion::class, 'id_tipo_org', 'id_tipo_org');
    }

    // Relación: Una Organización pertenece a un Subsector
    public function subsector(): BelongsTo
    {

        return $this->belongsTo(Subsector::class, 'id_subsector', 'id_subsector');
    }
    //Relacion: una organizacion debe tener un padre
    public function padre()
    {
        return $this->belongsTo(OrganizacionEstatal::class, 'id_padre');
    }
    // Relacion: una organizacion debe uno o muchos hijos
    public function hijos()
    {
        return $this->hasMany(OrganizacionEstatal::class, 'id_padre');
    }
    //Objetivos estrategicos
    //Relacion: Una organizacion puende tener muchos objetivos
    public function objetivos()
    {

        return $this->hasMany(ObjetivoEstrategico::class, 'id_organizacion', 'id_organizacion');
    }

    // Alineacion estrategica
    public function alineaciones()
    {

        return $this->hasMany(AlineacionEstrategica::class, 'organizacion_id', 'id_organizacion');
    }
    //Relacion: una organizacion puede tener muchos proyectos
    public function proyectos()
    {
        return $this->hasMany(ProyectoInversion::class, 'id_organizacion');
    }
    // Relacion: una organizacion puede tener muchos programas
    public function programas()
    {
        return $this->hasMany(Programa::class, 'id_organizacion');
    }
    //  Relacion: una organizacion puede tener muchas unidades ejecutoras
    public function unidadesEjecutoras()
    {
        return $this->hasMany(UnidadEjecutora::class, 'id_organizacion', 'id_organizacion');
    }
    // Relacion: una organizacion puede tener muchos planes de inversion
    public function planesInversion()
    {
        return $this->hasMany(
            PlanInversion::class,
            'organizacion_id',
            'id_organizacion'
        );
    }
    // Eventos del modelo
    protected static function boot()
    {
        parent::boot();

        //  Al eliminar
        static::deleting(function ($organizacion) {

            $organizacion->alineaciones()->delete();
            $organizacion->objetivos()->delete();
        });
    }
}
