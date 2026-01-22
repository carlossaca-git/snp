<?php

namespace App\Models\Seguridad;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\Auditable;

use App\Models\Institucional\OrganizacionEstatal;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;
    use HasRoles;
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
        // Convertir a array si es texto simple
        if (is_string($roles)) {
            $roles = [$roles];
        }

        // Cargamos los roles si no están cargados
        if (!$this->relationLoaded('roles')) {
            $this->load('roles');
        }

        // Obtenemos los name de los roles del usuario
        $misRoles = $this->roles->pluck('name')->toArray();

        //  LLAVE MAESTRA: Si soy SUPER_ADMIN, paso siempre
        if (in_array('SUPER_ADMIN', $misRoles)) {
            return true;
        }

        // Verificamos coincidencia
        return count(array_intersect($misRoles, $roles)) > 0;
    }
    // Relación con Organización (para mostrar el nombre en el sidebar)
    public function tienePermiso($permisoSlug)
    {
        // 1. Cargamos roles y sus permisos
        if (!$this->relationLoaded('roles')) {
            $this->load('roles.permisos');
            //  Asegúrate que en tu modelo Rol.php tengas la relación public function permisos()
        }

        // Recorremos los roles
        foreach ($this->roles as $rol) {

            // Si el rol es SUPER_ADMIN, tiene permiso para todo
            if ($rol->name === 'SUPER_ADMIN') {
                return true;
            }

            // Buscamos el permiso dentro del rol
            if ($rol->permisos->contains('name', $permisoSlug)) {
                return true;
            }
        }

        return false;
    }
    //Scope para infiltrar usuarios por organizacion automaticamente
    public function scopeDelMismoEntorno($query)
    {
        //Obtenemos el usuario logeado

        $user = $this->getUsuario();
        if ($user->tieneRol('SUPER_ADMIN')) {
            return $query;
        }
        return $query ->where('id_organizacion', $user->id_organizacion);
    }
    private function getUsuario(): User
    {
        /** @var User */
        return Auth::user();
    }
}
