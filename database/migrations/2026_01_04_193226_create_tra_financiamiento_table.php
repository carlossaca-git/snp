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
        Schema::create('tra_financiamiento', function (Blueprint $table) {
            $table->id(); // Este será tu PK (id)

            // Relación con el proyecto (Padre)
            // 'id_proyecto' coincida con el nombre y tipo de la tabla tra_proyecto_inversion
            $table->unsignedBigInteger('id_proyecto');

            $table->integer('anio');

            // Usamos unsignedBigInteger para la fuente, asumiendo que crearás cat_fuentes
            $table->unsignedBigInteger('id_fuente');

            // Monto con 15 dígitos en total y 2 decimales
            $table->decimal('monto', 15, 2);

            $table->timestamps(); // Crea created_at y updated_at

            // Definición de la Llave Foránea para integridad de datos
            $table->foreign('id_proyecto')
                ->references('id_proyecto') // Nombre de la PK en tu tabla proyectos
                ->on('tra_proyecto_inversion')
                ->onDelete('cascade'); // Si se borra el proyecto, se borra su financiamiento
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tra_financiamiento');
    }
};
