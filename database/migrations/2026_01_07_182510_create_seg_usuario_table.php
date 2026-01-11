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
        Schema::create('seg_usuario', function (Blueprint $table) {
            $table->id('id_usuario'); // PK
            $table->string('identificacion', 10)->unique(); // Cédula
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('correo_electronico')->unique();
            $table->string('usuario')->unique(); // Login
            $table->string('password'); // Password encriptado

            // Relación con la Institución (Fundamental)
            $table->integer('id_organizacion')->nullable();
            $table->foreign('id_organizacion')->references('id_organizacion')->on('cat_organizacion_estatal');

            $table->integer('intentos_fallidos')->default(0);
            $table->boolean('estado')->default(true); // Activo por defecto

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken(); // Para la sesión de Breeze
            $table->timestamps(); // Crea created_at y updated_at automáticamente
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seg_usuario');
    }
};
