<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;

class MetaPnd extends Model
{
    protected $table = 'cat_meta_pnd';
    protected $primaryKey = 'id_meta_pnd';
    public $timestamps = false; // Al ser catálogo, usualmente no lleva created_at

    protected $fillable = [
        'id_objetivo_nacional', // <--- OJO: Revisa si al final le cambiaste el nombre
        'descripcion_meta',
        'valor_meta',
        'anio_cumplimiento'
    ];

    /**
     * Relación Inversa: Pertenece a un Objetivo Nacional
     */
    public function objetivoNacional()
    {
        // Asegúrate que el segundo parámetro coincida con tu columna FK en la base de datos
        return $this->belongsTo(ObjetivoNacional::class, 'id_objetivo_nacional', 'id_objetivo_nacional');
    }

    /**
     * (Opcional) Si decidiste mantener la tabla de Indicadores separada
     */
    /*
    public function indicadores()
    {
        return $this->hasMany(IndicadorPnd::class, 'id_meta_pnd', 'id_meta_pnd');
    }
    */
}
