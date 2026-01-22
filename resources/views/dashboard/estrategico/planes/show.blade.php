@extends('layouts.app')

@section('titulo', 'Detalle del Plan')

@section('content')
    <x-layouts.header_content titulo="Expediente del Plan" subtitulo="Visualización de datos históricos">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </x-layouts.header_content>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4">
                <div class="card shadow mb-4 border-left-secondary" style="border-left: 5px solid #858796;">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-secondary">Datos Generales</h6>
                    </div>
                    <div class="card-body">
                        <h4 class="fw-bold text-dark">{{ $plan->nombre_plan }}</h4>
                        <span class="badge bg-secondary mb-3">{{ $plan->estado }}</span>

                        <hr>

                        <div class="mb-3">
                            <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">Tipo de
                                Instrumento</small>
                            <p class="mb-0 fw-bold">{{ $plan->tipo_plan }}</p>
                        </div>

                        <div class="mb-3">
                            <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">Vigencia</small>
                            <p class="mb-0">
                                <i class="far fa-calendar-alt me-2"></i>{{ $plan->anio_inicio }} - {{ $plan->anio_fin }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">Descripción</small>
                            <p class="small text-muted mb-0">
                                {{ $plan->descripcion ?? 'Sin descripción registrada.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: Los Objetivos de ese Plan --}}
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-primary">Objetivos Estratégicos Definidos</h6>
                        <span class="badge bg-light text-dark border">Total:
                            {{ $plan->objetivos_estrategicos_count ?? $plan->objetivosEstrategicos->count() }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0" width="100%">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="font-size: 11px;">Objetivo Institucional</th>
                                        <th style="font-size: 11px;">Alineación PND (Histórica)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($plan->objetivosEstrategicos as $obj)
                                        <tr>
                                            <td class="p-3">
                                                <span class="fw-bold d-block text-dark"
                                                    style="font-size: 13px;">{{ $obj->nombre ?? 'Sin nombre' }}</span>
                                                <p class="small text-muted mb-1">{{ Str::limit($obj->descripcion, 100) }}
                                                </p>
                                                @if ($obj->indicador)
                                                    <span class="badge bg-info text-dark" style="font-size: 9px;">IND:
                                                        {{ $obj->indicador }}</span>
                                                @endif
                                            </td>
                                            <td class="p-3 border-start">
                                                @if ($obj->alineacion && $obj->alineacion->metaNacional)
                                                    <small class="d-block text-primary fw-bold">
                                                        {{ $obj->alineacion->metaNacional->codigo_meta ?? 'S/C' }}
                                                    </small>
                                                    <span class="small text-secondary" style="font-size: 11px;">
                                                        {{ Str::limit($obj->alineacion->metaNacional->nombre_meta, 80) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small fst-italic">Sin alineación
                                                        registrada</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-4 text-muted">
                                                Este plan no tuvo objetivos registrados.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
