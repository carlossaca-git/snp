@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        {{-- 1. ENCABEZADO SUPERIOR --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Editar Objetivo Estratégico</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('estrategico.objetivos.index') }}">Planificación</a>
                        </li>
                        {{-- Muestra el código del objetivo actual en el breadcrumb --}}
                        <li class="breadcrumb-item active" aria-current="page">Editar: {{ $objetivo->codigo }}</li>
                    </ol>
                </nav>
                <div class=" border-bottom px-4 py-2 d-flex align-items-center">
                    <i class="fas fa-info-circle me-2"></i>
                    <small class="fw-bold">
                        Organización:
                        <span class="text-dark text-uppercase ms-1">
                            {{ auth()->user()->organizacion->nom_organizacion ?? 'Tu Organización' }}
                        </span>
                    </small>
                </div>
            </div>
            <a href="{{ route('estrategico.objetivos.index') }}"
                class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center">
                <i class="fas fa-arrow-left me-1" data-feather="arrow-left"></i> Volver
            </a>
        </div>

        @include('partials.mensajes')

        {{-- FORMULARIO DE EDICIÓN --}}
        {{-- IMPORTANTE: Ruta update con el ID --}}
        <form action="{{ route('estrategico.objetivos.update', $objetivo->id_objetivo_estrategico) }}" method="POST">
            @csrf
            @method('PUT') {{-- Directiva obligatoria para updates --}}

            <div class="card card-clean shadow-sm border-0">
                {{-- Cabecera de la Tarjeta --}}
                <div class="card-header bg-secondary text-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold"><i class="fas fa-edit me-2"></i>Formulario de Edición</h6>
                    <span class="text-white">{{ auth()->user()->organizacion->siglas ?? 'Sin Organización' }}</span>
                </div>

                {{-- SECCIÓN A: ALINEACIÓN --}}
                <div class="bg-white p-4 rounded shadow-sm mb-4 border-start">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase text-info mb-2">
                                <i class="fas fa-link me-1"></i> 1. Alineación con Plan Nacional (PND) <span
                                    class="text-danger">*</span>
                            </label>
                            <select name="id_objetivo_nacional"
                                class="form-select form-select-lg @error('id_objetivo_nacional') is-invalid @enderror"
                                required>
                                <option value="">Seleccione el Objetivo Nacional...</option>
                                @foreach ($objetivosNacionales as $nac)
                                    <option value="{{ $nac->id_objetivo_nacional }}" {{-- Lógica: Si falla validación usa old(), sino usa la variable del controlador --}}
                                        {{ old('id_objetivo_nacional', $alineacionActual) == $nac->id_objetivo_nacional ? 'selected' : '' }}>
                                        {{ $nac->codigo_objetivo }} - {{ Str::limit($nac->descripcion_objetivo, 150) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_objetivo_nacional')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN B: DEFINICIÓN INSTITUCIONAL --}}
                <div class="bg-white p-4 rounded shadow-sm mb-4">
                    <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">2. Definición Institucional</h6>
                    {{-- Fila 1: Código, Nombre y Tipo --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-uppercase text-muted">Código <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="codigo"
                                class="form-control fw-bold @error('codigo') is-invalid @enderror"
                                value="{{ old('codigo', $objetivo->codigo) }}" placeholder="Objetivo.." required>
                            @error('codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Nombre del Objetivo <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                value="{{ old('nombre', $objetivo->nombre) }}" placeholder="Ej: Incrementar..." required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Tipo de Objetivo</label>
                            <select name="tipo_objetivo" class="form-select">
                                <option value="Estrategico"
                                    {{ old('tipo_objetivo', $objetivo->tipo_objetivo) == 'Estrategico' ? 'selected' : '' }}>
                                    Estratégico (Largo Plazo)
                                </option>
                                <option value="Tactico"
                                    {{ old('tipo_objetivo', $objetivo->tipo_objetivo) == 'Tactico' ? 'selected' : '' }}>
                                    Táctico (Mediano Plazo)
                                </option>
                            </select>
                        </div>
                    </div>

                    {{-- Fila 2: Descripción y Unidad --}}
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-uppercase text-muted">Descripción /
                                Justificación</label>
                            <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="2"
                                placeholder="Explique brevemente...">{{ old('descripcion', $objetivo->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Unidad Responsable</label>
                            <select name="unidad_responsable_id"
                                class="form-select @error('unidad_responsable_id') is-invalid @enderror">
                                <option value="">Seleccione Unidad...</option>
                                {{-- Aquí compara con el valor guardado --}}
                                <option value="1"
                                    {{ old('unidad_responsable_id', $objetivo->unidad_responsable_id) == 1 ? 'selected' : '' }}>
                                    Planificación</option>
                                <option value="2"
                                    {{ old('unidad_responsable_id', $objetivo->unidad_responsable_id) == 2 ? 'selected' : '' }}>
                                    Administrativa</option>
                            </select>
                            @error('unidad_responsable_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN C: INDICADORES Y METAS --}}
                <div class="bg-white p-4 rounded shadow-sm">
                    <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">3. Medición y Tiempos</h6>

                    <div class="row g-3 align-items-start">
                        {{-- Indicador --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Indicador de Resultado</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-chart-line"></i></span>
                                <input type="text" name="indicador"
                                    class="form-control @error('indicador') is-invalid @enderror"
                                    value="{{ old('indicador', $objetivo->indicador) }}"
                                    placeholder="Ej: % de cumplimiento">
                            </div>
                            @error('indicador')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Línea Base --}}
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-uppercase text-muted">Línea Base</label>
                            <input type="text" name="linea_base" step="0.01" class="form-control"
                                value="{{ old('linea_base', (float) $objetivo->linea_base) }}" placeholder="0.00">
                        </div>
                        {{-- Meta --}}
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-uppercase text-muted">Meta Final</label>
                            <input type="text" name="meta" step="0.01" class="form-control border-success"
                                value="{{ old('meta', (float) $objetivo->meta) }}" placeholder="100.00">
                        </div>
                        {{-- Fechas --}}
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-uppercase text-muted">Fecha Inicio <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="fecha_inicio"
                                class="form-control @error('fecha_inicio') is-invalid @enderror"
                                value="{{ old('fecha_inicio', $objetivo->fecha_inicio) }}" required>
                            @error('fecha_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-uppercase text-muted">Fecha Fin <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="fecha_fin"
                                class="form-control @error('fecha_fin') is-invalid @enderror"
                                value="{{ old('fecha_fin', $objetivo->fecha_fin) }}" required>
                            @error('fecha_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- FOOTER DE LA TARJETA --}}
                <div class="card-footer bg-white py-3 border-top">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('estrategico.objetivos.index') }}" class="btn btn-light border px-4">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="btn btn-secondary fw-bold px-4 shadow-sm btn-sm d-inline-flex align-items-center">
                            <i class="fas fa-save me-1"></i> Actualizar Objetivo
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
@endsection
