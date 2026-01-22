<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tra_indicadores_marco', function (Blueprint $table) {
            $table->id('id_indicador');


            $table->unsignedBigInteger('marco_logico_id');
            $table->string('descripcion', 500)
                  ->comment('Descripción verificable objetivamente ');
            $table->string('unidad_medida')->comment('Ej: Porcentaje, Número, Kilómetros');
            $table->decimal('linea_base', 12, 2)->default(0)
                  ->comment('Situación actual antes del proyecto ');
            $table->decimal('meta_total', 12, 2)
                  ->comment('Situación esperada al finalizar el proyecto');
            $table->decimal('ponderacion', 5, 2)->default(0)
                  ->comment('Peso del indicador para el cumplimiento del propósito ');
            $table->text('medio_verificacion')
                  ->comment('Fuente de información para verificar el logro (Ej: Informes, Actas) ');

            $table->timestamps();

            $table->foreign('marco_logico_id')->references('id_marco_logico')->on('tra_marco_logico')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('indicadores_marco');
    }
};
