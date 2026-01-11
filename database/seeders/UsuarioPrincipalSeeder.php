<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Seguridad\User;
use Illuminate\Support\Facades\Hash;

class UsuarioPrincipalSeeder extends Seeder
{
    public function run(): void
    {
        // Usamos updateOrCreate para evitar errores si ejecutas el seeder dos veces
        User::updateOrCreate(
            ['correo_electronico' => 'cfsaca@utpl.edu.ec'], // Busca por este campo
            [
                'identificacion' => '1900715002',
                'nombres' => 'Carlos Francisco',
                'apellidos' => 'Saca Japa',
                'usuario' => 'admin',
                'password' => Hash::make('xarlos12'),
                'id_organizacion' => 9,
                'estado' => 1
            ]
        );
    }
}
