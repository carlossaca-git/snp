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
        Schema::create('seg_usuario_perfil', function (Blueprint $table) {
            $table->id('id_usuario_perfil'); // Llave primaria

            // Conexión con Usuario
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id_usuario')->on('seg_usuario')->onDelete('cascade');

            // Conexión con Rol <-- ESTA ES LA QUE FALTA EN TU LISTA
            $table->unsignedBigInteger('id_rol');
            $table->foreign('id_rol')->references('id_rol')->on('seg_rol')->onDelete('cascade');

            $table->timestamps(); // Crea created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seg_usuario_perfil');
    }
};
