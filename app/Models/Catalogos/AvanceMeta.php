<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Seguridad\User;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class AvanceMeta extends Model
{
    use HasFactory;
    use Auditable;
    use SoftDeletes;

    protected $table = 'tra_meta_avances';


    protected $primaryKey = 'id_avance';


    protected $fillable = [
        'id_meta_nacional',
        'id_usuario',
        'valor',
        'fecha_reporte',
        'evidencia',
        'observaciones'
    ];

    // --- RELACIONES ---

    // Un avance pertenece a una Meta Nacional
    public function metaNacional()
    {
        return $this->belongsTo(MetaNacional::class, 'id_meta_nacional', 'id_meta_nacional');
    }

    // Un avance fue hecho por un Usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}
