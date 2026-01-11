<?php

namespace App\Models\Inversion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FuenteFinanciamiento extends Model
{
    use SoftDeletes;

    protected $table = 'cat_fuente_financiamiento';
    protected $primaryKey = 'id_fuente';

    protected $fillable = [
        'nombre_fuente',
        'codigo_fuente',
        'estado'
    ];
}
