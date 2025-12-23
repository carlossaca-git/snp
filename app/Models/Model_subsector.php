<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Model_subsector extends Model
{
    use HasFactory;

    protected $table = 'cat_subsector';
        protected $fillable = [
            'id',
        'id_subsector',
        'id_sector',
        'nombre_subsector',
        'codigo_referencia',
    ];
    public $timestamps = false;
}
