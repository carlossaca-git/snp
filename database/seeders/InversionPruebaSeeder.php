<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InversionPruebaSeeder extends Seeder
{
    public function run(): void
    {

        $planId = DB::table('tra_plan_inversion')->insertGetId([
            'nombre_plan' => 'Plan Anual de Inversiones (PAI) 2026',
            'anio_fiscal' => 2026,
            'estado' => 'Vigente',
            'created_at' => now(),
        ]);


        DB::table('tra_programa')->insert([
            'id_plan' => $planId,
            'id_organizacion' => 13,
            'codigo_programa' => 'PROG-001',
            'nombre_programa' => 'Programa de Infraestructura Educativa',
            'created_at' => now(),
        ]);

        DB::table('tra_programa')->insert([
            'id_plan' => $planId,
            'id_organizacion' => 13,
            'codigo_programa' => 'PROG-002',
            'nombre_programa' => 'Programa de Fortalecimiento Institucional',
            'created_at' => now(),
        ]);
    }
}
