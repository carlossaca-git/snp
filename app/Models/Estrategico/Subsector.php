<?php

namespace App\Models\Estrategico;

use Illuminate\Database\Eloquent\Model;
use App\Models\Estrategico\Sector;

class Subsector extends Model
{
    protected $table = 'cat_subsector';
    protected $primaryKey = 'id_subsector';

    // Relación con el padre
    public function sector() {

        return $this->belongsTo(Sector::class, 'id_sector','id_sector');
    }
}
