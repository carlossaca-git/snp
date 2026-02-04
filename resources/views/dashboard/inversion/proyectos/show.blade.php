@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Detalle del Proyecto" subtitulo="Visualización completa de la ficha de inversión">

        <a href="{{ route('inversion.proyectos.index') }}" class="btn btn-outline-secondary align-items-center"
            title="Proyectos">
            <i class="fas fa-home me-1"></i>Proyectos
        </a>
        <button type="button" class="btn btn-secondary" onclick="history.back()">
            <i class="fas fa-arrow-left me-1"></i> Atras
        </button>

    </x-layouts.header_content>
    @include('partials.mensajes')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-3 border-1">
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

                <div class="card shadow-sm border-1 mb-3">
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
                <div class="card shadow-sm border-1 mb-3">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-bold text-info">
                            <i class="fas fa-stethoscope me-2" style="width: 20px;"></i> Diagnóstico y justificación
                        </h5>
                    </div>

                    <div class="card-body">
                        @if ($proyecto->descripcion_diagnostico)
                            <div style="margin-left: 10px;">
                                <p class="text-muted mb-0" style="text-align: justify; line-height: 1.5;">
                                    {{ $proyecto->descripcion_diagnostico }}
                                </p>
                            </div>
                            @else{{-- Estado dicatame --}}
                            <div class="card mb-3">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="font-weight-bold text-primary">{{ $proyecto->nombre_proyecto }}</h4>
                                        <span class="text-muted">Código: {{ $proyecto->id }}</span>
                                    </div>

                                    <div class="text-end">
                                        <h6 class="text-uppercase text-secondary small mb-1">Estado Actual</h6>

                                        @if ($proyecto->estado_dictamen == 'Pendiente')
                                            <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                                <i class="fas fa-clock me-1"></i> Pendiente de Revisión
                                            </span>
                                        @elseif($proyecto->estado_dictamen == 'Aprobado')
                                            <span class="badge bg-success fs-6 px-3 py-2">
                                                <i class="fas fa-check-circle me-1"></i> Aprobado
                                            </span>
                                        @elseif($proyecto->estado_dictamen == 'Rechazado')
                                            <span class="badge bg-danger fs-6 px-3 py-2">
                                                <i class="fas fa-times-circle me-1"></i> Rechazado
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div style="margin-left: 1px;">
                                <p class="text-muted small mb-0 italic">No hay información registrada.</p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card shadow-sm border-1 mb-3">
                    {{-- CABECERA CON CONTEXTO DEL AVANCE --}}
                    <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-success-emphasis">
                            <i class="fas fa-sitemap me-2 text-info"></i> Alineación y Contribución al PND
                        </h6>
                        {{-- DATO CLAVE: Mostramos el avance físico aquí para entender los cálculos de abajo --}}
                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2">
                            Avance Físico Proyecto: <strong>{{ number_format($proyecto->avance_fisico_real, 2) }}%</strong>
                        </span>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle small">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th width="35%">Indicador Nacional / Meta</th>
                                        <th width="10%" class="text-center" title="Peso del Indicador en el PND">
                                            Ponderacion
                                        </th>
                                        <th width="15%" class="text-center" title="Peso del Proyecto en el Indicador">
                                            Contr.<br>Proy.
                                        </th>
                                        {{-- NUEVA COLUMNA: El cálculo intermedio (Avance Proyecto * Peso Proy) --}}
                                        <th width="20%" class="text-center bg-light-soft text-primary"
                                            title="Lo que has aportado al indicador basado en tu avance">
                                            Aporte Real<br><small>(Al Indicador)</small>
                                        </th>
                                        {{-- LA JOYA DE LA CORONA: El impacto final en la meta --}}
                                        <th width="20%"
                                            class="text-center bg-success-subtle text-success-emphasis fw-bold"
                                            title="Impacto global en la Meta Nacional">
                                            Impacto Final<br><small>(A la Meta)</small>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Agrupamos por Meta Nacional para ordenar visualmente
                                        $gruposPorMeta = $proyecto->indicadoresNacionales->groupBy(function ($ind) {
                                            return $ind->metaNacional->id_meta_nacional ?? 0;
                                        });
                                    @endphp

                                    @forelse($gruposPorMeta as $idMeta => $indicadores)
                                        @php
                                            $meta = $indicadores->first()->metaNacional;
                                        @endphp

                                        {{-- FILA DE LA META (AGRUPADOR) --}}
                                        <tr class="table-secondary">
                                            <td colspan="5" class="fw-bold text-dark px-3 py-2">
                                                <i class="fas fa-flag text-secondary me-2"></i>
                                                @if ($meta)
                                                    Meta: {{ $meta->codigo_meta }} -
                                                    {{ Str::limit($meta->nombre_meta, 80) }}
                                                @else
                                                    <span class="text-muted fst-italic">Sin Meta Vinculada</span>
                                                @endif
                                            </td>
                                        </tr>

                                        @foreach ($indicadores as $ind)
                                            @php
                                                //VARIABLES DE CLACULO
                                                $avanceFisico = $proyecto->avance_fisico_real;
                                                $pesoPND = $ind->peso_oficial;
                                                $pesoProy = $ind->pivot->contribucion_proyecto;

                                                // APORTE AL INDICADOR (Nivel Micro)
                                                // Fórmula: (Avance Físico * Peso Proyecto) / 100
                                                $aporteLogrado = ($avanceFisico * $pesoProy) / 100;

                                                // IMPACTO EN el idicador (Nivel Macro)
                                                // Fórmula: (Aporte Logrado * Peso PND) / 100
                                                $impactoFinal = ($aporteLogrado * $pesoPND) / 100;
                                            @endphp

                                            <tr>
                                                {{-- NOMBRE INDICADOR --}}
                                                <td class="ps-4 py-3">
                                                    <div class="d-flex align-items-start">
                                                        <i class="fas fa-chart-bar text-info mt-1 me-2 opacity-50"></i>
                                                        <div>
                                                            <div class="fw-bold text-dark">{{ $ind->codigo_indicador }}
                                                            </div>
                                                            <div class="text-muted" style="font-size: 0.85rem;">
                                                                {{ $ind->nombre_indicador }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- PESO PND --}}
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-white text-secondary border border-secondary-subtle">
                                                        {{ $pesoPND }}%
                                                    </span>
                                                </td>

                                                {{-- PESO PROYECTO --}}
                                                <td class="text-center">
                                                    <span class="badge bg-white text-dark border">
                                                        {{ $pesoProy }}%
                                                    </span>
                                                    <div class="text-muted" style="font-size: 10px;">Comprometido</div>
                                                </td>

                                                {{-- APORTE REAL --}}
                                                <td class="text-center bg-light-soft">
                                                    <div class="fw-bold text-primary">
                                                        {{ number_format($aporteLogrado, 2) }}%</div>
                                                    <div class="progress mt-1"
                                                        style="height: 3px; width: 60px; margin: 0 auto;">
                                                        <div class="progress-bar bg-primary" role="progressbar"
                                                            style="width: {{ $pesoProy > 0 ? ($aporteLogrado / $pesoProy) * 100 : 0 }}%">
                                                        </div>
                                                    </div>
                                                </td>

                                                {{--  IMPACTO FINAL --}}
                                                <td class="text-center bg-success-subtle">
                                                    <h6 class="mb-0 fw-bold text-success-emphasis">
                                                        {{ number_format($impactoFinal, 2) }}%
                                                    </h6>
                                                    <div class="text-success small opacity-75" style="font-size: 10px;">
                                                        <i class="fas fa-arrow-up me-1"></i>al PND
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted bg-light">
                                                <i class="fas fa-folder-open fa-2x mb-3 text-secondary opacity-25"></i>
                                                <p class="mb-0">Este proyecto no tiene indicadores vinculados.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-2">
                        <small class="text-muted fst-italic d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            El <strong>Impacto Final</strong> se calcula multiplicando el Aporte Real por el Peso PND.
                        </small>
                    </div>
                </div>
            </div>


            <div class="col-lg-4">
                <div class=" card shadow-sm mb-3 border-left-info">
                    <div class="px-2 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold border-bottom border-white border-opacity-25">
                            <i class="fas fa-check me-2"></i>Acciones
                        </h5>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip"
                                title="Ver Ficha Técnica"
                                onclick="abrirVisorPdf('{{ route('reportes.proyecto.individual', $proyecto->id) }}')">
                                <i class="fas fa-file-pdf"></i> Generar Reporte
                            </button>
                            <button type="button">
                                <a href="{{ route('inversion.proyectos.edit', $proyecto->id) }}"
                                    class="btn btn-warning  btn-sm">
                                    <i class="fas fa-edit me-1"></i> Editar
                                </a>
                            </button>
                        </div>
                    </div>
                </div>
                {{-- Alineacion --}}
                <div class="card shadow-sm mb-3 border-0 bg-primary text-white"
                    style="background: linear-gradient(135deg, #4a6fa5 0%, #34495e 100%);">
                    <div class="card-body">
                        <h5 class="fw-bold border-bottom border-white pb-2 mb-3 border-opacity-25">
                            <i class="fas fa-bullseye me-2"></i>Alineación PND
                        </h5>
                        <div class="mb-3">
                            <small class="text-white-50 d-block text-uppercase fw-bold" style="font-size: 0.75rem;">
                                Objetivo Nacional
                            </small>
                            <p class="mb-0 lh-sm">
                                @forelse($proyecto->objetivoEstrategico->metasNacionales as $meta)
                                    {{ $meta->objetivoNacional->descripcion_objetivo ?? 'Sin Objetivo Nacional' }}
                                    @if (!$loop->last)
                                        <br><span class="text-white-50">|</span>
                                    @endif
                                @empty
                                    No definido
                                @endforelse
                            </p>
                        </div>
                        <div class="mb-0">
                            <small class="text-white-50 d-block text-uppercase fw-bold" style="font-size: 0.75rem;">
                                Meta Nacional Asociada
                            </small>
                            <p class="mb-0 lh-sm">
                                @forelse($proyecto->objetivoEstrategico->metasNacionales as $meta)
                                    {{ $meta->nombre_meta }}

                                    @if (!$loop->last)
                                        <br><span class="text-white-50">|</span>
                                    @endif
                                @empty
                                    No definido
                                @endforelse
                            </p>
                        </div>
                        <div class="mb-3">
                            <small class="text-white-50 d-block text-uppercase fw-bold"
                                style="font-size: 0.75rem;">objetivo Estratégico</small>
                            <p class="fw-bold mb-0">
                                {{ $proyecto->objetivoEstrategico->nombre ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                {{-- DICTAMEN --}}
                <div class="card shadow-sm mb-3 border-left-info">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-info-emphasis">
                            <i class="fas fa-gavel me-2"></i> Gestión del Dictamen
                        </h6>
                        <div>
                            @if ($proyecto->estado_dictamen == 'Pendiente')
                                <span class="badge bg-warning text-dark px-3 py-2">
                                    <i class="fas fa-clock me-1"></i> Pendiente
                                </span>
                            @elseif($proyecto->estado_dictamen == 'Corregir')
                                <span class="badge bg-warning text-dark px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i> Corregir
                                </span>
                            @elseif($proyecto->estado_dictamen == 'Aprobado')
                                <span class="badge bg-success px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i> Aprobado
                                </span>
                            @elseif($proyecto->estado_dictamen == 'Rechazado')
                                <span class="badge bg-danger px-3 py-2">
                                    <i class="fas fa-times-circle me-1"></i> Rechazado
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        @if ($proyecto->observaciones)
                            @php
                                $claseAlerta =
                                    $proyecto->estado_dictamen == 'Aprobado'
                                        ? 'alert-success border-left-success'
                                        : 'alert-warning border-left-warning';
                                $icono =
                                    $proyecto->estado_dictamen == 'Aprobado'
                                        ? 'fa-check-circle'
                                        : 'fa-exclamation-triangle';
                            @endphp
                            <div class="alert {{ $claseAlerta }} shadow-sm mb-4" role="alert">
                                <h6 class="alert-heading font-weight-bold">
                                    <i class="fas {{ $icono }} me-2"></i>
                                    @if ($proyecto->estado_dictamen == 'Aprobado')
                                        Mensaje de Aprobación:
                                    @else
                                        Observaciones del Revisor:
                                    @endif
                                </h6>
                                <hr>
                                <p class="mb-0 text-dark">{{ $proyecto->observaciones }}</p>
                            </div>
                        @endif
                        <form action="{{ route('inversion.proyectos.dictamen', $proyecto->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="observaciones" class="form-label small fw-bold text-muted">
                                    Motivo del dictamen / Observaciones:
                                </label>
                                <textarea class="form-control" name="observaciones" id="observaciones" rows="3"
                                    placeholder="Escriba aquí las observaciones... (Si aprueba y lo deja vacío, se guardará un mensaje predeterminado).">{{ old('observaciones', $proyecto->observaciones) }}</textarea>
                                <div class="form-text text-xs text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Este mensaje quedará visible en el historial del proyecto.
                                </div>
                            </div>
                            <hr class="my-4">
                            <div class="d-flex gap-2">
                                <button type="submit" name="nuevo_dictamen" value="Aprobar"
                                    onclick="confirmarDictamen(event, 'Aprobar')"
                                    title="{{ $proyecto->estado_dictamen == 'Aprobado' ? 'Este proyecto ya está aprobado' : 'Aprobar proyecto' }}"
                                    {{ $proyecto->estado_dictamen == 'Aprobado' ? 'disabled' : '' }}
                                    class="btn btn-success flex-fill btn-sm py-2">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ $proyecto->estado_dictamen == 'Aprobado' ? 'Aprobado' : 'Aprobar' }}
                                </button>
                                <button type="submit" name="nuevo_dictamen" value="Corregir"
                                    onclick="confirmarDictamen(event, 'Corregir')"
                                    title="{{ $proyecto->dictamen == 'Corregir' ? 'Este proyecto fue enviado a corrección' : 'Enviar a corrección' }}"
                                    {{ $proyecto->estado_dictamen == 'Corregir' ? 'disabled' : '' }}
                                    class="btn btn-warning text-dark flex-fill btn-sm py-2">
                                    <i class="fas fa-edit me-1"></i> Corregir
                                </button>
                                <button type="submit" name="nuevo_dictamen" value="Rechazar"
                                    onclick="confirmarDictamen(event, 'Rechazar')"
                                    title="{{ $proyecto->estado_dictamen == 'Rechazado' ? 'El proyecto ya fue rechazado' : 'Rechazar definitivamente' }}"
                                    {{ $proyecto->estado_dictamen == 'Rechazado' ? 'disabled' : '' }}
                                    class="btn btn-danger flex-fill btn-sm py-2">
                                    <i class="fas fa-times-circle me-1"></i>
                                    {{ $proyecto->dictamen == 'Rechazado' ? 'Rechazado' : 'Rechazar' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card shadow-sm border-1 mb-3">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 fw-bold text-dark">Unidad Responsable</h6>
                    </div>

                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-light rounded-circle p-3 me-3 text-secondary">
                                <i class="fas fa-building fa-lg"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Unidad Ejecutora</small>
                                <span
                                    class="fw-bold text-dark">{{ $proyecto->unidadEjecutora->nombre_unidad ?? 'No asignada' }}</span>
                            </div>
                        </div>

                        <hr class="mb-3">
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-light rounded-circle p-3 me-3 text-secondary">
                                    <i class="fas fa-tasks fa-2x fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">Marco Lógico</h5>
                                    <small class="text-muted">Matriz de Planificación</small>
                                </div>
                            </div>

                            <div class="card-body">
                                {{--  Resumen de Estadísticas --}}
                                <div class="d-flex justify-content-between text-muted small mb-3">
                                    <span><i class="fas fa-layer-group me-1"></i>Componentes:</span>
                                    <span
                                        class="fw-bold">{{ $proyecto->marcoLogico->where('nivel', 'COMPONENTE')->count() }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-muted small mb-3">
                                    <span><i class="fas fa-check-square me-1"></i>Actividades:</span>
                                    <span
                                        class="fw-bold">{{ $proyecto->marcoLogico->where('nivel', 'ACTIVIDAD')->count() }}</span>
                                </div>

                                <hr class="my-3">

                                {{-- Barra de Avance --}}
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="fw-bold">Avance Físico Actual</small>
                                        <small
                                            class="fw-bold text-primary">{{ number_format($proyecto->avance_real, 2) }}%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-primary" role="progressbar"
                                            style="width: {{ $proyecto->avance_real }}%"></div>
                                    </div>
                                </div>

                                {{-- Boton de acceso al modulo --}}
                                <div class="d-grid">
                                    <a href="{{ route('inversion.proyectos.marco-logico.index', ['id' => $proyecto->id]) }}"
                                        class="btn btn-outline-primary fw-bold">
                                        <i class="fas fa-edit me-2"></i>Gestionar Matriz
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- DOCUMENTACION --}}
                <div class="card shadow-sm border-1">
                    <div class="card-header bg-white py-3 mb-3">
                        <h6 class="m-0 fw-bold text-dark">Documentacion</h6>
                    </div>
                    @foreach ($proyecto->documentos as $doc)
                        <div class="d-flex align-items-start p-3 bg-white shadow-sm">

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

                                    <form action="{{ route('inversion.proyectos.documentos.destroy', $doc->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('¿Estás seguro de eliminar este archivo?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger px-3 py-1"
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

    <script>
        feather.replace();
    </script>
@endsection
@section('scripts')
    <script>
        function confirmarDictamen(event, tipoDictamen) {
            event.preventDefault(); // 1. Detenemos el envío automático del formulario

            let form = event.target.closest('form'); // Obtenemos el formulario

            // Configuramos los textos y colores según el botón
            let titulo = '';
            let texto = '';
            let icono = '';
            let colorBoton = '';

            if (tipoDictamen === 'Aprobar') {
                titulo = '¿Aprobar Proyecto?';
                texto = 'El proyecto pasará a estado de ejecución.';
                icono = 'question'; // o 'success'
                colorBoton = '#198754'; // Verde
            } else if (tipoDictamen === 'Rechazar') {
                titulo = '¿Rechazar Proyecto?';
                texto = 'Esta acción puede ser definitiva. Asegúrese de haber escrito las observaciones.';
                icono = 'warning';
                colorBoton = '#dc3545'; // Rojo
            } else {
                titulo = '¿Solicitar Corrección?';
                texto = 'El proyecto será devuelto al formulador.';
                icono = 'info';
                colorBoton = '#ffc107'; // Amarillo
            }

            // Lanzamos SweetAlert
            Swal.fire({
                title: titulo,
                text: texto,
                icon: icono,
                showCancelButton: true,
                confirmButtonColor: colorBoton,
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // 2. TRUCO IMPORTANTE:
                    // Como detuvimos el submit, el controlador no sabrá qué botón se apretó.
                    // Inyectamos un input oculto dinámicamente con el valor.

                    let inputHidden = document.createElement('input');
                    inputHidden.type = 'hidden';
                    inputHidden.name = 'nuevo_dictamen'; // El nombre que espera el controlador
                    inputHidden.value = tipoDictamen;
                    form.appendChild(inputHidden);

                    // 3. Enviamos el formulario manualmente
                    form.submit();
                }
            });
        }
    </script>
@endsection
