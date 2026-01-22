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
        Schema::create('cat_planes_institucionales', function (Blueprint $table) {
            $table->id('id_plan');

            $table->unsignedBigInteger('id_organizacion');
            $table->foreign('id_organizacion')
                  ->references('id_organizacion')
                  ->on('cat_organizacion_estatal')
                  ->onDelete('cascade');

            $table->string('nombre_plan');
            $table->integer('anio_inicio');
            $table->integer('anio_fin');

            $table->enum('tipo_plan', ['PEI', 'PDOT', 'SECTORIAL']);
            $table->enum('estado', ['VIGENTE', 'HISTORICO', 'BORRADOR'])->default('BORRADOR');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_planes_institucionales');
    }
};
