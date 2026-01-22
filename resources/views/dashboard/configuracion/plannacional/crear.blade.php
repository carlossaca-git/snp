@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Registro Plan Nacional" subtitulo="Registre un nuevo plan nacional de desarrollo">
        @if (Auth::user()->tienePermiso('pnd.gestionar'))
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        @endif
    </x-layouts.header_content>
    <div class="container-fluid">
        @include('partials.mensajes')
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-plus-circle me-1"></i> Registrar Nuevo Plan Nacional
                        </h5>
                    </div>
                    <div class="card-body p-4">

                        <form action="{{ route('catalogos.planes-nacionales.store') }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="nombre" class="form-label fw-bold">Nombre del Plan de Desarrollo</label>
                                <input type="text"
                                    class="form-control form-control-lg @error('nombre') is-invalid @enderror"
                                    id="nombre" name="nombre" value="{{ old('nombre') }}"
                                    placeholder="Ej: Plan Nacional Para el Nuevo Ecuador 2024-2025" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="periodo_inicio" class="form-label fw-bold">Año Inicio</label>
                                    <input type="number" class="form-control @error('periodo_inicio') is-invalid @enderror"
                                        id="periodo_inicio" name="periodo_inicio"
                                        value="{{ old('periodo_inicio', date('Y')) }}" min="2000" max="2100"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label for="periodo_fin" class="form-label fw-bold">Año Fin</label>
                                    <input type="number" class="form-control @error('periodo_fin') is-invalid @enderror"
                                        id="periodo_fin" name="periodo_fin" value="{{ old('periodo_fin', date('Y') + 4) }}"
                                        min="2000" max="2100" required>
                                    @error('periodo_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text mt-2 text-muted">
                                    <i class="fas fa-info-circle"></i> El año fin debe ser mayor al año de inicio.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="registro_oficial" class="form-label fw-bold">Registro Oficial / Decreto </label>
                                <input type="text" class="form-control @error('registro_oficial') is-invalid @enderror"
                                    id="registro_oficial" name="registro_oficial" value="{{ old('registro_oficial') }}"
                                    placeholder="Ej: Registro Oficial Suplemento Nro. 123">
                            </div>

                            <div class="alert alert-warning d-flex align-items-center" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <div>
                                    <strong>Nota:</strong> El plan se creará en estado
                                    <span class="badge bg-secondary">INACTIVO</span>.
                                    Puede activarlo desde el listado principal una vez verifique los datos.
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('catalogos.planes-nacionales.index') }}"
                                    class="btn btn-light border">Cancelar</a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-1"></i> Guardar Plan
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
