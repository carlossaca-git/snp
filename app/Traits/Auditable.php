<?php

namespace App\Traits;

use App\Models\Seguridad\Auditoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

trait Auditable
{
    public static function bootAuditable()
    {
        // Al crear un registro
        static::created(function (Model $model) {
            static::registrarAuditoria($model, 'CREAR');
        });

        // Al actualizar (guarda lo viejo y lo nuevo)
        static::updated(function (Model $model) {
            static::registrarAuditoria($model, 'MODIFICAR');
        });

        // Al eliminar
        static::deleted(function (Model $model) {
            static::registrarAuditoria($model, 'ELIMINAR');
        });
    }

    protected static function registrarAuditoria($model, $accion)
    {
        if (!Auth::check()) {
            return;
        }

        // Obtener SOLO los atributos originales y los nuevos
        $original = $model->getOriginal();
        $nuevo    = $model->getAttributes();
        $accionUpper = strtoupper($accion);

        // Limpiamos datos sensibles (como passwords) para que no queden en el log
        if (isset($original['password'])) $original['password'] = '[OCULTO]';
        if (isset($nuevo['password']))    $nuevo['password']    = '[OCULTO]';

        //  Detectar qué cambió realmente
        $cambios = [];
        if ($accion === 'MODIFICAR') {
            foreach ($model->getDirty() as $key => $value) {
                if ($key === 'updated_at') continue;

                $originalVal = $original[$key] ?? null;
                $cambios[$key] = [
                    'antes' => $originalVal,
                    'despues' => $value
                ];
            }
        }

        $accionUpper = strtoupper($accion);

        //  Definimos cuándo guardar qué (Aceptamos MODIFICAR o ACTUALIZAR)
        $esModificacion = in_array($accionUpper, ['ACTUALIZAR', 'MODIFICAR', 'EDITAR']);
        $esCreacion     = in_array($accionUpper, ['CREAR', 'INSERTAR']);
        $esEliminacion  = in_array($accionUpper, ['ELIMINAR', 'BORRAR']);

        // Asignamos los datos usando json_encode
        $dataOriginal = ($esModificacion || $esEliminacion) ? json_encode($original) : null;

        // Si es Creación o Modificación -> Guardamos lo NUEVO (lo que quedó)
        $dataNueva    = ($esCreacion || $esModificacion) ? json_encode($nuevo) : null;
        // Inserción adaptada a tus columnas reales según tu log de error
        DB::table('seg_auditoria')->insert([
            'id_usuario'      => Auth::id(),
            'id_registro'     => $model->getKey(),
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
            'modulo'          => class_basename($model),
            'funcionalidad'   => request()->route() ? request()->route()->getName() : 'sistema',
            'accion'          => $accionUpper,
            'data_original'   => $dataOriginal,
            'data_nueva'      => $dataNueva,
            'fecha_hora'      => now(),
        ]);
    }
}
