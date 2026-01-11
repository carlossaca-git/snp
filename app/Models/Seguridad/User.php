<?php

namespace App\Models\Seguridad;

use App\Models\Institucional\OrganizacionEstatal;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Auditable;



class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;
    use Auditable;

    protected $table = 'seg_usuario';
    protected $primaryKey = 'id_usuario';
    protected $keyType = 'int';

    protected $fillable = [
        'usuario',
        'correo_electronico',
        'email_verified_at',
        'password',
        'identificacion',
        'nombres',
        'apellidos',
        'id_organizacion',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'estado' => 'boolean',
        ];
    }

    // Identificación para Breeze
    public function getAuthIdentifierName()
    {
        return 'id_usuario';
    }

    /**
     * Relación con la Organización (Entidad)
     * El sistema debe permitir segmentar usuarios por institución[cite: 153, 249].
     */
    public function organizacion()
    {
        return $this->belongsTo(OrganizacionEstatal::class, 'id_organizacion', 'id_organizacion');
    }

    /**
     * Relación con Roles
     * Define quién es Admin, Técnico o Revisor[cite: 1125].
     */
    public function roles()
    {
        return $this->belongsToMany(
            Rol::class,
            'seg_usuario_perfil',
            'id_usuario',
            'id_rol'

        );
    }

    /**
     * Determina si el usuario tiene un rol específico.
     * Útil para proteger acciones administrativas y vistas
     */


    public function tieneRol($roles)
    {
        // 1. Convertir a array si es un solo texto
        if (is_string($roles)) {
            $roles = [$roles];
        }

        // 2. Obtener los nombres de los roles del usuario desde la relación
        $misRoles = $this->roles->pluck('slug')->toArray();

        // 3. LA LLAVE MAESTRA VISUAL:
        if (in_array('SUPER_ADMIN', $misRoles) || in_array('SUPER_ADMIN', $misRoles)) {
            return true;
        }

        // 4. Verificar si alguno de los roles requeridos está en mis roles
        return count(array_intersect($misRoles, $roles)) > 0;
    }
    // Relación con Organización (para mostrar el nombre en el sidebar)

}
