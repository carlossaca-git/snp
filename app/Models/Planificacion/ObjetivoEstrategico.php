<?php

namespace App\Models\Planificacion;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;
use App\Models\Catalogos\ObjetivoNacional;
use App\Models\Catalogos\Ods;
use App\Models\Catalogos\MetaNacional;
use App\Models\Institucional\OrganizacionEstatal;
use App\Models\Planificacion\AlineacionEstrategica;

class ObjetivoEstrategico extends Model
{
    use SoftDeletes;
    use Auditable;
    // Nombre de la tabla
    protected $table = 'cat_objetivo_estrategico';

    protected $primaryKey = 'id_objetivo_estrategico';

    // 3. ACTUALIZACIÓN DE CAMPOS (Mass Assignment)
    // Deben coincidir con los que usamos en el Controlador (store/create)
    protected $fillable = [
        'id_organizacion',
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
        'tipo_documento',
        'estado',
    ];

    public $timestamps = true; // O false, dependiendo de tu tabla

    // --- RELACIONES ---

    // Relación 1: Pertenece a una Institución
    public function organizacion()
    {
        return $this->belongsTo(OrganizacionEstatal::class, 'id_organizacion', 'id');
    }


    //  Se alinea a un Objetivo Nacional (PND)
    public function objetivosNacionales()
    {
        return $this->belongsToMany(
            ObjetivoNacional::class,
            'alineacion_estrategica',
            'objetivo_estrategico_id',
            'objetivo_nacional_id'
        );
    }
    public function metasNacionales()
    {
        return $this->belongsToMany(
            MetaNacional::class,
            'alineacion_estrategica',
            'id_objetivo_estrategico', // FK de este modelo en la intermedia
            'id_meta_nacional'         // FK del otro modelo en la intermedia
        );
    }
    // Dentro de la clase ObjetivoEstrategico
    public function alineacion()
    {
        //  alineación está en la tabla alineaciones_estrategicas
        // y se vincula por la columna objetivo_estrategico_id"
        return $this->hasOne(
            AlineacionEstrategica::class,
            'objetivo_estrategico_id',
            'id_objetivo_estrategico'
            );
    }
    // Relación 3: Muchos a Muchos con ODS (NUEVO)
    // Esta es la magia para que funcione el $objetivo->ods()->sync(...)
    public function ods()
    {

        return $this->belongsToMany(
            Ods::class,
            ///'piv_objetivo_ods',         // NOMBRE DE TU TABLA INTERMEDIA (Ver SQL abajo)
            //'id_objetivo_estrategico',  // Llave foránea en la pivote que apunta a este modelo
            //'id_ods'                    // Llave foránea en la pivote que apunta a ODS
        );
    }
}
