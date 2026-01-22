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
        Schema::table('tra_proyecto_inversion', function (Blueprint $table) {
            $table->unsignedBigInteger('id_usuario_creacion')->nullable()->after('id_organizacion');
            $table->foreign('id_usuario_creacion')
                ->references('id_usuario')->on('seg_usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tra_proyecto_inversion', function (Blueprint $table) {
            $table->dropForeign(['id_usuario_creacion']);
            $table->dropColumn('id_usuario_creacion');
        });
    }
};
