@extends('layouts.app')

@section('content')
    {{-- Estilos personalizados para este archivo --}}
    <style>
        /* Paleta de colores Institucional */
        .text-slate-800 { color: #1e293b; }
        .text-slate-500 { color: #64748b; }
        .bg-slate-50 { background-color: #f8fafc; }

        /* Hover suave en la fila */
        .clickable-row:hover {
            background-color: #f1f5f9 !important;
            cursor: pointer;
        }

        /* Botón principal */
        .btn-institutional {
            background-color: #2563eb;
            border-color: #2563eb;
            color: white;
        }

        .btn-institutional:hover {
            background-color: #1d4ed8;
            color: white;
        }

        /* Tarjeta limpia */
        .card-clean {
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }

        /* Ajuste para iconos de Feather si los usas */
        .icon-sm { width: 16px; height: 16px; vertical-align: text-bottom; }
    </style>

    <div class="container mx-auto py-5">

        {{-- 1. ENCABEZADO MODERNO --}}
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="h3 fw-bold text-slate-800 mb-1">Bitácora de Auditoría</h1>
                <p class="text-slate-500 mb-0">Trazabilidad y monitoreo de acciones realizadas en el sistema</p>
            </div>

            <div class="row align-items-center">
                {{-- COLUMNA IZQUIERDA: BUSCADOR --}}
                <div class="col-md-12">
                    <form action="{{ route('auditoria.index') }}" method="GET">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i data-feather="search" class="icon-sm"></i>
                            </span>
                            <input type="text" name="busqueda"
                                class="form-control border-start-0 border-end-0 shadow-none"
                                placeholder="Buscar por usuario, módulo o IP..." value="{{ request('busqueda') }}">

                            @if (request('busqueda'))
                                <a href="{{ route('auditoria.index') }}"
                                    class="btn bg-white border-top border-bottom border-end-0 text-danger"
                                    title="Limpiar filtro">
                                    <i data-feather="x" class="icon-sm"></i>
                                </a>
                            @endif
                            <button class="btn btn-primary" type="submit">Buscar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 2. MENSAJES DE ALERTA --}}
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                <i data-feather="check-circle" class="me-2"></i> {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <hr class="mb-3">

        {{-- 3. TARJETA DE LA TABLA --}}
        <div class="card card-clean rounded-3 overflow-hidden bg-white mb-5">
            <div class="card-header py-3 bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-slate-800">
                        <i data-feather="list" class="me-2 text-muted icon-sm"></i> Registros de Actividad
                    </h6>
                    <span class="badge bg-light text-dark border rounded-pill">
                        Total: {{ $logs->total() }}
                    </span>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 ps-4 text-uppercase text-secondary small fw-bold">Fecha / Hora</th>
                                <th class="py-3 text-uppercase text-secondary small fw-bold">Usuario</th>
                                <th class="py-3 text-uppercase text-secondary small fw-bold">Módulo</th>
                                <th class="py-3 text-uppercase text-secondary small fw-bold text-center">Acción</th>
                                <th class="py-3 text-uppercase text-secondary small fw-bold">Dirección IP</th>
                                <th class="py-3 pe-4 text-end text-uppercase text-secondary small fw-bold">Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr class="clickable-row" onclick="window.location='{{ route('auditoria.show', $log->id_auditoria) }}'">
                                    <td class="ps-4">
                                        <div class="fw-medium text-slate-800">{{ $log->fecha_hora->format('d/m/Y') }}</div>
                                        <div class="small text-slate-500">{{ $log->fecha_hora->format('H:i:s') }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2 bg-slate-50 border rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i data-feather="user" class="text-slate-500" style="width: 14px;"></i>
                                            </div>
                                            <span class="text-slate-800">{{ $log->usuario->usuario ?? 'Sistema' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-slate-50 text-slate-500 border">{{ $log->modulo }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($log->accion == 'CREAR')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3">CREAR</span>
                                        @elseif($log->accion == 'MODIFICAR' || $log->accion == 'ACTUALIZAR')
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3">MODIFICAR</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3">ELIMINAR</span>
                                        @endif
                                    </td>
                                    <td>
                                        <code class="small text-slate-500">{{ $log->ip_address }}</code>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('auditoria.show', $log->id_auditoria) }}"
                                           class="btn btn-sm btn-white border shadow-sm text-slate-800">
                                            <i data-feather="eye" class="icon-sm me-1"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-center text-slate-500">
                                        No se encontraron registros de auditoría.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- FOOTER CON PAGINACIÓN --}}
            @if($logs->hasPages())
                <div class="card-footer bg-white py-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small text-slate-500">
                            Mostrando {{ $logs->firstItem() }} a {{ $logs->lastItem() }} de {{ $logs->total() }} resultados
                        </div>
                        <div>
                            {{ $logs->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Script para activar Feather Icons --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>
@endsection
