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
        Schema::create('alineacion_metas_ods', function (Blueprint $table) {
            $table->id();


            $table->unsignedBigInteger('id_meta_nacional');
            $table->unsignedBigInteger('id_ods');


            $table->foreign('id_meta_nacional')
                ->references('id_meta_nacional')
                ->on('cat_meta_nacional')
                ->onDelete('cascade');

            $table->foreign('id_ods')
                ->references('id_ods')
                ->on('cat_ods')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alineacion_metas_ods');
    }
};
