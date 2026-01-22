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
        Schema::create('seg_usuario_permiso', function (Blueprint $table) {

            $table->unsignedBigInteger('id_permiso');
            $table->string('model_type');
            $table->unsignedBigInteger('id_usuario');

            // Llave foránea hacia la tabla de permisos
            $table->foreign('id_permiso')
                  ->references('id_permiso')
                  ->on('seg_permiso')
                  ->onDelete('cascade');

            // Llave Primaria Compuesta
            $table->primary(['id_permiso', 'id_usuario', 'model_type'], 'pk_seg_usu_perm');

            // Índice para hacer las búsquedas rápidas por usuario
            $table->index(['id_usuario', 'model_type'], 'idx_seg_usu_perm_usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seg_usuario_permiso');
    }
};
