<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
              $table->renameColumn('id', 'id_usuario')->primary();
            $table->renameColumn('name','usuario')->unique();
            $table->renameColumn('password','passwords_hash');
            $table->string('identificacion')->unique();
            $table->string('nombres')->nullable();
            $table->string('apellidos')->nullable();
            $table->renameColumn('email','correo_electronico')->unique();
            $table->integer('intentos_fallidos')->default(0);
            $table->boolean('cuenta_bloqueada')->default(false);
            $table->dateTime('fecha_creacion')->useCurrent();
            $table->char('estado', 1)->default('A');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
             $table->renameColumn('id_usuario', 'id');
            $table->renameColumn('usuario','name')->unique();
            $table->renameColumn('passwords_hash','password');
            $table->dropColumn('identificacion')->unique();
            $table->dropColumn('nombres')->nullable();
            $table->dropColumn('apellidos')->nullable();
            $table->renameColumn('correo_electronico','email');
            $table->dropColumn('intentos_fallidos')->default(0);
            $table->dropColumn('cuenta_bloqueada')->default(false);
            $table->dropColumn('fecha_creacion')->useCurrent();
            $table->dropColumn('estado', 1)->default('A');
        });
    }
};
