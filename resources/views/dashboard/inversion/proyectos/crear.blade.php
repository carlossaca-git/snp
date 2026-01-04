@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nuevo Proyecto de Inversión</h1>
        <a href="{{ route('inversion.proyectos.index') }}" class="btn btn-outline-secondary btn-sm">
            <span data-feather="arrow-left"></span> Volver
        </a>
    </div>

    {{-- FORMULARIO TARJETA BLANCA --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <form action="{{ route('inversion.proyectos.store') }}" method="POST">
                @csrf

                <h6 class="heading-small text-muted mb-4">Información General</h6>

                <div class="row">
                    {{-- 1. Programa (Foreign Key) --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label fw-bold">Programa Asociado <span class="text-danger">*</span></label>
                        <select name="id_programa" class="form-select @error('id_programa') is-invalid @enderror" required>
                            <option value="">Seleccione un programa...</option>
                            @foreach ($programas as $programa)
                                <option value="{{ $programa->id }}"
                                    {{ old('id_programa') == $programa->id ? 'selected' : '' }}>
                                    {{ $programa->codigo_programa }} - {{ $programa->nombre_programa }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_programa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- 2. CUP (Código Único) --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label fw-bold">CUP / Código BPIN</label>
                        <input type="text" name="cup" class="form-control @error('cup') is-invalid @enderror"
                            value="{{ old('cup') }}" placeholder="Ej: 2025.001">
                        <small class="text-muted">Dejar en blanco si aún no tiene código oficial.</small>
                        @error('cup')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- 3. Nombre del Proyecto --}}
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Nombre del Proyecto <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_proyecto"
                            class="form-control @error('nombre_proyecto') is-invalid @enderror"
                            value="{{ old('nombre_proyecto') }}" required>
                        @error('nombre_proyecto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- 4. Tipo de Inversión --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Tipo de Inversión <span class="text-danger">*</span></label>
                        <select name="tipo_inversion" class="form-select @error('tipo_inversion') is-invalid @enderror"
                            required>
                            <option value="">Seleccionar...</option>
                            <option value="Obra" {{ old('tipo_inversion') == 'Obra' ? 'selected' : '' }}>Obra</option>
                            <option value="Bien" {{ old('tipo_inversion') == 'Bien' ? 'selected' : '' }}>Bien</option>
                            <option value="Servicio" {{ old('tipo_inversion') == 'Servicio' ? 'selected' : '' }}>Servicio
                            </option>
                        </select>
                        @error('tipo_inversion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- 5. Monto Referencial --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Monto Referencial ($)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="monto_total_inversion" class="form-control"
                                value="{{ old('monto_total_inversion', 0) }}">
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="heading-small text-muted mb-4">Cronograma Estimado</h6>

                <div class="row">
                    {{-- 6. Fechas --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio_estimada" id="fecha_inicio"
                            class="form-control @error('fecha_inicio_estimada') is-invalid @enderror"
                            value="{{ old('fecha_inicio_estimada') }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Fecha Fin</label>
                        <input type="date" name="fecha_fin_estimada" id="fecha_fin"
                            class="form-control @error('fecha_fin_estimada') is-invalid @enderror"
                            value="{{ old('fecha_fin_estimada') }}" required>
                    </div>

                    {{-- Cálculo automático visual (JavaScript opcional abajo) --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted">Duración Estimada</label>
                        <input type="text" id="duracion_display" class="form-control bg-light" readonly
                            placeholder="Calculando...">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Descripción / Diagnóstico</label>
                    <textarea name="descripcion_diagnostico" rows="4" class="form-control">{{ old('descripcion_diagnostico') }}</textarea>
                </div>
                {{--Localizacion del proyecto--}}
                <hr class="my-4">
                <h5 class="mb-3 text-primary"><span data-feather="map-pin"></span> Localización del Proyecto</h5>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Provincia</label>
                        <select name="codigo_provincia" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <option value="Pichincha">Pichincha</option>
                            <option value="Guayas">Guayas</option>
                            <option value="Azuay">Azuay</option>
                            {{-- Aquí irían todas las provincias --}}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Cantón</label>
                        <input type="text" name="codigo_canton" class="form-control" placeholder="Ej: Quito" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Parroquia (Opcional)</label>
                        <input type="text" name="codigo_parroquia" class="form-control" placeholder="Ej: Iñaquito">
                    </div>
                </div>
                <hr class="my-4">
<h5 class="mb-3 text-primary"><span data-feather="dollar-sign"></span> Fuente de Financiamiento</h5>

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label small fw-bold">Año de Asignación</label>
        <input type="number" name="anio_financiamiento" class="form-control" value="{{ date('Y') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">Fuente</label>
        <select name="fuente_financiamiento" class="form-select" required>
            <option value="">Seleccione...</option>
            <option value="Recursos Fiscales">Recursos Fiscales</option>
            <option value="Préstamos Externos">Préstamos Externos</option>
            <option value="Donaciones">Donaciones</option>
            <option value="Recursos Propios">Recursos Propios</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">Monto de esta Fuente</label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" step="0.01" name="monto_financiamiento" class="form-control" placeholder="0.00" required>
        </div>
    </div>
</div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <span data-feather="save"></span> Guardar Proyecto
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- SCRIPT PARA CALCULAR FECHAS EN VIVO --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inicio = document.getElementById('fecha_inicio');
            const fin = document.getElementById('fecha_fin');
            const display = document.getElementById('duracion_display');

            function calcularMeses() {
                if (inicio.value && fin.value) {
                    const d1 = new Date(inicio.value);
                    const d2 = new Date(fin.value);

                    // Cálculo simple de meses
                    let months = (d2.getFullYear() - d1.getFullYear()) * 12;
                    months -= d1.getMonth();
                    months += d2.getMonth();

                    // Ajuste +1 si queremos contar inclusive el mes de inicio
                    months = months <= 0 ? 0 : months + 1;

                    display.value = months + " Meses";
                }
            }

            inicio.addEventListener('change', calcularMeses);
            fin.addEventListener('change', calcularMeses);
        });
    </script>
@endsection
