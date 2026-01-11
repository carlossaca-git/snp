<?php

namespace App\Traits;

use App\Models\Seguridad\Auditoria;
use Illuminate\Support\Facades\Auth;

trait Auditable {
    public static function bootAuditable() {
        // Al crear un registro
        static::created(function ($model) {
            static::registrarAuditoria($model, 'CREAR');
        });

        // Al actualizar (guarda lo viejo y lo nuevo)
        static::updated(function ($model) {
            static::registrarAuditoria($model, 'MODIFICAR');
        });

        // Al eliminar
        static::deleted(function ($model) {
            static::registrarAuditoria($model, 'ELIMINAR');
        });
    }

    protected static function registrarAuditoria($model, $accion) {
        Auditoria::create([
            'id_usuario'     => Auth::id(),
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'modulo'         => (new \ReflectionClass($model))->getShortName(),
            'funcionalidad'  => request()->route() ? request()->route()->getName() : 'N/A',
            'accion'         => $accion,
            'data_original'  => $accion === 'CREAR' ? null : $model->getOriginal(),
            'data_nueva'     => $accion === 'ELIMINAR' ? null : $model->getAttributes(),
            'fecha_hora'     => now(),
        ]);
    }
}
