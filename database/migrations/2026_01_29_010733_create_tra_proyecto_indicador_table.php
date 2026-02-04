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
        Schema::create('tra_proyecto_indicador', function (Blueprint $table) {
            $table->id();

            $table->foreignId('proyecto_id')->constrained('tra_proyecto_inversion')->onDelete('cascade');
            $table->foreignId('indicador_nacional_id')->constrained('cat_indicadores_nacionales', 'id_indicador');
            $table->decimal('contribucion_proyecto', 5, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tra_proyecto_indicador');
    }
};
