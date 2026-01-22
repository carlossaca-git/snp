<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('metas_anuales', function (Blueprint $table) {
            $table->id('id_meta_anual');
            $table->unsignedBigInteger('indicador_id');


            $table->integer('anio');
            $table->decimal('valor_meta', 12, 2)
                  ->comment('Meta programada para el año específico [cite: 307]');
            $table->decimal('meta_ponderada', 12, 2)->nullable()
                  ->comment('Meta anual ponderada según fórmula de la Guía Pág 16 [cite: 316]');

            $table->timestamps();

            $table->foreign('indicador_id')->references('id_indicador')->on('tra_indicadores_marco')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('metas_anuales');
    }
};

