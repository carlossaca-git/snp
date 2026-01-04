<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Estrategico\Macrosector; // Ajusta si tus modelos están en App\Models
use App\Models\Estrategico\Sector;
use App\Models\Estrategico\Subsector;

class ClasificacionSeeder extends Seeder
{
    public function run()
    {
        // 1. DESACTIVAR REVISIÓN DE CLAVES FORÁNEAS
        // (Esto permite borrar las tablas sin errores)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 2. LIMPIAR TABLAS (TRUNCATE)
        // Esto borra todo y reinicia los contadores de ID
        echo "Limpíando tablas antiguas...\n";
        Macrosector::truncate();
        Sector::truncate();
        Subsector::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 3. DEFINIR LA ESTRUCTURA DE DATOS (ÁRBOL)
        $data = [
            'Sector Social' => [
                'Educación' => ['Educación Inicial', 'Educación Básica', 'Educación Superior', 'Infraestructura Educativa'],
                'Salud' => ['Salud Pública', 'Hospitales', 'Nutrición Infantil', 'Vigilancia Sanitaria'],
                'Protección Social' => ['Inclusión Social', 'Desarrollo Infantil', 'Atención al Adulto Mayor']
            ],
            'Sector Infraestructura' => [
                'Transporte' => ['Vialidad', 'Transporte Terrestre', 'Puertos', 'Aeropuertos'],
                'Vivienda' => ['Vivienda Social', 'Saneamiento', 'Agua Potable'],
                'Energía' => ['Electricidad', 'Energía Renovable', 'Hidrocarburos']
            ],
            'Sector Seguridad' => [
                'Seguridad Ciudadana' => ['Policía Nacional', 'Orden Público'],
                'Defensa' => ['Defensa Nacional', 'Seguridad Marítima'],
                'Justicia' => ['Rehabilitación Social', 'Derechos Humanos']
            ],
            'Sector Productivo' => [
                'Agricultura' => ['Agricultura Familiar', 'Agroindustria', 'Riego'],
                'Turismo' => ['Promoción Turística', 'Regulación Turística'],
                'Comercio Exterior' => ['Inversiones', 'Exportaciones']
            ]
        ];

        // 4. INSERTAR LOS DATOS (DOBLE BUCLE)
        echo "Insertando datos nuevos y relacionados...\n";

        foreach ($data as $nomMacro => $sectores) {

            // A. Crear Macrosector
            $macro = Macrosector::create([
                'nombre' => $nomMacro,
                'estado' => '1' // Asumiendo que usas 'A' o 1
            ]);

            foreach ($sectores as $nomSector => $subsectores) {

                // B. Crear Sector (Vinculado al Macrosector)
                $sector = Sector::create([
                    'id_macrosector' => $macro->id_macrosector, // O $macro->id_macrosector si tu PK es personalizada
                    'nombre' => $nomSector,
                    'siglas' => strtoupper(substr($nomSector, 0, 3)),
                    'estado' => '1'
                ]);

                foreach ($subsectores as $nomSub) {

                    // C. Crear Subsector (Vinculado al Sector)
                    Subsector::create([
                        'id_sector' => $sector->id_sector, // Aquí usamos tu PK personalizada
                        'nombre' => $nomSub,
                        'codigo' => strtoupper(substr($nomSub, 0, 3)) . rand(100,999),
                        'estado' => 1 // Aquí usas 1 según me dijiste
                    ]);
                }
            }
        }

        echo "¡Proceso terminado! Base de datos poblada correctamente.\n";
    }
}
