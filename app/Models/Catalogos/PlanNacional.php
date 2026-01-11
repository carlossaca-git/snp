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

    // 1. Configuración de la Tabla
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

    // 2. Relaciones

    /**
     * Un Plan Nacional tiene muchos Ejes Estratégicos.
     * Ejemplo: El Plan "Nuevo Ecuador" tiene los Ejes: Social, Económico...
     */
    public function ejes()
    {
        // hasMany(ModeloHijo, 'FK_en_hijo', 'PK_en_padre')
        return $this->hasMany(EjePnd::class, 'id_plan', 'id_plan');
    }

    // 3. Scopes (Atajos para consultas)

    /**
     * Scope para obtener solo el plan activo actualmente.
     * Uso: PlanNacional::activo()->first();
     */
    public function scopeActivo($query)
    {
        return $query->where('estado', 'ACTIVO');
    }

    // 4. Accessors (Formatos visuales)

    /**
     * Retorna el periodo completo como string.
     * Uso: $plan->periodo_texto // Retorna "2024 - 2025"
     */
    public function getPeriodoTextoAttribute()
    {
        return "{$this->periodo_inicio} - {$this->periodo_fin}";
    }
}
