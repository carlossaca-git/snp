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
        Schema::table('cat_eje_pnd', function (Blueprint $table) {
            // Agregamos el campo después de la descripción
            $table->string('url_documento', 500)->nullable()->after('descripcion');
        });
    }

    public function down()
    {
        Schema::table('cat_eje_pnd', function (Blueprint $table) {
            $table->dropColumn('url_documento');
        });
    }
};
