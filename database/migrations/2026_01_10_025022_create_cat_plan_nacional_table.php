<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_plan_nacional', function (Blueprint $table) {

            $table->id('id_plan');
            $table->string('nombre', 255);
            $table->integer('periodo_inicio');
            $table->integer('periodo_fin');
            $table->string('registro_oficial')->nullable();
            $table->enum('estado', ['ACTIVO', 'INACTIVO', 'HISTORICO'])->default('ACTIVO');


            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_plan_nacional');
    }
};
