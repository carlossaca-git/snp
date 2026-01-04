<?php

namespace App\Models\Estrategico; // <--- Namespace Unificado

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoOrganizacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cat_tipo_organizacion';
    protected $primaryKey = 'id_tipo_org';

    protected $fillable = [
        'id_tipo_org',
        'nombre_tipo_org', // Verificado
        'descripcion',
        'estado'
    ];

    public $timestamps = true;
    protected $dates = ['deleted_at'];

    /**
     * RELACIONES
     */
    public function organizaciones(): HasMany
    {
        return $this->hasMany(OrganizacionEstatal::class, 'id_tipo_org');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }
}
