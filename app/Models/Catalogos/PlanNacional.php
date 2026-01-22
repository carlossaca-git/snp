<?php

namespace App\Models\Catalogos;

use App\Models\Catalogos\EjePnd;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditoriaTrait; // Tu Trait de auditoría

class PlanNacional extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;

    // Configuración de la Tabla
    protected $table = 'cat_plan_nacional';
    protected $primaryKey = 'id_plan';

    // Permitimos asignación masiva para estos campos
    protected $fillable = [
        'nombre',
        'periodo_inicio',
        'periodo_fin',
        'registro_oficial',
        'estado', // 'ACTIVO', 'INACTIVO'
        // Los campos de auditoría (created_by, etc.) los maneja el Trait
    ];

    // Relaciones

    /**
     * Un Plan Nacional tiene muchos Ejes Estratégicos.
     * Ejemplo: El Plan "Nuevo Ecuador" tiene los Ejes: Social, Económico...
     */
    public function ejes()
    {
        return $this->hasMany(EjePnd::class, 'id_plan', 'id_plan');
    }


    /**
     * Scope para obtener solo el plan activo actualmente.
     *
     */
    public function scopeActivo($query)
    {
        return $query->where('estado', 'ACTIVO');
    }

    /**
     * Retorna el periodo completo como string.
     */
    public function getPeriodoTextoAttribute()
    {
        return "{$this->periodo_inicio} - {$this->periodo_fin}";
    }
}
