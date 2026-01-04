@extends('layouts.app')
@section('title', 'Resumen')
@section('header', 'Resumen General')
@section('content')
    <div class="container-fluid">
        <!-- Encabezado del Dashboard -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Panel de Control Administrativo</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary">
                        <span data-feather="calendar"></span> {{ date('d/m/Y') }}
                    </button>
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                        <span data-feather="calendar"></span>
                        This week
                    </button>
                </div>
            </div>
        </div>

        <!-- Fila de Tarjetas de Indicadores (KPIs) -->
        <div class="row">
            <!-- Usuarios Registrados (Tabla seg_usuario) -->
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1 small">Usuarios Activos</h6>
                                <h2 class="mb-0">12</h2> {{-- Aquí irá tu variable {{ $totalUsuarios }} --}}
                            </div>
                            <span data-feather="users" style="width: 48px; height: 48px; opacity: 0.5;"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inversión Total (Tabla tra_proyecto_inversion) -->
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1 small">Inversión Planificada</h6>
                                <h2 class="mb-0">$4.5M</h2>
                            </div>
                            <span data-feather="dollar-sign" style="width: 48px; height: 48px; opacity: 0.5;"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Auditoría (Alertas Recientes) -->
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm bg-warning text-dark h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1 small">Cambios Hoy</h6>
                                <h2 class="mb-0">45</h2>
                            </div>
                            <span data-feather="activity" style="width: 48px; height: 48px; opacity: 0.3;"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estado de Proyectos -->
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm bg-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1 small">Proyectos Nuevos</h6>
                                <h2 class="mb-0">8</h2>
                            </div>
                            <span data-feather="file-text" style="width: 48px; height: 48px; opacity: 0.5;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección Inferior: Auditoría y Actividad -->
        <div class="row mt-2">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-muted">Últimos Proyectos Registrados</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0">Proyecto</th>
                                        <th class="border-0">Entidad</th>
                                        <th class="border-0">Monto</th>
                                        <th class="border-0">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Remodelación Hospital Sur</td>
                                        <td>Min. Salud</td>
                                        <td>$1,200,000</td>
                                        <td><span
                                                class="badge bg-soft-success text-success border border-success">Aprobado</span>
                                        </td>
                                    </tr>
                                    {{-- Aquí harás un @foreach de tus proyectos reales --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Lateral de Auditoría (seg_auditoria) -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-muted text-uppercase small">Bitácora de Seguridad</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            {{-- Ejemplo de lo que traerás de tu tabla de auditoría --}}
                            <div class="list-group-item px-0 border-0 mb-2">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 fw-bold small">Admin_TI</h6>
                                    <small class="text-muted">hace 5 min</small>
                                </div>
                                <p class="mb-1 small text-muted">Actualizó el perfil del usuario "tecnico_01".</p>
                            </div>
                            <div class="list-group-item px-0 border-0">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 fw-bold small">SuperAdmin</h6>
                                    <small class="text-muted">hace 1 hora</small>
                                </div>
                                <p class="mb-1 small text-muted">Eliminó un registro de la tabla de indicadores.</p>
                            </div>
                        </div>
                        <div class="d-grid mt-3">
                            <a href="{{ route('dashboard', ['seccion' => 'auditoria']) }}"
                                class="btn btn-sm btn-outline-primary">
                                Ver toda la actividad
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Estilos para badges suaves (opcional) */
        .bg-soft-success {
            background-color: rgba(40, 167, 69, 0.1);
        }

        .border-left-primary {
            border-left: 4px solid #007bff !important;
        }
    </style>
@endsection
