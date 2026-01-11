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

        Schema::create('cat_unidades_ejecutoras', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->id();


            $table->integer('id_organizacion')->index();


            $table->string('nombre_unidad');
            $table->string('codigo_interno')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });


        Schema::table('cat_unidades_ejecutoras', function (Blueprint $table) {
            $table->foreign('id_organizacion')
                ->references('id_organizacion')
                ->on('cat_organizacion_estatal')
                ->onDelete('cascade');
        });

        Schema::table('tra_proyecto_inversion', function (Blueprint $table) {

            $table->unsignedBigInteger('id_unidad_ejecutora')->nullable()->after('nombre_proyecto');

            $table->foreign('id_unidad_ejecutora')
                ->references('id')
                ->on('cat_unidades_ejecutoras');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
