<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //ACTUALIZACIÓN DE PERMISOS EXISTENTES Renombrar names y asignar Módulos

        $actualizaciones = [

            'Ver Usuarios'           => ['name' => 'usuarios.ver',       'modulo' => 'Administración'],
            'Gestionar Usuarios'     => ['name' => 'usuarios.gestionar', 'modulo' => 'Administración'],
            'Asignar Roles'          => ['name' => 'roles.gestionar',    'modulo' => 'Administración'],


            'Gestionar Organizacion' => ['name' => 'organizacion.editar',     'modulo' => 'Institucional'],
            'Gestionar Objetivos OEI'=> ['name' => 'planificacion.gestionar', 'modulo' => 'Estratégico'],
            'Cargar POA'             => ['name' => 'poa.cargar',              'modulo' => 'Planificación'],
            'AprobarPOA'             => ['name' => 'poa.aprobar',             'modulo' => 'Planificación'],


            'Postular Proyectos'     => ['name' => 'proyectos.crear',    'modulo' => 'Inversión'],
            'Editar Proyecto'        => ['name' => 'proyectos.editar',   'modulo' => 'Inversión'],
            'Emitir Dictamen'        => ['name' => 'proyectos.dictamen', 'modulo' => 'Inversión'],


            'Reportar Avance'        => ['name' => 'avance.reportar',    'modulo' => 'Seguimiento'],
            'Ver Reportes'           => ['name' => 'reportes.ver',       'modulo' => 'Reportes'],
        ];

        foreach ($actualizaciones as $nombreViejo => $datos) {

            DB::table('seg_permiso')
                ->where('nombre_largo_largo', $nombreViejo)
                ->update([
                    'name' => $datos['name'],
                    'modulo' => $datos['modulo'],
                    'updated_at' => now()
                ]);
        }



        $nuevosPermisos = [

            [
                'nombre_largo' => 'Ver Auditoría',
                'name' => 'auditoria.ver',
                'modulo' => 'Administración',
                'descripcion' => 'Permite ver los logs del sistema'
            ],

            [
                'nombre_largo' => 'Gestionar Catálogos',
                'name' => 'catalogos.gestionar',
                'modulo' => 'Normativa',
                'descripcion' => 'Crear y editar Ejes, Objetivos PND y ODS'
            ],

            [
                'nombre_largo' => 'Ver Proyectos',
                'name' => 'proyectos.ver',
                'modulo' => 'Inversión',
                'descripcion' => 'Permite acceder al listado general de proyectos'
            ],

            [
                'nombre_largo' => 'Eliminar Proyectos',
                'name' => 'proyectos.eliminar',
                'modulo' => 'Inversión',
                'descripcion' => 'Permite eliminar registros de proyectos (Solo Jefes)'
            ],

             [
                'nombre_largo' => 'Eliminar Documentos',
                'name' => 'documentos.eliminar',
                'modulo' => 'Inversión',
                'descripcion' => 'Permite borrar archivos adjuntos'
            ]
        ];

        foreach ($nuevosPermisos as $permiso) {

            DB::table('seg_permiso')->updateOrInsert(
                ['name' => $permiso['name']], //
                [
                    'nombre_largo' => $permiso['nombre_largo'],
                    'modulo' => $permiso['modulo'],
                    'descripcion' => $permiso['descripcion'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
