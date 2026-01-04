<?php

namespace App\Models\Estrategico;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Estrategico\AlineacionEstrategica;
use App\Models\Estrategico\ObjetivoEstrategico;

use App\Models\Inversion\ProyectoInversion;
use App\Models\Inversion\Programa;

// Importamos los modelos de los catálogos para las relaciones
use App\Models\Estrategico\TipoOrganizacion;
use App\Models\Estrategico\Subsector;
use Symfony\Component\Mime\Email;

class OrganizacionEstatal extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Configuración de Tabla
    protected $table = 'cat_organizacion_estatal';
    protected $primaryKey = 'id_organizacion';
    public $incrementing = true; // Por defecto es true, pero está bien dejarlo explícito

    // 2. Fechas (Para que funcione SoftDeletes)
    protected $dates = ['deleted_at'];

    // 3. Asignación Masiva
    protected $fillable = [
        'id_tipo_org',
        'id_subsector',
        'codigo_oficial',
        'nom_organizacion',
        'nivel_gobierno', // Ej: 1=Central, 2=GAD...
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

    // Relación: Una Organización pertenece a un Tipo (Ej: Ministerio, GAD)
    public function tipo(): BelongsTo
    {
        // Asegúrate de que el modelo TipoOrganizacion exista en esa ruta
        return $this->belongsTo(TipoOrganizacion::class, 'id_tipo_org', 'id_tipo_org');
    }

    // Relación: Una Organización pertenece a un Subsector
    public function subsector(): BelongsTo
    {
        // Asegúrate de que el modelo Subsector exista en esa ruta
        return $this->belongsTo(Subsector::class, 'id_subsector', 'id_subsector');
    }
    //Objetivos estrategicos

    public function objetivos()
    {
        // Ajusta 'id_organizacion' si tu llave foránea tiene otro nombre
        return $this->hasMany(ObjetivoEstrategico::class, 'id_organizacion', 'id_organizacion');
    }

    // En OrganizacionEstatal.php

    public function alineaciones()
    {
        // CAMBIO: En vez de Alineacion::class, usa tu modelo real
        return $this->hasMany(AlineacionEstrategica::class, 'organizacion_id', 'id_organizacion');
    }
    public function proyectos()
    {
        return $this->hasMany(ProyectoInversion::class, 'id_organizacion');
    }
    public function programas()
    {
        return $this->hasMany(Programa::class, 'id_organizacion');
    }



    protected static function boot()
    {
        parent::boot();

        // Evento: Al eliminar (deleting)
        static::deleting(function ($organizacion) {

            //Borado logico de alineacions
            $organizacion->alineaciones()->delete();
            $organizacion->objetivos()->delete();
        });
    }
}
