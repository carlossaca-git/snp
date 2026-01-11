<?php

namespace App\Models\Inversion;

use Illuminate\Database\Eloquent\Model;

class DocumentoProyecto extends Model
{
    protected $table = 'documentos_proyectos';

    protected $fillable = [
        'id_proyecto',
        'nombre_archivo',
        'url_archivo',
        'extension'
    ];

    public function proyecto()
    {
        return $this->belongsTo(ProyectoInversion::class, 'id_proyecto');
    }
}
