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
        Schema::table('cat_indicador', function (Blueprint $table) {
            // Añadimos los campos después de frecuencia
            $table->string('fuente_informacion')->nullable()->after('frecuencia');
            $table->text('descripcion_indicador')->nullable()->after('fuente_informacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cat_indicador', function (Blueprint $table) {
            $table->dropColumn(['fuente_informacion', 'descripcion_indicador']);
        });
    }
};
