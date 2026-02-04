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
        Schema::create('tra_plan_inversion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organizacion_id');

            $table->foreign('organizacion_id')
                ->references('id_organizacion')
                ->on('cat_organizacion_estatal')
                ->onDelete('cascade');

            $table->string('nombre');
            $table->year('anio');
            $table->decimal('monto_total', 15, 2);
            // Estado del plan de inversión
            $table->enum('estado', ['FORMULACION', 'APROBADO', 'EJECUCION', 'CERRADO'])->default('FORMULACION');

            $table->text('descripcion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tra_plan_inversion');
    }
};
