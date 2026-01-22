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
use App\Models\Seguridad\User;
use App\Traits\Auditable;

class AlineacionEstrategica extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;

    protected $table = 'alineacion_estrategica';
    protected $primaryKey = 'id';

    protected $fillable = [
        'organizacion_id',
        'objetivo_estrategico_id',
        'meta_nacional_id',

    ];
    /**
     * Relación con la Organización
     */
    public function usuario(){
        return $this-> belongsTo(User::class, 'usuario_id', 'id_usuario');
    }
    public function organizacion()
    {
        return $this->belongsTo(OrganizacionEstatal::class, 'organizacion_id', 'id_organizacion');

    }

    /**
     * Relación con el Objetivo Nacional (PND)
     */
    public function metaNacional()
    {
        return $this->belongsTo(MetaNacional::class, 'meta_nacional_id', 'id_meta_nacional');
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
