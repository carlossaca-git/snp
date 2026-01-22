@extends('layouts.app')

@section('titulo', 'Editar Plan Institucional')

@section('content')
    <x-layouts.header_content titulo="Editar Planificación"
        subtitulo="Modificar datos del instrumento: {{ $plan->nombre_plan }}">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </x-layouts.header_content>
    @include('partials.mensajes')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div
                        class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="fas fa-edit me-2"></i>Formulario de Edición
                        </h6>
                        @if ($plan->estado == 'VIGENTE')
                            <span class="badge bg-success">PLAN VIGENTE</span>
                        @else
                            <span class="badge bg-secondary">HISTÓRICO</span>
                        @endif
                    </div>

                    <div class="card-body">
                        <form action="{{ route('estrategico.planificacion.planes.update', $plan->id_plan) }}"
                            method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold small">Nombre del Instrumento <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="nombre_plan"
                                        class="form-control @error('nombre_plan') is-invalid @enderror"
                                        value="{{ old('nombre_plan', $plan->nombre_plan) }}" required>
                                    @error('nombre_plan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">Tipo de Plan <span
                                            class="text-danger">*</span></label>
                                    <select name="tipo_plan" class="form-select @error('tipo_plan') is-invalid @enderror"
                                        required>
                                        <option value="PDOT"
                                            {{ old('tipo_plan', $plan->tipo_plan) == 'PDOT' ? 'selected' : '' }}>PDOT
                                        </option>
                                        <option value="PEI"
                                            {{ old('tipo_plan', $plan->tipo_plan) == 'PEI' ? 'selected' : '' }}>PEI</option>
                                        <option value="PEDI"
                                            {{ old('tipo_plan', $plan->tipo_plan) == 'PEDI' ? 'selected' : '' }}>PEDI
                                        </option>
                                        <option value="SECTORIAL"
                                            {{ old('tipo_plan', $plan->tipo_plan) == 'SECTORIAL' ? 'selected' : '' }}>Plan
                                            Sectorial</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Periodo de
                                        Vigencia</label>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Año Inicio <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="far fa-calendar-alt"></i></span>
                                        <input type="number" name="anio_inicio"
                                            class="form-control @error('anio_inicio') is-invalid @enderror" min="2000"
                                            max="2100" value="{{ old('anio_inicio', $plan->anio_inicio) }}" required>
                                    </div>
                                    @error('anio_inicio')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small">Año Fin <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="far fa-calendar-check"></i></span>
                                        <input type="number" name="anio_fin"
                                            class="form-control @error('anio_fin') is-invalid @enderror" min="2000"
                                            max="2100" value="{{ old('anio_fin', $plan->anio_fin) }}" required>
                                    </div>
                                    @error('anio_fin')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold small">Descripción / Observaciones</label>
                                <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $plan->descripcion) }}</textarea>
                            </div>

                            {{-- Botones --}}
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
                                <a href="{{ route('estrategico.planificacion.planes.index') }}"
                                    class="btn btn-light border px-4">Cancelar</a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-sync-alt me-2"></i> Actualizar Datos
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="alert alert-warning shadow-sm border-0">
                    <h6 class="alert-heading fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Advertencia</h6>
                    <p class="small mb-0">
                        Modificar las fechas de vigencia no eliminará los proyectos asociados, pero podría afectar los
                        reportes históricos si los años quedan fuera del rango esperado.
                    </p>
                </div>
            </div>

        </div>
    </div>
@endsection
