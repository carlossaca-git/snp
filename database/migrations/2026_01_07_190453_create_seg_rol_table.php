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
        Schema::create('seg_rol', function (Blueprint $table) {
            $table->id('id_rol'); // Llave primaria
            $table->string('nombre_corto', 50)->unique(); // Nombre visible: "Administrador de TI"
            $table->string('name', 50)->unique();   // Referencia técnica: "ADMIN_TI"
            $table->string('descripcion', 255)->nullable();
            $table->boolean('estado')->default(true); // Activo/Inactivo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seg_rol');
    }
};
