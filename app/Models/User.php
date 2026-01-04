<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;


    protected $keyType = 'int';

    // 3. Este método le dice a los componentes de Breeze cómo identificar al usuario
    public function getAuthIdentifierName()
    {
        return 'id_usuario';
    }


    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'seg_usuario';
    protected $primaryKey = 'id_usuario';
    protected $fillable = [
        'usuario',
        'correo_electronico',
        'password',
        'identificacion',
        'nombres',
        'apellidos',
    ];
    public function getRouteKeyName()
    {
        return 'id_usuario';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(
            Rol::class,             // Modelo de Rol
            'seg_usuario_perfil',   // 2. Tabla intermedia
            'id_usuario',           // 3. FK en pivote que apunta a User
            'id_rol',               // 4. FK en pivote que apunta a Rol
            'id_usuario',           // 5. PK real en tabla seg_usuario
            'id_rol'                // 6. PK real en tabla seg_rol
        );
    }
    public function tieneRol($rolNombre)
    {

        if (!auth()->user()->tieneRol('ADMIN_TI')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $usuarios = User::with('roles')->paginate(10);
        return view('admin.users.index', compact('usuarios'));
    }

}
