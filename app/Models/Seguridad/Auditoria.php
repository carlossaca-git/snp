<?php

namespace App\Models\Seguridad;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Auditoria extends Model
{
    use Auditable;
    use HasFactory;
    // SoftDeletes;

    //  Nombre de la tabla según tu migración
    protected $table = 'seg_auditoria';

    //  Llave primaria personalizada
    protected $primaryKey = 'id_auditoria';

    // Desactivamos timestamps estándar de Laravel
    // porque usamos 'fecha_hora' de forma personalizada
    public $timestamps = false;

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'id_usuario',
        'ip_address',
        'user_agent',
        'modulo',
        'funcionalidad',
        'accion',
        'data_original',
        'data_nueva',
        'fecha_hora',
    ];

    /**
     * El sistema exige trazabilidad de datos.
     * Convertimos automáticamente los campos JSON a arrays de PHP.
     */
    protected $casts = [
        'data_original' => 'array',
        'data_nueva'    => 'array',
        'fecha_hora'    => 'datetime',
    ];

    /**
     * Relación con el Usuario
     * Permite saber quién realizó la acción en los reportes de auditoría.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}
