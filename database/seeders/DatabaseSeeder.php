<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
         $this->call([
            UsuarioPrincipalSeeder::class,
            MetaPndSeeder::class,
        ]);
        // 1. DESACTIVAR REGLAS DE SEGURIDAD
        Schema::disableForeignKeyConstraints();

        // 2. VACIAR TABLAS (Orden de hijos a padres)
        DB::table('alineacion_estrategica')->truncate();
        DB::table('cat_meta_pnd')->truncate();
        DB::table('cat_objetivo_nacional')->truncate();


        Schema::enableForeignKeyConstraints();
        // 3. INSERTAR LOS 10 OBJETIVOS NACIONALES
        $objetivosNacionales = [
            [
                'id_objetivo_nacional' => 1,
                'codigo_objetivo' => 'ON-01',
                'descripcion_objetivo' => 'Erradicación de la pobreza y justicia social',
                'id_eje' => 1, // Eje Social
                'periodo' => '2021-2025',
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_objetivo_nacional' => 2,
                'codigo_objetivo' => 'ON-02',
                'descripcion_objetivo' => 'Salud integral, universal y gratuita',
                'id_eje' => 1,
                'periodo' => '2021-2025',
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_objetivo_nacional' => 3,
                'codigo_objetivo' => 'ON-03',
                'descripcion_objetivo' => 'Educación de calidad y fortalecimiento del talento humano',
                'id_eje' => 1,
                'periodo' => '2021-2025',
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_objetivo_nacional' => 4,
                'codigo_objetivo' => 'ON-04',
                'descripcion_objetivo' => 'Seguridad humana y convivencia ciudadana',
                'id_eje' => 2, // Eje Seguridad/Institucional
                'periodo' => '2021-2025',
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_objetivo_nacional' => 5,
                'codigo_objetivo' => 'ON-05',
                'descripcion_objetivo' => 'Transformación económica y soberanía alimentaria',
                'id_eje' => 3, // Eje Económico
                'periodo' => '2021-2025',
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_objetivo_nacional' => 6,
                'codigo_objetivo' => 'ON-06',
                'descripcion_objetivo' => 'Trabajo digno y pleno empleo',
                'id_eje' => 3,
                'periodo' => '2021-2025',
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_objetivo_nacional' => 7,
                'codigo_objetivo' => 'ON-07',
                'descripcion_objetivo' => 'Sostenibilidad ambiental y cambio climático',
                'id_eje' => 4, // Eje Ambiental
                'periodo' => '2021-2025',
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_objetivo_nacional' => 8,
                'codigo_objetivo' => 'ON-08',
                'descripcion_objetivo' => 'Infraestructura, conectividad y energía limpia',
                'id_eje' => 3,
                'periodo' => '2021-2025',
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_objetivo_nacional' => 9,
                'codigo_objetivo' => 'ON-09',
                'descripcion_objetivo' => 'Transparencia y lucha contra la corrupción',
                'id_eje' => 2,
                'periodo' => '2021-2025',
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_objetivo_nacional' => 10,
                'codigo_objetivo' => 'ON-10',
                'descripcion_objetivo' => 'Soberanía e integración regional',
                'id_eje' => 2,
                'periodo' => '2021-2025',
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($objetivosNacionales as $obj) {
            DB::table('cat_objetivo_nacional')->insert($obj);
        }
        // 4. INSERTAR 30 METAS NACIONALES (Con campos técnicos)
        // 4. INSERTAR 30 METAS NACIONALES (3 por cada Objetivo Nacional)
        $metas = [
            // Objetivo 1: Erradicación de la pobreza
            ['id_meta_pnd' => 1, 'id_objetivo_nacional' => 1, 'id_ods' => 1, 'codigo_meta' => 'M1.1', 'descripcion' => 'Reducir la pobreza extrema por ingresos al 10.7%', 'valor_meta' => 10.7, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 2, 'id_objetivo_nacional' => 1, 'id_ods' => 10, 'codigo_meta' => 'M1.2', 'descripcion' => 'Reducir la brecha de desigualdad de ingresos (Gini)', 'valor_meta' => 0.45, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 3, 'id_objetivo_nacional' => 1, 'id_ods' => 1, 'codigo_meta' => 'M1.3', 'descripcion' => 'Aumentar la cobertura de bonos en sectores rurales', 'valor_meta' => 85.0, 'anio_cumplimiento' => 2025],

            // Objetivo 2: Salud integral
            ['id_meta_pnd' => 4, 'id_objetivo_nacional' => 2, 'id_ods' => 3, 'codigo_meta' => 'M2.1', 'descripcion' => 'Reducir la tasa de mortalidad materna a 35 por 100k', 'valor_meta' => 35.0, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 5, 'id_objetivo_nacional' => 2, 'id_ods' => 2, 'codigo_meta' => 'M2.2', 'descripcion' => 'Erradicar la desnutrición crónica infantil en menores de 2 años', 'valor_meta' => 0.0, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 6, 'id_objetivo_nacional' => 2, 'id_ods' => 3, 'codigo_meta' => 'M2.3', 'descripcion' => 'Aumentar personal de salud en primer nivel de atención', 'valor_meta' => 25.0, 'anio_cumplimiento' => 2025],

            // Objetivo 3: Educación de calidad
            ['id_meta_pnd' => 7, 'id_objetivo_nacional' => 3, 'id_ods' => 4, 'codigo_meta' => 'M3.1', 'descripcion' => 'Alcanzar el 95% de asistencia a educación básica', 'valor_meta' => 95.0, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 8, 'id_objetivo_nacional' => 3, 'id_ods' => 4, 'codigo_meta' => 'M3.2', 'descripcion' => 'Incrementar becas para educación técnica superior', 'valor_meta' => 15000.0, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 9, 'id_objetivo_nacional' => 3, 'id_ods' => 4, 'codigo_meta' => 'M3.3', 'descripcion' => 'Mejorar infraestructura escolar en zonas fronterizas', 'valor_meta' => 100.0, 'anio_cumplimiento' => 2025],

            // Objetivo 4: Seguridad humana
            ['id_meta_pnd' => 10, 'id_objetivo_nacional' => 4, 'id_ods' => 16, 'codigo_meta' => 'M4.1', 'descripcion' => 'Reducir tasa de homicidios por cada 100k habitantes', 'valor_meta' => 8.5, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 11, 'id_objetivo_nacional' => 4, 'id_ods' => 5, 'codigo_meta' => 'M4.2', 'descripcion' => 'Reducir índice de violencia de género en espacios públicos', 'valor_meta' => 15.0, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 12, 'id_objetivo_nacional' => 4, 'id_ods' => 16, 'codigo_meta' => 'M4.3', 'descripcion' => 'Fortalecer unidades de vigilancia comunitaria', 'valor_meta' => 90.0, 'anio_cumplimiento' => 2025],

            // Objetivo 5: Transformación económica
            ['id_meta_pnd' => 13, 'id_objetivo_nacional' => 5, 'id_ods' => 2, 'codigo_meta' => 'M5.1', 'descripcion' => 'Aumentar productividad de agricultura familiar', 'valor_meta' => 15.0, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 14, 'id_objetivo_nacional' => 5, 'id_ods' => 12, 'codigo_meta' => 'M5.2', 'descripcion' => 'Fomentar valor agregado en exportaciones primarias', 'valor_meta' => 20.0, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 15, 'id_objetivo_nacional' => 5, 'id_ods' => 9, 'codigo_meta' => 'M5.3', 'descripcion' => 'Diversificar matriz productiva con incentivos locales', 'valor_meta' => 30.0, 'anio_cumplimiento' => 2025],

            // Objetivo 6: Trabajo digno
            ['id_meta_pnd' => 16, 'id_objetivo_nacional' => 6, 'id_ods' => 8, 'codigo_meta' => 'M6.1', 'descripcion' => 'Reducir tasa de desempleo juvenil al 8%', 'valor_meta' => 8.0, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 17, 'id_objetivo_nacional' => 6, 'id_ods' => 8, 'codigo_meta' => 'M6.2', 'descripcion' => 'Incrementar empleo con seguridad social', 'valor_meta' => 60.0, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 18, 'id_objetivo_nacional' => 6, 'id_ods' => 5, 'codigo_meta' => 'M6.3', 'descripcion' => 'Reducir brecha salarial por género', 'valor_meta' => 5.0, 'anio_cumplimiento' => 2025],

            // Objetivo 7: Sostenibilidad ambiental
            ['id_meta_pnd' => 19, 'id_objetivo_nacional' => 7, 'id_ods' => 13, 'codigo_meta' => 'M7.1', 'descripcion' => 'Reducir emisiones de CO2 mediante energías limpias', 'valor_meta' => 20.0, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 20, 'id_objetivo_nacional' => 7, 'id_ods' => 15, 'codigo_meta' => 'M7.2', 'descripcion' => 'Incrementar hectáreas de protección hídrica', 'valor_meta' => 50000.0, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 21, 'id_objetivo_nacional' => 7, 'id_ods' => 14, 'codigo_meta' => 'M7.3', 'descripcion' => 'Mejorar gestión de residuos en áreas marinas', 'valor_meta' => 100.0, 'anio_cumplimiento' => 2025],

            // Objetivo 8: Infraestructura y energía
            ['id_meta_pnd' => 22, 'id_objetivo_nacional' => 8, 'id_ods' => 9, 'codigo_meta' => 'M8.1', 'descripcion' => 'Aumentar cobertura internet banda ancha rural', 'valor_meta' => 70.0, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 23, 'id_objetivo_nacional' => 8, 'id_ods' => 7, 'codigo_meta' => 'M8.2', 'descripcion' => 'Alcanzar 90% de generación eléctrica limpia', 'valor_meta' => 90.0, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 24, 'id_objetivo_nacional' => 8, 'id_ods' => 11, 'codigo_meta' => 'M8.3', 'descripcion' => 'Mantenimiento de red vial estatal primaria', 'valor_meta' => 80.0, 'anio_cumplimiento' => 2025],

            // Objetivo 9: Transparencia
            ['id_meta_pnd' => 25, 'id_objetivo_nacional' => 9, 'id_ods' => 16, 'codigo_meta' => 'M9.1', 'descripcion' => 'Implementar portales de datos abiertos al 100%', 'valor_meta' => 100.0, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 26, 'id_objetivo_nacional' => 9, 'id_ods' => 17, 'codigo_meta' => 'M9.2', 'descripcion' => 'Mejorar índice de integridad en servicio público', 'valor_meta' => 8.5, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 27, 'id_objetivo_nacional' => 9, 'id_ods' => 16, 'codigo_meta' => 'M9.3', 'descripcion' => 'Reducir tiempos en procesos de auditoría', 'valor_meta' => 30.0, 'anio_cumplimiento' => 2025],

            // Objetivo 10: Soberanía e integración
            ['id_meta_pnd' => 28, 'id_objetivo_nacional' => 10, 'id_ods' => 17, 'codigo_meta' => 'M10.1', 'descripcion' => 'Aumentar acuerdos de cooperación internacional', 'valor_meta' => 40.0, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 29, 'id_objetivo_nacional' => 10, 'id_ods' => 10, 'codigo_meta' => 'M10.2', 'descripcion' => 'Proteger derechos de ciudadanos en el exterior', 'valor_meta' => 100.0, 'anio_cumplimiento' => 2025],
            ['id_meta_pnd' => 30, 'id_objetivo_nacional' => 10, 'id_ods' => 17, 'codigo_meta' => 'M10.3', 'descripcion' => 'Fortalecer presencia comercial en mercados globales', 'valor_meta' => 15.0, 'anio_cumplimiento' => 2025],
        ];

        foreach ($metas as $meta) {
            DB::table('cat_meta_pnd')->insert($meta);
        }

        $this->command->info('¡Catálogos de planificación nacional cargados con éxito!');
    }
}
