<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{

    public function up(): void
    {
        Schema::table('cat_eje_pnd', function (Blueprint $table) {
            // Agregamos la columna FK después del ID
            $table->unsignedBigInteger('id_plan_nacional')->after('id_eje');

            // Definimos la relación
            $table->foreign('id_plan_nacional')
                  ->references('id_plan_nacional')
                  ->on('cat_plan_nacional')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('cat_eje_pnd', function (Blueprint $table) {
            $table->dropForeign(['id_plan_nacional']);
            $table->dropColumn('id_plan_nacional');
        });
    }
};
