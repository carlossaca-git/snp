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
    Schema::table('cat_plan_nacional', function (Blueprint $table) {
        $table->year('periodo_inicio')->change();
        $table->year('periodo_fin')->change();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('cat_plan_nacional', function (Blueprint $table) {
        $table->integer('periodo_inicio')->change();
        $table->integer('periodo_fin')->change();
    });
}
};
