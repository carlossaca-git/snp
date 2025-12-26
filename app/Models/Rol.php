<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'seg_rol';
    protected $primaryKey = 'id_rol';
    public $incrementing = true;
    protected $fillable = [
        'nombre',
        'descripcion'];

    public function usuarios()
    {
        return $this->belongsToMany(
            User::class,
            'seg_usuario_perfil',
            'id_rol',
            'id_usuario');
    }

}
