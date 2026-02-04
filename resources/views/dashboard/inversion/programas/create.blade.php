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
    <x-layouts.header_content titulo="Nuevo Programa de Inversión" subtitulo="Registro en el Plan Anual de Inversiones">
        <a href="{{ route('inversion.programas.create') }}" class="btn btn-outline-secondary align-items-center"
            title="Proyectos">
            <i class="fas fa-home me-1"></i>Nuevo
        </a>
        <button type="button" class="btn btn-secondary" onclick="history.back()">
            <i class="fas fa-arrow-left me-1"></i> Atras
        </button>

    </x-layouts.header_content>
    @include('partials.mensajes')
    <div class="container-fluid py-4">

        <form action="{{ route('inversion.programas.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-4">
                {{--  DATOS GENERALES --}}
                <div class="col-lg-7">
                    <div class="card card-clean rounded-3 h-100">
                        <div class="alert alert-info border-info d-flex align-items-center mb-4 px-3" role="alert">
                            <i class="fas fa-info-circle me-2 fs-4"></i>
                            <div>
                                Registro de programa para el <strong>Plan Anual {{ $planInversion->anio }}</strong>
                                <br>
                                <small>Fechas válidas: {{ $planInversion->fecha_inicio_fiscal->format('d/m/Y') }} al
                                    {{ $planInversion->fecha_fin_fiscal->format('d/m/Y') }}</small>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h6 class="section-title">1. Identificación del Programa</h6>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Código CUP</label>
                                    <input type="text" name="codigo_programa" value="{{ old('codigo_programa') }}"
                                        class="form-control" placeholder="Ej: 2024-001" required>
                                    <small class="text-muted" style="font-size: 0.65rem;">Código Único de
                                        Proyecto/Programa</small>
                                </div>
                                <input type="hidden" name="plan_id" value="{{ $planInversion->id }}">
                                <div class="col-md-8">
                                    <label class="form-label">Nombre del Programa</label>
                                    <input type="text" name="nombre_programa" value="{{ old('nombre_programa') }}"
                                        class="form-control" placeholder="Nombre oficial aprobado por la SNP" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Descripción / Objetivo General</label>
                                    <textarea name="descripcion" class="form-control" rows="3" placeholder="Breve resumen del impacto esperado...">{{ old('descripcion') }}</textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Sector de Inversión</label>
                                    <select name="sector" class="form-select" required>
                                        <option value="">Seleccione Sector...</option>
                                        <option value="SOCIAL">Social</option>
                                        <option value="ECONOMICO">Económico</option>
                                        <option value="INFRAESTRUCTURA">Infraestructura</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Cobertura Geográfica</label>
                                    <select name="cobertura" class="form-select" required>
                                        <option value="NACIONAL">Nacional</option>
                                        <option value="ZONAL">Zonal / Regional</option>
                                        <option value="PROVINCIAL">Provincial</option>
                                        <option value="CANTONAL">Cantonal</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tipo de Programa</label>
                                    <select name="tipo_programa" class="form-select" required>
                                        <option value="" selected disabled>-- Seleccione --</option>
                                        <option value="INVERSION"
                                            {{ old('tipo_programa') == 'INVERSION' ? 'selected' : '' }}>
                                            Inversión (Obra Pública)
                                        </option>
                                        <option value="GASTO_CORRIENTE"
                                            {{ old('tipo_programa') == 'GASTO_CORRIENTE' ? 'selected' : '' }}>
                                            Gasto Corriente (Administrativo)
                                        </option>
                                        <option value="CAPITAL_HUMANO"
                                            {{ old('tipo_programa') == 'CAPITAL_HUMANO' ? 'selected' : '' }}>
                                            Capital Humano (Capacitación)
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Documento Habilitante (Resolución/Acta)</label>
                                    <input type="file" name="documento_habilitante" class="form-control" accept=".pdf">
                                    <div class="form-text">Subir en formato PDF (Máx. 10MB).</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{--  TIEMPO Y PRESUPUESTO --}}
                <div class="col-lg-5">
                    <div class="card card-clean rounded-3 mb-4">
                        <div class="card-body p-4">
                            <h6 class="section-title">2. Vigencia y Presupuesto</h6>

                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold ">Fecha Inicio</label>
                                    <input type="date" name="fecha_inicio" class="form-control"
                                        min="{{ $planInversion?->anio }}-01-01" max="{{ $planInversion?->anio }}-12-31"
                                        value="{{ $planInversion?->anio }}-01-01" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold">Fecha Fin</label>
                                    <input type="date" name="fecha_fin" class="form-control"
                                        min="{{ $planInversion?->anio }}-01-01" max="{{ $planInversion?->anio }}-12-31"
                                        value="{{ $planInversion?->anio }}-12-31" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Monto Asignado ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">$</span>
                                        <input type="number" step="0.01" name="monto_asignado"
                                            class="form-control fw-bold" placeholder="0.00" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Monto Total Planificado ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">$</span>
                                        <input type="number" step="0.01" name="monto_planificado"
                                            class="form-control fw-bold" placeholder="0.00" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Fuente de Financiamiento</label>
                                    <select name="id_fuente" class="form-select" required>
                                        <option value="">Seleccione una fuente...</option>
                                        @foreach ($fuentes as $fuente)
                                            <option value="{{ $fuente->id_fuente }}">
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
                                <select name="id_objetivo_estrategico" class="form-select bg-light-gray" required>
                                    <option value="">Seleccione Objetivo...</option>
                                    @foreach ($objetivosEstrategicos as $obj)
                                        <option value="{{ $obj->id_objetivo_estrategico }}">{{ $obj->codigo }} -
                                            {{ Str::limit($obj->nombre, 40) }}</option>
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
                    <button type="submit" class="btn btn-success btn-lg shadow px-5">
                        <i class="fas fa-save me-2"></i> Registrar
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
