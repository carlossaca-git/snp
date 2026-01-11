<?php

namespace App\Http\Controllers\Inversion;

use Illuminate\Database\Eloquent\Model;

class Financiamiento extends Model
{
    protected $table = 'tra_financiamiento';

    protected $fillable = [
        'id_proyecto',
        'anio',
        'id_fuente',
        'monto'
    ];
}
