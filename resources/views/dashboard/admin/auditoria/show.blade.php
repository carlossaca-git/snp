@extends('layouts.app')

@section('content')
    <style>
        .text-slate-800 { color: #1e293b; }
        .text-slate-500 { color: #64748b; }
        .card-clean {
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            background-color: #fff;
        }
        .bg-light-gray { background-color: #f8fafc; }

        /* ESTILOS DE LA TABLA COMPARATIVA  */
        .table-diff th {
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background-color: #f8f9fa; /* Gris muy suave para cabecera */
        }

        /* Valor Anterior: Rojo claro pero evidente */
        .diff-old {
            background-color: #ffe5e5;
            color: #b91c1c;
            border-right: 1px solid #fecaca;
        }

        /* Valor Nuevo: Verde claro pero evidente */
        .diff-new {
            background-color: #d1e7dd;
            color: #0f5132;
        }
    </style>

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h4 fw-bold text-slate-800 mb-0">
                    <i class="fas fa-history text-secondary me-2"></i>Detalle de Auditoría
                </h2>
                <p class="text-slate-500 small mb-0">Movimiento registrado: <strong>#{{ $auditoria->id_auditoria }}</strong></p>
            </div>
            <a href="{{ route('administracion.auditoria.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fas fa-arrow-left me-1"></i> Regresar
            </a>
        </div>
        <hr class="mb-3">
        {{--  RESUMEN  --}}
        <div class="card card-clean rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="m-0 fw-bold text-slate-800">
                    <i class="far fa-file-alt me-2 text-primary"></i>Resumen del Evento
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6 border-end">
                        <div class="mb-3">
                            <label class="text-xs text-uppercase text-slate-500 fw-bold d-block">Fecha y Hora</label>
                            <span class="fs-5 text-slate-800">
                                {{ $auditoria->fecha_hora->format('d/m/Y') }}
                                <small class="text-muted ms-1">{{ $auditoria->fecha_hora->format('H:i:s') }}</small>
                            </span>
                        </div>

                        <div class="mb-3">
                            <label class="text-xs text-uppercase text-slate-500 fw-bold d-block">Usuario Responsable</label>
                            <div class="d-flex align-items-center mt-1">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width:35px; height:35px;">
                                    <i class="fas fa-user text-secondary"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-slate-800">
                                        {{ $auditoria->usuario ? $auditoria->usuario->usuario : 'Sistema / Desconocido' }}
                                    </div>
                                    <div class="small text-slate-500">
                                        {{ $auditoria->usuario->nombres ?? '' }} {{ $auditoria->usuario->apellidos ?? '' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs text-uppercase text-slate-500 fw-bold d-block">Dirección IP</label>
                            <code class="text-dark bg-light px-2 py-1 rounded">{{ $auditoria->ip_address }}</code>
                        </div>
                    </div>
                    <div class="col-md-6 ps-md-4">
                        <div class="mb-3">
                            <label class="text-xs text-uppercase text-slate-500 fw-bold d-block">Módulo</label>
                            <span class="badge bg-light text-dark border mt-1">{{ $auditoria->modulo }}</span>
                        </div>

                        <div class="mb-3">
                            <label class="text-xs text-uppercase text-slate-500 fw-bold d-block">Acción Realizada</label>
                            <div class="mt-1">
                                @if ($auditoria->accion == 'CREAR')
                                    <span class="badge bg-success"><i class="fas fa-plus me-1"></i> CREACIÓN</span>
                                @elseif($auditoria->accion == 'MODIFICAR')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-pen me-1"></i> MODIFICACIÓN</span>
                                @elseif($auditoria->accion == 'ELIMINAR')
                                    <span class="badge bg-danger"><i class="fas fa-trash me-1"></i> ELIMINACIÓN</span>
                                @else
                                    <span class="badge bg-secondary">{{ $auditoria->accion }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TARJETA DE EVIDENCIA  --}}
        <div class="card card-clean rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="m-0 fw-bold text-slate-800">
                    <i class="fas fa-database me-2 text-primary"></i>Datos Afectados
                </h6>
            </div>
            <div class="card-body p-0">

                {{-- MODIFICACIÓN --}}
                @if ($auditoria->accion == 'MODIFICAR')
                    <div class="p-3 bg-light border-bottom">
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Comparativa: Izquierda (Valor Antiguo) vs Derecha (Valor Nuevo).</small>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle table-diff">
                            <thead>
                                <tr>
                                    <th class="ps-4 text-secondary" style="width: 25%">Campo Modificado</th>
                                    <th class="text-danger" style="width: 37.5%"><i class="fas fa-minus-circle me-1"></i> Valor Anterior</th>
                                    <th class="text-success" style="width: 37.5%"><i class="fas fa-plus-circle me-1"></i> Valor Nuevo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $allKeys = array_unique(array_merge(array_keys($auditoria->data_original ?? []), array_keys($auditoria->data_nueva ?? [])));
                                    $cambios = false;
                                @endphp

                                @foreach ($allKeys as $key)
                                    @php
                                        $valOld = $auditoria->data_original[$key] ?? null;
                                        $valNew = $auditoria->data_nueva[$key] ?? null;
                                        $diff = json_encode($valOld) !== json_encode($valNew);
                                    @endphp

                                    @if ($diff)
                                        @php $cambios = true; @endphp
                                        <tr>
                                            <td class="ps-4 fw-bold text-slate-800 text-capitalize border-end">
                                                {{ str_replace('_', ' ', $key) }}
                                            </td>

                                            {{-- Celda ROJA (Anterior) --}}
                                            <td class="diff-old font-monospace small">
                                                @if(is_array($valOld))
                                                    {{ json_encode($valOld) }}
                                                @else
                                                    {{ $valOld ?? 'Vacío / Nulo' }}
                                                @endif
                                            </td>

                                            {{-- Celda VERDE (Nuevo) --}}
                                            <td class="diff-new font-monospace small fw-bold">
                                                @if(is_array($valNew))
                                                    {{ json_encode($valNew) }}
                                                @else
                                                    {{ $valNew ?? 'Vacío / Nulo' }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach

                                @if (!$cambios)
                                    <tr><td colspan="3" class="text-center py-4 text-muted">No se detectaron cambios efectivos.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                {{--  CREACIÓN --}}
                @elseif($auditoria->accion == 'CREAR')
                    <div class="p-4">
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="fas fa-check-circle fs-4 me-3"></i>
                            <div><strong>Registro Exitoso:</strong> Datos nuevos ingresados.</div>
                        </div>
                        <div class="row g-3 mt-2">
                            @foreach ($auditoria->data_nueva as $key => $value)
                                <div class="col-md-6 col-lg-4">
                                    <div class="border rounded p-3 h-100 bg-light shadow-sm">
                                        <label class="text-xs text-uppercase text-success fw-bold mb-1">{{ str_replace('_', ' ', $key) }}</label>
                                        <div class="text-dark fw-bold text-break">
                                            {{ is_array($value) ? json_encode($value) : $value }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                {{-- ELIMINACIÓN --}}
                @elseif($auditoria->accion == 'ELIMINAR')
                    <div class="p-4">
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-trash-alt fs-4 me-3"></i>
                            <div><strong>Registro Eliminado:</strong> Datos que fueron borrados.</div>
                        </div>
                        <div class="row g-3 mt-2">
                            @foreach ($auditoria->data_original as $key => $value)
                                <div class="col-md-6 col-lg-4">
                                    <div class="border border-danger rounded p-3 h-100 bg-white" style="border-left: 5px solid #dc3545 !important;">
                                        <label class="text-xs text-uppercase text-danger fw-bold mb-1">{{ str_replace('_', ' ', $key) }}</label>
                                        <div class="text-dark text-break">
                                            {{ is_array($value) ? json_encode($value) : $value }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
