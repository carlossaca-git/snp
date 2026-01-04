<?php

namespace App\Models\Estrategico;

use Illuminate\Database\Eloquent\Model;
use App\Models\Estrategico\ObjetivoNacional;
use App\Models\Estrategico\OrganizacionEstatal;
use App\Models\Estrategico\Ods;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObjetivoEstrategico extends Model
{
    use SoftDeletes;
    // Nombre de la tabla
    protected $table = 'cat_objetivo_estrategico';

    // 2. CORRECCIÓN DE TYPO: Tenías 'id_ojetivo', corregido a 'id_objetivo'
    protected $primaryKey = 'id_objetivo_estrategico';

    // 3. ACTUALIZACIÓN DE CAMPOS (Mass Assignment)
    // Deben coincidir con los que usamos en el Controlador (store/create)
    protected $fillable = [
        'id_organizacion',
        'codigo',
        'nombre',
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
    public function objetivoNacional()
    {
        // belongsTo(Modelo, 'foreign_key_local', 'primary_key_padre')
        return $this->belongsTo(ObjetivoNacional::class, 'id_objetivo_nacional', 'id_objetivo_nacional');
    }
    // Dentro de la clase ObjetivoEstrategico
public function alineacion()
{
    //  alineación está en la tabla alineaciones_estrategicas
    // y se vincula por la columna objetivo_estrategico_id"
    return $this->hasOne(AlineacionEstrategica::class, 'objetivo_estrategico_id','id_objetivo_estrategico');
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
