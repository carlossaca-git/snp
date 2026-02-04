@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Formulacion de Proyecto" subtitulo="{{ Auth::user()->organizacion->nom_organizacion }}">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('inversion.proyectos.index') }}"
                class="btn btn-outline-secondary border-2 fw-bold d-inline-flex align-items-center">
                <i class="fas fa-arrow-left me-2"></i> Volver al Banco
            </a>
        </div>
    </x-layouts.header_content>

    <div class="container-fluid py-4">
        @include('partials.mensajes')
        <form action="{{ route('inversion.proyectos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card shadow-sm border-0">
                {{-- Tabs de Navegación --}}
                <div class="card-header bg-white p-0 border-bottom">
                    <ul class="nav nav-tabs border-bottom-0" id="proyectoTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active text-secondary fw-bold px-4 py-3" id="general-tab"
                                data-bs-toggle="tab" data-bs-target="#general" type="button">
                                <i class="fas fa-file-alt me-1"></i> 1. Datos Generales
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-secondary fw-bold px-4 py-3" id="alineacion-tab"
                                data-bs-toggle="tab" data-bs-target="#alineacion" type="button">
                                <i class="fas fa-crosshairs me-1"></i> 2. Alineación Estratégica
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-secondary fw-bold px-4 py-3" id="tecnico-tab" data-bs-toggle="tab"
                                data-bs-target="#tecnico" type="button">
                                <i class="fas fa-map-marker-alt me-1"></i> 3. Diagnóstico y Ubicación
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-secondary fw-bold px-4 py-3" id="docs-tab" data-bs-toggle="tab"
                                data-bs-target="#docs" type="button">
                                <i class="fas fa-folder-open me-1"></i> 4. Documentación
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content">

                        {{-- DATOS GENERALES --}}
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">CUP (Código Único)</label>
                                    <input type="text" name="cup" class="form-control form-control-lg border-2"
                                        placeholder="PR-001..." required>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-bold">Nombre Oficial del Proyecto</label>
                                    <input type="text" name="nombre_proyecto" placeholder="Nombre oficial..."
                                        class="form-control form-control-lg border-2" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Unidad Ejecutora</label>
                                    <select name="id_unidad_ejecutora" class="form-select border-2" required>
                                        <option value="" disabled selected>-- Seleccione --</option>
                                        @foreach ($unidades as $unidad)
                                            <option value="{{ $unidad->id }}">{{ $unidad->nombre_unidad }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Programa Presupuestario</label>
                                    <select name="id_programa" class="form-select border-2" required>
                                        <option value="" disabled selected>-- Seleccione Programa --</option>
                                        @foreach ($programas as $programa)
                                            <option value="{{ $programa->id }}">{{ $programa->codigo_programa }} -
                                                {{ $programa->nombre_programa }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Tipo de Inversión</label>
                                    <select name="tipo_inversion" class="form-select border-2" required>
                                        <option value="OBRA PUBLICA">Obra Pública</option>
                                        <option value="ADQUISICION">Adquisición de Bienes</option>
                                        <option value="SERVICIOS">Servicios / Consultoría</option>
                                        <option value="OTRO">Otro</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-success">Monto Total Inversión</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-2 fw-bold text-success">$</span>
                                        <input type="number" step="0.01" name="monto_total_inversion"
                                            id="monto_total_inversion" class="form-control border-2 fw-bold"
                                            placeholder="0.00" required>
                                    </div>
                                </div>
                                {{-- Fechas --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Fecha Inicio</label>
                                    <input type="date" name="fecha_inicio_estimada" id="fecha_inicio"
                                        class="form-control border-2" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Fecha Fin</label>
                                    <input type="date" name="fecha_fin_estimada" id="fecha_fin"
                                        class="form-control border-2" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Duración (Meses)</label>
                                    <input type="number" name="duracion_meses" id="duracion"
                                        class="form-control border-2 bg-light" readonly>
                                </div>
                            </div>

                            {{-- SECCIÓN CRONOGRAMA VALORADO --}}
                            <div class="card mt-4 shadow-sm border-0 bg-light">
                                <div
                                    class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i> Programación Financiera
                                        Plurianual (Cronograma Valorado)</h6>
                                    <button type="button" class="btn btn-light btn-sm fw-bold" onclick="agregarFila()">
                                        <i class="fas fa-plus"></i> Añadir Año
                                    </button>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-hover" id="tablaFinanciamiento">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Año Fiscal</th>
                                                <th>Fuente de Financiamiento</th>
                                                <th>Monto Programado</th>
                                                <th width="50"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="cuerpoFinanciamiento">
                                            {{-- Las filas se agregan con JS --}}
                                        </tbody>
                                    </table>
                                    <div class="row text-end mt-3">
                                        <div class="col-md-8 fw-bold">Total Programado:</div>
                                        <div class="col-md-4">
                                            <span id="suma_total_format" class="fw-bold fs-5 text-muted">$ 0.00</span>
                                            <input type="hidden" id="suma_total_raw" value="0">
                                        </div>
                                    </div>
                                    <div id="alerta_monto" class="alert alert-warning mt-2 d-none small">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        La suma de los años no coincide con el <strong>Monto Total de Inversión</strong>.
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--  ALINEACIÓN ESTRATÉGICA  --}}
                        <div class="tab-pane fade" id="alineacion" role="tabpanel">
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <div class="alert alert-info border-0 shadow-sm mb-4">
                                        <div class="d-flex">
                                            <i class="fas fa-sitemap fa-2x me-3 opacity-50"></i>
                                            <div>
                                                <h5 class="alert-heading h6 fw-bold">Principio de Cascada</h5>
                                                <p class="mb-0 small">Al seleccionar su <strong>Objetivo Estratégico
                                                        Institucional</strong>, el sistema vinculará automáticamente este
                                                    proyecto a la Meta Nacional del PND correspondiente.</p>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- SELECT DE OBJETIVO --}}
                                    <div class="card mb-4 border-left-primary shadow-sm">
                                        <div class="card-body">
                                            <label class="fw-bold text-primary">1. Seleccione Objetivo Estratégico:</label>
                                            <select id="select_objetivo" name="objetivo_estrategico_id"
                                                class="form-select">
                                                <option value="">-- Seleccione --</option>
                                                @foreach ($objetivosEstr as $obj)
                                                    <option value="{{ $obj->id_objetivo_estrategico }}">{{ $obj->codigo }}
                                                        - {{ $obj->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        {{-- SELECCIÓN DE METAS --}}
                                        <div class="col-md-4">
                                            <div class="card h-100 shadow-sm">
                                                <div class="card-header bg-light fw-bold">2. Seleccione Metas Impactadas
                                                </div>
                                                <div class="card-body" id="contenedor_metas">
                                                    <div class="text-muted small text-center p-3">
                                                        Seleccione un objetivo primero.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- SELECCIÓN DE INDICADORES --}}
                                        <div class="col-md-8">
                                            <div class="card h-100 shadow-sm">
                                                <div class="card-header bg-light fw-bold">3. Defina Contribución a
                                                    Indicadores</div>
                                                <div class="card-body p-0">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover align-middle mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th width="5%">Sel.</th>
                                                                    <th>Indicador / Meta</th>
                                                                    <th width="15%">Peso PND</th>
                                                                    <th width="20%">Tu Aporte %</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tabla_indicadores">
                                                                <tr>
                                                                    <td colspan="4" class="text-center p-4 text-muted">
                                                                        Seleccione metas a la izquierda.</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{--  DIAGNÓSTICO Y UBICACIÓN --}}
                        <div class="tab-pane fade" id="tecnico" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Diagnóstico y Justificación del Problema</label>
                                    <textarea name="descripcion_diagnostico" class="form-control border-2" rows="5"
                                        placeholder="Describa el problema central, causas, efectos y la justificación de la intervención..." required></textarea>
                                </div>

                                <div class="col-12 mt-4 mb-2">
                                    <h6 class="text-uppercase fw-bold text-secondary border-bottom pb-2">Localización
                                        Geográfica</h6>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Provincia</label>
                                    <select name="provincia" id="select_provincia" class="form-select border-2" required>
                                        <option value="">-- Seleccione --</option>
                                        {{-- OBTENER UBICACION --}}
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Cantón</label>
                                    <select name="canton" id="select_canton" class="form-select border-2" required
                                        disabled>
                                        <option value="">-- Seleccione Provincia Primero --</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Parroquia</label>
                                    <select name="parroquia" id="select_parroquia" class="form-select border-2" required
                                        disabled>
                                        <option value="">-- Seleccione Cantón Primero --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{--  DOCUMENTACIÓN --}}
                        <div class="tab-pane fade" id="docs" role="tabpanel">
                            <div class="alert alert-info border-0 d-flex align-items-center mb-4">
                                <i class="fas fa-info-circle fa-2x me-3 text-info"></i>
                                <div>
                                    <strong>Importante:</strong> Debe cargar los documentos habilitantes (Estudios,
                                    Factibilidad, Dictamen previo si existe).
                                    <br>Formatos permitidos: PDF. Máximo 10MB por archivo.
                                </div>
                            </div>

                            <div class="d-flex justify-content-center w-100">
                                <label id="drop-area"
                                    class="w-100 p-5 border border-2 border-dashed bg-light text-center rounded cursor-pointer">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-secondary mb-3"></i>
                                        <p id="file-name" class="text-muted fw-bold mb-1">Haga clic o arrastre sus
                                            archivos PDF aquí</p>
                                        <small class="text-muted">Mantenga presionada la tecla Ctrl para seleccionar
                                            múltiples archivos</small>
                                    </div>
                                    <input type="file" name="documentos[]" class="d-none" accept=".pdf" multiple
                                        onchange="updateFileName(this)">
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-footer bg-light p-3 d-flex justify-content-end align-items-center">
                    <span class="text-muted small me-3 fst-italic">Complete todos los campos obligatorios para activar el
                        botón</span>
                    <button type="submit" id="btnGuardarProyecto" class="btn btn-secondary px-5 py-2 fw-bold" disabled>
                        <i class="fas fa-save me-1"></i> Guardar Proyecto
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            // CALCULOS FINANCIEROS Y VALIDACIONES
            function agregarFila() {
                const cuerpo = document.getElementById('cuerpoFinanciamiento');
                const fila = document.createElement('tr');
                fila.innerHTML = `
            <td><input type="number" name="anio[]" class="form-control form-control-sm" placeholder="Ej: 2026" required></td>
            <td>
                <select name="id_fuente[]" class="form-select form-select-sm" required>
                    <option value="">-- Fuente --</option>
                    @foreach ($fuentes as $fuente)
                        <option value="{{ $fuente->id_fuente }}">{{ $fuente->nombre_fuente }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" step="0.01" name="monto_anio[]" class="form-control form-control-sm monto-input" placeholder="0.00" required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="eliminarFila(this)"><i class="fas fa-times"></i></button>
            </td>`;
                cuerpo.appendChild(fila);
            }

            function eliminarFila(btn) {
                btn.closest('tr').remove();
                calcularTotal();
            }

            function calcularTotal() {
                let suma = 0;
                document.querySelectorAll('.monto-input').forEach(i => suma += parseFloat(i.value) || 0);

                const display = document.getElementById('suma_total_format');
                const globalInput = document.getElementById('monto_total_inversion');
                const global = parseFloat(globalInput.value) || 0;
                const alerta = document.getElementById('alerta_monto');

                display.innerText = '$ ' + suma.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                });

                const cuadra = (global > 0 && Math.abs(suma - global) <= 0.01);

                if (global > 0) {
                    display.className = cuadra ? "fw-bold fs-5 text-success" : "fw-bold fs-5 text-danger";
                    if (cuadra) alerta.classList.add('d-none');
                    else alerta.classList.remove('d-none');
                }
                validarBotonGuardar(cuadra);
            }

            function validarBotonGuardar(presupuestoCuadra) {
                const btn = document.getElementById('btnGuardarProyecto');
                const form = document.querySelector('form');
                if (presupuestoCuadra && form.checkValidity()) {
                    btn.disabled = false;
                    btn.classList.remove('btn-secondary');
                    btn.classList.add('btn-dark');
                } else {
                    btn.disabled = true;
                    btn.classList.remove('btn-dark');
                    btn.classList.add('btn-secondary');
                }
            }

            // Event Listeners Globales para montos
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('monto-input') || e.target.id === 'monto_total_inversion') {
                    calcularTotal();
                }
            });

            // --- FECHAS ---
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
            fInicio.addEventListener('change', calcFechas);
            fFin.addEventListener('change', calcFechas);

            // --- UBICACIÓN ---
            document.addEventListener('DOMContentLoaded', function() {

                // Referencias
                const selProvincia = document.getElementById('select_provincia');
                const selCanton = document.getElementById('select_canton');
                const selParroquia = document.getElementById('select_parroquia');

                let datosEcuador = {};

                // CARGAR JSON
                fetch("{{ asset('json/ecuador.json') }}")
                    .then(r => r.json())
                    .then(data => {
                        console.log("✅ JSON cargado.");
                        datosEcuador = data;
                        cargarProvincias();
                    })
                    .catch(e => console.error("❌ Error:", e));


                // LLENAR PROVINCIAS
                function cargarProvincias() {
                    selProvincia.innerHTML = '<option value="">-- Seleccione --</option>';

                    // Recorremos las llaves numéricas
                    for (let idProv in datosEcuador) {
                        const infoProv = datosEcuador[idProv];

                        // Creamos la opción
                        let opcion = new Option(infoProv.provincia, infoProv.provincia);
                        opcion.setAttribute('data-id', idProv);

                        selProvincia.add(opcion);
                    }
                }


                // CAMBIO DE PROVINCIA -> CARGAR CANTONES
                selProvincia.addEventListener('change', function() {
                    // Reseteamos
                    selCanton.innerHTML = '<option value="">-- Seleccione Cantón --</option>';
                    selParroquia.innerHTML = '<option value="">-- Seleccione Parroquia --</option>';
                    selCanton.disabled = true;
                    selParroquia.disabled = true;

                    // Buscamos cuál opción eligió el usuario para sacar su ID oculto
                    const opcionSeleccionada = selProvincia.options[selProvincia.selectedIndex];
                    const idProv = opcionSeleccionada.getAttribute('data-id');

                    if (idProv && datosEcuador[idProv]) {
                        selCanton.disabled = false;
                        const listaCantones = datosEcuador[idProv].cantones;

                        // Recorremos los IDs de cantones ("101", "102"...)
                        for (let idCant in listaCantones) {
                            const infoCanton = listaCantones[idCant];

                            let opcion = new Option(infoCanton.canton, infoCanton.canton);
                            opcion.setAttribute('data-id', idCant);

                            selCanton.add(opcion);
                        }
                    }
                });


                // CAMBIO DE CANTÓN -> CARGAR PARROQUIAS
                selCanton.addEventListener('change', function() {
                    selParroquia.innerHTML = '<option value="">-- Seleccione Parroquia --</option>';
                    selParroquia.disabled = true;

                    // Recuperamos ID Provincia y ID Cantón
                    const opProv = selProvincia.options[selProvincia.selectedIndex];
                    const idProv = opProv.getAttribute('data-id');

                    const opCant = selCanton.options[selCanton.selectedIndex];
                    const idCant = opCant.getAttribute('data-id');

                    if (idProv && idCant) {
                        // Navegamos: JSON -> Provincia -> Cantones -> Canton -> Parroquias
                        const listaParroquias = datosEcuador[idProv].cantones[idCant].parroquias;

                        if (listaParroquias) {
                            selParroquia.disabled = false;

                            // Recorremos las parroquias
                            for (let idParr in listaParroquias) {
                                const nombreParroquia = listaParroquias[idParr];

                                // Aquí el valor directo es el nombre
                                selParroquia.add(new Option(nombreParroquia, nombreParroquia));
                            }
                        }
                    }
                });
            });
            // ARCHIVOS
            function updateFileName(input) {
                const nameContainer = document.getElementById('file-name');
                const area = document.getElementById('drop-area');
                if (input.files.length > 0) {
                    nameContainer.innerText = input.files.length === 1 ?
                        "Archivo: " + input.files[0].name :
                        input.files.length + " archivos seleccionados";
                    nameContainer.classList.add('text-success');
                    area.classList.add('border-success');
                }
            }
            // Variable global para guardar los datos que traemos del servidor
            let metasData = [];

            // EVENTO  CAMBIO DE OBJETIVO
            document.getElementById('select_objetivo').addEventListener('change', function() {
                let idObj = this.value;
                let divMetas = document.getElementById('contenedor_metas');
                let tbody = document.getElementById('tabla_indicadores');

                // Limpiar todo
                divMetas.innerHTML =
                    '<div class="text-center p-2"><div class="spinner-border spinner-border-sm text-primary"></div> Cargando...</div>';
                tbody.innerHTML =
                    '<tr><td colspan="4" class="text-center p-4 text-muted">Seleccione metas a la izquierda.</td></tr>';
                metasData = [];

                if (!idObj) return;

                // Petición AJAX
                fetch(`{{ url('estrategico/alineacion') }}/api/objetivos/${idObj}/arbol-alineacion`)
                    .then(res => res.json())
                    .then(data => {
                        metasData = data;
                        divMetas.innerHTML = '';

                        if (data.length === 0) {
                            divMetas.innerHTML =
                                '<div class="alert alert-warning small">Este objetivo no tiene metas alineadas.</div>';
                            return;
                        }
                        // Dibujar Checkboxes de Metas
                        data.forEach(meta => {
                            divMetas.innerHTML += `
                        <div class="form-check border-bottom py-2">
                            <input class="form-check-input check-meta"
                            type="checkbox"
                            name="metas_id[]"
                            value="${meta.id_meta_nacional}" id="meta_${meta.id_meta_nacional}">
                            <label class="form-check-label small fw-bold" for="meta_${meta.id_meta_nacional}">
                                ${meta.codigo_meta}
                            </label>
                            <div class="text-muted extra-small">${meta.nombre_meta.substring(0, 60)}...</div>
                        </div>
                    `;
                        });

                        // Activar listeners para los nuevos checkboxes
                        activarListenersMetas();
                    });
            });

            // EVENTO  CAMBIO EN CHECKBOX DE META
            function activarListenersMetas() {
                document.querySelectorAll('.check-meta').forEach(check => {
                    check.addEventListener('change', function() {
                        actualizarTablaIndicadores();
                    });
                });
            }

            function actualizarTablaIndicadores() {
                let tbody = document.getElementById('tabla_indicadores');
                tbody.innerHTML = '';

                let checksMarcados = document.querySelectorAll('.check-meta:checked');

                if (checksMarcados.length === 0) {
                    tbody.innerHTML =
                        '<tr><td colspan="4" class="text-center p-4 text-muted">Seleccione metas a la izquierda.</td></tr>';
                    return;
                }

                checksMarcados.forEach(chk => {
                    let metaId = parseInt(chk.value);
                    let metaInfo = metasData.find(m => m.id_meta_nacional === metaId);

                    if (metaInfo && metaInfo.indicadores_nacionales.length > 0) {
                        // Cabecera de Meta
                        tbody.innerHTML += `
                <tr class="table-secondary"><td colspan="4" class="py-1 px-3 small fw-bold text-dark">
                    <i class="fas fa-bullseye me-1"></i> ${metaInfo.codigo_meta} - ${metaInfo.nombre_meta}
                </td></tr>
            `;

                        metaInfo.indicadores_nacionales.forEach(ind => {
                            //  Creamos un ID ÚNICO para cada input usando el ID del indicador
                            // Ejemplo: id="peso_105", id="peso_106"
                            let idUnicoInput = `peso_${ind.id_indicador}`;

                            tbody.innerHTML += `
                    <tr>
                        <td class="text-center">
                            <input class="form-check-input check-ind"
                                   type="checkbox"
                                   name="indicadores[]"
                                   value="${ind.id_indicador}"
                                   data-target="${idUnicoInput}"> </td>
                        <td>
                            <div class="fw-bold small">${ind.codigo_indicador ?? 'S/C'}</div>
                            <div class="small text-muted">${ind.nombre_indicador}</div>
                        </td>
                        <td><span class="badge bg-white text-dark border">${ind.peso_oficial ?? 0}%</span></td>
                        <td>
                            <div class="input-group input-group-sm">
                                <input type="number"
                                       class="form-control"
                                       name="contribuciones[${ind.id_indicador}]"
                                       id="${idUnicoInput}"
                                       disabled
                                       placeholder="0" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </td>
                    </tr>
                `;
                        });
                    }
                });
            }
            document.getElementById('tabla_indicadores').addEventListener('change', function(e) {
                // Verificamos si lo que cambió fue un checkbox de indicador
                if (e.target && e.target.classList.contains('check-ind')) {

                    // Buscamos el ID del input objetivo que guardamos en 'data-target'
                    let targetId = e.target.getAttribute('data-target');
                    let inputPeso = document.getElementById(targetId);

                    if (inputPeso) {
                        inputPeso.disabled = !e.target.checked;

                        if (!e.target.checked) {
                            inputPeso.value = '';
                            inputPeso.classList.remove('is-valid');
                        } else {
                            inputPeso.focus();
                        }
                    }
                }
            });
            // Activar/Desactivar el input de peso
            window.togglePeso = function(chk, id) {
                let input = document.getElementById(`peso_${id}`);
                input.disabled = !chk.checked;
                if (!chk.checked) input.value = '';
                else input.focus();
            }
        </script>
    @endpush
@endsection
