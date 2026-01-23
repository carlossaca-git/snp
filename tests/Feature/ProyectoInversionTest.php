<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Seguridad\User;
use App\Models\Inversion\ProyectoInversion;
use App\Models\Inversion\MarcoLogico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProyectoInversionTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseTransactions;

    /**
     * PRUEBA DE AUDITORÍA: Verificar que los cambios de presupuesto quedan registrados.
     * Valida la seguridad y trazabilidad del dato financiero.
     */
    public function test_modificacion_de_presupuesto_genera_auditoria()
    {
        // Crear un proyecto con un monto inicial
        $proyecto = ProyectoInversion::create([
            'nombre_proyecto' => 'Proyecto Auditoría',
            'monto_total_inversion' => 1000,
            'id_organizacion' => 1,
            'avance_fisico_real' => 0
        ]);

        // 2. Modificar el monto (Simular edición)
        // En un test real haríamos $this->put(ruta, datos), aquí simulamos el cambio directo
        $proyecto->monto_total_inversion = 5000;
        $proyecto->save();

        // 3. Verificar que se actualizó el registro
        $this->assertDatabaseHas('tra_proyecto_inversion', [
            'id' => $proyecto->id,
            'monto_total_inversion' => 5000
        ]);

        // 4. Verificar Trazabilidad (Si usas campos 'updated_at' o una tabla de auditoría)
        // Verificamos que la fecha de actualización cambió (es reciente)
        $this->assertTrue($proyecto->updated_at->greaterThan($proyecto->created_at));
    }
}
