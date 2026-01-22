@extends('layouts.app')

@section('titulo', 'Nuevo Plan Institucional')

@section('content')
    <x-layouts.header_content titulo="Planificación Institucional"
        subtitulo="Registre el instrumento de planificación vigente (PEI / PDOT)">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </x-layouts.header_content>
    <div class="container-fluid">
        @include('partials.mensajes')
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="fas fa-book-reader me-2"></i>Datos del Plan
                        </h6>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('estrategico.planificacion.planes.store') }}" method="POST">
                            @csrf
                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold small">Nombre del Instrumento <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="nombre_plan"
                                        class="form-control @error('nombre_plan') is-invalid @enderror"
                                        placeholder="Ej: Plan de Desarrollo y Ordenamiento Territorial 2024-2028"
                                        value="{{ old('nombre_plan') }}" required>
                                    @error('nombre_plan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">Tipo de Plan <span
                                            class="text-danger">*</span></label>
                                    <select name="tipo_plan" class="form-select @error('tipo_plan') is-invalid @enderror"
                                        required>
                                        <option value="" disabled selected>Seleccione...</option>
                                        <option value="PDOT" {{ old('tipo_plan') == 'PDOT' ? 'selected' : '' }}>PDOT
                                            (GADs)</option>
                                        <option value="PEI" {{ old('tipo_plan') == 'PEI' ? 'selected' : '' }}>PEI
                                            (Institucional)</option>
                                        <option value="PEDI" {{ old('tipo_plan') == 'PEDI' ? 'selected' : '' }}>PEDI
                                            (Universidades/Otros)</option>
                                        <option value="SECTORIAL" {{ old('tipo_plan') == 'SECTORIAL' ? 'selected' : '' }}>
                                            Plan Sectorial (Ministerios)</option>
                                    </select>
                                    @error('tipo_plan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                            class="form-control @error('anio_inicio') is-invalid @enderror"
                                            placeholder="Ej: 2024" min="2000" max="2100"
                                            value="{{ old('anio_inicio', date('Y')) }}" required>
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
                                            class="form-control @error('anio_fin') is-invalid @enderror"
                                            placeholder="Ej: 2028" min="2000" max="2100"
                                            value="{{ old('anio_fin') }}" required>
                                    </div>
                                    @error('anio_fin')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold small">Descripción / Observaciones</label>
                                <textarea name="descripcion" class="form-control" rows="3"
                                    placeholder="Breve descripción del alcance del plan...">{{ old('descripcion') }}</textarea>
                            </div>

                            <div class="alert alert-info small d-flex align-items-center">
                                <i class="fas fa-info-circle fa-lg me-3"></i>
                                <div>
                                    <strong>Nota Importante:</strong> Al registrar este plan, se marcará automáticamente
                                    como
                                    <span class="badge bg-success">VIGENTE</span> y cualquier plan anterior pasará a
                                    historial.
                                </div>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="reset" class="btn btn-light border">Cancelar</button>
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="fas fa-save me-2"></i> Guardar y Continuar
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 bg-success-subtle text-dark">
                    <div class="card-body">
                        <h5 class="card-title fw-bold"><i class="fas fa-lightbulb me-2"></i>¿Informacion?</h5>
                        <p class="small opacity-75">
                            Es el instrumento macro de planificación de su institución.
                        </p>
                        <ul class="small ps-3 mb-0">
                            <li class="mb-1">Aquí define el periodo de gobierno o gestión.</li>
                            <li class="mb-1">Los Objetivos Estratégicos se vincularán a este plan.</li>
                            <li>Los Proyectos de Inversión (PAI) deben responder a este plan.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
