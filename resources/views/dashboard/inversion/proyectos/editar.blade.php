@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        {{-- CAMBIO: Título dinámico --}}
        <h1 class="h3 mb-0 text-gray-800">Editar Proyecto: {{ $proyecto->cup }}</h1>
        <a href="{{ route('inversion.proyectos.index') }}" class="btn btn-outline-secondary btn-sm">
            <span data-feather="arrow-left"></span> Volver
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            {{-- CAMBIO: La ruta ahora lleva el ID y el método es POST (Laravel usará @method abajo) --}}
            <form action="{{ route('inversion.proyectos.update', $proyecto->id) }}" method="POST">
                @csrf
                @method('PUT') {{-- NUEVO: Obligatorio para actualizaciones --}}

                <h6 class="heading-small text-muted mb-4">Información General</h6>

                <div class="row">
                    {{-- 1. Programa --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label fw-bold">Programa Asociado <span class="text-danger">*</span></label>
                        <select name="id_programa" class="form-select @error('id_programa') is-invalid @enderror" required>
                            @foreach ($programas as $programa)
                                <option value="{{ $programa->id }}"
                                    {{ old('id_programa', $proyecto->id_programa) == $programa->id ? 'selected' : '' }}>
                                    {{ $programa->codigo_programa }} - {{ $programa->nombre_programa }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. CUP --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label fw-bold">CUP / Código BPIN</label>
                        <input type="text" name="cup" class="form-control @error('cup') is-invalid @enderror"
                            value="{{ old('cup', $proyecto->cup) }}">
                    </div>
                </div>

                <div class="row">
                    {{-- 3. Nombre del Proyecto --}}
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Nombre del Proyecto <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_proyecto"
                            class="form-control @error('nombre_proyecto') is-invalid @enderror"
                            value="{{ old('nombre_proyecto', $proyecto->nombre_proyecto) }}" required>
                    </div>
                </div>

                <div class="row">
                    {{-- 4. Tipo de Inversión --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Tipo de Inversión <span class="text-danger">*</span></label>
                        <select name="tipo_inversion" class="form-select" required>
                            <option value="Obra" {{ old('tipo_inversion', $proyecto->tipo_inversion) == 'Obra' ? 'selected' : '' }}>Obra</option>
                            <option value="Bien" {{ old('tipo_inversion', $proyecto->tipo_inversion) == 'Bien' ? 'selected' : '' }}>Bien</option>
                            <option value="Servicio" {{ old('tipo_inversion', $proyecto->tipo_inversion) == 'Servicio' ? 'selected' : '' }}>Servicio</option>
                        </select>
                    </div>

                    {{-- 5. Monto Referencial --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Monto Referencial ($)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="monto_total_inversion" class="form-control"
                                value="{{ old('monto_total_inversion', $proyecto->monto_total_inversion) }}">
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="heading-small text-muted mb-4">Cronograma Estimado</h6>

                <div class="row">
                    {{-- 6. Fechas (Convertidas a formato Y-m-d para el input date) --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio_estimada" id="fecha_inicio" class="form-control"
                            value="{{ old('fecha_inicio_estimada', $proyecto->fecha_inicio_estimada->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Fecha Fin</label>
                        <input type="date" name="fecha_fin_estimada" id="fecha_fin" class="form-control"
                            value="{{ old('fecha_fin_estimada', $proyecto->fecha_fin_estimada->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted">Duración Estimada</label>
                        <input type="text" id="duracion_display" class="form-control bg-light" readonly
                               value="{{ $proyecto->duracion_meses }} Meses">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Descripción / Diagnóstico</label>
                    <textarea name="descripcion_diagnostico" rows="4" class="form-control">{{ old('descripcion_diagnostico', $proyecto->descripcion_diagnostico) }}</textarea>
                </div>

                {{-- Localización --}}
                <hr class="my-4">
                <h5 class="mb-3 text-primary"><span data-feather="map-pin"></span> Localización</h5>
                @php $loc = $proyecto->localizaciones->first(); @endphp
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Provincia</label>
                        <select name="codigo_provincia" class="form-select" required>
                            <option value="Pichincha" {{ old('codigo_provincia', $loc->codigo_provincia ?? '') == 'Pichincha' ? 'selected' : '' }}>Pichincha</option>
                            <option value="Guayas" {{ old('codigo_provincia', $loc->codigo_provincia ?? '') == 'Guayas' ? 'selected' : '' }}>Guayas</option>
                            <option value="Azuay" {{ old('codigo_provincia', $loc->codigo_provincia ?? '') == 'Azuay' ? 'selected' : '' }}>Azuay</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Cantón</label>
                        <input type="text" name="codigo_canton" class="form-control"
                               value="{{ old('codigo_canton', $loc->codigo_canton ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Parroquia</label>
                        <input type="text" name="codigo_parroquia" class="form-control"
                               value="{{ old('codigo_parroquia', $loc->codigo_parroquia ?? '') }}">
                    </div>
                </div>

                {{-- Financiamiento --}}
                <hr class="my-4">
                <h5 class="mb-3 text-primary"><span data-feather="dollar-sign"></span> Financiamiento</h5>
                @php $fin = $proyecto->financiamientos->first(); @endphp
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Año</label>
                        <input type="number" name="anio_financiamiento" class="form-control"
                               value="{{ old('anio_financiamiento', $fin->anio ?? date('Y')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Fuente</label>
                        <select name="fuente_financiamiento" class="form-select" required>
                            <option value="Recursos Fiscales" {{ old('fuente_financiamiento', $fin->fuente_financiamiento ?? '') == 'Recursos Fiscales' ? 'selected' : '' }}>Recursos Fiscales</option>
                            <option value="Donaciones" {{ old('fuente_financiamiento', $fin->fuente_financiamiento ?? '') == 'Donaciones' ? 'selected' : '' }}>Donaciones</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Monto Fuente</label>
                        <input type="number" step="0.01" name="monto_financiamiento" class="form-control"
                               value="{{ old('monto_financiamiento', $fin->monto ?? 0) }}" required>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success px-4 text-white">
                        <span data-feather="refresh-cw"></span> Actualizar Proyecto
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const inicio = document.getElementById('fecha_inicio');
        const fin = document.getElementById('fecha_fin');
        const display = document.getElementById('duracion_display');

        function calcularMeses() {
            if (inicio.value && fin.value) {
                const d1 = new Date(inicio.value);
                const d2 = new Date(fin.value);

                // Calculamos la diferencia de años y meses
                let months = (d2.getFullYear() - d1.getFullYear()) * 12;
                months -= d1.getMonth();
                months += d2.getMonth();

                // Sumamos 1 para incluir el mes de inicio (criterio estándar de proyectos)
                // Si la fecha fin es menor a la de inicio, ponemos 0
                const resultado = months <= 0 ? 0 : months + 1;

                display.value = resultado + " Meses";
            }
        }

        // 1. Ejecutar al cargar la página para que muestre la duración actual
        calcularMeses();

        // 2. Escuchar cambios en los inputs
        inicio.addEventListener('change', calcularMeses);
        fin.addEventListener('change', calcularMeses);
    });
</script>
@endsection
