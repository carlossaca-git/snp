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
        <form action="{{ route('estrategico.objetivos.update', $objetivoEstr->id_objetivo_estrategico) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card card-clean shadow-sm border-0">
                <div class="card-header bg-secondary text-white py-2 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold"><i class="fas fa-edit me-2"></i>Formulario de Edición</h6>
                    <span class="text-white">{{ auth()->user()->organizacion->siglas ?? 'Sin Organización' }}</span>
                </div>
                {{--  DEFINICIÓN INSTITUCIONAL --}}
                <div class="bg-white p-4 rounded shadow-sm mb-4">
                    <h6 class="fw-bold text-dark mb-4 border-bottom pb-2"><i class="fas fa-building me-2"></i> 1. Definición
                        Institucional</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-uppercase text-muted">Código <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="codigo"
                                class="form-control fw-bold @error('codigo') is-invalid @enderror"
                                value="{{ old('codigo', $objetivoEstr->codigo) }}" required>
                            @error('codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Nombre del Objetivo <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                value="{{ old('nombre', $objetivoEstr->nombre) }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Tipo de Objetivo</label>
                            <select name="tipo_objetivo" class="form-select">
                                <option value="Estrategico"
                                    {{ old('tipo_objetivo', $objetivoEstr->tipo_objetivo) == 'Estrategico' ? 'selected' : '' }}>
                                    Estratégico (Largo Plazo)</option>
                                <option value="Tactico"
                                    {{ old('tipo_objetivo', $objetivoEstr->tipo_objetivo) == 'Tactico' ? 'selected' : '' }}>
                                    Táctico (Mediano Plazo)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-uppercase text-muted">Descripción /
                                Justificación</label>
                            <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="2">{{ old('descripcion', $objetivoEstr->descripcion) }}</textarea>
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
                                    {{ old('unidad_responsable_id', $objetivoEstr->unidad_responsable_id) == 1 ? 'selected' : '' }}>
                                    Planificación</option>
                                <option value="2"
                                    {{ old('unidad_responsable_id', $objetivoEstr->unidad_responsable_id) == 2 ? 'selected' : '' }}>
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
                    <h6 class="fw-bold text-dark mb-4 border-bottom pb-2"> <i class="fas fa-clock me-2"></i>3. Medición y
                        Tiempos</h6>
                    <div class="row g-3 align-items-start">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Indicador de
                                Resultado</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-chart-line"></i></span>
                                <input type="text" name="indicador"
                                    class="form-control @error('indicador') is-invalid @enderror"
                                    value="{{ old('indicador', $objetivoEstr->indicador) }}">
                            </div>
                            @error('indicador')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-uppercase text-muted">Línea Base</label>
                            <input type="number" step="0.01" name="linea_base" class="form-control"
                                value="{{ old('linea_base', $objetivoEstr->linea_base) }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-uppercase text-muted">Meta Final</label>
                            <input type="number" step="0.01" name="meta" class="form-control border-success"
                                value="{{ old('meta', $objetivoEstr->meta) }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-uppercase text-muted">Fecha Inicio <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="fecha_inicio"
                                class="form-control @error('fecha_inicio') is-invalid @enderror"
                                value="{{ old('fecha_inicio', $objetivoEstr->fecha_inicio) }}" required>
                            @error('fecha_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-uppercase text-muted">Fecha Fin <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="fecha_fin"
                                class="form-control @error('fecha_fin') is-invalid @enderror"
                                value="{{ old('fecha_fin', $objetivoEstr->fecha_fin) }}" required>
                            @error('fecha_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                {{-- Documento habilitante --}}
                <div class="col-12 mb-4">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <label class="form-label fw-bold">
                                <i class="fas fa-file-pdf me-1 text-danger"></i> Documento de Respaldo / Resolución
                            </label>
                            <div class="row align-items-center">

                                @if ($objetivoEstr->url_documento)
                                    <div class="col-md-6 mb-2 mb-md-0">
                                        <div
                                            class="p-2 bg-white border rounded d-flex justify-content-between align-items-center shadow-sm h-100">
                                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                <i class="fas fa-file-pdf text-danger fa-lg"></i>
                                                <span class="text-muted small">Actual:</span>
                                                <div class="fw-bold text-dark text-truncate" style="max-width: 150px;"
                                                    title="{{ $objetivoEstr->nombre_archivo }}">
                                                    {{ $objetivoEstr->nombre_archivo ?? 'Documento Adjunto' }}
                                                </div>
                                            </div>
                                            <a href="{{ Storage::url($objetivoEstr->url_documento) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary ms-2">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                                <div class="{{ $objetivoEstr->url_documento ? 'col-md-6' : 'col-12' }}">
                                    <input type="file" name="documento_respaldo" class="form-control"
                                        accept=".pdf,.doc,.docx">
                                </div>

                            </div>

                            @error('documento_respaldo')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Alineacion Estrategica --}}
                <div class="bg-white p-4 shadow-sm mb-4 border-info">
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-link me-1"></i> 5. Alineación PND</h6>

                    <div class="col-12 mb-5">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-th-large me-2"></i>Alineación PND</h5>
                                <span class="badge bg-light text-dark">Selección Múltiple</span>
                            </div>

                            <div class="card-body p-0">
                                <div class="row g-0">

                                    {{-- MENÚ LATERAL --}}
                                    <div class="col-md-3 bg-light border-end p-4">
                                        <div class="sticky-top" style="top: 20px; z-index: 1;">
                                            <label class="form-label fw-bold text-primary mb-2">Seleccione el
                                                Objetivo:</label>
                                            <select id="filtro_objetivo_nacional"
                                                class="form-select form-select-lg shadow-sm mb-3">
                                                <option value="">-- Seleccione Objetivo --</option>
                                                @foreach ($alineacionPND as $objNacional)
                                                    <option value="grupo_metas_{{ $objNacional->id_objetivo_nacional }}">
                                                        {{ $objNacional->codigo_objetivo }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Seleccione un objetivo para ver y editar sus metas
                                                asociadas.</small>
                                        </div>
                                    </div>

                                    {{-- Metas --}}
                                    <div class="col-md-9 p-4 bg-white" style="min-height: 400px;">
                                        <div id="mensaje_inicial" class="text-center text-muted py-5 opacity-50">
                                            <i class="fas fa-arrow-left fa-3x mb-3"></i>
                                            <h4>Seleccione un Eje a la izquierda</h4>
                                        </div>

                                        @foreach ($alineacionPND as $objNacional)
                                            <div id="grupo_metas_{{ $objNacional->id_objetivo_nacional }}"
                                                class="contenedor-metas d-none animate__animated animate__fadeIn">

                                                <div class="border-bottom pb-2 mb-4">
                                                    <h4 class="text-dark fw-bold mb-1">{{ $objNacional->codigo_objetivo }}
                                                    </h4>
                                                    <p class="text-muted">{{ $objNacional->descripcion_objetivo }}</p>
                                                </div>

                                                @if ($objNacional?->metasNacionales?->count() > 0)
                                                    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
                                                        @foreach ($objNacional->metasNacionales as $meta)
                                                            <div class="col">
                                                                <label
                                                                    class="card h-100 border cursor-pointer hover-shadow position-relative label-card-check">
                                                                    <div class="card-body">
                                                                        <div class="d-flex align-items-start gap-2">
                                                                            <div class="mt-1">
                                                                                {{-- LOGICA CHECKED: Verifica si el ID está en el array que enviamos desde el controlador --}}
                                                                                <input
                                                                                    class="form-check-input check-meta scale-125"
                                                                                    type="checkbox" name="metas_id[]"
                                                                                    value="{{ $meta->id_meta_nacional }}"
                                                                                    @if (in_array($meta->id_meta_nacional, $metasSeleccionadas)) checked @endif>
                                                                            </div>
                                                                            <div>
                                                                                <span
                                                                                    class="badge bg-primary mb-2">{{ $meta->codigo_meta }}</span>
                                                                                <p
                                                                                    class="card-text small text-dark fw-bold mb-1 lh-sm">
                                                                                    {{ $meta->nombre_meta }}</p>
                                                                                <p
                                                                                    class="card-text extra-small text-muted mb-0">
                                                                                    {{ Str::limit($meta->descripcion_meta, 60) }}
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="alert alert-warning">No hay metas registradas.</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="card-footer bg-white py-4 border-top text-end">
                    <a href="{{ route('estrategico.objetivos.index') }}" class="btn btn-light border px-4">Cancelar</a>
                    <button type="submit" class="btn btn-secondary fw-bold px-4 py-2 shadow-sm btn-sm">
                        <i class="fas fa-save me-1"></i> Actualizar Objetivo
                    </button>
                </div>

            </div>
        </form>
    </div>
    <style>
        .cursor-pointer {
            cursor: pointer;
        }

        .scale-125 {
            transform: scale(1.25);
        }

        .extra-small {
            font-size: 0.8rem;
        }

        .hover-shadow:hover {
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
            border-color: #0d6efd !important;
        }

        .label-card-check:has(input:checked) {
            border-color: #0d6efd !important;
            background-color: #f0f8ff;
        }
    </style>
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
        document.addEventListener('DOMContentLoaded', function() {

            const filtroSelect = document.getElementById('filtro_objetivo_nacional');
            const contenedores = document.querySelectorAll('.contenedor-metas');
            const mensajeInicial = document.getElementById('mensaje_inicial');
            const checks = document.querySelectorAll('.check-meta');
            const resumenDiv = document.getElementById('resumen_seleccion');
            const sinSeleccionMsg = document.getElementById('sin_seleccion');

            //LÓGICA DEL SELECT (MOSTRAR / OCULTAR)
            filtroSelect.addEventListener('change', function() {
                // Ocultar todo
                contenedores.forEach(div => div.classList.add('d-none'));
                mensajeInicial.classList.add('d-none');

                const idGrupo = this.value;
                if (idGrupo) {
                    // Mostrar el seleccionado
                    const grupoSeleccionado = document.getElementById(idGrupo);
                    if (grupoSeleccionado) {
                        grupoSeleccionado.classList.remove('d-none');
                    }
                } else {
                    mensajeInicial.classList.remove('d-none');
                }
            });

            // 2. LÓGICA DEL RESUMEN (VISUALIZACIÓN)
            function actualizarResumen() {
                resumenDiv.innerHTML = ''; // Limpiar
                let haySeleccion = false;

                checks.forEach(chk => {
                    if (chk.checked) {
                        haySeleccion = true;
                        // Creamos una etiqueta (Badge) para el resumen
                        const codigo = chk.getAttribute('data-codigo');
                        const badge = document.createElement('span');
                        badge.className =
                            'badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-2 px-3';
                        badge.innerHTML = `<i class="fas fa-check me-1"></i> ${codigo}`;
                        resumenDiv.appendChild(badge);
                    }
                });

                if (!haySeleccion) {
                    resumenDiv.appendChild(sinSeleccionMsg);
                }
            }

            // Escuchar cambios en cualquier checkbox
            checks.forEach(chk => {
                chk.addEventListener('change', actualizarResumen);
            });

            // Ejecutar al inicio (útil para EDICIÓN)
            actualizarResumen();
        });
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('filtro_objetivo_nacional');
            const mensajeInicial = document.getElementById('mensaje_inicial');
            const contenedores = document.querySelectorAll('.contenedor-metas');

            select.addEventListener('change', function() {
                // 1. Ocultar todo
                mensajeInicial.classList.add('d-none');
                contenedores.forEach(div => div.classList.add('d-none'));

                // 2. Mostrar el seleccionado
                const idSeleccionado = this.value;
                if (idSeleccionado) {
                    const divMostrar = document.getElementById(idSeleccionado);
                    if (divMostrar) {
                        divMostrar.classList.remove('d-none');
                    }
                } else {
                    mensajeInicial.classList.remove('d-none');
                }
            });
            const primerCheckMarcado = document.querySelector('.check-meta:checked');
            if (primerCheckMarcado) {
                // Buscamos el contenedor padre
                const contenedorPadre = primerCheckMarcado.closest('.contenedor-metas');
                if (contenedorPadre) {
                    // Simulamos la selección en el dropdown
                    const idPadre = contenedorPadre.id; // ej: grupo_metas_1
                    select.value = idPadre;
                    select.dispatchEvent(new Event('change')); // Disparamos el evento para que se muestre
                }
            }
        });
    </script>
@endpush
