<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Model_entidad_publica extends Model
{

    use HasFactory;

    protected $table = 'cat_institucion';
        protected $fillable = [
        'id_subsector',
        'codigo_oficial',
        'nom_organizacion',
        'nivel_gobierno',
        'estado',
        'fecha_creacion',
        'fecha_actualizacion',
    ];
    public $timestamps = false;
    public function cat_subsector(){
        return $this->belongsTo('app\Models\Model_subsector');
    }
}
