<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Institucional\TipoOrganizacion;

class TipoOrganizacionSeeder extends Seeder
{
    public function run()
    {
        // 1. Limpiar tabla (Truncate)
        // Desactivamos claves foráneas temporalmente para evitar errores al borrar
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        TipoOrganizacion::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Definición de Datos (Nombre, Descripción, Nivel Sugerido)
        $tipos = [
            [
                'nombre' => 'Ministerio Sectorial',
                'descripcion' => 'Entidad rectora encargada de la formulación y ejecución de políticas públicas en un sector específico.',
                'nivel' => 'Nacional'
            ],
            [
                'nombre' => 'Secretaría Nacional / Técnica',
                'descripcion' => 'Organismo público especializado con rango de ministerio encargado de la gestión técnica o política.',
                'nivel' => 'Nacional'
            ],
            [
                'nombre' => 'Empresa Pública (EP)',
                'descripcion' => 'Entidad creada para la gestión de sectores estratégicos, prestación de servicios públicos o aprovechamiento de recursos naturales.',
                'nivel' => 'Nacional'
            ],
            [
                'nombre' => 'Agencia de Regulación y Control',
                'descripcion' => 'Entidad técnica encargada de regular, controlar y fiscalizar las actividades de un sector específico.',
                'nivel' => 'Nacional'
            ],
            [
                'nombre' => 'Instituto Público de Investigación',
                'descripcion' => 'Organismo dedicado a la investigación científica, generación de conocimiento y asistencia técnica.',
                'nivel' => 'Nacional'
            ],
            [
                'nombre' => 'Superintendencia',
                'descripcion' => 'Organismo técnico de vigilancia, auditoría, intervención y control de actividades económicas y sociales.',
                'nivel' => 'Nacional'
            ],
            [
                'nombre' => 'GAD Provincial (Prefectura)',
                'descripcion' => 'Gobierno Autónomo Descentralizado encargado de la gestión y desarrollo a nivel de provincia.',
                'nivel' => 'Provincial'
            ],
            [
                'nombre' => 'GAD Municipal (Alcaldía)',
                'descripcion' => 'Gobierno Autónomo Descentralizado encargado de la planificación y servicios a nivel cantonal.',
                'nivel' => 'Cantonal'
            ],
            [
                'nombre' => 'GAD Parroquial',
                'descripcion' => 'Gobierno local encargado de la administración en zonas rurales.',
                'nivel' => 'Parroquial'
            ],
            [
                'nombre' => 'Banca Pública',
                'descripcion' => 'Institución financiera del Estado para el fomento productivo y social.',
                'nivel' => 'Nacional'
            ],
        ];

        // 3. Insertar datos
        echo "Insertando Tipos de Organización...\n";
        $i=1;
        foreach ($tipos as $tipo) {
            TipoOrganizacion::create([
                'id_tipo_org' =>$i,
                'nombre'         => $tipo['nombre'],
                'descripcion'    => $tipo['descripcion'],
                'nivel_gobierno' => $tipo['nivel'],
                'estado'         => 1, // 1 = Activo
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
            $i++;
        }

        echo "¡Tipos de Organización creados correctamente!\n";
    }
}
