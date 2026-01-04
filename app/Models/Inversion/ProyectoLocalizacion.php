<?php

namespace App\Models\Inversion;

use App\Models\Inversion\ProyectoInversion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProyectoLocalizacion extends Model
{
    use SoftDeletes;
    protected $table = 'tra_proyecto_localizacion';

    // No olvides que estos campos deben coincidir con tu migración
    protected $fillable = [
        'id_proyecto',
        'codigo_provincia',
        'codigo_canton',
        'codigo_parroquia'
    ];

    public function proyecto()
    {
        return $this->belongsTo(ProyectoInversion::class, 'id_proyecto');
    }
}
