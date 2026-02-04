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
    Schema::table('cat_indicadores_nacionales', function (Blueprint $table) {
        $table->decimal('peso_oficial', 5, 2)->default(0)->after('nombre_indicador');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indicadores_metas', function (Blueprint $table) {
            //
        });
    }
};
