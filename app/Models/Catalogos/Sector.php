<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;
use App\Models\Catalogos\Macrosector;



class Sector extends Model
{
    protected $table = 'cat_sector';
    protected $primaryKey = 'id_sector';

    protected $fillable = [
        'id_macrosector',
        'nombre_sector',
        'siglas_sector',
        'descripcion',
        'estado'
    ];
     public $timestamps = true;

    // Relación inversa
    public function macrosector() {
        return $this->belongsTo(Macrosector::class, 'id_macrosector');
    }
}
