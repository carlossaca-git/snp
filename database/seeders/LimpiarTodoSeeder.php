<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
// Importa tus modelos para usar truncate()
use App\Models\Planificacion\AlineacionEstrategica;
use App\Models\Planificacion\ObjetivoEstrategico;
use App\Models\Catalogos\MetaNacional;
use App\Models\Catalogos\ObjetivoNacional;
use App\Models\Catalogos\EjePnd;
use App\Models\Catalogos\Ods;
use App\Models\Institucional\OrganizacionEstatal;

class LimpiarTodoSeeder extends Seeder
{
    public function run()
    {
        // 1. Desactivamos la protección de llaves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 2. Vaciamos las tablas (El orden no importa porque desactivamos los checks)
        $this->command->info('🧹 Limpiando alineaciones...');
        AlineacionEstrategica::truncate();

        $this->command->info('🧹 Limpiando objetivos estratégicos...');
        ObjetivoEstrategico::truncate();

        $this->command->info('🧹 Limpiando organizaciones...');
        OrganizacionEstatal::truncate();

        $this->command->info('🧹 Limpiando PND (Metas, Objetivos, Ejes)...');
        MetaNacional::truncate();
        ObjetivoNacional::truncate();
        EjePnd::truncate();

        $this->command->info('🧹 Limpiando ODS...');
        Ods::truncate();

        // 3. Reactivamos la protección
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✨ ¡Base de datos limpia! Lista para ingreso manual.');
    }
}
