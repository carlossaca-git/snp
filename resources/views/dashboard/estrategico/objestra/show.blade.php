@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Ficha Técnica de Objetivo Estratégico"
        subtitulo="Detalle de Objetivo: {{ $objetivo->codigo }}">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('estrategico.objetivos.index') }}"
                class="btn btn-sm btn-outline-secondary me-2 d-inline-flex align-items-center">
                <i class="fas fa-home"></i> Catalogo
            </a>
            <button type="button" class="btn btn-secondary" onclick="history.back()">
                <i class="fas fa-arrow-left me-1"></i> Atras
            </button>
        </div>
    </x-layouts.header_content>
    <div class="container-fluid">

        <div class="card shadow mb-4 border-top-primary">
            <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-dark">Información General</h6>

                @if ($objetivo->organizacion)
                    <span class="badge bg-light text-dark border">
                        <i class="fas fa-building me-1"></i> {{ $objetivo->organizacion->nombre ?? 'Organización' }}
                    </span>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- DATOS DE IDENTIDAD --}}
                    <div class="col-lg-8 border-end">
                        <div class="row mb-3">
                            <div class="col-md-3 text-muted small text-uppercase fw-bold">Código Interno:</div>
                            <div class="col-md-9 fw-bold text-primary">{{ $objetivo->codigo }}</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3 text-muted small text-uppercase fw-bold">Nombre Objetivo:</div>
                            <div class="col-md-9 h5 text-dark font-weight-bold">{{ $objetivo->nombre }}</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3 text-muted small text-uppercase fw-bold">Descripción:</div>
                            <div class="col-md-9 text-secondary text-justify">
                                {{ $objetivo->descripcion ?? 'No se ha registrado una descripción detallada.' }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 text-muted small text-uppercase fw-bold">Indicador Propio:</div>
                            <div class="col-md-9">{{ $objetivo->indicador ?? 'N/A' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 text-muted small text-uppercase fw-bold">Fecha Creación:</div>
                            <div class="col-md-9">{{ $objetivo->created_at->format('d/m/Y') }}</div>
                        </div>

                    </div>

                    {{-- DATOS DE EJECUCIÓN  --}}
                    <div
                        class="col-lg-4 d-flex flex-column justify-content-center align-items-center p-4 bg-gray-50 rounded">
                        <h6 class="text-uppercase text-muted font-weight-bold mb-3">Ejecución Física Global</h6>

                        <div class="position-relative d-flex align-items-center justify-content-center mb-3"
                            style="width: 140px; height: 140px; border-radius: 50%; border: 8px solid {{ $promedioAvance < 50 ? '#e74a3b' : '#1cc88a' }};">
                            <div class="h3 font-weight-bold mb-0 text-dark">
                                {{ number_format($promedioAvance, 1) }}%
                            </div>
                        </div>

                        <div class="w-100 text-center mt-2">
                            <div class="small text-uppercase text-muted fw-bold">Inversión Total</div>
                            <div class="h4 font-weight-bold text-success">
                                ${{ number_format($totalInversion, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALINEACIÓN COMPLETA (EJE -> OBJETIVO -> META) --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-gray-100">
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-sitemap me-2"></i> Alineación Plan Nacional de Desarrollo
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 align-middle">
                        <thead class="bg-light text-uppercase text-xs text-muted">
                            <tr>
                                <th style="width: 25%">Eje Estratégico</th>
                                <th style="width: 30%">Objetivo Nacional</th>
                                <th style="width: 45%">Meta Nacional Asociada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($objetivo->metasNacionales as $meta)
                                <tr>
                                    {{-- EJE --}}
                                    <td>
                                        @if ($meta->objetivoNacional && $meta->objetivoNacional->eje)
                                            <span class="badge bg-secondary mb-1">Eje
                                                {{ $meta->objetivoNacional->eje->id_eje ?? '#' }}</span>
                                            <div class="small fw-bold text-dark">
                                                {{ $meta->objetivoNacional->eje->nombre_eje ?? 'Eje sin nombre' }}
                                            </div>
                                        @else
                                            <span class="text-muted small">--</span>
                                        @endif
                                    </td>

                                    {{-- OBJETIVO NACIONAL --}}
                                    <td>
                                        @if ($meta->objetivoNacional)
                                            <div class="d-flex align-items-start">
                                                <span
                                                    class="badge bg-primary me-2 mt-1">{{ $meta->objetivoNacional->codigo_objetivo }}</span>
                                                <span
                                                    class="small text-muted">{{ Str::limit($meta->objetivoNacional->descripcion_objetivo, 80) }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted small">--</span>
                                        @endif
                                    </td>

                                    {{-- META NACIONAL --}}
                                    <td class="bg-light-soft">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-bullseye text-danger mt-1 me-2"></i>
                                            <div>
                                                <div class="fw-bold text-dark mb-1">
                                                    {{ $meta->codigo_meta }}
                                                </div>
                                                <a href="{{ route('catalogos.metas.show', $meta->id_meta_nacional) }}"
                                                    class="text-primary small text-decoration-none">
                                                    {{ $meta->nombre_meta }} <i
                                                        class="fas fa-external-link-alt ms-1 text-xs"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        <i class="fas fa-unlink fa-lg mb-2"></i><br>
                                        Sin alineación registrada al PND.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- SECCIÓN 3: PROYECTOS (Igual que antes) --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-dark">Proyectos de Inversión Vinculados</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre del Proyecto</th>
                                <th class="text-center">CUP</th>
                                <th class="text-center">Monto</th>
                                <th style="width: 20%">Avance</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proyectos as $proy)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $proy->nombre_proyecto }}</div>
                                    </td>
                                    <td class="text-center font-monospace small">{{ $proy->cup ?? '-' }}</td>
                                    <td class="text-end">${{ number_format($proy->monto_total_inversion, 2) }}</td>
                                    <td>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar {{ $proy->calculo_avance >= 100 ? 'bg-success' : 'bg-info' }}"
                                                style="width: {{ $proy->calculo_avance }}%"></div>
                                        </div>
                                        <div class="text-end text-xs mt-1 fw-bold">
                                            {{ number_format($proy->calculo_avance, 1) }}%</div>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('inversion.proyectos.show', $proy->id) }}"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">Sin proyectos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
