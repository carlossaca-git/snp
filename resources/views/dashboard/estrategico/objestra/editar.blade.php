@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Editar Objetivo Estratégico" subtitulo="Gestion de obejetivos estrategicos OEI">
        @if (Auth::user()->tienePermiso('pnd.gestionar'))
            <a href="{{ route('estrategico.objetivos.index') }}"
                class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        @endif
    </x-layouts.header_content>
    @include('partials.mensajes')
    <div class="container-fluid py-4">
        <div class="border-bottom px-4 py-2 d-flex align-items-center">
            <i class="fas fa-info-circle me-2"></i>
            <small class="fw-bold">
                <span class="text-dark text-uppercase ms-1">
                    {{ auth()->user()->organizacion->nom_organizacion ?? 'Tu Organización' }}
                </span>
            </small>
        </div>
        <form action="{{ route('estrategico.objetivos.update', $objetivo->id_objetivo_estrategico) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card card-clean shadow-sm border-0">
                <div class="card-header bg-secondary text-white py-2 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold"><i class="fas fa-edit me-2"></i>Formulario de Edición</h6>
                    <span class="text-white">{{ auth()->user()->organizacion->siglas ?? 'Sin Organización' }}</span>
                </div>
                <div class="bg-white p-4 shadow-sm mb-4 border-info">
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-link me-1"></i> 1. Alineación PND</h6>

                    <div class="row g-3">
                        {{-- OBJETIVO NACIONAL --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">
                                Paso 1: Objetivo Nacional
                            </label>
                            <select id="select_objetivo_nacional" class="form-select">
                                <option value="">Seleccione el Objetivo Nacional...</option>
                                @foreach ($objetivosNacionales as $nac)
                                    <option value="{{ $nac->id_objetivo_nacional }}"
                                        {{ $nac->id_objetivo_nacional == $alineacionActual ? 'selected' : '' }}>
                                        {{ $nac->codigo_objetivo }} - {{ Str::limit($nac->descripcion_objetivo, 80) }}
                                @endforeach
                            </select>
                        </div>

                        {{-- META NACIONAL --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-warning-emphasis">
                                Paso 2: META NACIONAL VINCULADA <span class="text-danger">*</span>
                            </label>
                            <select name="id_meta_nacional" id="select_meta_nacional"
                                class="form-select @error('id_meta_nacional') is-invalid @enderror" required>
                                @if ($metaVinculada)
                                    <option value="{{ $metaVinculada->id_meta_nacional }}" selected>
                                        {{ $metaVinculada->nombre_meta }} (Actual:
                                        {{ $metaVinculada->valor_actual ?? '0' }})
                                    </option>
                                @else
                                    <option value="">Primero seleccione un Objetivo...</option>
                                @endif
                            </select>
                            <div class="form-text text-muted small">
                                * Seleccione el Objetivo a la izquierda para actualizar la lista.
                            </div>
                            @error('id_meta_nacional')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>


                {{--  DEFINICIÓN INSTITUCIONAL --}}
                <div class="bg-white p-4 rounded shadow-sm mb-4">
                    <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">2. Definición Institucional</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-uppercase text-muted">Código <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="codigo"
                                class="form-control fw-bold @error('codigo') is-invalid @enderror"
                                value="{{ old('codigo', $objetivo->codigo) }}" required>
                            @error('codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Nombre del Objetivo <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                value="{{ old('nombre', $objetivo->nombre) }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Tipo de Objetivo</label>
                            <select name="tipo_objetivo" class="form-select">
                                <option value="Estrategico"
                                    {{ old('tipo_objetivo', $objetivo->tipo_objetivo) == 'Estrategico' ? 'selected' : '' }}>
                                    Estratégico (Largo Plazo)</option>
                                <option value="Tactico"
                                    {{ old('tipo_objetivo', $objetivo->tipo_objetivo) == 'Tactico' ? 'selected' : '' }}>
                                    Táctico (Mediano Plazo)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-uppercase text-muted">Descripción /
                                Justificación</label>
                            <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="2">{{ old('descripcion', $objetivo->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Unidad Responsable</label>
                            <select name="unidad_responsable_id"
                                class="form-select @error('unidad_responsable_id') is-invalid @enderror">
                                <option value="">Seleccione Unidad...</option>
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

                {{--  MEDICIÓN Y TIEMPOS --}}
                <div class="bg-white p-4 rounded shadow-sm">
                    <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">3. Medición y Tiempos</h6>
                    <div class="row g-3 align-items-start">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Indicador de Resultado</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-chart-line"></i></span>
                                <input type="text" name="indicador"
                                    class="form-control @error('indicador') is-invalid @enderror"
                                    value="{{ old('indicador', $objetivo->indicador) }}">
                            </div>
                            @error('indicador')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-uppercase text-muted">Línea Base</label>
                            <input type="number" step="0.01" name="linea_base" class="form-control"
                                value="{{ old('linea_base', $objetivo->linea_base) }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-uppercase text-muted">Meta Final</label>
                            <input type="number" step="0.01" name="meta" class="form-control border-success"
                                value="{{ old('meta', $objetivo->meta) }}">
                        </div>
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

                {{-- FOOTER --}}
                <div class="card-footer bg-white py-3 border-top text-end">
                    <a href="{{ route('estrategico.objetivos.index') }}" class="btn btn-light border px-4">Cancelar</a>
                    <button type="submit" class="btn btn-secondary fw-bold px-4 shadow-sm btn-sm">
                        <i class="fas fa-save me-1"></i> Actualizar Objetivo
                    </button>
                </div>

            </div>
        </form>
    </div>
@endsection
{{-- SCRIPT PARA LA CARGA DINÁMICA DE METAS --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectObjetivo = document.getElementById('select_objetivo_nacional');
            const selectMeta = document.getElementById('select_meta_nacional');

            //  Generamos la ruta con un ID falso')
            const urlTemplate = "{{ route('catalogos.api.obtener_metas', ['id' => 'FAKE_ID']) }}";

            selectObjetivo.addEventListener('change', function() {
                const objetivoId = this.value;

                selectMeta.innerHTML = '<option value="">Cargando metas...</option>';
                selectMeta.disabled = true;

                if (objetivoId) {
                    // Reemplazamos 'FAKE_ID' por el ID real seleccionado en el momento
                    const urlFinal = urlTemplate.replace('FAKE_ID', objetivoId);

                    console.log("Consultando:", urlFinal); // Para verificar

                    fetch(urlFinal)
                        .then(response => {
                            if (!response.ok) throw new Error(response.statusText);
                            return response.json();
                        })
                        .then(data => {
                            if (data.length > 0) {
                                selectMeta.innerHTML =
                                    '<option value="">Seleccione una Meta Nacional...</option>';
                                data.forEach(meta => {
                                    selectMeta.innerHTML += `
                                    <option value="${meta.id_meta_nacional}">
                                        ${meta.codigo_meta} --${meta.nombre_meta ?? 'N/A'}
                                    </option>`;
                                });
                                selectMeta.disabled = false;
                            } else {
                                selectMeta.innerHTML =
                                    '<option value="">No hay metas para este objetivo</option>';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            selectMeta.innerHTML = '<option value="">Error al cargar metas</option>';
                        });
                } else {
                    selectMeta.innerHTML =
                        '<option value="">Primero seleccione un Objetivo Nacional...</option>';
                    selectMeta.disabled = true;
                }
            });
        });
    </script>
@endpush
