@extends('layouts.app')

@section('titulo', 'Gestión de Planes Institucionales')

@section('content')
    <x-layouts.header_content titulo="Planificación Institucional"
        subtitulo="Gestión de instrumentos de planificación (PEI / PDOT)">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('estrategico.planificacion.planes.create') }}"
                class="btn btn-sm btn-secondary me-2 d-inline-flex align-items-center">
                <i class="fas fa-plus"></i>Nuevo Plan
            </a>
        </div>
    </x-layouts.header_content>
    <div class="container-fluid">

        @include('partials.mensajes')

        @if ($planVigente)
            <div class="card shadow mb-4 border-left-primary" style="border-left: 5px solid #1a4a72;">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-star me-2 text-warning"></i>Instrumento de Planificación Vigente
                    </h6>
                    <span class="badge bg-success px-3 py-2">ACTIVO</span>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="h4 fw-bold text-dark mb-1">{{ $planVigente->nombre_plan }}</h3>
                            <p class="text-muted mb-3">
                                <i class="far fa-calendar-alt me-1"></i> Periodo: <strong>{{ $planVigente->anio_inicio }} -
                                    {{ $planVigente->anio_fin }}</strong>
                                <span class="mx-2">|</span>
                                <i class="fas fa-tag me-1"></i> Tipo: {{ $planVigente->tipo_plan }}
                            </p>

                            <p class="mb-4 text-secondary small">
                                {{ $planVigente->descripcion ?? 'Sin descripción registrada.' }}
                            </p>

                            {{-- Estadísticas --}}
                            <div class="d-flex gap-4 mb-3">
                                <div class="border rounded p-2 px-3 text-center bg-light">
                                    <small class="d-block text-muted text-uppercase"
                                        style="font-size: 10px;">Objetivos</small>
                                    <span
                                        class="h5 fw-bold text-primary mb-0">{{ $planVigente->objetivos_estrategicos_count }}</span>
                                </div>
                                <div class="border rounded p-2 px-3 text-center bg-light">
                                    <small class="d-block text-muted text-uppercase" style="font-size: 10px;">Avance
                                        Periodo</small>
                                    @php
                                        $totalAnios = $planVigente->anio_fin - $planVigente->anio_inicio;
                                        $transcurrido = date('Y') - $planVigente->anio_inicio;
                                        $porcentaje =
                                            $totalAnios > 0
                                                ? min(100, max(0, ($transcurrido / $totalAnios) * 100))
                                                : 100;
                                    @endphp
                                    <span class="h5 fw-bold text-info mb-0">{{ round($porcentaje) }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center text-md-end border-start">
                            <p class="small text-muted mb-2">Acciones Rápidas</p>
                            <a href="{{ route('estrategico.objetivos.index', $planVigente->id_plan) }}"
                                class="btn btn-primary w-100 mb-2 shadow-sm">
                                <i class="fas fa-bullseye me-2"></i>Gestionar Objetivos Estratégicos
                            </a>
                            {{-- ACCIONES --}}
                            <div class="btn-group w-100">
                                <a href="{{ route('estrategico.planificacion.planes.edit', $planVigente->id_plan) }}"
                                    class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-edit me-1"></i> Editar
                                </a>
                                <form
                                    action="{{ route('estrategico.planificacion.planes.cerrar', $planVigente->id_plan) }}"
                                    method="POST" class="form-cerrar-plan d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-archive me-1"></i> Cerrar Periodo
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5 mb-4 bg-white rounded shadow-sm border border-dashed">
                <div class="mb-3">
                    <span class="fa-stack fa-3x text-muted opacity-50">
                        <i class="fas fa-circle fa-stack-2x"></i>
                        <i class="fas fa-book fa-stack-1x fa-inverse"></i>
                    </span>
                </div>
                <h4 class="text-dark">No hay un Plan Vigente registrado</h4>
                <p class="text-muted mb-4">Para comenzar a registrar proyectos de inversión, primero debe configurar su
                    instrumento de planificación (PEI/PDOT).</p>
                <a href="{{ route('estrategico.planificacion.planes.create') }}" class="btn btn-primary px-4 rounded-pill">
                    <i class="fas fa-plus me-2"></i>Registrar Plan Ahora
                </a>
            </div>
        @endif
        {{--  HISTORIAL DE PLANES --}}
        @if ($historial->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-secondary">Historial de Planificación</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nombre del Plan</th>
                                    <th>Tipo</th>
                                    <th>Periodo</th>
                                    <th>Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($historial as $plan)
                                    <tr>
                                        <td>{{ $plan->nombre_plan }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $plan->tipo_plan }}</span>
                                        </td>
                                        <td>{{ $plan->anio_inicio }} - {{ $plan->anio_fin }}</td>
                                        <td class="text-center">
                                            @if ($plan->estado == 'HISTORICO')
                                                <span class="badge bg-secondary text-white" data-bs-toggle="tooltip"
                                                    title="Reemplazado por nuevo plan">
                                                    <i class="fas fa-history me-1"></i> HISTÓRICO
                                                </span>
                                            @elseif($plan->estado == 'CERRADO')
                                                <span class="badge bg-dark text-white" data-bs-toggle="tooltip"
                                                    title="Cerrado manualmente">
                                                    <i class="fas fa-lock me-1"></i> CERRADO
                                                </span>
                                            @elseif($plan->estado == 'BORRADOR')
                                                <span class="badge bg-warning text-dark">BORRADOR</span>
                                            @else
                                                <span class="badge bg-light text-dark border">{{ $plan->estado }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('estrategico.planificacion.planes.show', $plan->id_plan) }}"
                                                    class="btn btn-sm btn-light border" title="Ver Expediente">
                                                    <i class="fas fa-eye text-primary"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionamos todos los formularios con la clase 'form-cerrar-plan'
            const forms = document.querySelectorAll('.form-cerrar-plan');

            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: '¿Está seguro de cerrar este Plan?',
                        text: "Al cerrar el plan, pasará al historial y ya no se podrán vincular nuevos objetivos ni proyectos. Esta acción marca el fin del periodo de gestión.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#1a4a72',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, cerrar plan',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
