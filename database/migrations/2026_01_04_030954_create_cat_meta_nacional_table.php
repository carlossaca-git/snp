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
        Schema::create('cat_meta_nacional', function (Blueprint $table) {
            $table->id('id_meta');
            // Llave foránea hacia objetivos
            $table->integer('id_objetivo_nacional');
            $table->string('codigo_meta', 50)->nullable();
            $table->text('nombre_meta');
            $table->text('descripcion_meta')->nullable();
            $table->string('url_documento')->nullable();
            $table->boolean('estado')->default(1);
            $table->timestamps();

            // Relación: Si se borra el objetivo, qué pasa con la meta (usualmente restrict)
            $table->foreign('id_objetivo')
                ->references('id_objetivo')
                ->on('cat_objetivo_nacional')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_meta_nacional');
    }
};
