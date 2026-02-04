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
        Schema::table('tra_programa', function (Blueprint $table) {
            $table->string('nombre_archivo')->nullable()->after('estado');
            $table->string('url_documento')->nullable()->after('nombre_archivo');
        });
    }

    public function down()
    {
        Schema::table('tra_programa', function (Blueprint $table) {
            $table->dropColumn('nombre_archivo');
            $table->dropColumn('url_documento');
        });
    }
};
