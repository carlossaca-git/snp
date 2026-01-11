<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos_proyectos', function (Blueprint $table) {
            $table->id();

            // Relación con el proyecto
            // Nota: La tabla 'proyectos_inversion' debe existir antes de ejecutar esto
            $table->foreignId('id_proyecto')
                  ->constrained('tra_proyecto_inversion')
                  ->onDelete('cascade');

            $table->string('nombre_archivo'); // Ej: "Certificación Presupuestaria 2026"
            $table->string('tipo_documento'); // Ej: LEGAL, TECNICO, FINANCIERO
            $table->string('url_archivo');    // La ruta en storage
            $table->string('extension', 10)->nullable(); // pdf, docx, xls

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_proyectos');
    }
};
