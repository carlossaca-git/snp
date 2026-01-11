<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seg_permiso', function (Blueprint $table) {
            $table->id('id_permiso');
            $table->string('nombre', 100); // Ej: "Crear Proyectos de Inversión"
            $table->string('slug', 100)->unique(); // Ej: "proyectos.crear"
            $table->string('descripcion', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seg_permiso');
    }
};
