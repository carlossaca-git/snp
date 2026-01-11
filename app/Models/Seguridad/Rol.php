<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'seg_rol';
    protected $primaryKey = 'id_rol';
    public $incrementing = true;
    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'estado'
        ];

    public function usuarios()
    {
        return $this->belongsToMany(User::class,'seg_usuario_perfil','id_rol','id_usuario');
    }

}
