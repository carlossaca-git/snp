<?php

namespace App\Models\Inversion;

use App\Models\Catalogos\EjePnd;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


use App\Models\Planificacion\Programa;
use App\Models\Inversion\ProyectoLocalizacion;
use App\Models\Inversion\Financiamiento;
use App\Models\Institucional\OrganizacionEstatal;
use App\Models\Institucional\UnidadEjecutora;
use App\Models\Planificacion\ObjetivoEstrategico;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\Seguridad\User;


class ProyectoInversion extends Model
{
    use Auditable;
    use HasFactory, SoftDeletes;

    protected $table = 'tra_proyecto_inversion';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_programa',
        'id_usuario_creacion',
        'avance_fisico_real',
        'cup',
        'nombre_proyecto',
        'id_unidad_ejecutora',
        'descripcion_diagnostico',
        'tipo_inversion',
        'fecha_inicio_estimada',
        'fecha_fin_estimada',
        'duracion_meses',
        'anio',
        'monto_total_inversion',
        'id_fuente',
        'monto_anio',
        'estado_dictamen',
        'id_organizacion',
        'id_objetivo_estrategico',
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

    // Un Proyecto se alinea a un Objetivo Nacional
    public function objetivo()
    {
        return $this->belongsTo(
            ObjetivoEstrategico::class,
            'id_objetivo_estrategico',
            'id_objetivo_estrategico'
        );
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'id_programa');
    }

    public function localizacion()
    {
        return $this->hasOne(ProyectoLocalizacion::class, 'id_proyecto', 'id');
    }
    //  Un proyecto tiene muchos registros de financiamiento (uno por cada año)
    public function financiamientos()
    {
        return $this->hasMany(Financiamiento::class, 'id_proyecto', 'id');
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
                $builder->where('id_organizacion', $user->id_organizacion);
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
        // Buscamos solo las ACTIVIDADES (Nivel inferior) de este proyecto
        $actividades = $this->marcoLogico->where('nivel', 'ACTIVIDAD');

        // Si no hay actividades, el avance es 0
        if ($actividades->isEmpty()) return 0;
        $totalPonderacion = $actividades->sum('ponderacion');

        if ($totalPonderacion == 0) return 0;

        $sumaPonderada = $actividades->sum(function ($act) {
            return $act->avance_actual * $act->ponderacion;
        });

        return $sumaPonderada / $totalPonderacion;
    }
    public function getEjeAttribute()
    {
        //  Obtenemos el Objetivo Estratégico a través del Programa
        $objetivo = $this->programa?->objetivoE;
        return $objetivo?->eje;
    }
}
