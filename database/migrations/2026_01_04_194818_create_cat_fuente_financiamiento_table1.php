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
        Schema::create('cat_fuente_financiamiento', function (Blueprint $table) {
            $table->id('id_fuente'); // Definimos id_fuente como la PK
            $table->string('nombre_fuente', 100);
            $table->string('codigo_fuente', 10)->nullable();
            $table->boolean('estado')->default(true); // Para activar/desactivar fuentes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_fuente_financiamiento_table1');
    }
};
