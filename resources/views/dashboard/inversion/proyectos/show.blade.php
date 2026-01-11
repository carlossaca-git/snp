@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 text-gray-800 fw-bold">
                    <i class="fas fa-folder-open text-primary me-2"></i>Detalle del Proyecto
                </h1>
                <p class="text-muted mb-0">Visualización completa de la ficha de inversión</p>
            </div>
            <div>
                <a href="{{ route('reportes.proyecto.individual', $proyecto->id) }}" class="btn btn-outline-secondary me-2" target="_blank">
                    <i class="fas fa-file-pdf me-2"></i>Reporte PDF
                </a>
                <a href="{{ route('inversion.proyectos.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
                <a href="{{ route('inversion.proyectos.edit', $proyecto->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-white py-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 35px; height: 35px;">
                                <i class="fas fa-info-circle text-primary" data-feather="info" style="width: 18px;"></i>
                            </div>

                            <h5 class="m-0 fw-bold text-primary">
                                Información General
                            </h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="text-uppercase text-secondary fs-7 fw-bold">Nombre del Proyecto</label>
                                <p class="fs-5 fw-bold text-dark mb-0">{{ $proyecto->nombre_proyecto }}</p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-uppercase text-secondary fs-7 fw-bold">CUP</label>
                                <p class="fw-semibold">{{ $proyecto->cup }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-uppercase text-secondary fs-7 fw-bold">Monto Total Inversión</label>
                                <p class="fw-bold text-success fs-5">
                                    $ {{ number_format($proyecto->monto_total_inversion, 2) }}
                                </p>
                            </div>

                            <div class="col-md-4">
                                <label class="text-uppercase text-secondary fs-7 fw-bold">Fecha Inicio</label>
                                <p class="mb-0"><i class="far fa-calendar-alt me-1 text-muted"></i>
                                    {{ \Carbon\Carbon::parse($proyecto->fecha_inicio)->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-uppercase text-secondary fs-7 fw-bold">Fecha Fin</label>
                                <p class="mb-0"><i class="far fa-calendar-check me-1 text-muted"></i>
                                    {{ \Carbon\Carbon::parse($proyecto->fecha_fin)->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-uppercase text-secondary fs-7 fw-bold">Duración</label>
                                <span class="badge bg-info text-dark">{{ $proyecto->duracion_meses }} Meses</span>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold text-secondary mb-3"><i class="fas fa-map-marker-alt me-1"></i> Ubicación</h6>
                        <div class="row g-3">
                            @if ($proyecto->localizacion)
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Provincia</small>
                                    <strong>{{ $proyecto->localizacion->provincia ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Cantón</small>
                                    <strong>{{ $proyecto->localizacion->canton ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Parroquia</small>
                                    <strong>{{ $proyecto->localizacion->parroquia ?? 'N/A' }}</strong>
                                </div>
                            @else
                                <div class="col-12 text-center text-muted">
                                    <small><em>Sin ubicación registrada (N/A)</em></small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-bold text-success">
                            <i class="fas fa-hand-holding-usd me-1"></i> Programación Financiera
                        </h5>
                        <span class="badge bg-light text-dark border">Plurianual</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0 align-middle">
                                <thead class="bg-light text-secondary">
                                    <tr>
                                        <th class="ps-4">Año</th>
                                        <th>Fuente de Financiamiento</th>
                                        <th class="text-end pe-4">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($proyecto->financiamientos as $detalle)
                                        <tr>
                                            <td class="ps-4 fw-bold">{{ $detalle->anio }}</td>
                                            <td>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                                    {{ $detalle->fuente->nombre_fuente ?? 'Sin fuente' }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4 font-monospace">
                                                $ {{ number_format($detalle->monto, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">
                                                No hay detalles financieros registrados.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-light border-top">
                                    <tr>
                                        <td colspan="2" class="text-end fw-bold py-3 text-uppercase">Total Programado:
                                        </td>
                                        <td class="text-end pe-4 py-3 fw-bold text-success fs-6">
                                            $ {{ number_format($proyecto->financiamientos->sum('monto'), 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-bold text-success">
                            <i class="fas fa-hand-holding-usd me-2" style="width: 20px;"></i> Diagnóstico y justificación
                        </h5>
                    </div>

                    <div class="card-body">
                        @if ($proyecto->descripcion_diagnostico)
                            <div style="margin-left: 10px;">
                                <p class="text-muted mb-0" style="text-align: justify; line-height: 1.5;">
                                    {{ $proyecto->descripcion_diagnostico }}
                                </p>
                            </div>
                        @else
                            <div style="margin-left: 1px;">
                                <p class="text-muted small mb-0 italic">No hay información registrada.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">

                <div class="card shadow-sm mb-4 border-0 bg-primary text-white"
                    style="background: linear-gradient(135deg, #4a6fa5 0%, #34495e 100%);">
                    <div class="card-body">
                        <h5 class="fw-bold border-bottom border-white pb-2 mb-3 border-opacity-25">
                            <i class="fas fa-bullseye me-2"></i>Alineación PND
                        </h5>

                        <div class="mb-3">
                            <small class="text-white-50 d-block text-uppercase fw-bold" style="font-size: 0.75rem;">Eje
                                Estratégico</small>
                            <p class="fw-bold mb-0">{{ $proyecto->objetivo->eje->nombre_eje ?? 'No definido' }}</p>
                        </div>

                        <div class="mb-0">
                            <small class="text-white-50 d-block text-uppercase fw-bold"
                                style="font-size: 0.75rem;">Objetivo
                                Nacional</small>
                            <p class="mb-0 lh-sm">{{ $proyecto->objetivo->descripcion_objetivo ?? 'No definido' }}</p>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 fw-bold text-dark">Unidad Responsable</h6>
                    </div>

                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-light rounded-circle p-3 me-3 text-secondary">
                                <i class="fas fa-building fa-lg" data-feather="trello"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Unidad Ejecutora</small>
                                <span
                                    class="fw-bold text-dark">{{ $proyecto->unidadEjecutora->nombre_unidad ?? 'No asignada' }}</span>
                            </div>
                        </div>
                        <hr class="mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded-circle p-3 me-3 text-secondary">
                                <i class="fas fa-clipboard-check fa-lg" data-feather="clipboard"></i>
                            </div>

                            <div>
                                <small class="text-muted d-block">Estado Dictamen</small>
                                <span
                                    class="fw-bold
                                    @if ($proyecto->estado_dictamen == 'FAVORABLE') text-success
                                    @elseif($proyecto->estado_dictamen == 'NEGATIVO')
                                    text-danger
                                    @else
                                    text-warning @endif">

                                    {{ $proyecto->estado_dictamen ?? 'PENDIENTE' }}
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="card-header bg-white py-3 mb-3">
                                <h6 class="m-0 fw-bold text-dark">Documentacion</h6>
                            </div>
                            @foreach ($proyecto->documentos as $doc)
                                <div class="d-flex align-items-start mb-4 p-3 border rounded-3 bg-white shadow-sm">

                                    <div class="bg-light rounded-circle p-3 me-3 text-danger border border-danger-subtle">
                                        <i class="fas fa-file-pdf fa-lg" data-feather="file"></i>
                                    </div>

                                    <div class="flex-grow-1">
                                        <div class="mb-2">
                                            <small class="text-muted d-block text-uppercase fw-semibold"
                                                style="font-size: 0.7rem;">
                                                {{ $doc->tipo_documento ?? 'Documento de Respaldo' }}
                                            </small>

                                            <a href="{{ Storage::url($doc->url_archivo) }}" target="_blank"
                                                class="fw-bold text-decoration-none text-dark link-primary d-block mb-1">
                                                {{ $doc->nombre_archivo }}
                                                <i class="fas fa-external-link-alt ms-1 small text-muted"></i>
                                            </a>
                                        </div>

                                        <div class="d-flex align-items-center mt-2">
                                            <a href="{{ Storage::url($doc->url_archivo) }}" target="_blank"
                                                class="btn btn-sm btn-light border text-primary me-2 px-3 py-1"
                                                style="font-size: 0.75rem;">
                                                <i class="fas fa-eye me-1"></i> Ver documento
                                            </a>

                                            <form action="{{ route('inversion.documentos.destroy', $doc->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('¿Estás seguro de eliminar este archivo?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-light border text-danger px-3 py-1"
                                                    style="font-size: 0.75rem;">
                                                    <i class="fas fa-trash-alt me-1"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            @if ($proyecto->documentos->isEmpty())
                                <div class="text-muted small italic">
                                    <i class="fas fa-info-circle me-1"></i> No existen documentos adjuntos.
                                </div>
                            @endif
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    <script>
        feather.replace();
    </script>
@endsection
