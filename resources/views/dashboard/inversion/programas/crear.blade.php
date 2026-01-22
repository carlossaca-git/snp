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
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('inversion.programas.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fas fa-arrow-left me-1"></i> Cancelar y Volver
            </a>
        </div>

    </x-layouts.header_content>
    @include('partials.mensajes')
    <div class="container-fluid py-4">

        <form action="{{ route('inversion.programas.store') }}" method="POST">
            @csrf

            <div class="row g-4">
                {{--  DATOS GENERALES --}}
                <div class="col-lg-7">
                    <div class="card card-clean rounded-3 h-100">
                        <div class="card-body p-4">
                            <h6 class="section-title">I. Identificación del Programa</h6>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Código CUP</label>
                                    <input type="text" name="cup" value="{{ old('cup') }}"
                                        class="form-control" placeholder="Ej: 2024-001" required>
                                    <small class="text-muted" style="font-size: 0.65rem;">Código Único de
                                        Proyecto/Programa</small>
                                </div>

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
                                    <select name="id_sector" class="form-select" required>
                                        <option value="">Seleccione Sector...</option>
                                        <option value="1">Social</option>
                                        <option value="2">Económico</option>
                                        <option value="3">Infraestructura</option>
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
                            </div>
                        </div>
                    </div>
                </div>
                {{--  TIEMPO Y PRESUPUESTO --}}
                <div class="col-lg-5">
                    <div class="card card-clean rounded-3 mb-4">
                        <div class="card-body p-4">
                            <h6 class="section-title">II. Vigencia y Presupuesto</h6>

                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label">Año Inicio</label>
                                    <input type="number" name="anio_inicio" value="{{ date('Y') }}"
                                        class="form-control" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Año Fin</label>
                                    <input type="number" name="anio_fin" value="{{ date('Y') + 4 }}" class="form-control"
                                        required>
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
                            <h6 class="section-title text-success" style="border-left-color: #10b981;">III. Alineación
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
