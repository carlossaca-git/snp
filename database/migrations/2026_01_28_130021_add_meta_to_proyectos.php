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
        Schema::table('tra_proyecto_inversion', function (Blueprint $table) {
            $table->foreignId('meta_nacional_id')
                ->nullable()
                ->after('objetivo_estrategico_id')
                ->constrained('cat_meta_nacional', 'id_meta_nacional');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tra_proyecto_inversion', function (Blueprint $table) {
            //
        });
    }
};
