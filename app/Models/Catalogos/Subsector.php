<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;
use App\Models\Catalogos\Sector;

class Subsector extends Model
{
    protected $table = 'cat_subsector';
    protected $primaryKey = 'id_subsector';

    // Relación con el padre
    public function sector() {

        return $this->belongsTo(Sector::class, 'id_sector','id_sector');
    }
}
