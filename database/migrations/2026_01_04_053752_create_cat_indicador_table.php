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
        Schema::create('cat_indicador', function (Blueprint $table) {
            $table->id('id_indicador');
            // Relación con Metas Nacionales
            $table->unsignedBigInteger('id_meta');

            $table->text('nombre_indicador');
            $table->text('metodo_calculo')->nullable(); // La fórmula técnica

            // Datos de medición
            $table->decimal('linea_base', 12, 2)->nullable();
            $table->integer('anio_linea_base')->nullable();
            $table->decimal('meta_final', 12, 2)->nullable();

            // Atributos del indicador
            $table->string('unidad_medida')->nullable(); // Ej: Porcentaje, USD, Tasa
            $table->string('frecuencia')->nullable(); // Ej: Anual, Semestral
            $table->string('fuente_informacion')->nullable(); // Ej: INEC, Banco Central

            $table->boolean('estado')->default(1);
            $table->timestamps();

            // Llave foránea
            $table->foreign('id_meta')
                ->references('id_meta')
                ->on('cat_meta_nacional')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_indicador');
    }
};
