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
        Schema::table('cat_organizacion_estatal', function (Blueprint $table) {
            // Agregamos solo si no existen (para evitar errores)
            if (!Schema::hasColumn('organizaciones', 'mision')) {
                $table->text('mision')->nullable()->after('nom_organizacion');
            }
            if (!Schema::hasColumn('organizaciones', 'vision')) {
                $table->text('vision')->nullable()->after('mision');
            }
            if (!Schema::hasColumn('organizaciones', 'telefono')) {
                $table->string('telefono')->nullable()->after('vision');
            }
            if (!Schema::hasColumn('organizaciones', 'logo')) {
                $table->string('logo')->nullable()->after('email'); // Ruta del archivo
            }
        });
    }

    public function down()
    {
        Schema::table('cat_organizacion_estatal', function (Blueprint $table) {
            $table->dropColumn(['mision', 'vision', 'telefono', 'logo']);
        });
    }
};
