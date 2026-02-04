@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Detalle del Programa" subtitulo="Visualización completa de la ficha de Programa">

        {{-- <a href="{{ route('inversion.programas.index') }}" class="btn btn-outline-secondary align-items-center"
            title="Proyectos">
            <i class="fas fa-home me-1"></i>Programas
        </a> --}}
        <a href="{{ route('inversion.programas.create') }}" class="btn btn-outline-secondary align-items-center"
            title="Proyectos">
            <i class="fas fa-plus me-1"></i>Nuevo
        </a>
        <button type="button" class="btn btn-secondary" onclick="history.back()">
            <i class="fas fa-arrow-left me-1"></i> Atras
        </button>

    </x-layouts.header_content>
    @include('partials.mensajes')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold text-dark">
                    {{ $programa->codigo_programa }} - {{ Str::limit($programa->nombre_programa, 50) }}
                </h4>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small">Plan Anual {{ $programa->plan->anio }}</span>
                    <span class="text-muted small">|</span>
                    @php
                        $badgeClass = match ($programa->estado) {
                            'APROBADO' => 'bg-success',
                            'POSTULADO' => 'bg-warning text-dark',
                            'SUSPENDIDO' => 'bg-danger',
                            'CERRADO' => 'bg-dark',
                            default => 'bg-secondary',
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }} rounded-pill px-3">
                        {{ $programa->estado }}
                    </span>
                </div>
            </div>
            <div class="d-flex gap-2">
                {{-- <a href="{{ route('inversion.programas.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Volver
                </a>
                <a href="{{ route('inversion.programas.edit', $programa->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i> Editar
                </a> --}}
            </div>
        </div>

        {{-- TARJETAS DE RESUMEN FINANCIERO --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-bold mb-1">Monto Asignado (Techo)</div>
                        <div class="fs-3 fw-bold text-dark">${{ number_format($programa->monto_asignado, 2) }}</div>
                        <div class="small text-muted"><i class="fas fa-wallet me-1"></i> Presupuesto Total</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-bold mb-1">Monto Planificado</div>
                        <div class="fs-3 fw-bold text-info">${{ number_format($programa->monto_planificado, 2) }}</div>

                        <div class="d-flex justify-content-between small mt-2">
                            <span>Ocupación:</span>
                            <span class="fw-bold">{{ number_format($porcentajeUso, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info"
                                style="width: {{ $porcentajeUso > 100 ? 100 : $porcentajeUso }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div
                    class="card border-0 shadow-sm h-100 border-start border-4 {{ $saldo < 0 ? 'border-danger' : 'border-success' }}">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-bold mb-1">Saldo Disponible</div>
                        <div class="fs-3 fw-bold {{ $saldo < 0 ? 'text-danger' : 'text-success' }}">
                            ${{ number_format($saldo, 2) }}
                        </div>
                        @if ($saldo < 0)
                            <div class="small text-danger fw-bold"><i class="fas fa-exclamation-circle me-1"></i> Sobregiro
                            </div>
                        @else
                            <div class="small text-success"><i class="fas fa-check-circle me-1"></i> Disponible para
                                proyectos</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i> Información General
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="small text-muted text-uppercase fw-bold">Descripción / Objetivo</label>
                                <p class="mb-0 bg-light p-3 rounded text-secondary">
                                    {{ $programa->descripcion ?? 'Sin descripción registrada.' }}
                                </p>
                            </div>

                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Sector:</span>
                                        <span class="fw-bold">{{ $programa->sector }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Cobertura:</span>
                                        <span class="fw-bold">{{ $programa->cobertura }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Tipo:</span>
                                        <span class="fw-bold">{{ $programa->tipo_programa }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Fecha Inicio:</span>
                                        <span
                                            class="fw-bold">{{ \Carbon\Carbon::parse($programa->fecha_inicio)->format('d/m/Y') }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Fecha Fin:</span>
                                        <span
                                            class="fw-bold">{{ \Carbon\Carbon::parse($programa->fecha_fin)->format('d/m/Y') }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Duración:</span>
                                        <span class="fw-bold">
                                            {{ number_format(\Carbon\Carbon::parse($programa->fecha_inicio)->diffInMonths($programa->fecha_fin),1 )}}
                                            meses
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ALINEACION ESTRATEGICA --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-bullseye me-2 text-success"></i> Alineación Estratégica
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small text-muted text-uppercase fw-bold">Objetivo Estratégico (PEDI)</label>
                            <div class="d-flex align-items-start mt-1">
                                <i class="fas fa-caret-right text-success mt-1 me-2"></i>
                                <div>
                                    <span class="fw-bold text-dark">{{ $programa->objetivoE->codigo_programa }}</span>
                                    <p class="mb-0 text-muted">{{ $programa->objetivoE->nombre }}</p>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-0">
                            <label class="small text-muted text-uppercase fw-bold">Fuente de Financiamiento</label>
                            <div class="d-flex align-items-center mt-1">
                                <span
                                    class="badge bg-light text-dark border me-2">{{ $programa->fuenteFinanciamiento->codigo_fuente }}</span>
                                <span class="text-dark">{{ $programa->fuenteFinanciamiento->nombre_fuente }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                {{-- DOCUMENTO HABILITANTE --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-folder-open me-2 text-warning"></i> Documentación</h6>
                    </div>
                    <div class="card-body text-center py-4">
                        @if ($programa->url_documento)
                            <i class="fas fa-file-pdf text-danger fa-3x mb-3"></i>
                            <h6 class="mb-1 text-truncate" title="{{ $programa->nombre_archivo }}">
                                {{ Str::limit($programa->nombre_archivo, 25) }}
                            </h6>
                            <small class="text-muted d-block mb-3">Documento Habilitante</small>
                            <a href="{{ asset('storage/' . $programa->url_documento) }}" target="_blank"
                                class="btn btn-sm btn-outline-danger w-100">
                                <i class="fas fa-download me-2"></i> Descargar / Ver
                            </a>
                        @else
                            <div class="text-muted">
                                <i class="far fa-file fa-2x mb-2"></i>
                                <p class="small mb-0">No se ha cargado documento habilitante.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- LISTA RÁPIDA DE PROYECTOS --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Proyectos ({{ $programa->proyectos->count() }})</h6>
                        <a href="#" class="btn btn-xs btn-primary rounded-circle" title="Nuevo Proyecto"><i
                                class="fas fa-plus"></i></a>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($programa->proyectos as $proyecto)
                            <div class="list-group-item px-3 py-3">
                                <div class="d-flex w-100 justify-content-between mb-1">
                                    <h6 class="mb-0 small fw-bold text-truncate" style="max-width: 70%;">
                                        {{ $proyecto->nombre_proyecto }}
                                    </h6>
                                    <small class="text-muted">{{ $proyecto->codigo_cup }}</small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">
                                        {{ $proyecto->estado }}
                                    </span>
                                    <small class="fw-bold text-success" style="font-size: 0.75rem;">
                                        ${{ number_format($proyecto->monto, 2) }}
                                    </small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <small>No hay proyectos registrados aún.</small>
                            </div>
                        @endforelse
                    </div>
                    @if ($programa->proyectos->count() > 0)
                        <div class="card-footer bg-white text-center">
                            <a href="{{route('inversion.programas.index')}}" class="small text-decoration-none fw-bold">Ver todos los proyectos</a>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
