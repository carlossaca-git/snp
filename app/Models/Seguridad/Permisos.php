<?php

namespace App\Models\Seguridad;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permisos extends SpatiePermission
{

    use Auditable;
    use SoftDeletes;

    public $timestamps = false;
    protected $table = 'seg_permiso';
    protected $primaryKey = 'id_permiso';
    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'nombre_corto',
        'modulo',
        'descripcion',
        'guard_name'
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Rol::class,
            'seg_rol_permiso',
            'id_permiso',
            'id_rol'
        );
    }
}
