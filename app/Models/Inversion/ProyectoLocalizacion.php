<?php

namespace App\Models\Inversion;

use App\Models\Inversion\ProyectoInversion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class ProyectoLocalizacion extends Model
{
    use SoftDeletes;
    use Auditable;
    protected $table = 'tra_proyecto_localizacion';
    protected $primaryKey = 'id';


    // Estos campos deben coincidir con tu migración
    protected $fillable = [
        'id_proyecto',
        'provincia',
        'canton',
        'parroquia'
    ];


    public function proyecto()
    {
        return $this->belongsTo(ProyectoInversion::class, 'id_proyecto');
    }
}
