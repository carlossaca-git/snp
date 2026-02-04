@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Ficha tecnica de ODS" subtitulo="Objetivos de Desarrollo Sostenible (Agenda 2030)">
        @if (Auth::user()->tienePermiso('ods.gestionar'))
            <button type="button" class="btn btn-secondary shadow-sm d-inline-flex align-items-center" data-bs-toggle="modal"
                data-bs-target="#modalCrearOds">
                <i class="fas fa-plus me-1"></i> Nuevo ODS
            </button>
        @endif
    </x-layouts.header_content>
    @php
        $colorOds = $ods->color_hex ?? '#6c757d';
        $avanceOds = $ods->avance_promedio;
    @endphp
    <div class="position-relative mb-4 shadow-sm rounded-3 overflow-hidden bg-white"
        style="border-left: 10px solid {{ $colorOds }}; min-height: 200px;">
        <div class="position-relative p-4 p-lg-5 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="text-center rounded-3 shadow-sm d-flex align-items-center justify-content-center me-4"
                    style="width: 100px; height: 100px; min-width: 100px;
                        background-color: #fff;
                        border: 4px solid {{ $colorOds }};">
                    <span class="fw-bold" style="font-size: 1rem; color: {{ $colorOds }};">
                        {{ $ods->codigo }}
                    </span>
                </div>
                <div>
                    <div class="text-uppercase fw-bold text-muted mb-1" style="letter-spacing: 1px; font-size: 0.75rem;">
                        Objetivo de Desarrollo Sostenible
                    </div>
                    <h1 class="display-5 fw-bold mb-2 text-dark">{{ $ods->nombre }}</h1>
                    <p class="lead mb-0 text-muted d-none d-md-block" style="font-size: 1rem; max-width: 800px;">
                        {{ $ods->descripcion }}
                    </p>
                </div>
            </div>
            <div class="text-center bg-light rounded-3 p-3 border shadow-sm">
                <div class="text-uppercase text-xs fw-bold mb-1 text-muted">Cumplimiento País</div>
                <div class="display-4 fw-bold text-dark">
                    {{ number_format($avanceOds, 1) }}%
                </div>
                <div class="progress mt-2" style="height: 6px; width: 150px; background-color: #e9ecef;">
                    <div class="progress-bar" role="progressbar"
                        style="width: {{ $avanceOds }}%; background-color: {{ $colorOds }};">
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
            <i class="fas fa-arrow-left me-2"></i> Volver al Catálogo
        </button>

        @if (Auth::user()->tienePermiso('ods.gestionar'))
            <button class="btn text-white shadow-sm" style="background-color: {{ $colorOds }}; filter: brightness(90%);"
                data-bs-toggle="modal" data-bs-target="#modalEditarOds"
                onclick="cargarDatosEdicion('{{ $ods->id_ods }}', '{{ $ods->codigo }}', '{{ $ods->nombre }}', '{{ $ods->descripcion }}', '{{ $ods->color_hex }}', '{{ $ods->pilar }}', '{{ $ods->estado }}')">
                <i class="fas fa-edit me-1"></i> Editar ODS
            </button>
        @endif
    </div>
    {{-- Mensajes de Error y Exito al editar--}}
    @include('partials.mensajes')

    <div class="row g-4">
        <div class="col-lg-3">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold py-3">Resumen de Impacto</div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div>
                            <i class="fas fa-bullseye text-secondary me-2"></i> Metas Nacionales
                        </div>
                        <span class="badge rounded-pill bg-light text-dark border">{{ $totalMetas }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div>
                            <i class="fas fa-chart-pie text-secondary me-2"></i> Indicadores
                        </div>
                        <span class="badge rounded-pill bg-light text-dark border">{{ $totalIndicadores }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div>
                            <i class="fas fa-project-diagram text-secondary me-2"></i> Proyectos
                        </div>
                        <span class="badge rounded-pill bg-light text-dark border">{{ $totalProyectos }}</span>
                    </div>
                </div>
                <div class="card-footer bg-light p-3">
                    <small class="text-muted d-block lh-sm">
                        <strong>Pilar Estratégico:</strong><br> {{ $ods->pilar ?? 'No definido' }}
                    </small>
                </div>
            </div>
        </div>

        {{-- DESGLOSE DE METAS --}}
        <div class="col-lg-9">
            <h5 class="fw-bold mb-3 text-secondary">
                <i class="fas fa-tasks me-2"></i> Contribución por Metas Nacionales
            </h5>

            @forelse($ods->metasNacionales as $meta)
                @php
                    $avanceMeta = $meta->avance_actual;
                    // Accessor del Modelo MetaNacional
                    // Lógica de Semáforo para la Meta
                    $colorBarra = 'bg-danger';
                    if ($avanceMeta >= 40) {
                        $colorBarra = 'bg-warning';
                    }
                    if ($avanceMeta >= 80) {
                        $colorBarra = 'bg-primary';
                    } // Azul institucional
                    if ($avanceMeta >= 100) {
                        $colorBarra = 'bg-success';
                    }
                @endphp

                <div class="card border-0 shadow-sm mb-3 hover-shadow transition-all">
                    <div class="card-body">
                        <div class="row align-items-center">

                            {{-- 1. INFORMACIÓN DE LA META --}}
                            <div class="col-md-5 mb-3 mb-md-0">
                                <div class="text-xs text-uppercase fw-bold text-muted mb-1">
                                    {{ $meta->codigo_meta ?? 'Meta' }}
                                </div>
                                <h6 class="fw-bold text-dark mb-1">
                                    <a href="{{ route('catalogos.metas.show', $meta->id_meta_nacional) }}"
                                        class="text-decoration-none text-dark stretched-link">
                                        {{ $meta->nombre_meta }}
                                    </a>
                                </h6>
                                <div class="small text-muted text-truncate">
                                    {{ $meta->indicadoresNacionales->count() }} indicadores vinculados
                                </div>
                            </div>
                            <div class="col-md-5 mb-3 mb-md-0">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Progreso ponderado</span>
                                    <span class="fw-bold text-dark">{{ number_format($avanceMeta, 1) }}%</span>
                                </div>
                                <div class="progress" style="height: 10px; background-color: #f1f1f1;">
                                    <div class="progress-bar {{ $colorBarra }}" role="progressbar"
                                        style="width: {{ $avanceMeta }}%"></div>
                                </div>
                            </div>
                            <div class="col-md-2 text-end position-relative">
                                <span class="btn btn-sm btn-light rounded-circle">
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </span>
                            </div>
                        </div>
                        @if ($meta->indicadoresNacionales->count() > 0)
                            <div class="mt-3 pt-3 border-top bg-light bg-opacity-25 rounded p-2">
                                <small class="d-block text-muted mb-2 fw-bold text-xs">PRINCIPALES INDICADORES:</small>
                                <div class="row g-2">
                                    @foreach ($meta->indicadoresNacionales->take(3) as $ind)
                                        <div class="col-md-4">
                                            <div
                                                class="d-flex align-items-center bg-white border rounded p-1 shadow-sm h-100">
                                                <div class="rounded-circle me-2 flex-shrink-0"
                                                    style="width: 8px; height: 8px; background-color: {{ $ind->porcentaje_cumplimiento >= 80 ? '#1cc88a' : ($ind->porcentaje_cumplimiento < 40 ? '#e74a3b' : '#f6c23e') }}">
                                                </div>

                                                <div class="text-truncate" style="font-size: 0.7rem; max-width: 90%;"
                                                    title="{{ $ind->nombre }}">
                                                    {{ Str::limit($ind->nombre, 30) }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if ($meta->indicadoresNacionales->count() > 3)
                                        <div class="col-md-12 text-center mt-1">
                                            <span
                                                class="text-xs text-muted fst-italic">+{{ $meta->indicadoresNacionales->count() - 3 }}
                                                indicadores más...</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

            @empty
                <div class="alert alert-light text-center py-5 border border-dashed">
                    <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                    <h5>Sin Metas Vinculadas</h5>
                    <p class="text-muted">Este ODS aún no tiene metas nacionales asignadas para medir su avance.</p>
                    <a href="{{ route('catalogos.metas.index') }}" class="btn btn-primary btn-sm mt-2">
                        Ir a Metas Nacionales
                    </a>
                </div>
            @endforelse
        </div>
    </div>
    @include('dashboard.configuracion.ods.edit')

@endsection

@section('scripts')
    <script>
        // Función auxiliar para cargar datos en el modal sin recargar JS complejo
        function cargarDatosEdicion(id, codigo, nombre, descripcion, color, pilar, estado) {
            document.getElementById('edit_codigo').value = codigo;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_descripcion').value = descripcion;
            document.getElementById('edit_color').value = color;
            document.getElementById('edit_pilar').value = pilar;
            document.getElementById('edit_estado').value = estado;
            document.getElementById('edit_id_temp').value = id;
            document.getElementById('formEditarOds').action = `/catalogos/ods/${id}`;
        }
    </script>
    <style>
        .hover-shadow:hover {
            transform: translateY(-2px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        .backdrop-blur {
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }
    </style>
@endsection
