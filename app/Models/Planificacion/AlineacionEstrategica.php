<?php

namespace App\Models\Planificacion;

use App\Models\Catalogos\MetaNacional;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Catalogos\ObjetivoNacional;
use App\Models\Catalogos\Ods;
use App\Models\Institucional\OrganizacionEstatal;
use App\Models\Planificacion\ObjetivoEstrategico;


class AlineacionEstrategica extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'alineacion_estrategica';
    protected $primaryKey = 'id';

    protected $fillable = [
        'organizacion_id',
        'objetivo_estrategico_id',
        'objetivo_nacional_id',
        'ods_id',
        'meta_pnd_id',
        // Agregar 'user_id' o 'estado' si se usan
    ];

    // ==========================================
    // RELACIONES (Para reportes y listados)
    // ==========================================

    /**
     * Relación con la Organización (Ministerio, Secretaría, etc.)
     */
    public function organizacion()
    {
        return $this->belongsTo(OrganizacionEstatal::class, 'organizacion_id', 'id_organizacion');
        // Ajusta 'id' si la llave primaria en cat_organizacion_estatal es diferente
    }

    /**
     * Relación con el Objetivo Nacional (PND)
     */
    public function objetivoNacional()
    {
        return $this->belongsTo(ObjetivoNacional::class, 'objetivo_nacional_id', 'id_objetivo_nacional');
    }

    /**
     * Relación con el Objetivo Estratégico (Institucional)
     */
    public function objetivoEstrategico()
    {
        return $this->belongsTo(ObjetivoEstrategico::class, 'objetivo_estrategico_id', 'id_objetivo_estrategico');
    }

    /**
     * Relación con el ODS
     */
    public function ods()
    {
        return $this->belongsTo(Ods::class, 'ods_id', 'id_ods');
    }

    /**
     * Relación con la Meta Nacional (PND)
     */
    public function metaPnd()
    {

        return $this->belongsTo(MetaNacional::class, 'meta_pnd_id', 'id_meta_nacional');
    }



}
