<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FuenteFinanciamientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    $fuentes = [
        ['nombre_fuente' => 'Recursos Fiscales', 'codigo_fuente' => '001'],
        ['nombre_fuente' => 'Préstamos de Desembolso Externo', 'codigo_fuente' => '701'],
        ['nombre_fuente' => 'Donaciones Externas', 'codigo_fuente' => '202'],
        ['nombre_fuente' => 'Asistencia Técnica', 'codigo_fuente' => '999'],
    ];

    foreach ($fuentes as $fuente) {
        \App\Models\Inversion\FuenteFinanciamiento::create($fuente);
    }
}
}
