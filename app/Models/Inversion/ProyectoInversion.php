<?php

namespace App\Models\Inversion;

use App\Models\Catalogos\EjePnd;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\Seguridad\User;

use App\Models\Inversion\Programa;
use App\Models\Inversion\ProyectoLocalizacion;
use App\Models\Inversion\Financiamiento;
use App\Models\Institucional\OrganizacionEstatal;
use App\Models\Institucional\UnidadEjecutora;
use App\Models\Planificacion\ObjetivoEstrategico;
use App\Models\Catalogos\MetaNacional;
use App\Models\Catalogos\IndicadorNacional;


class ProyectoInversion extends Model
{
    use Auditable;
    use HasFactory, SoftDeletes;

    protected $table = 'tra_proyecto_inversion';
    protected $primaryKey = 'id';

    protected $fillable = [
        'programa_id',
        'usuario_creacion_id',
        'avance_fisico_real',
        'cup',
        'nombre_proyecto',
        'unidad_ejecutora_id',
        'descripcion_diagnostico',
        'tipo_inversion',
        'fecha_inicio_estimada',
        'fecha_fin_estimada',
        'duracion_meses',
        'anio',
        'monto_total_inversion',
        'fuente_id',
        'monto_anio',
        'estado_dictamen',
        'organizacion_id',
        'objetivo_estrategico_id',
        'provincia',
        'canton',
        'parroquia',
        'estado',
        'documentos'
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
        return $this->belongsTo(
            OrganizacionEstatal::class,
            'id_organizacion',
            'id_organizacion'
        );
    }

    //Un proyecto se vincula a un objetivo estrategico
    public function objetivoEstrategico()
    {
        return $this->belongsTo(
            ObjetivoEstrategico::class,
            'objetivo_estrategico_id',
            'id_objetivo_estrategico'
        );
    }
//Un proyecto contrubuse a uno o mas indicadores

    public function indicadoresNacionales()
    {
        return $this->belongsToMany(
            IndicadorNacional::class,
            'tra_proyecto_indicador',
            'proyecto_id',
            'indicador_nacional_id'
        )
            ->withPivot('contribucion_proyecto')
            ->withTimestamps();
    }

    // Alineación Nacional
    public function metaNacional()
    {
        return $this->belongsTo(MetaNacional::class, 'meta_nacional_id');
    }

    // 3. Obtener ODS a través de la Meta (Automático)
    public function getOdsAttribute()
    {
        // Si no hay meta, devolvemos colección vacía
        return $this->metaNacional ? $this->metaNacional->ods : collect([]);
    }

    // Un Proyecto pertenece a un Programa
    public function programa()
    {
        return $this->belongsTo(Programa::class, 'programa_id');
    }
    // Un Proyecto tiene una localización
    public function localizacion()
    {
        return $this->hasOne(ProyectoLocalizacion::class, 'id_proyecto', 'id');
    }
    //  Un proyecto tiene muchos registros de financiamiento (uno por cada año)
    public function financiamientos()
    {
        return $this->hasMany(Financiamiento::class, 'id_proyecto', 'id');
    }
    // Un proyecto pertenece a una Unidad Ejecutora
    public function unidadEjecutora()
    {
        // Modelo Padre
        return $this->belongsTo(UnidadEjecutora::class, 'id_unidad_ejecutora');
    }
    public function documentos()
    {
        return $this->hasMany(DocumentoProyecto::class, 'id_proyecto');
    }
    /**
     * El booted filtro de seguridad
     */
    protected static function booted()
    {
        static::addGlobalScope('organizacion_actual', function (Builder $builder) {

            // Solo aplicamos filtro si hay alguien logueado
            if (Auth::check()) {

                /** @var User $user */
                $user = Auth::user();

                // Si es SUPER ADMIN no hacemos nada
                if ($user->tieneRol('SUPER_ADMIN')) {
                    return;
                }

                // Si es un usuario normal Admin TI, Jefe, Técnico,
                $builder->where('organizacion_id', $user->id_organizacion);
            }
        });
    }
    // Relación para obtener solo el Propósito
    public function proposito()
    {
        return $this->hasOne(MarcoLogico::class, 'proyecto_id', 'id')
            ->where('nivel', 'PROPOSITO');
    }

    // Relación para obtener solo los Componentes
    public function componentes()
    {
        return $this->hasMany(MarcoLogico::class, 'proyecto_id', 'id')
            ->where('nivel', 'COMPONENTE');
    }

    // Relación general todo el marco lógico sin filtrar
    public function marcoLogico()
    {
        return $this->hasMany(MarcoLogico::class, 'proyecto_id', 'id');
    }

    // Calcula el avance real del proyecto basado en las actividades y sus ponderaciones
    public function getAvanceRealAttribute()
    {
      return $this->avance_fisico_real ?? 0;
    }
    public function getEjeAttribute()
    {
        //  Obtenemos el Objetivo Estratégico a través del Programa
        $objetivo = $this->programa?->objetivoE;
        return $objetivo?->eje;
    }
}
