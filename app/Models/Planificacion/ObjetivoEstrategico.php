<?php

namespace App\Models\Planificacion;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;
use App\Models\Catalogos\Ods;
Use App\Models\Catalogos\MetaNacional;
use App\Models\Institucional\OrganizacionEstatal;
use App\Models\Planificacion\AlineacionEstrategica;
use App\Models\Inversion\ProyectoInversion;

class ObjetivoEstrategico extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $table = 'cat_objetivo_estrategico';

    protected $primaryKey = 'id_objetivo_estrategico';

    protected $fillable = [
        'organizacion_id',
        'usuario_id',
        'plan_id',
        'codigo',
        'nombre',
        'descripcion',
        'tipo_objetivo',
        'unidad_responsable_id',
        'linea_base',
        'meta',
        'indicador',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'url_documento',
        'nombre_archivo',
    ];

    public $timestamps = true; // O false, dependiendo de tu tabla

    // --- RELACIONES ---

    // Relación Pertenece a una Institución
    public function organizacion()
    {
        return $this->belongsTo(OrganizacionEstatal::class, 'id_organizacion', 'id_organizacion');
    }


    //  Se alinea a un Objetivo Nacional (PND)
    public function metasNacionales()
    {
        return $this->belongsToMany(
            MetaNacional::class,
            'alineacion_estrategica',
            'objetivo_estrategico_id',
            'meta_nacional_id'
        );
    }
    // Dentro de la clase ObjetivoEstrategico
    public function alineacion()
    {
        //  alineación está en la tabla alineaciones_estrategicas
        return $this->hasOne(
            AlineacionEstrategica::class,
            'objetivo_estrategico_id',
            'id_objetivo_estrategico'
        );
    }


    public function ods()
    {

        return $this->belongsToMany(
            Ods::class,
            ///'piv_objetivo_ods',
            //'id_objetivo_estrategico',
            //'id_ods'
        );
    }
    //
    public function getInfoAlineacionAttribute()
    {
        // Preparamos la estructura
        return $this->metasNacionales->map(fn($m) => [
            'id'     => $m->id_meta_nacional,
            'codigo' => $m->codigo_meta,
            'descripcion' => $m->nombre_meta,
            'ods'    => $m->ods->map(fn($o) => [
                'numero' => $o->codigo ?? $o->id_ods,
                'nombre' => $o->nombre,
                'imagen' => $o->imagen ?? null,
                'color'  => $o->color_hex,
            ])
        ]);
    }
    public function proyectos()
    {
        return $this->hasMany(
            ProyectoInversion::class,
            'objetivo_estrategico_id',
            'id_objetivo_estrategico'
        );
    }
    public function getEjeAttribute()
    {
        // Intenta obtener la primera alineación
        $alineacion = $this->alineacion->first();

        // Si existe alineación -> meta -> obj nacional, devuelve el EJE. Si no, null.
        return $alineacion?->metaNacional?->objetivoNacional?->eje;
    }

    public function getDocumentoRespaldoAttribute()
    {
        // Prioridad: El objetivo tiene su propio documento específico
        if ($this->url_documento) {
            return $this->url_documento;
        }
        //
        return $this->plan->url_documento;
    }
}
