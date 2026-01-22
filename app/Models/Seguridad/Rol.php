<?php

namespace App\Models\Seguridad;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role as SpatieRole;

class Rol extends SpatieRole
{
    use SoftDeletes;
    use Auditable;

    protected $table = 'seg_rol';
    protected $primaryKey = 'id_rol';
    public $incrementing = true;
    protected $fillable = [
        'nombre_corto',
        'name',
        'descripcion',
        'estado'
    ];

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'seg_usuario_perfil', 'id_rol', 'id_usuario');
    }
    // Relación Muchos a Muchos
    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(
            Permisos::class,
            'seg_rol_permiso',
            'id_rol',
            'id_permiso'
        );
    }

}
