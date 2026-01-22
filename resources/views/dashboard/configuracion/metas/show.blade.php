@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Ficha Técnica de Meta Nacional"
        subtitulo="Compromisos cuantificables vinculados a los Objetivos Nacionales">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('catalogos.metas.index') }}"
                class="btn btn-sm btn-outline-secondary me-2 d-inline-flex align-items-center">
                <i class="fas fa-home"></i> Catalogo
            </a>
            <button type="button" class="btn btn-secondary" onclick="history.back()">
                <i class="fas fa-arrow-left me-1"></i> Atras
            </button>

        </div>
    </x-layouts.header_content>
    @include('partials.mensajes')
    <div class="container">
        <div class="row">
            {{--  Definición y Alineación --}}
            <div class="col-lg-8 mb-4">
                <div class="card shadow h-100 border-top-primary">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary">Información Estratégica</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="text-xs font-weight-bold text-uppercase text-muted mb-1">Nombre de la Meta</label>
                            <div class="h5 font-weight-bold text-gray-800">{{ $meta->nombre_meta }}</div>
                            <div class="p-3 bg-light rounded border-0 text-dark small"
                                style="text-align: justify; line-height: 1.6;">
                                {{ $meta->descripcion ?? ($meta->descripcion_meta ?? 'No se ha registrado una descripción detallada para esta meta.') }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-xs font-weight-bold text-uppercase text-muted mb-1">Objetivo Nacional
                                    (Superior)</label>
                                <div class="p-2 bg-light rounded border-left-primary">
                                    <span class="fw-bold">{{ $meta->objetivoNacional->codigo_objetivo ?? 'N/A' }}</span>:
                                    {{ Str::limit($meta->objetivoNacional->descripcion_objetivo ?? 'Sin Asignar', 50) }}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-xs font-weight-bold text-uppercase text-muted mb-1">
                                    Indicador Asociado
                                </label>
                                <div class="p-2 bg-light rounded d-flex align-items-center">
                                    <i class="fas fa-chart-line text-info fa-lg me-2"></i>
                                    <div style="line-height: 1.2;">
                                        <span class="fw-bold text-dark text-xs">
                                            {{ $meta->indicador ?? ($meta->nombre_indicador ?? 'Indicador no definido') }}
                                        </span>
                                        @if (!empty($meta->unidad_medida))
                                            <div class="text-muted" style="font-size: 0.9rem;">
                                                Unidad: <strong>{{ $meta->unidad_medida }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2">
                            <label class="text-xs font-weight-bold text-uppercase text-muted mb-1">Alineación ODS</label>
                            <div class="d-flex flex-wrap align-items-start gap-3">

                                @forelse($meta->ods as $od)
                                    <div class="d-flex flex-column align-items-center" style="width: 100px;">
                                        <span class="badge rounded-pill mb-1 p-2 w-100"
                                            style="background-color: {{ $od->color_hex ?? '#777777' }};">
                                            <i class="fas fa-globe-americas me-1"></i>
                                            {{ $od->codigo ?? 'ODS' }}
                                        </span>
                                        <div class="text-center lh-sm">
                                            <small class="text-muted fw-bold" style="font-size: 0.7rem;">
                                                {{ Str::limit($od->nombre_ods ?? $od->nombre, 45) }}
                                            </small>
                                        </div>

                                    </div>
                                @empty
                                    <span class="text-muted small">
                                        <i class="fas fa-info-circle me-1"></i> No vinculado a ODS
                                    </span>
                                @endforelse

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Métricas y Números --}}
            <div class="col-lg-4 mb-4">
                <div class="card shadow h-100 border-top-info">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-info">Métricas de Desempeño</h6>
                    </div>
                    <div class="card-body">
                        {{-- TARJETAS DATOS --}}
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <span class="text-xs font-weight-bold text-uppercase text-muted">Línea Base</span>
                                    <div class="h6 mb-0">{{ number_format($meta->linea_base, 2) }}</div>
                                </div>
                                <span class="badge bg-secondary rounded-circle p-2"><i class="fas fa-history"></i></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <span class="text-xs font-weight-bold text-uppercase text-muted">Meta Planificada</span>
                                    <div class="h6 mb-0">{{ number_format($meta->meta_valor, 2) }}</div>
                                </div>
                                <span class="badge bg-primary rounded-circle p-2"><i class="fas fa-bullseye"></i></span>
                            </li>
                        </ul>
                        {{-- EL RESULTADO CALCULADO GRANDE --}}
                        <div class="text-center mt-2">
                            <span class="text-xs font-weight-bold text-uppercase text-muted">Ejecución Actual (Promedio
                                Proyectos)</span>
                            <div
                                class="h1 font-weight-bold {{ $promedioMeta < 50 ? 'text-danger' : 'text-success' }} mb-1">
                                {{ number_format($promedioMeta, 2) }}%
                            </div>
                            <div class="progress" style="height: 15px; border-radius: 10px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated {{ $promedioMeta < 50 ? 'bg-danger' : 'bg-success' }}"
                                    role="progressbar" style="width: {{ $promedioMeta }}%"></div>
                            </div>
                            <p class="small text-muted mt-2">Calculado en base a {{ $proyectos->count() }} proyectos
                                activos.</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        {{-- RANKING DE CONTRIBUCIÓN POR OBJETIVO ESTRATÉGICO --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom-warning">
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-trophy text-warning me-2"></i> Ranking de Contribución por Objetivo Estratégico
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        @foreach ($rankingObjetivos as $obj)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-end mb-1">
                                    <div>
                                        <span class="badge bg-secondary me-2">{{ $obj->codigo }}</span>
                                        <span class="fw-bold text-dark">{{ $obj->nombre_objetivo }}</span>
                                        <div class="text-xs text-muted mt-1">
                                            Maneja ${{ number_format($obj->inversion_total, 2) }} en
                                            {{ $obj->conteo_proyectos }} proyectos.
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span
                                            class="h5 font-weight-bold {{ $obj->avance_ponderado < 50 ? 'text-danger' : 'text-success' }}">
                                            {{ number_format($obj->avance_ponderado, 2) }}%
                                        </span>
                                    </div>
                                </div>

                                {{-- BARRA DE PROGRESO --}}
                                <div class="progress" style="height: 15px;">
                                    <div class="progress-bar {{ $obj->avance_ponderado < 50 ? 'bg-danger' : 'bg-success' }}"
                                        role="progressbar" style="width: {{ $obj->avance_ponderado }}%">
                                        {{-- Texto dentro de la barra si es ancha --}}
                                        @if ($obj->avance_ponderado > 10)
                                            Contribución
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if ($rankingObjetivos->isEmpty())
                            <p class="text-muted text-center">No hay objetivos estratégicos contribuyendo actualmente.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- BUSCADOR --}}
        <div class="row mb-3 justify-content-end">
            <div class="col-md-4">
                <form action="{{ route('catalogos.metas.index') }}" method="GET">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="busqueda" id="inputBusqueda"
                            class="form-control border-start-0 border-end-0 shadow-none"
                            placeholder="Buscar por nombre, codigo..." value="{{ request('busqueda') }}">
                        <button type="button" id="btnLimpiarBusqueda"
                            class="btn bg-white border-top border-bottom border-end-0 text-danger"
                            style="display: none; z-index: 1000 !important;">
                            <i class="fas fa-times"></i>
                        </button>
                        <button class="btn btn-secondary" type="submit">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
        {{-- TABLA DE PROYECTOS --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-gray-100 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-project-diagram me-2"></i> Desglose de Proyectos que alimentan esta Meta
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40%">Proyecto de Inversión</th>
                                <th>Objetivo Estratégico</th>
                                <th class="text-center">Monto</th>
                                <th class="text-center" style="width: 20%">Avance Físico</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proyectos as $proy)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $proy->nombre_proyecto }}</div>
                                        <div class="small text-muted">CUP: {{ $proy->cup ?? 'S/N' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border shadow-sm">
                                            <i class="fas fa-bullseye text-danger me-1"></i>
                                            {{ $proy->objetivo->codigo ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="text-right text-nowrap">
                                        ${{ number_format($proy->monto_total_inversion, 2) }}
                                    </td>
                                    {{-- BARRA INDIVIDUAL --}}
                                    <td style="vertical-align: middle;">
                                        <div class="d-flex justify-content-between">
                                            <span
                                                class="small fw-bold">{{ number_format($proy->calculo_avance, 1) }}%</span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar {{ $proy->calculo_avance >= 100 ? 'bg-success' : 'bg-info' }}"
                                                style="width: {{ $proy->calculo_avance }}%"></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('inversion.proyectos.show', $proy->id) }}"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Ver ficha completa del proyecto">
                                            <i class="fas fa-external-link-alt"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="bg-light rounded-circle p-4 mb-3">
                                                <i class="fas fa-folder-open fa-3x text-gray-400"></i>
                                            </div>
                                            <h5 class="text-gray-600">Sin proyectos vinculados</h5>
                                            <p class="text-gray-500 mb-0">Esta meta no tiene proyectos asociados a través
                                                de Objetivos Estratégicos.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
