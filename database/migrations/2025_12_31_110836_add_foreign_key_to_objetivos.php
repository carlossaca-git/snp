<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// --- AQUI EMPIEZA LA CLASE (Los 'use' van arriba de esta línea) ---
return new class extends Migration
{
    public function up()
    {
        // AQUÍ VA EL CÓDIGO DE LA TABLA, PERO NO LOS 'USE'
        Schema::table('cat_objetivo_nacional', function (Blueprint $table) {
            $table->foreign('id_ods')
                  ->references('id_ods')
                  ->on('cat_ods')
                  ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::table('cat_objetivo_nacional', function (Blueprint $table) {
            $table->dropForeign(['id_ods']);
        });
    }
};
