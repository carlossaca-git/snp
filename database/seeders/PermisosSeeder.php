<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Permisos;

class PermisosSeeder extends Seeder
{
    public function run()
    {
        $permisos = [

            // MÓDULO GESTIÓN INSTITUCIONAL (Admin y Configuración)

            [
                'nombre_largo' => 'Ver Usuarios',
                'name'   => 'sis.usuarios.ver',
                'modulo' => 'Institucional',
                'descripcion' => 'Permite ver el listado de usuarios del sistema.'
            ],
            [
                'nombre_largo' => 'Gestionar Usuarios',
                'name'   => 'sis.usuarios.gestion',
                'modulo' => 'Institucional',
                'descripcion' => 'Permite crear, editar usuarios y resetear contraseñas.'
            ],
            [
                'nombre_largo' => 'Asignar Roles',
                'name'   => 'sis.roles.asignar',
                'modulo' => 'Institucional',
                'descripcion' => 'Permite cambiar el rol y permisos de un usuario.'
            ],
            [
                'nombre_largo' => 'Gestionar Organización',
                'name'   => 'inst.organizacion.editar',
                'modulo' => 'Institucional',
                'descripcion' => 'Permite editar datos de la propia institución (Misión, Visión, Logo).'
            ],


            //PLANIFICACIÓN ESTRATÉGICA (PEI / POA)

            [
                'nombre_largo' => 'Gestionar Objetivos (PEI)',
                'name'   => 'plan.pei.gestionar',
                'modulo' => 'Planificación',
                'descripcion' => 'Crear y editar objetivos estratégicos e indicadores.'
            ],
            [
                'nombre_largo' => 'Cargar POA',
                'name'   => 'plan.poa.cargar',
                'modulo' => 'Planificación',
                'descripcion' => 'Registrar actividades y metas anuales.'
            ],
            [
                'nombre_largo' => 'Aprobar POA',
                'name'   => 'plan.poa.aprobar',
                'modulo' => 'Planificación',
                'descripcion' => 'Autorizar la planificación anual (Rol Directivo).'
            ],


            // INVERSIÓN PÚBLICA (Proyectos)

            [
                'nombre_largo' => 'Postular Proyectos',
                'name'   => 'inv.proyecto.crear',
                'modulo' => 'Inversión',
                'descripcion' => 'Crear nuevas fichas de proyectos y subir requisitos.'
            ],
            [
                'nombre_largo' => 'Editar Proyecto',
                'name'   => 'inv.proyecto.editar',
                'modulo' => 'Inversión',
                'descripcion' => 'Modificar datos técnicos de proyectos en estado borrador.'
            ],
            [
                'nombre_largo' => 'Emitir Dictamen',
                'name'   => 'inv.dictamen.emitir',
                'modulo' => 'Inversión',
                'descripcion' => 'Rol Rector: Aprobar o rechazar la viabilidad del proyecto.'
            ],


            // SEGUIMIENTO Y EVALUACIÓN

            [
                'nombre_largo' => 'Reportar Avance',
                'name'   => 'seg.avance.reportar',
                'modulo' => 'Seguimiento',
                'descripcion' => 'Cargar avances físicos y presupuestarios mensuales.'
            ],
            [
                'nombre_largo' => 'Ver Reportes Gerenciales',
                'name'   => 'seg.reportes.ver',
                'modulo' => 'Seguimiento',
                'descripcion' => 'Acceso a tableros de control y alertas de gestión.'
            ]
        ];

        foreach ($permisos as $permiso) {
            DB::table('seg_permiso')->updateOrInsert(
                ['name' => $permiso['name']],
                $permiso
            );
        }
    }
}
