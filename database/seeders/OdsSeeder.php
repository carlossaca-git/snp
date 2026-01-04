<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OdsSeeder extends Seeder
{
    public function run()
    {
        $ods = [
            ['numero' => 1,  'color_hex' => '#E5243B', 'nombre_corto' => 'Fin de la Pobreza'],
            ['numero' => 2,  'color_hex' => '#DDA63A', 'nombre_corto' => 'Hambre Cero'],
            ['numero' => 3,  'color_hex' => '#4C9F38', 'nombre_corto' => 'Salud y Bienestar'],
            ['numero' => 4,  'color_hex' => '#C5192D', 'nombre_corto' => 'Educación de Calidad'],
            ['numero' => 5,  'color_hex' => '#FF3A21', 'nombre_corto' => 'Igualdad de Género'],
            ['numero' => 6,  'color_hex' => '#26BDE2', 'nombre_corto' => 'Agua Limpia y Saneamiento'],
            ['numero' => 7,  'color_hex' => '#FCC30B', 'nombre_corto' => 'Energía Asequible'],
            ['numero' => 8,  'color_hex' => '#A21942', 'nombre_corto' => 'Trabajo Decente'],
            ['numero' => 9,  'color_hex' => '#FD6925', 'nombre_corto' => 'Industria e Infraestructura'],
            ['numero' => 10, 'color_hex' => '#DD1367', 'nombre_corto' => 'Reducción de Desigualdades'],
            ['numero' => 11, 'color_hex' => '#FD9D24', 'nombre_corto' => 'Ciudades Sostenibles'],
            ['numero' => 12, 'color_hex' => '#BF8B2E', 'nombre_corto' => 'Producción y Consumo'],
            ['numero' => 13, 'color_hex' => '#3F7E44', 'nombre_corto' => 'Acción por el Clima'],
            ['numero' => 14, 'color_hex' => '#0A97D9', 'nombre_corto' => 'Vida Submarina'],
            ['numero' => 15, 'color_hex' => '#56C02B', 'nombre_corto' => 'Vida de Ecosistemas Terrestres'],
            ['numero' => 16, 'color_hex' => '#00689D', 'nombre_corto' => 'Paz y Justicia'],
            ['numero' => 17, 'color_hex' => '#19486A', 'nombre_corto' => 'Alianzas para los Objetivos'],
        ];

        foreach ($ods as $item) {
            DB::table('cat_ods')->updateOrInsert(
                ['numero' => $item['numero']], // Si el número existe, lo actualiza, si no, lo crea
                [
                    'nombre_corto' => $item['nombre_corto'],
                    'color_hex' => $item['color_hex'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}
