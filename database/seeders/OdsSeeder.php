<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OdsSeeder extends Seeder
{
    public function run()
    {
        $ods = [
            [
                'id' => 1,
                'cod' => 'ODS-1',
                'nom' => 'Fin de la pobreza',
                'col' => '#E5243B',
                'pil' => 'Social',
                'desc' => 'Poner fin a la pobreza en todas sus formas en todo el mundo.'
            ],
            [
                'id' => 2,
                'cod' => 'ODS-2',
                'nom' => 'Hambre cero',
                'col' => '#DDA63A',
                'pil' => 'Social',
                'desc' => 'Poner fin al hambre, lograr la seguridad alimentaria y la mejora de la nutrición.'
            ],
            [
                'id' => 3,
                'cod' => 'ODS-3',
                'nom' => 'Salud y bienestar',
                'col' => '#4C9F38',
                'pil' => 'Social',
                'desc' => 'Garantizar una vida sana y promover el bienestar para todos en todas las edades.'
            ],
            [
                'id' => 4,
                'cod' => 'ODS-4',
                'nom' => 'Educación de calidad',
                'col' => '#C5192D',
                'pil' => 'Social',
                'desc' => 'Garantizar una educación inclusiva, equitativa y de calidad.'
            ],
            [
                'id' => 5,
                'cod' => 'ODS-5',
                'nom' => 'Igualdad de género',
                'col' => '#FF3A21',
                'pil' => 'Social',
                'desc' => 'Lograr la igualdad entre los géneros y empoderar a todas las mujeres y las niñas.'
            ],
            [
                'id' => 6,
                'cod' => 'ODS-6',
                'nom' => 'Agua limpia y saneamiento',
                'col' => '#26BDE2',
                'pil' => 'Ambiental',
                'desc' => 'Garantizar la disponibilidad de agua y su gestión sostenible y el saneamiento.'
            ],
            [
                'id' => 7,
                'cod' => 'ODS-7',
                'nom' => 'Energía asequible y no contaminante',
                'col' => '#FCC30B',
                'pil' => 'Económico',
                'desc' => 'Garantizar el acceso a una energía asequible, segura, sostenible y moderna.'
            ],
            [
                'id' => 8,
                'cod' => 'ODS-8',
                'nom' => 'Trabajo decente y crecimiento económico',
                'col' => '#A21942',
                'pil' => 'Económico',
                'desc' => 'Promover el crecimiento económico inclusivo y sostenible, el empleo y el trabajo decente.'
            ],
            [
                'id' => 9,
                'cod' => 'ODS-9',
                'nom' => 'Industria, innovación e infraestructura',
                'col' => '#FD6925',
                'pil' => 'Económico',
                'desc' => 'Construir infraestructuras resilientes, promover la industrialización sostenible y la innovación.'
            ],
            [
                'id' => 10,
                'cod' => 'ODS-10',
                'nom' => 'Reducción de las desigualdades',
                'col' => '#DD1367',
                'pil' => 'Social',
                'desc' => 'Reducir la desigualdad en y entre los países.'
            ],
            [
                'id' => 11,
                'cod' => 'ODS-11',
                'nom' => 'Ciudades y comunidades sostenibles',
                'col' => '#FD9D24',
                'pil' => 'Ambiental',
                'desc' => 'Lograr que las ciudades sean inclusivas, seguras, resilientes y sostenibles.'
            ],
            [
                'id' => 12,
                'cod' => 'ODS-12',
                'nom' => 'Producción y consumo responsables',
                'col' => '#BF8B2E',
                'pil' => 'Ambiental',
                'desc' => 'Garantizar modalidades de consumo y producción sostenibles.'
            ],
            [
                'id' => 13,
                'cod' => 'ODS-13',
                'nom' => 'Acción por el clima',
                'col' => '#3F7E44',
                'pil' => 'Ambiental',
                'desc' => 'Adoptar medidas urgentes para combatir el cambio climático y sus efectos.'
            ],
            [
                'id' => 14,
                'cod' => 'ODS-14',
                'nom' => 'Vida submarina',
                'col' => '#0A97D9',
                'pil' => 'Ambiental',
                'desc' => 'Conservar y utilizar sosteniblemente los océanos, los mares y los recursos marinos.'
            ],
            [
                'id' => 15,
                'cod' => 'ODS-15',
                'nom' => 'Vida de ecosistemas terrestres',
                'col' => '#56C02B',
                'pil' => 'Ambiental',
                'desc' => 'Gestionar sosteniblemente los bosques, luchar contra la desertificación y detener la pérdida de biodiversidad.'
            ],
            [
                'id' => 16,
                'cod' => 'ODS-16',
                'nom' => 'Paz, justicia e instituciones sólidas',
                'col' => '#00689D',
                'pil' => 'Institucional',
                'desc' => 'Promover sociedades justas, pacíficas e inclusivas.'
            ],
            [
                'id' => 17,
                'cod' => 'ODS-17',
                'nom' => 'Alianzas para lograr los objetivos',
                'col' => '#19486A',
                'pil' => 'Institucional',
                'desc' => 'Fortalecer los medios de ejecución y revitalizar la Alianza Mundial para el Desarrollo Sostenible.'
            ],
        ];

        foreach ($ods as $item) {
            DB::table('cat_ods')->updateOrInsert(
                ['id_ods' => $item['id']],
                [
                    'codigo'      => $item['cod'],
                    'nombre'      => $item['nom'],
                    'descripcion' => $item['desc'], // <--- Ahora sí es la descripción real
                    'pilar'       => $item['pil'],
                    'color_hex'   => $item['col'],
                    'estado'      => 1,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]
            );
        }
    }
}
