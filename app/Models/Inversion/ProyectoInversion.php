<?php

namespace App\Models\Inversion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Inversion\Programa;
use App\Models\Inversion\ProyectoLocalizacion;
use App\Models\Inversion\Financiamiento;
use App\Models\Estrategico\OrganizacionEstatal;

class ProyectoInversion extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Nombre exacto de la tabla en la DB
    protected $table = 'tra_proyecto_inversion';

    // 2. Campos que permitimos llenar masivamente (Mass Assignment)
    protected $fillable = [
        'id_programa',
        'cup',
        'nombre_proyecto',
        'descripcion_diagnostico',
        'tipo_inversion',
        'fecha_inicio_estimada',
        'fecha_fin_estimada',
        'duracion_meses',
        'monto_total_inversion',
        'estado_dictamen',
    ];

    // 3. Conversión de tipos (Casting)
    // Esto asegura que Laravel trate las fechas como objetos Carbon y los números como decimales
    protected $casts = [
        'fecha_inicio_estimada' => 'date',
        'fecha_fin_estimada'    => 'date',
        'monto_total_inversion' => 'decimal:2',
    ];

    /**
     * RELACIONES
     */

    // Un Proyecto pertenece a un Programa
    public function programa()
    {
        // El segundo parámetro es la llave foránea en ESTA tabla
        return $this->belongsTo(Programa::class, 'id_programa');
    }

    // Un Proyecto puede tener muchas localizaciones (Provincia, Cantón, Parroquia)
    public function localizaciones()
    {
        return $this->hasMany(ProyectoLocalizacion::class, 'id_proyecto');
    }

    // Un Proyecto puede tener varios registros de financiamiento (por año/fuente)
    public function financiamientos()
    {
        return $this->hasMany(Financiamiento::class, 'id_proyecto');
    }
    // app/Models/Inversion/ProyectoInversion.php

public function organizacion()
{
    // El Proyecto tiene una organización A TRAVÉS del Programa
    return $this->hasOneThrough(
        OrganizacionEstatal::class,
        Programa::class,
        'id', // Llave foránea en Programa (id del programa)
        'id', // Llave foránea en Organizacion (id de la organizacion)
        'id_programa', // Llave local en Proyecto
        'id_organizacion' // Llave local en Programa
    );
}
}
