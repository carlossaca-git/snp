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
        Schema::table('cat_objetivo_estrategico', function (Blueprint $table) {
            $table->foreignId('usuario_id')
                ->nullable()
                ->after('organizacion_id')
                ->constrained('seg_usuario', 'id_usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('objetivo_estrategico_', function (Blueprint $table) {
            //
        });
    }
};
