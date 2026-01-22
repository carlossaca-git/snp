@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Modificacion de Proyecto"
        subtitulo="{{ Auth::user()->organizacion->nom_organizacion }}">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('inversion.proyectos.index') }}"
                class="btn btn-outline-secondary border-2 fw-bold d-inline-flex align-items-center">
                <i class="fas fa-arrow-left me-2"></i> Volver al Banco
            </a>
        </div>
    </x-layouts.header_content>
    <div class="container-fluid py-4">
        @include('partials.mensajes')
        <form action="{{ route('inversion.proyectos.update', $proyecto->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white p-0 border-bottom">
                    <ul class="nav nav-tabs border-bottom-0" id="proyectoTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active text-secondary fw-bold px-4 py-3" id="general-tab"
                                data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                <span data-feather="file-text" class="me-1"></span> 1. Datos y CUP
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-secondary fw-bold px-4 py-3" id="alineacion-tab"
                                data-bs-toggle="tab" data-bs-target="#alineacion" type="button" role="tab">
                                <span data-feather="target" class="me-1"></span> 2. Alineación Estratégica
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-secondary fw-bold px-4 py-3" id="tecnico-tab" data-bs-toggle="tab"
                                data-bs-target="#tecnico" type="button" role="tab">
                                <span data-feather="map-pin" class="me-1"></span> 3. Diagnóstico y Ubicación
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-secondary fw-bold px-4 py-3" id="docs-tab" data-bs-toggle="tab"
                                data-bs-target="#docs" type="button" role="tab">
                                <span class="me-1"></span> 4. Documentación
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="proyectoTabContent">

                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="row g-3">

                                <div class="col-md-3">
                                    <label class="form-label fw-bold">CUP</label>
                                    <input type="text" name="cup" class="form-control form-control-lg border-2"
                                        placeholder="Código único" required value="{{ old('cup', $proyecto->cup) }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Estado</label>
                                    <div class="d-flex align-items-center form-control form-control-lg border-2">
                                        <div class="form-check form-switch form-check-custom form-check-solid mb-0">
                                            <input type="hidden" name="estado" value="0">
                                            <input class="form-check-input" type="checkbox" name="estado" value="1"
                                                id="estadoSwitch" {{ $proyecto->estado == 1 ? 'checked' : '' }}>
                                            <label
                                                class="form-check-label fw-bold {{ $proyecto->estado == 1 ? 'text-success' : 'text-muted' }} ms-2"
                                                for="estadoSwitch">
                                                {{ $proyecto->estado == 1 ? 'Activo' : 'Inactivo' }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nombre del Proyecto</label>
                                    <input type="text" name="nombre_proyecto"
                                        class="form-control form-control-lg border-2" required
                                        value="{{ old('nombre_proyecto', $proyecto->nombre_proyecto) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Programa Presupuestario</label>
                                    <select name="id_programa" class="form-select border-2" required>
                                        <option value="" disabled selected>-- Seleccione Programa --</option>
                                        @foreach ($programas as $programa)
                                            <option value="{{ $programa->id }}"
                                                {{ $proyecto->id_programa == $programa->id ? 'selected' : '' }}>
                                                {{ $programa->nombre_programa }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tipo de Inversión</label>
                                    <select name="tipo_inversion" class="form-select border-2" required>
                                        <option value="OBRA PUBLICA"
                                            {{ $proyecto->tipo_inversion == 'OBRA PUBLICA' ? 'selected' : '' }}>Obra
                                            Pública</option>
                                        <option value="ADQUISICION"
                                            {{ $proyecto->tipo_inversion == 'ADQUISICION' ? 'selected' : '' }}>Adquisición
                                        </option>
                                        <option value="OTRO"
                                            {{ $proyecto->tipo_inversion == 'OTRO' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Monto Total Inversión</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-2">$</span>
                                        <input type="number" step="0.01" name="monto_total_inversion"
                                            id="monto_total_inversion" class="form-control border-2" placeholder="0.00"
                                            required
                                            value="{{ old('monto_total_inversion', $proyecto->monto_total_inversion) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Estado Dictamen</label>
                                    <select name="estado_dictamen" class="form-select border-2" required>
                                        <option value="PENDIENTE"
                                            {{ $proyecto->estado_dictamen == 'PENDIENTE' ? 'selected' : '' }}>Pendiente
                                        </option>
                                        <option value="FAVORABLE"
                                            {{ $proyecto->estado_dictamen == 'FAVORABLE' ? 'selected' : '' }}>Favorable
                                        </option>
                                        <option value="NEGATIVO"
                                            {{ $proyecto->estado_dictamen == 'NEGATIVO' ? 'selected' : '' }}>Negativo
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Fecha Inicio</label>
                                    <input type="date" name="fecha_inicio_estimada" id="fecha_inicio"
                                        class="form-control border-2" required
                                        value="{{ old('fecha_inicio_estimada', optional($proyecto->fecha_inicio_estimada)->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Fecha Fin</label>
                                    <input type="date" name="fecha_fin_estimada" id="fecha_fin"
                                        class="form-control border-2" required
                                        value="{{ old('fecha_fin_estimada', optional($proyecto->fecha_fin_estimada)->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Duración (Meses)</label>
                                    <input type="number" name="duracion_meses" id="duracion"
                                        class="form-control border-2 bg-light" readonly
                                        value="{{ old('duracion_meses', $proyecto->duracion_meses) }}">
                                </div>
                            </div>

                            <hr class="my-4" style="opacity: 0.15; border-top: 2px solid #6c757d;">

                            <div class="card mt-4 shadow-sm border-0">
                                <div
                                    class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Programación Financiera Plurianual</h5>
                                    <button type="button" class="btn btn-light btn-sm" onclick="agregarFila()">+ Añadir
                                        Año</button>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm" id="tablaFinanciamiento">
                                        <thead class="table-light text-muted">
                                            <tr>
                                                <th>Año</th>
                                                <th>Fuente de Financiamiento</th>
                                                <th>Monto Programado</th>
                                                <th width="50"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="cuerpoFinanciamiento">
                                            @foreach ($proyecto->financiamientos as $index => $fin)
                                                <tr>
                                                    <input type="hidden" name="financiamientos[{{ $index }}][id]"
                                                        value="{{ $fin->id }}">
                                                    <td>
                                                        <input type="number"
                                                            name="financiamientos[{{ $index }}][anio]"
                                                            class="form-control form-control-sm"
                                                            value="{{ $fin->anio }}" required>
                                                    </td>
                                                    <td>
                                                        <select name="financiamientos[{{ $index }}][id_fuente]"
                                                            class="form-select form-select-sm">
                                                            @foreach ($fuentes as $fuente)
                                                                <option value="{{ $fuente->id_fuente }}"
                                                                    {{ $fin->id_fuente == $fuente->id_fuente ? 'selected' : '' }}>
                                                                    {{ $fuente->nombre_fuente }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01"
                                                            name="financiamientos[{{ $index }}][monto]"
                                                            class="form-control form-control-sm monto-input"
                                                            value="{{ $fin->monto }}" required>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox"
                                                            name="financiamientos[{{ $index }}][_delete]"
                                                            value="1" class="form-check-input d-none delete-chk">
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm p-0 d-inline-flex align-items-center justify-content-center"
                                                            style="width: 30px; height: 30px;"
                                                            onclick="borrarFilaInteligente(this)" title="Eliminar">
                                                            <span
                                                                style="font-size: 1.5rem; line-height: 1; margin-top: -2px;">&times;</span>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="card-footer bg-light">
                                        <div class="row text-end">
                                            <div class="col-md-8">
                                                <span class="fw-bold">Total Programado:</span>
                                            </div>
                                            <div class="col-md-4">
                                                <span id="suma_total_format" class="fw-bold text-primary">$ 0.00</span>
                                            </div>
                                        </div>
                                        <div id="alerta_monto" class="alert alert-warning mt-2 d-none">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            La suma de los años no coincide con el **Monto Total de Inversión**.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="alineacion" role="tabpanel">
                            <div class="row justify-content-center">
                                <div class="col-md-10">
                                    <div class="alert alert-info border-0 shadow-sm mb-4">
                                        <div class="d-flex">
                                            <i class="fas fa-sitemap fa-2x me-3 opacity-50"></i>
                                            <div>
                                                <p class="mb-0 small">Al seleccionar su <strong>Objetivo Estratégico
                                                        Institucional</strong>, el sistema vinculará automáticamente este
                                                    proyecto a la Meta Nacional del PND correspondiente.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Objetivo Estratégico (PEI)</label>

                                        <select name="id_objetivo_estrategico" id="select_objetivo"
                                            class="form-select form-select-lg shadow-sm" required>
                                            <option value="">-- Seleccione Objetivo --</option>

                                            @foreach ($objetivos as $obj)
                                                <option value="{{ $obj->id_objetivo_estrategico }}"
                                                    data-metas='@json($obj->info_alineacion)'
                                                    {{ $proyecto->id_objetivo_estrategico == $obj->id_objetivo_estrategico ? 'selected' : '' }}>
                                                    {{ $obj->codigo }} - {{ Str::limit($obj->nombre, 100) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- Tarjeta de Feedback Automático --}}
                                    <div id="info_alineacion" class="card bg-light border-0 shadow-sm d-none">
                                        <div class="card-body">
                                            <h6 class="text-secondary fw-bold text-uppercase small mb-3">
                                                <i class="fas fa-network-wired me-1"></i> Impacto Nacional Identificado
                                            </h6>
                                            <div id="lista_metas_container"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tecnico" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Diagnóstico y Justificación</label>
                                    <textarea name="descripcion_diagnostico" class="form-control border-2" rows="4" required>{{ old('descripcion_diagnostico', $proyecto->descripcion_diagnostico) }}</textarea>
                                </div>
                                <div class="col-12 mt-4">
                                    <h6 class="text-uppercase fw-bold text-secondary">Localización Geográfica
                                    </h6>
                                    <hr>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Provincia</label>
                                    <select name="provincia" id="select_provincia" class="form-select border-2"
                                        data-old="{{ old('provincia', $proyecto->localizacion?->provincia) }}">
                                        <option value="">Cargando...</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Cantón</label>
                                    <select name="canton" id="select_canton" class="form-select border-2"
                                        data-old="{{ old('canton', $proyecto->localizacion?->canton) }}">
                                        <option value="">-- Seleccione Provincia --</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Parroquia</label>
                                    <select name="parroquia" id="select_parroquia" class="form-select border-2"
                                        data-old="{{ old('parroquia', $proyecto->localizacion?->parroquia) }}">
                                        <option value="">-- Seleccione Cantón --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="docs" role="tabpanel">
                            <div class="row g-3">
                                <div class="section-title">Gestión de Documentos</div>
                                <div class="mt-3">
                                    <label>Subir nuevos documentos (opcional)</label>
                                    <input type="file" name="nuevos_documentos[]" id="input-nuevos-docs" multiple
                                        class="form-control">
                                </div>
                                <table class="table" id="tabla-documentos">
                                    <thead>
                                        <tr>
                                            <th>Archivo</th>
                                            <th>Tipo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="lista-documentos-cuerpo">
                                        @foreach ($proyecto->documentos as $doc)
                                            <tr id="doc-{{ $doc->id }}">
                                                <td>
                                                    <a href="{{ asset('storage/' . $doc->url_archivo) }}" target="_blank"
                                                        title="{{ $doc->nombre_archivo }}">
                                                        <span class="col-nombre-archivo">{{ $doc->nombre_archivo }}</span>
                                                    </a>
                                                </td>
                                                <td>{{ $doc->tipo_documento }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="confirmarEliminacion({{ $doc->id }})">
                                                        <i class="fas fa-trash" data-feather="trash-2"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light p-3 d-flex justify-content-end">
                    <button type="reset" class="btn btn-link text-muted me-3">Restaurar Valores</button>
                    <button type="submit" id="btnGuardarProyecto"
                        class="btn btn-secondary fw-bold px-4 shadow-sm btn-sm">
                        <span data-feather="save" class="me-1"></span> Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
    <style>
        .table-warning a {
            color: #1a4a72;
            text-decoration: none;
            font-weight: bold;
        }

        .table-warning a:hover {
            text-decoration: underline;
            color: #0d2a44;
        }


        #lista-documentos-cuerpo tr:hover {
            background-color: #f1f4f9 !important;

            cursor: pointer;
        }


        #lista-documentos-cuerpo tr.table-warning:hover {
            background-color: #ffeeba !important;

            cursor: pointer;
        }


        #lista-documentos-cuerpo tr {
            transition: all 0.2s ease-in-out;
        }


        #lista-documentos-cuerpo tr:hover td a {
            color: #1a4a72 !important;
            font-weight: bold;
        }

        /* Definimos un ancho máximo para la columna del nombre */
        .col-nombre-archivo {
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            vertical-align: middle;
        }

        /*  Que al pasar el mouse se vea el nombre completo en un tooltip */
        .col-nombre-archivo:hover {
            overflow: visible;
            white-space: normal;
            background: #fff;
            position: absolute;
            z-index: 10;
            border: 1px solid #ccc;
            padding: 2px 5px;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
    </style>
@endsection
@push('scripts')
    <script>
        /**
         * ==========================================
         *  GESTIÓN FINANCIERA (TABLA DINÁMICA)
         * ==========================================
         */
        function agregarFila() {
            const tbody = document.getElementById('cuerpoFinanciamiento');
            const index = Date.now();
            const fila = `
            <tr>
                <td>
                    <input type="number" name="financiamientos[new_${index}][anio]" class="form-control form-control-sm" required placeholder="202X">
                </td>
                <td>
                    <select name="financiamientos[new_${index}][id_fuente]" class="form-select form-select-sm">
                        @foreach ($fuentes as $fuente)
                            <option value="{{ $fuente->id_fuente }}">{{ $fuente->nombre_fuente }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" name="financiamientos[new_${index}][monto]" class="form-control form-control-sm monto-input" required placeholder="0.00">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm p-0 d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px;" onclick="borrarFilaInteligente(this)">
                        <span style="font-size: 1.5rem; line-height: 1; margin-top: -2px;">&times;</span>
                    </button>
                </td>
            </tr>`;
            tbody.insertAdjacentHTML('beforeend', fila);
        }

        function borrarFilaInteligente(boton) {
            const fila = boton.closest('tr');
            const checkbox = fila.querySelector('.delete-chk');

            if (checkbox) {
                checkbox.checked = true;
                fila.style.display = 'none';
                const inputMonto = fila.querySelector('.monto-input');
                if (inputMonto) inputMonto.value = 0;
            } else {
                fila.remove();
            }
            calcularTotal();
        }

        function calcularTotal() {
            let suma = 0;
            const inputs = document.querySelectorAll('.monto-input');

            inputs.forEach(input => {
                const fila = input.closest('tr');
                // Solo sumar si la fila es visible
                if (fila && fila.style.display !== 'none') {
                    suma += parseFloat(input.value) || 0;
                }
            });

            const display = document.getElementById('suma_total_format');
            const inputGlobal = document.getElementById('monto_total_inversion');
            const global = parseFloat(inputGlobal ? inputGlobal.value : 0) || 0;
            const diff = Math.abs(suma - global);
            const cuadra = (global > 0 && diff <= 0.01);
            const alerta = document.getElementById('alerta_monto');

            if (display) {
                display.innerText = '$ ' + suma.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                });

                if (suma > 0 || global > 0) {
                    display.style.setProperty('color', cuadra ? '#198754' : '#dc3545', 'important');
                    if (alerta) cuadra ? alerta.classList.add('d-none') : alerta.classList.remove('d-none');
                }
            }
            validarBotonGuardar(cuadra);
        }

        function validarBotonGuardar(presupuestoCuadra) {
            const btn = document.getElementById('btnGuardarProyecto');
            if (!btn) return;

            if (presupuestoCuadra) {
                btn.classList.replace('btn-secondary', 'btn-dark');
                btn.style.opacity = "1";
            } else {
                btn.classList.replace('btn-dark', 'btn-secondary');
                btn.style.opacity = "0.9";
            }
        }

        /**
         *
         *  GESTIÓN DE ARCHIVOS (PREVISUALIZACIÓN Y BORRADO)
         *
         */
        let listaArchivosACargar = new DataTransfer();

        //  Visualizar archivos seleccionados antes de subir
        const inputDocs = document.getElementById('input-nuevos-docs');
        if (inputDocs) {
            inputDocs.addEventListener('change', function(e) {
                const cuerpoTabla = document.getElementById('lista-documentos-cuerpo');
                const nuevosArchivos = Array.from(e.target.files);

                nuevosArchivos.forEach((archivo) => {
                    listaArchivosACargar.items.add(archivo); // Añadir al global

                    const tempId = 'file-' + Math.random().toString(36).substr(2, 9);
                    const urlTemporal = URL.createObjectURL(archivo);

                    // Renderizar fila temporal
                    const filaHTML = `
                    <tr id="${tempId}" class="table-warning">
                        <td>
                            <a href="${urlTemporal}" target="_blank" class="text-decoration-none text-dark" title="${archivo.name}">
                                <i class="fas fa-file-upload text-primary me-2"></i>
                                <strong>${archivo.name}</strong> <small class="text-muted">(${(archivo.size / 1024).toFixed(1)} KB)</small>
                            </a>
                        </td>
                        <td><span class="badge bg-primary">Por subir</span></td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removerArchivoSeleccionado('${tempId}', '${archivo.name}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                    cuerpoTabla.insertAdjacentHTML('beforeend', filaHTML);
                });
                // Sincronizar input real
                inputDocs.files = listaArchivosACargar.files;
            });
        }

        // Remover archivo de la lista de subida
        function removerArchivoSeleccionado(filaId, nombreArchivo) {
            const contenedorNuevo = new DataTransfer();
            let borrado = false;

            Array.from(listaArchivosACargar.files).forEach((file) => {
                if (file.name === nombreArchivo && !borrado) {
                    borrado = true;
                } else {
                    contenedorNuevo.items.add(file);
                }
            });

            listaArchivosACargar = contenedorNuevo;
            if (inputDocs) inputDocs.files = listaArchivosACargar.files;

            const fila = document.getElementById(filaId);
            if (fila) fila.remove();
        }

        //  Borrar archivo guardado
        function confirmarEliminacion(id) {
            if (!confirm('¿Estás seguro de que deseas eliminar este documento permanentemente?')) return;

            let url = "{{ route('inversion.proyectos.documentos.destroy', ':id') }}".replace(':id', id);

            fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.ok ? res.json() : Promise.reject(res))
                .then(data => {
                    if (data.success) {
                        document.getElementById(`fila-doc-${id}`).remove();
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error al eliminar el documento.');
                });
        }
        //EVENTOS GLOBALES
        document.addEventListener('DOMContentLoaded', function() {

            // --- EVENTOS FINANCIEROS ---
            const tablaFin = document.getElementById('tablaFinanciamiento');
            const inputGlobal = document.getElementById('monto_total_inversion');
            const form = document.querySelector('form');

            if (tablaFin) tablaFin.addEventListener('input', e => {
                if (e.target.classList.contains('monto-input')) calcularTotal();
            });
            if (inputGlobal) inputGlobal.addEventListener('input', calcularTotal);
            if (form) form.addEventListener('input', calcularTotal);

            // Ejecutar cálculo inicial
            calcularTotal();

            // --- CÁLCULO DE FECHAS ---
            const fInicio = document.getElementById('fecha_inicio');
            const fFin = document.getElementById('fecha_fin');
            const duracion = document.getElementById('duracion');

            function calcFechas() {
                if (fInicio && fFin && fInicio.value && fFin.value) {
                    const d1 = new Date(fInicio.value);
                    const d2 = new Date(fFin.value);
                    let m = (d2.getFullYear() - d1.getFullYear()) * 12;
                    m -= d1.getMonth();
                    m += d2.getMonth();
                    if (duracion) duracion.value = m <= 0 ? 0 : m;
                }
            }
            if (fInicio) fInicio.addEventListener('change', calcFechas);
            if (fFin) fFin.addEventListener('change', calcFechas);

            // --- UBICACIÓN GEOGRÁFICA  ---
            const selProvincia = document.getElementById('select_provincia');
            const selCanton = document.getElementById('select_canton');
            const selParroquia = document.getElementById('select_parroquia');
            let datosEcuador = {};

            if (selProvincia) {
                fetch("{{ asset('json/ecuador.json') }}")
                    .then(r => r.json())
                    .then(data => {
                        datosEcuador = data;
                        cargarProvincias();
                    })
                    .catch(e => console.error("Error JSON:", e));

                // Funciones auxiliares de ubicación
                const limpiar = str => str ? str.toString().trim().toUpperCase() : "";

                function cargarProvincias() {
                    selProvincia.innerHTML = '<option value="">-- Seleccione --</option>';
                    const dbProv = selProvincia.getAttribute('data-old');
                    let idEncontrado = null;

                    for (let id in datosEcuador) {
                        let nombre = datosEcuador[id].provincia || "ZONA NO DELIMITADA";
                        let opt = new Option(nombre, nombre);
                        opt.setAttribute('data-id', id);

                        if (limpiar(nombre) === limpiar(dbProv)) {
                            opt.selected = true;
                            idEncontrado = id;
                        }
                        selProvincia.add(opt);
                    }
                    if (idEncontrado) cargarCantones(idEncontrado);
                }

                function cargarCantones(idProv) {
                    selCanton.innerHTML = '<option value="">-- Seleccione --</option>';
                    selParroquia.innerHTML = '<option value="">-- Seleccione Cantón --</option>';

                    if (!datosEcuador[idProv]?.cantones) return;
                    selCanton.disabled = false;

                    const cantones = datosEcuador[idProv].cantones;
                    const dbCanton = selCanton.getAttribute('data-old');
                    let idEncontrado = null;

                    for (let id in cantones) {
                        let nombre = cantones[id].canton;
                        if (!nombre) continue;
                        let opt = new Option(nombre, nombre);
                        opt.setAttribute('data-id', id);

                        if (limpiar(nombre) === limpiar(dbCanton)) {
                            opt.selected = true;
                            idEncontrado = id;
                        }
                        selCanton.add(opt);
                    }
                    if (idEncontrado) cargarParroquias(idProv, idEncontrado);
                }

                function cargarParroquias(idProv, idCant) {
                    selParroquia.innerHTML = '<option value="">-- Seleccione --</option>';
                    const parroquias = datosEcuador[idProv]?.cantones[idCant]?.parroquias;
                    if (!parroquias) return;

                    selParroquia.disabled = false;
                    const dbParr = selParroquia.getAttribute('data-old');

                    for (let id in parroquias) {
                        let nombre = parroquias[id];
                        let opt = new Option(nombre, nombre);
                        if (limpiar(nombre) === limpiar(dbParr)) opt.selected = true;
                        selParroquia.add(opt);
                    }
                }

                // Listeners de Ubicación
                selProvincia.addEventListener('change', function() {
                    const id = this.options[this.selectedIndex].getAttribute('data-id');
                    selCanton.setAttribute('data-old', '');
                    cargarCantones(id);
                });

                selCanton.addEventListener('change', function() {
                    const idP = selProvincia.options[selProvincia.selectedIndex].getAttribute('data-id');
                    const idC = this.options[this.selectedIndex].getAttribute('data-id');
                    selParroquia.setAttribute('data-old', '');
                    cargarParroquias(idP, idC);
                });
            }
        });
        // --- ALINEACIÓN OBJETIVOS - METAS ---
        document.addEventListener('DOMContentLoaded', function() {

            const selectObj = document.getElementById('select_objetivo');
            const containerMeta = document.getElementById('info_alineacion');
            const listaDivMeta = document.getElementById('lista_metas_container');

            function mostrarAlineacion() {
                listaDivMeta.innerHTML = '';

                if (!selectObj.value) {
                    containerMeta.classList.add('d-none');
                    return;
                }

                const option = selectObj.options[selectObj.selectedIndex];
                const rawData = option.getAttribute('data-metas');

                if (!rawData) return;

                const metas = JSON.parse(rawData);

                if (metas.length > 0) {
                    metas.forEach(meta => {

                        // Construir Badges de ODS
                        let htmlODS = '';
                        if (meta.ods && meta.ods.length > 0) {
                            htmlODS = '<div class="mt-2 d-flex flex-wrap gap-1">';

                            meta.ods.forEach(ods => {
                                const bg = ods.color || '#6c757d';

                                htmlODS += `
                                <span class="badge border border-white shadow-sm"
                                      style="background-color: ${bg}; color: white;"
                                      title="${ods.nombre}">
                                    ODS ${ods.numero}
                                </span>
                            `;
                            });
                            htmlODS += '</div>';
                        }

                        // Construir HTML de la Meta
                        const html = `
                        <div class="border-bottom pb-2 mb-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                                META ${meta.codigo}
                            </span>
                            <div class="small fw-bold mt-1 text-dark">${meta.nombre}</div>
                            ${htmlODS}
                        </div>`;

                        listaDivMeta.insertAdjacentHTML('beforeend', html);
                    });
                    containerMeta.classList.remove('d-none');
                } else {
                    listaDivMeta.innerHTML = '<span class="text-muted small">Sin alineación configurada</span>';
                    containerMeta.classList.remove('d-none');
                }
            }

            selectObj.addEventListener('change', mostrarAlineacion);
            mostrarAlineacion();
        });
    </script>
@endpush
