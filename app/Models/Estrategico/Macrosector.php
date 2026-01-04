<?php

namespace App\Models\Estrategico;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Macrosector extends Model
{
    use HasFactory, SoftDeletes; // <--- Activamos el trait

    protected $table = 'cat_macrosector';
    protected $primaryKey = 'id_macrosector';

    protected $fillable = [
        'nombre_macrosector',
        'siglas_macrosector',
        'descripcion',
        'estado'
    ];

    public $timestamps = true;

    // Protegemos la fecha de borrado
    protected $dates = ['deleted_at'];

    /**
     * RELACIONES
     */

    // Un Macrosector tiene muchos Sectores
    // Ejemplo: El Macrosector "Social" tiene sectores "Salud", "Educación", etc.
    public function sectores(): HasMany
    {
        // Como Sector está en el mismo Namespace (App\Models\Estrategico),
        // no hace falta importarlo con 'use', Laravel lo detecta automáticamente.
        return $this->hasMany(Sector::class, 'id_macrosector');
    }

    /**
     * SCOPES
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }
}
