<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. DESACTIVAR REVISIÓN DE LLAVES (Indispensable)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 2. ELIMINAR LAS TABLAS SI EXISTEN (Para limpiar intentos fallidos anteriores)
        // Incluimos la tabla de auditoría que está dando problemas para que se recree bien
        Schema::dropIfExists('aud_proyecto_estado');
        Schema::dropIfExists('tra_financiamiento');
        Schema::dropIfExists('tra_proyecto_localizacion');
        Schema::dropIfExists('tra_proyecto_inversion');
        Schema::dropIfExists('tra_programa');
        Schema::dropIfExists('tra_plan_inversion');

        // 3. EMPEZAR CREACIÓN

        // --- PLANES ---
        Schema::create('tra_plan_inversion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_plan');
            $table->year('anio_fiscal');
            $table->enum('estado', ['Formulación', 'Vigente', 'Cerrado'])->default('Formulación');
            $table->timestamps();
            $table->softDeletes();
        });

        // --- PROGRAMAS ---
        Schema::create('tra_programa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_plan')->constrained('tra_plan_inversion');
            $table->integer('id_organizacion');
            $table->foreign('id_organizacion')->references('id_organizacion')->on('cat_organizacion_estatal');
            $table->string('codigo_programa', 20)->nullable();
            $table->string('nombre_programa');
            $table->timestamps();
            $table->softDeletes();
        });

        // --- PROYECTOS (La tabla del conflicto) ---
        Schema::create('tra_proyecto_inversion', function (Blueprint $table) {
            $table->id(); // Este ID es el que busca la tabla 'aud_proyecto_estado'
            $table->foreignId('id_programa')->constrained('tra_programa')->onDelete('cascade');
            $table->string('cup', 30)->unique()->nullable();
            $table->string('nombre_proyecto');
            $table->text('descripcion_diagnostico')->nullable();
            $table->string('tipo_inversion');
            $table->date('fecha_inicio_estimada');
            $table->date('fecha_fin_estimada');
            $table->integer('duracion_meses')->nullable();
            $table->decimal('monto_total_inversion', 20, 2)->default(0);
            $table->enum('estado_dictamen', ['Solicitado', 'Observado', 'Aprobado', 'Rechazado'])->default('Solicitado');
            $table->timestamps();
            $table->softDeletes();
        });

        // --- LOCALIZACIÓN ---
        Schema::create('tra_proyecto_localizacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_proyecto')->constrained('tra_proyecto_inversion')->onDelete('cascade');
            $table->string('codigo_provincia');
            $table->string('codigo_canton');
            $table->string('codigo_parroquia')->nullable();
            $table->timestamps();
        });

        // --- FINANCIAMIENTO ---
        Schema::create('tra_financiamiento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_proyecto')->constrained('tra_proyecto_inversion')->onDelete('cascade');
            $table->year('anio');
            $table->string('fuente_financiamiento');
            $table->decimal('monto', 20, 2);
            $table->timestamps();
        });

        // 4. REACTIVAR REVISIÓN
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('tra_financiamiento');
        Schema::dropIfExists('tra_proyecto_localizacion');
        Schema::dropIfExists('tra_proyecto_inversion');
        Schema::dropIfExists('tra_programa');
        Schema::dropIfExists('tra_plan_inversion');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
