@extends('layouts.app')
<style>
    .text-slate-800 {
        color: #1e293b;
    }

    .text-slate-500 {
        color: #64748b;
    }

    .card-clean {
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
    }

    .form-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #64748b;
    }

    .section-title {
        border-left: 4px solid #3b82f6;
        padding-left: 10px;
        font-weight: bold;
        color: #1e293b;
        margin-bottom: 20px;
    }
</style>
@section('content')
    <x-layouts.header_content titulo="Editar Programa de Inversión"
        subtitulo="Modificar los datos del programa seleccionado.">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('inversion.programas.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fas fa-arrow-left me-1"></i> Cancelar y Volver
            </a>
        </div>

    </x-layouts.header_content>
    @include('partials.mensajes')
    <div class="container-fluid py-4">

        <form action="{{ route('inversion.programas.update', $programa->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') <div class="row g-4">
                {{-- DATOS GENERALES --}}
                <div class="col-lg-7">
                    <div class="card card-clean rounded-3 h-100">
                        <div class="alert alert-warning border-warning d-flex align-items-center mb-4 px-3" role="alert">
                            <i class="fas fa-edit me-2 fs-4"></i>
                            <div>
                                Editando programa del <strong>Plan Anual {{ $programa->plan->anio }}</strong>
                                <br>
                                <small>Fechas válidas: {{ $programa->plan->fecha_inicio_fiscal->format('d/m/Y') }} al
                                    {{ $programa->plan->fecha_fin_fiscal->format('d/m/Y') }}</small>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <h6 class="section-title">1. Identificación del Programa</h6>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Estado del Programa</label>
                                    <select name="estado" class="form-select" required>
                                        <option value="POSTULADO"
                                            {{ old('estado', $programa->estado) == 'POSTULADO' ? 'selected' : '' }}>
                                            🟡 Postulado / En Revisión
                                        </option>
                                        <option value="APROBADO"
                                            {{ old('estado', $programa->estado) == 'APROBADO' ? 'selected' : '' }}>
                                            🟢 Aprobado / Activo
                                        </option>
                                        <option value="SUSPENDIDO"
                                            {{ old('estado', $programa->estado) == 'SUSPENDIDO' ? 'selected' : '' }}>
                                            🟠 Suspendido
                                        </option>
                                        <option value="CERRADO"
                                            {{ old('estado', $programa->estado) == 'CERRADO' ? 'selected' : '' }}>
                                            🔴 Cerrado / Finalizado
                                        </option>
                                    </select>
                                    <div id="alerta-estado" class="alert alert-warning mt-2 d-none">
                                        <small><i class="fas fa-info-circle"></i> <span id="texto-alerta"></span></small>
                                    </div>


                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Código CUP</label>
                                    <input type="text" name="codigo_programa"
                                        value="{{ old('codigo_programa', $programa->codigo_programa) }}"
                                        class="form-control" placeholder="Ej: 2024-001" required>
                                    <small class="text-muted" style="font-size: 0.65rem;">Código Único de
                                        Proyecto/Programa</small>
                                </div>

                                <input type="hidden" name="plan_id" value="{{ $programa->plan_id }}">

                                <div class="col-md-12">
                                    <label class="form-label">Nombre del Programa</label>
                                    <input type="text" name="nombre_programa"
                                        value="{{ old('nombre_programa', $programa->nombre_programa) }}"
                                        class="form-control" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Descripción / Objetivo General</label>
                                    <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $programa->descripcion) }}</textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Sector de Inversión</label>
                                    <select name="sector" class="form-select" required>
                                        <option value="">Seleccione Sector...</option>

                                        <option value="SOCIAL"
                                            {{ old('sector', $programa->sector) == 'SOCIAL' ? 'selected' : '' }}>
                                            Social
                                        </option>

                                        <option value="ECONOMICO"
                                            {{ old('sector', $programa->sector) == 'ECONOMICO' ? 'selected' : '' }}>
                                            Económico
                                        </option>

                                        <option value="INFRAESTRUCTURA"
                                            {{ old('sector', $programa->sector) == 'INFRAESTRUCTURA' ? 'selected' : '' }}>
                                            Infraestructura
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Cobertura Geográfica</label>
                                    <select name="cobertura" class="form-select" required>
                                        <option value="NACIONAL"
                                            {{ old('cobertura', $programa->cobertura) == 'NACIONAL' ? 'selected' : '' }}>
                                            Nacional</option>
                                        <option value="ZONAL"
                                            {{ old('cobertura', $programa->cobertura) == 'ZONAL' ? 'selected' : '' }}>Zonal
                                            / Regional</option>
                                        <option value="PROVINCIAL"
                                            {{ old('cobertura', $programa->cobertura) == 'PROVINCIAL' ? 'selected' : '' }}>
                                            Provincial</option>
                                        <option value="CANTONAL"
                                            {{ old('cobertura', $programa->cobertura) == 'CANTONAL' ? 'selected' : '' }}>
                                            Cantonal</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tipo de Programa</label>
                                    <select name="tipo_programa" class="form-select" required>
                                        <option value="" disabled>-- Seleccione --</option>
                                        <option value="INVERSION"
                                            {{ old('tipo_programa', $programa->tipo_programa) == 'INVERSION' ? 'selected' : '' }}>
                                            Inversión (Obra Pública)
                                        </option>

                                        <option value="GASTO_CORRIENTE"
                                            {{ old('tipo_programa', $programa->tipo_programa) == 'GASTO_CORRIENTE' ? 'selected' : '' }}>
                                            Gasto Corriente (Administrativo)
                                        </option>
                                        <option value="CAPITAL_HUMANO"
                                            {{ old('tipo_programa', $programa->tipo_programa) == 'CAPITAL_HUMANO' ? 'selected' : '' }}>
                                            Capital Humano (Capacitación)
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Documento Habilitante</label>
                                    @if ($programa->nombre_archivo)
                                        <div
                                            class="mb-2 p-2 bg-light border rounded d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-file-pdf text-danger"></i>
                                                <span class="text-muted">Archivo Actual:</span>
                                                <div class="fw-bold text-dark">{{ $programa->nombre_archivo }}</div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="{{ asset('storage/' . $programa->url_documento) }}"
                                                    target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                            </div>
                                        </div>
                                        <div class="small text-muted mb-1">Para cambiarlo, sube uno nuevo:</div>
                                    @endif

                                    <input type="file" name="documento_habilitante" class="form-control" accept=".pdf">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TIEMPO Y PRESUPUESTO --}}
                <div class="col-lg-5">
                    <div class="card card-clean rounded-3 mb-4">
                        <div class="card-body p-4">
                            <h6 class="section-title">2. Vigencia y Presupuesto</h6>

                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold">Fecha Inicio</label>
                                    <input type="date" name="fecha_inicio" class="form-control"
                                        min="{{ $programa->plan->anio }}-01-01" max="{{ $programa->plan->anio }}-12-31"
                                        value="{{ old('fecha_inicio', $programa->fecha_inicio) }}" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold">Fecha Fin</label>
                                    <input type="date" name="fecha_fin" class="form-control"
                                        min="{{ $programa->plan->anio }}-01-01" max="{{ $programa->plan->anio }}-12-31"
                                        value="{{ old('fecha_fin', $programa->fecha_fin) }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Monto Asignado ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">$</span>
                                        <input type="number" step="0.01" name="monto_asignado"
                                            value="{{ old('monto_asignado', $programa->monto_asignado) }}"
                                            class="form-control fw-bold" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Monto Total Planificado ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">$</span>
                                        <input type="number" step="0.01" name="monto_planificado"
                                            value="{{ old('monto_planificado', $programa->monto_planificado) }}"
                                            class="form-control fw-bold" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Fuente de Financiamiento</label>
                                    <select name="fuente_id" class="form-select" required>
                                        <option value="">Seleccione una fuente...</option>
                                        @foreach ($fuentes as $fuente)
                                            <option value="{{ $fuente->id_fuente }}"
                                                {{ old('fuente_id', $programa->fuente_id) == $fuente->id_fuente ? 'selected' : '' }}>

                                                {{ $fuente->codigo_fuente }} - {{ $fuente->nombre_fuente }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-clean rounded-3">
                        <div class="card-body p-4">
                            <h6 class="section-title text-success" style="border-left-color: #10b981;">3. Alineación
                                Estratégica</h6>
                            <div class="mb-3">
                                <label class="form-label small">Objetivo Estratégico Institucional</label>
                                <select name="objetivo_estrategico_id" class="form-select bg-light-gray" required>
                                    <option value="">Seleccione Objetivo...</option>
                                    @foreach ($objetivosEstrategicos as $obj)
                                        <option value="{{ $obj->id_objetivo_estrategico }}"
                                            {{ old('objetivo_estrategico_id', $programa->objetivo_estrategico_id) == $obj->id_objetivo_estrategico ? 'selected' : '' }}>

                                            {{ $obj->codigo }} - {{ Str::limit($obj->nombre, 40) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="text-muted small italic">
                                <i class="fas fa-info-circle me-1"></i> Este programa heredará la alineación al PND y ODS
                                definida en el objetivo seleccionado.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-end">
                    <hr class="my-4">
                    <a href="{{ route('inversion.programas.index') }}" class="btn btn-light border me-2">Cancelar</a>

                    <button type="submit" class="btn btn-warning btn-lg shadow px-5 text-white">
                        <i class="fas fa-save me-2"></i> Actualizar
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('scripts')
    <script>
        document.querySelector('select[name="estado"]').addEventListener('change', function() {
            const estado = this.value;
            const alerta = document.getElementById('alerta-estado');
            const texto = document.getElementById('texto-alerta');

            alerta.classList.remove('d-none');
            alerta.classList.remove('alert-danger', 'alert-success', 'alert-warning');

            if (estado === 'APROBADO') {
                alerta.classList.add('alert-success');
                texto.innerText = "Al aprobar, se habilitará la creación de proyectos bajo este programa.";
            } else if (estado === 'CERRADO') {
                alerta.classList.add('alert-danger');
                texto.innerText = "¡Cuidado! Se bloquearán todas las ediciones y no se podrán agregar proyectos.";
            } else if (estado === 'POSTULADO') {
                alerta.classList.add('alert-warning');
                texto.innerText = "El programa volverá a estado de borrador.";
            } else {
                alerta.classList.add('d-none');
            }
        });
    </script>
@endsection
