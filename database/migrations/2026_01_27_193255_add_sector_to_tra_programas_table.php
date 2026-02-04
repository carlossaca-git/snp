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
            $table->enum('sector', ['SOCIAL', 'ECONOMICO', 'INFRAESTRUCTURA'])
                ->after('descripcion')
                ->nullable();
        });
    }

    public function down()
    {
        Schema::table('tra_programa', function (Blueprint $table) {
            $table->dropColumn('sector');
        });
    }
};
