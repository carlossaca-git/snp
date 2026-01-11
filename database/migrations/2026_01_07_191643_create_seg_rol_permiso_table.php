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
        Schema::create('seg_rol_permiso', function (Blueprint $table) {
            $table->id('id_rol_permiso');

            // Relación con el Rol
            $table->unsignedBigInteger('id_rol');
            $table->foreign('id_rol')->references('id_rol')->on('seg_rol')->onDelete('cascade');

            // Relación con el Permiso
            $table->unsignedBigInteger('id_permiso');
            $table->foreign('id_permiso')->references('id_permiso')->on('seg_permiso')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seg_rol_permiso');
    }
};
