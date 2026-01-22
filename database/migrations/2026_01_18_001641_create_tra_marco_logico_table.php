<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tra_marco_logico', function (Blueprint $table) {
            $table->id('id_marco_logico');

            $table->unsignedBigInteger('proyecto_id');
            $table->enum('nivel', ['FIN', 'PROPOSITO', 'COMPONENTE', 'ACTIVIDAD'])
                  ->comment('Jerarquía del Marco Lógico ');
            $table->text('resumen_narrativo')
                  ->comment('Descripción del objetivo o componente ');
            $table->text('supuestos')->nullable()
                  ->comment('Condiciones necesarias para el éxito fuera del control del gerente ');

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('proyecto_id')->references('id')->on('tra_proyecto_inversion')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('marco_logico');
    }
};
