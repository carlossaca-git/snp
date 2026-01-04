<?php

namespace App\Models\Estrategico;

use Illuminate\Database\Eloquent\Model;
use App\Models\Estrategico\Macrosector;



class Sector extends Model
{
    protected $table = 'cat_sector'; // Tu nombre real de tabla
    protected $primaryKey = 'id_sector'; // Tu ID personalizado

    // Si tus tablas no tienen created_at/updated_at, pon esto en false:
     public $timestamps = true;

    // Relación inversa (opcional pero útil)
    public function macrosector() {
        return $this->belongsTo(Macrosector::class, 'id_macrosector');
    }
}
