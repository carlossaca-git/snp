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
        Schema::create('cat_avances', function (Blueprint $table) {
            $table->id('id_avance');

            // Relación con el Indicador
            $table->unsignedBigInteger('id_indicador');
            $table->foreign('id_indicador')->references('id_indicador')->on('cat_indicador')->onDelete('cascade');

            $table->date('fecha_reporte');
            $table->decimal('valor_logrado', 10, 2); // El dato numérico real
            $table->string('evidencia_path')->nullable(); // Ruta del PDF/Imagen
            $table->text('observaciones')->nullable();

            $table->unsignedBigInteger('id_usuario_registro')->nullable(); // Quién reportó
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avance_indicadors');
    }
};
