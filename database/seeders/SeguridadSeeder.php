<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Seguridad\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SeguridadSeeder extends Seeder
{
    public function run(): void
    {
        //  Crear la Organización Maestra
        // Se usa DB::table porque quizás no tengas el modelo Organización aún
        DB::table('cat_organizacion_estatal')->insertOrIgnore([
            'id_organizacion' => 1,
            'nom_organizacion' => 'Secretaría Nacional de Planificación',
            'siglas' => 'SNP',
            'estado' => true,
            'created_at' => now(),
        ]);

        //  Crear Roles Básicos según el caso de estudio
        $roles = [
            ['id_rol' => 1, 'nombre_corto' => 'Administrador de TI', 'name' => 'ADMIN_TI'],
            ['id_rol' => 2, 'nombre_corto' => 'Técnico de Planificación', 'name' => 'TECNICO_PLAN'],
            ['id_rol' => 3, 'nombre_corto' => 'Revisor Institucional', 'name' => 'REVISOR_SNP'],
        ];

        foreach ($roles as $rol) {
            DB::table('seg_rol')->insertOrIgnore(array_merge($rol, ['created_at' => now()]));
        }

        //  Crear Usuario SuperAdmin
        $admin = User::updateOrCreate(
            ['identificacion' => '0000000000'],
            [
                'nombre_cortos' => 'Super',
                'apellidos' => 'Admin',
                'correo_electronico' => 'cfsaca@gmail.com',
                'usuario' => 'admin',
                'password' => Hash::make('xarlos12'),
                'id_organizacion' => 1,
                'estado' => true,
            ]
        );

        // 4. Asignar el Rol de ADMIN_TI al usuario
        DB::table('seg_usuario_perfil')->insertOrIgnore([
            'id_usuario' => $admin->id_usuario,
            'id_rol' => 1,
            'created_at' => now(),
        ]);
    }
}
