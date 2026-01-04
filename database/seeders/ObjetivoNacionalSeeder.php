<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObjetivoNacionalSeeder extends Seeder
{
    public function run()
    {
        $objetivos = [
            [
                'codigo_objetivo' => 'OBJ-SOC-01',
                'descripcion_objetivo' => 'Garantizar una vida digna con iguales oportunidades para todas las personas.',
                'estado' => 1
            ],
            [
                'codigo_objetivo' => 'OBJ-ECO-02',
                'descripcion_objetivo' => 'Fomentar un sistema económico social y solidario.',
                'estado' => 1
            ],
            [
                'codigo_objetivo' => 'OBJ-SEG-03',
                'descripcion_objetivo' => 'Fortalecer la seguridad integral y la paz ciudadana.',
                'estado' => 1
            ],
        ];

        foreach ($objetivos as $obj) {
            DB::table('cat_objetivo_nacional')->updateOrInsert(
                ['codigo_objetivo' => $obj['codigo_objetivo']],
                [
                    'descripcion_objetivo' => $obj['descripcion_objetivo'],
                    'estado' => $obj['estado'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}
