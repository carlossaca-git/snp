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
            $table->string('url_documento')->nullable()->after('estado');
            $table->string('nombre_documento_original')->nullable()->after('url_documento');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('objetivo_estrategico', function (Blueprint $table) {
            //
        });
    }
};
