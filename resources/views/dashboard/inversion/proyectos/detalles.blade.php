@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- Encabezado con botones de acción --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('inversion.proyectos.index') }}">Banco de Proyectos</a>
                        </li>
                        <li class="breadcrumb-item active">Ficha Técnica</li>
                    </ol>
                </nav>
                <h2 class="h3 mb-0 text-gray-800">{{ $proyecto->cup ?? 'Sin CUP' }}</h2>
            </div>
            <div>
                <a href="{{ route('inversion.proyectos.index') }}" class="btn btn-outline-secondary me-2">
                    <span data-feather="arrow-left"></span> Volver
                </a>
                <a href="#" class="btn btn-primary">
                    <span data-feather="edit"></span> Editar Proyecto
                </a>
            </div>
        </div>

        <div class="row">
            {{-- Columna Izquierda: Información Principal --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 text-primary">Información General</h5>
                    </div>
                    <div class="card-body">
                        <h4 class="mb-3">{{ $proyecto->nombre_proyecto }}</h4>
                        <div class="row mb-4">
                            <div class="col-sm-4 text-muted">Programa Asociado:</div>
                            <div class="col-sm-8 fw-bold text-dark">
                                {{ $proyecto->programa->nombre_programa ?? 'No asignado' }}
                            </div>
                        </div>
                        <hr>
                        <h6 class="text-uppercase text-muted small fw-bold">Diagnóstico y Justificación</h6>
                        <p class="text-dark" style="text-align: justify; line-height: 1.6;">
                            {{ $proyecto->descripcion_diagnostico ?? 'No se ha ingresado un diagnóstico.' }}
                        </p>
                        {{-- En tu archivo detalles.blade.php, dentro de la columna derecha o debajo del diagnóstico --}}
                        <div class="card shadow-sm border-0 mt-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title mb-0 small fw-bold text-primary">Ubicación Geográfica</h5>
                            </div>
                            <div class="card-body">
                                @if ($proyecto->localizaciones->count() > 0)
                                    @foreach ($proyecto->localizaciones as $loc)
                                        <div class="d-flex align-items-center mb-2">
                                            <span data-feather="map-pin" class="text-danger me-2"></span>
                                            <div>
                                                <span class="d-block fw-bold">{{ $loc->codigo_provincia }}</span>
                                                <span class="text-muted small">{{ $loc->codigo_canton }} -
                                                    {{ $loc->codigo_parroquia ?? 'Sin Parroquia' }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted small mb-0">No hay ubicación registrada.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna Derecha: Datos Rápidos y Estado --}}
            <div class="col-lg-4">
                {{-- Tarjeta de Estado y Monto --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body text-center py-4">
                        <h6 class="text-muted text-uppercase small">Monto Total de Inversión</h6>
                        <h2 class="display-6 fw-bold text-success">${{ number_format($proyecto->monto_total_inversion, 2) }}
                        </h2>
                        <span
                            class="badge rounded-pill bg-{{ $proyecto->estado_dictamen == 'Aprobado' ? 'success' : 'warning' }} px-3 py-2">
                            Estado: {{ $proyecto->estado_dictamen }}
                        </span>
                    </div>
                </div>

                {{-- Tarjeta de Tiempos --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0 small fw-bold">Cronograma Estimado</h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><span data-feather="calendar" class="me-2 text-muted"></span>Inicio:</span>
                            <span class="fw-bold">{{ $proyecto->fecha_inicio_estimada->format('d/m/Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><span data-feather="calendar" class="me-2 text-muted"></span>Fin:</span>
                            <span class="fw-bold">{{ $proyecto->fecha_fin_estimada->format('d/m/Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><span data-feather="clock" class="me-2 text-muted"></span>Duración:</span>
                            <span class="badge bg-light text-dark border">{{ $proyecto->duracion_meses }} meses</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><span data-feather="tag" class="me-2 text-muted"></span>Tipo:</span>
                            <span class="fw-bold text-primary">{{ $proyecto->tipo_inversion }}</span>
                        </li>
                    </ul>
                    <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-primary">Estructura de Financiamiento</h5>
                    <span class="badge bg-light text-dark border">Año Fiscal: {{ date('Y') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Fuente de Financiamiento</th>
                                    <th class="text-center">Año</th>
                                    <th class="text-end pe-4">Monto Asignado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($proyecto->financiamientos as $finan)
                                    <tr>
                                        <td class="ps-4">
                                            <span data-feather="database" class="text-muted me-2"></span>
                                            {{ $finan->fuente_financiamiento }}
                                        </td>
                                        <td class="text-center">{{ $finan->anio }}</td>
                                        <td class="text-end pe-4 fw-bold text-dark">
                                            ${{ number_format($finan->monto, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            No hay registros de financiamiento cargados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="text-end fw-bold ps-4">Total Programado:</td>
                                    <td class="text-end pe-4 fw-bold text-success">
                                        ${{ number_format($proyecto->financiamientos->sum('monto'), 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>
@endsection
