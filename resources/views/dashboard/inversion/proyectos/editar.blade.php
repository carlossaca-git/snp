@extends('layouts.app')

@section('content')
    <script src="https://unpkg.com/feather-icons"></script>
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-dark fw-bold">Editar Inversión: Formulación de Proyecto</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Gestión</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('inversion.proyectos.index') }}">Proyectos</a></li>
                        <li class="breadcrumb-item active">Editar Proyecto</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('inversion.proyectos.index') }}" class="btn btn-outline-secondary border-2 fw-bold">
                <span data-feather="arrow-left"></span> Volver al Banco
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm border-2">
                <strong><i class="fas fa-exclamation-circle"></i> Error de Validación:</strong>
                <ul class="mt-2 mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('inversion.proyectos.update', $proyecto->id) }}" method="POST"
            enctype="multipart/form-data">
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
                                <span data-feather="folder" class="me-1"></span> 4. Documentación
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
                                    <label class="form-label fw-bold">Entidad Responsable</label>
                                    <select name="id_organizacion" class="form-select border-2" required>
                                        <option value="" disabled selected>-- Seleccione Ministerio/GAD --</option>
                                        @foreach ($organizaciones as $entidad)
                                            <option value="{{ $entidad->id_organizacion }}"
                                                {{ $proyecto->id_organizacion == $entidad->id_organizacion ? 'selected' : '' }}>
                                                {{ $entidad->nom_organizacion }}
                                            </option>
                                        @endforeach
                                    </select>
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

                                <div class="col-md-4">
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
                            <div class="alert alert-secondary d-flex align-items-center mb-4 border-0">
                                <span data-feather="info" class="me-2"></span>
                                <div>Todo proyecto debe responder a un objetivo del Plan Nacional de Desarrollo (PND).</div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Eje Estratégico</label>
                                    <select name="id_eje" id="select_eje" class="form-select border-2" required>
                                        <option value="" selected disabled>-- Seleccione un Eje --</option>
                                        @foreach ($ejes as $eje)
                                            <option value="{{ $eje->id_eje }}"
                                                {{ $proyecto->objetivo?->id_eje == $eje->id_eje ? 'selected' : '' }}>
                                                {{ $eje->nombre_eje }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Objetivo Nacional (PND)</label>
                                    <select name="id_objetivo_nacional" id="select_objetivo" class="form-select border-2"
                                        required>
                                        @foreach ($objetivos as $obj)
                                            <option value="{{ $obj->id_objetivo_nacional }}"
                                                {{ $proyecto->objetivo_nacional == $obj->id_objetivo_nacional ? 'selected' : '' }}>
                                                {{ $obj->descripcion_objetivo }}
                                            </option>
                                        @endforeach
                                    </select>
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
                    <button type="submit" id="btnGuardarProyecto" class="btn btn-dark px-5 py-2 fw-bold">
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

        /* 1. Para los documentos que ya vienen de la BD */
        #lista-documentos-cuerpo tr:hover {
            background-color: #f1f4f9 !important;
            /* Gris azulado suave */
            cursor: pointer;
        }

        /* 2. Para los documentos TEMPORALES (los amarillos) */
        #lista-documentos-cuerpo tr.table-warning:hover {
            background-color: #ffeeba !important;
            /* Un amarillo más fuerte para que se note el hover */
            cursor: pointer;
        }

        /* 3. Animación suave para todas las filas */
        #lista-documentos-cuerpo tr {
            transition: all 0.2s ease-in-out;
        }

        /* 4. Opcional: Que el texto se ponga azul al pasar el mouse */
        #lista-documentos-cuerpo tr:hover td a {
            color: #1a4a72 !important;
            font-weight: bold;
        }

        /* Definimos un ancho máximo para la columna del nombre */
        .col-nombre-archivo {
            max-width: 250px;
            /* Puedes ajustar este valor según tu pantalla */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            vertical-align: middle;
        }

        /* Opcional: Que al pasar el mouse se vea el nombre completo en un tooltip */
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

    @push('scripts')
        <script>
            // FUNCIÓN AGREGAR FILA (JS)
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
                    // Al ocultar una fila, su monto ya no debe sumar.
                    // Lo ponemos en 0 visualmente o quitamos la clase,

                    const inputMonto = fila.querySelector('.monto-input');
                    if (inputMonto) {
                        inputMonto.value = 0;
                    }
                    calcularTotal(); // Recalcular al borrar
                } else {
                    fila.remove();
                    calcularTotal(); // Recalcular al borrar
                }
            }

            // 2. CÁLCULOS
            function calcularTotal() {
                let suma = 0;
                // Ahora sí encontrará los inputs porque les pusimos la clase 'monto-input'
                // sumamos las filas visibles (style.display != none)
                const inputs = document.querySelectorAll('.monto-input');

                inputs.forEach(input => {
                    const fila = input.closest('tr');
                    if (fila.style.display !== 'none') {
                        suma += parseFloat(input.value) || 0;
                    }
                });

                const display = document.getElementById('suma_total_format');
                display.innerText = '$ ' + suma.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                });

                const global = parseFloat(document.getElementById('monto_total_inversion').value) || 0;
                const diff = Math.abs(suma - global);
                const cuadra = (global > 0 && diff <= 0.01);

                const alerta = document.getElementById('alerta_monto');

                if (suma > 0 || global > 0) {
                    if (cuadra) {
                        display.style.setProperty('color', '#198754', 'important'); // Verde
                        alerta.classList.add('d-none');
                    } else {
                        display.style.setProperty('color', '#dc3545', 'important'); // Rojo
                        alerta.classList.remove('d-none');
                    }
                }

                validarBotonGuardar(cuadra);
            }

            function validarBotonGuardar(presupuestoCuadra) {
                const btn = document.getElementById('btnGuardarProyecto');

                // Solo cambiamos el color visualmente, PERO YA NO LO BLOQUEAMOS
                if (presupuestoCuadra) {
                    btn.classList.remove('btn-secondary');
                    btn.classList.add('btn-dark');
                    btn.style.opacity = "1";
                } else {
                    // Lo dejamos gris para avisar, pero habilitado para que puedas hacer clic
                    btn.classList.remove('btn-dark');
                    btn.classList.add('btn-secondary');
                    // btn.disabled = true;  <-- ESTA LÍNEA LA BORRAMOS O COMENTAMOS
                    btn.style.opacity = "0.9";
                }
            }

            // EVENTOS
            document.addEventListener('DOMContentLoaded', function() {
                // Eventos de cálculo financiero
                const tabla = document.getElementById('tablaFinanciamiento');
                const inputGlobal = document.getElementById('monto_total_inversion');
                const form = document.querySelector('form');

                if (tabla) {
                    tabla.addEventListener('input', function(e) {
                        if (e.target.classList.contains('monto-input')) calcularTotal();
                    });
                }
                if (inputGlobal) {
                    inputGlobal.addEventListener('input', calcularTotal);
                }
                if (form) {
                    form.addEventListener('input', function() {
                        calcularTotal();
                    }); // Revalidar todo al escribir
                }

                // Eventos de Fechas
                const fInicio = document.getElementById('fecha_inicio');
                const fFin = document.getElementById('fecha_fin');
                const duracion = document.getElementById('duracion');

                function calcFechas() {
                    if (fInicio.value && fFin.value) {
                        const d1 = new Date(fInicio.value);
                        const d2 = new Date(fFin.value);
                        let m = (d2.getFullYear() - d1.getFullYear()) * 12;
                        m -= d1.getMonth();
                        m += d2.getMonth();
                        duracion.value = m <= 0 ? 0 : m;
                    }
                }
                if (fInicio) fInicio.addEventListener('change', calcFechas);
                if (fFin) fFin.addEventListener('change', calcFechas);

                //  CALCULAR AL INICIO                //
                calcularTotal();

                ///////////////////////////////
                ////Carga de objetivos frente a ejes
                // E. LÓGICA DE OBJETIVOS (AJAX)
                const selectEje = document.getElementById('select_eje');
                if (selectEje) {
                    selectEje.addEventListener('change', function() {
                        const ejeId = this.value;
                        const target = document.getElementById('select_objetivo');
                        if (!ejeId) return;

                        target.innerHTML = '<option>Cargando...</option>';
                        let url = "{{ route('inversion.proyectos.getObjetivos', ['ejeId' => 'TEMP']) }}"
                            .replace('TEMP', ejeId);

                        fetch(url)
                            .then(r => r.json())
                            .then(data => {
                                target.innerHTML = '<option value="">-- Seleccione --</option>';
                                data.forEach(d => target.add(new Option(d.descripcion_objetivo, d
                                    .id_objetivo_nacional)));
                            })
                            .catch(e => target.innerHTML = '<option>Error</option>');
                    });
                }
            });


            /////////////////////////////////////////////////////
            //Lectura archivos JSON de las provincias del ecuador

            document.addEventListener('DOMContentLoaded', function() {

                const selProvincia = document.getElementById('select_provincia');
                const selCanton = document.getElementById('select_canton');
                const selParroquia = document.getElementById('select_parroquia');
                let datosEcuador = {};

                function limpiar(str) {
                    return str ? str.toString().trim().toUpperCase() : "";
                }

                // 1. CARGAR JSON
                fetch("{{ asset('json/ecuador.json') }}")
                    .then(r => r.json())
                    .then(data => {
                        datosEcuador = data;
                        console.log("✅ JSON cargado.");
                        cargarProvincias();
                    })
                    .catch(e => console.error("❌ Error:", e));

                // 2. CARGAR PROVINCIAS
                function cargarProvincias() {
                    selProvincia.innerHTML = '<option value="">-- Seleccione --</option>';

                    const dbProv = selProvincia.getAttribute('data-old');
                    console.log(`🔍 BD envía: "${dbProv}"`); // Veremos qué llega realmente

                    let idEncontrado = null;

                    for (let id in datosEcuador) {
                        const info = datosEcuador[id];

                        // --- CORRECCIÓN CRÍTICA PARA EL ID 90 ---
                        // Si el nodo no tiene propiedad 'provincia', le ponemos un nombre genérico o lo saltamos
                        let nombreProvincia = info.provincia;
                        if (!nombreProvincia) {
                            // Opción A: Saltarlo
                            // continue;
                            // Opción B: Bautizarlo (Mejor)
                            nombreProvincia = "ZONA NO DELIMITADA";
                        }
                        // -----------------------------------------

                        let opcion = new Option(nombreProvincia, nombreProvincia);
                        opcion.setAttribute('data-id', id);

                        // Comparar
                        if (limpiar(nombreProvincia) === limpiar(dbProv)) {
                            opcion.selected = true;
                            idEncontrado = id;
                        }

                        selProvincia.add(opcion);
                    }

                    if (idEncontrado) cargarCantones(idEncontrado);
                }

                // 3. CARGAR CANTONES
                function cargarCantones(idProv) {
                    selCanton.innerHTML = '<option value="">-- Seleccione --</option>';
                    selParroquia.innerHTML = '<option value="">-- Seleccione Cantón --</option>';

                    // Validación extra de seguridad
                    if (!datosEcuador[idProv] || !datosEcuador[idProv].cantones) return;

                    selCanton.disabled = false;
                    const cantonesObj = datosEcuador[idProv].cantones;
                    const dbCanton = selCanton.getAttribute('data-old');

                    let idEncontrado = null;

                    for (let id in cantonesObj) {
                        const info = cantonesObj[id];

                        // Validación por si algún cantón viene roto
                        if (!info || !info.canton) continue;

                        let opcion = new Option(info.canton, info.canton);
                        opcion.setAttribute('data-id', id);

                        if (limpiar(info.canton) === limpiar(dbCanton)) {
                            opcion.selected = true;
                            idEncontrado = id;
                        }

                        selCanton.add(opcion);
                    }

                    if (idEncontrado) cargarParroquias(idProv, idEncontrado);
                }

                // 4. CARGAR PARROQUIAS
                function cargarParroquias(idProv, idCant) {
                    selParroquia.innerHTML = '<option value="">-- Seleccione --</option>';

                    if (!datosEcuador[idProv].cantones[idCant] || !datosEcuador[idProv].cantones[idCant].parroquias)
                        return;

                    selParroquia.disabled = false;
                    const parroquiasObj = datosEcuador[idProv].cantones[idCant].parroquias;
                    const dbParr = selParroquia.getAttribute('data-old');

                    for (let id in parroquiasObj) {
                        const nombre = parroquiasObj[id];
                        let opcion = new Option(nombre, nombre);

                        if (limpiar(nombre) === limpiar(dbParr)) {
                            opcion.selected = true;
                        }
                        selParroquia.add(opcion);
                    }
                }

                // Eventos
                selProvincia.addEventListener('change', function() {
                    const op = this.options[this.selectedIndex];
                    const id = op.getAttribute('data-id');
                    selCanton.setAttribute('data-old', '');
                    cargarCantones(id);
                });

                selCanton.addEventListener('change', function() {
                    const opP = selProvincia.options[selProvincia.selectedIndex];
                    const idP = opP.getAttribute('data-id');
                    const opC = this.options[this.selectedIndex];
                    const idC = opC.getAttribute('data-id');
                    selParroquia.setAttribute('data-old', '');
                    cargarParroquias(idP, idC);
                });
            });
            //////////////////////
            //Eliminar documnetos
            function confirmarEliminacion(id) {
                if (confirm('¿Estás seguro de que deseas eliminar este documento?')) {


                    let url = "{{ route('inversion.documentos.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Ruta no encontrada o error de servidor');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                const fila = document.getElementById(`fila-doc-${id}`);
                                fila.remove();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error: La ruta no pudo ser encontrada o el servidor no responde.');
                        });
                }
            }
            /////////////////////////////////////////
            //Vista previa de documento antes de subir
            // 1. Iniciamos el contenedor virtual de archivos
            let listaArchivosACargar = new DataTransfer();

            document.getElementById('input-nuevos-docs').addEventListener('change', function(e) {
                const cuerpoTabla = document.getElementById('lista-documentos-cuerpo');
                const input = e.target;
                const nuevosArchivos = Array.from(input.files);

                nuevosArchivos.forEach((archivo) => {
                    // Añadimos el archivo al contenedor virtual
                    listaArchivosACargar.items.add(archivo);

                    // Creamos un ID único usando el tiempo para evitar duplicados
                    const tempId = 'file-' + Math.random().toString(36).substr(2, 9);
                    const urlTemporal = URL.createObjectURL(archivo);
                    const iconoTrash = feather.icons['trash-2'].toSvg({
                        class: 'text-red'
                    });
                    const filaHTML = `
            <tr id="${tempId}" class="table-warning">
                <td>
                    <a href="${urlTemporal}" target="_blank" class="text-decoration-none text-dark" title="${archivo.name}">
                <i class="fas fa-file-upload text-primary me-2"></i>
                <span class="col-nombre-archivo"><strong>${archivo.name}</strong></span>
                <small class="text-muted ms-2">(${(archivo.size / 1024).toFixed(1)} KB)</small>
                </a>
                </td>
                <td><span class="badge bg-primary">Por subir</span></td>
                <td class="">
                    <button type="button" class="btn btn-danger btn-sm"
                            onclick="removerArchivoSeleccionado('${tempId}', '${archivo.name}')">
                        <i class="fas fa-trash data-feather="trash-2">${iconoTrash}</i>
                    </button>
                </td>
            </tr>`;

                    cuerpoTabla.insertAdjacentHTML('beforeend', filaHTML);
                    //feather.replace();
                });

                // 2. Sincronizamos el input real con nuestro contenedor virtual
                input.files = listaArchivosACargar.files;

            });

            function removerArchivoSeleccionado(filaId, nombreArchivo) {
                const input = document.getElementById('input-nuevos-docs');
                const contenedorNuevo = new DataTransfer();
                let encontrado = false;

                // 3. Reconstruimos el contenedor excluyendo el archivo eliminado
                Array.from(listaArchivosACargar.files).forEach((file) => {
                    // Solo excluimos el primero que coincida con el nombre para evitar borrar duplicados de un solo golpe
                    if (file.name === nombreArchivo && !encontrado) {
                        encontrado = true; // Marcamos que ya lo quitamos
                    } else {
                        contenedorNuevo.items.add(file);
                    }
                });

                // Actualizamos la referencia global y el input
                listaArchivosACargar = contenedorNuevo;
                input.files = listaArchivosACargar.files;

                // Quitamos la fila de la vista con un pequeño efecto
                const fila = document.getElementById(filaId);
                if (fila) {
                    fila.style.opacity = '0';
                    setTimeout(() => fila.remove(), 200);
                }
            }

            function removerFilaTemporal(rowId) {
                // Esto solo quita la fila de la vista.
                // Nota: El archivo sigue en el input, si el usuario se equivocó
                // lo ideal es que limpie el input y vuelva a seleccionar.
                document.getElementById(rowId).remove();
                alert(
                    "Para cancelar la subida de este archivo específico, debe limpiar el selector de archivos o volver a seleccionar los correctos."
                );
            }
        </script>
    @endpush
@endsection
