<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Model_sector extends Model
{

    use HasFactory;

    protected $table = 'tbl_cat_sector';
        protected $fillable = [
        'id_sector',
        'id_macrosector',
        'nombre',
        'estado',
    ];
    public $timestamps = false;

}
