<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tra_plan_inversion', function (Blueprint $table) {
            // Creamos la columna para el nombre legible
            $table->string('nombre_archivo')->nullable()->after('ruta_documento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tra_plan_inversion', function (Blueprint $table) {
            //
        });
    }
};
