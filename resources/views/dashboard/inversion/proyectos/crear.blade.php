@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-dark fw-bold">Nueva Inversión: Formulación de Proyecto</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Gestión</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('inversion.proyectos.index') }}">Proyectos</a></li>
                        <li class="breadcrumb-item active">Crear Proyecto</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('inversion.proyectos.index') }}" class="btn btn-outline-secondary border-2 fw-bold d-inline-flex align-items-center">
                <span data-feather="arrow-left" ></span> Volver al Banco
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

        <form action="{{ route('inversion.proyectos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
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
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">CUP (Código Único de Proyecto)</label>
                                    <input type="text" name="cup" class="form-control form-control-lg border-2"
                                        placeholder="Codigo unico de proyecto" required>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-bold">Nombre del Proyecto</label>
                                    <input type="text" name="nombre_proyecto"
                                        class="form-control form-control-lg border-2" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Entidad Responsable</label>
                                    <select name="id_organizacion" class="form-select border-2" required>
                                        <option value="" disabled selected>-- Seleccione Ministerio/GAD --</option>
                                        @foreach ($entidades as $entidad)
                                            <option value="{{ $entidad->id_organizacion }}">
                                                {{ $entidad->nom_organizacion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Unidad Ejecutora Responsable</label>

                                    <select name="id_unidad_ejecutora" class="form-select border-2" required>
                                        <option value="" disabled selected>-- Seleccione Unidad --</option>
                                        @foreach ($unidades as $unidad)
                                            <option value="{{ $unidad->id }}">
                                                {{ $unidad->nombre_unidad }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Programa Presupuestario</label>
                                    <select name="id_programa" class="form-select border-2" required>
                                        <option value="" disabled selected>-- Seleccione Programa --</option>
                                        @foreach ($programas as $programa)
                                            <option value="{{ $programa->id }}">{{ $programa->nombre_programa }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Tipo de Inversión</label>
                                    <select name="tipo_inversion" class="form-select border-2" required>
                                        <option value="OBRA PUBLICA">Obra Pública</option>
                                        <option value="ADQUISICION">Adquisición</option>
                                        <option value="OTRO">Otro</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Monto Total Inversión</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-2">$</span>
                                        <input type="number" step="0.01" name="monto_total_inversion"
                                            id="monto_total_inversion" class="form-control border-2" placeholder="0.00"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Estado Dictamen</label>
                                    <select name="estado_dictamen" class="form-select border-2" required>
                                        <option value="PENDIENTE">Pendiente</option>
                                        <option value="FAVORABLE">Favorable</option>
                                        <option value="NEGATIVO">Negativo</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Fecha Inicio Estimada</label>
                                    <input type="date" name="fecha_inicio_estimada" id="fecha_inicio"
                                        class="form-control border-2" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Fecha Fin Estimada</label>
                                    <input type="date" name="fecha_fin_estimada" id="fecha_fin"
                                        class="form-control border-2" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Duración (Meses)</label>
                                    <input type="number" name="duracion_meses" id="duracion"
                                        class="form-control border-2 bg-light" readonly>
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
                                        </tbody>
                                    </table>

                                    <div class="card-footer bg-light">
                                        <div class="row text-end">
                                            <div class="col-md-8">
                                                <span class="fw-bold">Total Programado:</span>
                                            </div>
                                            <div class="col-md-4">
                                                <span id="suma_total_format" class="fw-bold text-primary">$ 0.00</span>
                                                <input type="hidden" id="suma_total_raw" value="0" required>
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
                                <div>Según la normativa, todo proyecto de inversión debe responder a un objetivo del Plan
                                    Nacional de Desarrollo. (PND).</div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Eje Estratégico</label>
                                    <select name="id_eje" id="select_eje" class="form-select border-2" required>
                                        <option value="" selected disabled>-- Seleccione un Eje --</option>
                                        @foreach ($ejes as $eje)
                                            <option value="{{ $eje->id_eje }}">{{ $eje->nombre_eje }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Objetivo Nacional (PND)</label>
                                    <select name="id_objetivo_nacional" id="select_objetivo" class="form-select border-2"
                                        required>
                                        <option value="">-- Seleccione un Eje primero --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tecnico" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Diagnóstico y Justificación</label>
                                    <textarea name="descripcion_diagnostico" class="form-control border-2" rows="4"
                                        placeholder="Describa el problema y la necesidad técnica..." required></textarea>
                                </div>
                                <div class="col-12 mt-4">
                                    <h6 class="text-uppercase fw-bold text-secondary">Localización Geográfica</h6>
                                    <hr>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Provincia</label>
                                    <select name="provincia" id="select_provincia" class="form-select border-2" required>
                                        <option value="">-- Seleccione Provincia --</option>
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
                        <div class="tab-pane fade" id="docs" role="tabpanel">

                            <div class="alert alert-info border-0 d-flex align-items-center mb-4">
                                <span data-feather="info" class="me-2"></span>
                                <div>
                                    Puede seleccionar varios archivos de <strong>respaldo</strong> del proyecto.
                                    Puede subir múltiples archivos
                                </div>
                            </div>

                            <div class="row g-3 justify-content-center">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold">Seleccionar Archivos de Respaldo (PDF)</label>

                                    <div class="d-flex justify-content-center w-100">
                                        <label id="drop-area"
                                            class="w-100 p-5 border border-2 border-dashed bg-light text-center rounded cursor-pointer hover-shadow transition">
                                            <div class="d-flex flex-column align-items-center justify-content-center">
                                                <i class="fas fa-cloud-upload-alt fa-3x text-secondary mb-3"></i>

                                                <p id="file-name" class="text-muted fw-bold mb-1">
                                                    Haga clic o arrastre sus archivos aquí
                                                </p>
                                                <small class="text-muted">Soporta carga múltiple (Mantenga Ctrl)</small>
                                            </div>

                                            <input type="file" name="documentos[]" class="d-none" accept=".pdf"
                                                multiple onchange="updateFileName(this)">
                                        </label>
                                    </div>
                                    <div class="form-text mt-2 text-center">
                                        Asegúrese de que los archivos no superen los 10MB cada uno.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light p-3 d-flex justify-content-end">
                    <button type="reset" class="btn btn-link text-muted me-3">Limpiar Formulario</button>
                    <button type="submit" id="btnGuardarProyecto" class="btn btn-dark px-5 py-2 fw-bold" disabled>
                        <span data-feather="save" class="me-1"></span> Guardar Proyecto
                    </button>
                </div>
            </div>
        </form>
    </div>
    @push('scripts')
        <script>
            // ==========================================
            // 1. VARIABLES Y FUNCIONES GLOBALES
            // ==========================================

            // Función para agregar filas
            function agregarFila() {
                const cuerpo = document.getElementById('cuerpoFinanciamiento');
                if (!cuerpo) return;

                const fila = document.createElement('tr');
                fila.innerHTML = `
            <td><input type="number" name="anio[]" class="form-control form-control-sm" placeholder="2026" required></td>
            <td>
                <select name="id_fuente[]" class="form-select form-select-sm" required>
                    <option value="">-- Seleccione --</option>
                    @foreach ($fuentes as $fuente)
                        <option value="{{ $fuente->id_fuente }}">{{ $fuente->nombre_fuente }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" step="0.01" name="monto_anio[]" class="form-control form-control-sm monto-input" required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarFila(this)">X</button>
            </td>
        `;
                cuerpo.appendChild(fila);
            }

            // Función auxiliar para eliminar fila y recalcular
            function eliminarFila(boton) {
                boton.closest('tr').remove();
                calcularTotal();
            }

            // LA FUNCIÓN DE CÁLCULO (El corazón del sistema)
            function calcularTotal() {
                console.log("--> Calculando..."); // Para depuración

                //  Sumar
                let suma = 0;
                const inputs = document.querySelectorAll('.monto-input');
                inputs.forEach(input => {
                    suma += parseFloat(input.value) || 0;
                });

                //  Mostrar Texto
                const display = document.getElementById('suma_total_format');
                if (display) {
                    display.innerText = '$ ' + suma.toLocaleString('en-US', {
                        minimumFractionDigits: 2
                    });

                    //  Validar Colores (Matemática pura)
                    const inputGlobal = document.getElementById('monto_total_inversion');
                    const montoGlobal = inputGlobal ? parseFloat(inputGlobal.value) || 0 : 0;
                    const diff = Math.abs(suma - montoGlobal);
                    const cuadra = (montoGlobal > 0 && diff <= 0.01);
                    const alerta = document.getElementById('alerta_monto');

                    if (inputs.length > 0 || suma > 0) {
                        if (cuadra) {
                            display.style.setProperty('color', '#198754', 'important'); // Verde
                            if (alerta) alerta.classList.add('d-none');
                        } else {
                            display.style.setProperty('color', '#dc3545', 'important'); // Rojo
                            if (alerta) alerta.classList.remove('d-none');
                        }
                    } else {
                        display.style.setProperty('color', '#6c757d', 'important'); // Gris
                        if (alerta) alerta.classList.add('d-none');
                    }

                    // 4. Llamar a la validación del botón (Si existe)
                    validarBotonGuardar(cuadra);
                }
            }

            function validarBotonGuardar(presupuestoCuadra) {
                const btn = document.getElementById('btnGuardarProyecto');
                const form = document.querySelector('form');

                if (btn && form) {
                    const formularioValido = form.checkValidity();

                    // Depuración para saber por qué no se activa
                    if (presupuestoCuadra && !formularioValido) {
                        console.warn("--- EL DINERO CUADRA, PERO FALTAN CAMPOS ---");
                        // Buscar qué campos están vacíos
                        const invalidos = form.querySelectorAll(':invalid');
                        invalidos.forEach(campo => {
                            console.log("Falta llenar: " + (campo.name || campo.id));
                        });
                    }

                    if (presupuestoCuadra && formularioValido) {
                        btn.disabled = false;
                        btn.classList.remove('btn-secondary');
                        btn.classList.add('btn-success');
                        btn.style.opacity = "1";
                        console.log("¡BOTÓN ACTIVADO!");
                    } else {
                        btn.disabled = true;
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-secondary');
                        btn.style.opacity = "0.6";
                    }
                }
            }

            // ==========================================
            //  CARGA DE EVENTOS
            // ==========================================
            document.addEventListener('DOMContentLoaded', function() {

                //  ESCUCHADOR MAESTRO DE LA TABLA
                // Esto detecta cambios en cualquier input de monto, presente o futuro
                const tabla = document.getElementById('tablaFinanciamiento');
                if (tabla) {
                    tabla.addEventListener('input', function(e) {
                        if (e.target.classList.contains('monto-input')) {
                            calcularTotal();
                        }
                    });
                }

                // ESCUCHADOR DEL MONTO GLOBAL
                const inputGlobal = document.getElementById('monto_total_inversion');
                if (inputGlobal) {
                    inputGlobal.addEventListener('input', calcularTotal);
                }

                // VALIDACIÓN GENERAL DEL FORMULARIO
                // Cada vez que se escriba algo, intentamos validar el botón
                const form = document.querySelector('form');
                if (form) {
                    form.addEventListener('input', function(e) {
                        // Solo validamos el botón, el cálculo financiero ya lo manejan los eventos de arriba
                        // Calculamos de nuevo para obtener el estado 'cuadra'
                        const inputs = document.querySelectorAll('.monto-input');
                        let suma = 0;
                        inputs.forEach(i => suma += parseFloat(i.value) || 0);
                        const global = parseFloat(document.getElementById('monto_total_inversion').value) || 0;
                        const cuadra = (global > 0 && Math.abs(suma - global) <= 0.01);

                        validarBotonGuardar(cuadra);
                    });
                }

                //  LÓGICA DE FECHAS
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

                //  LÓGICA DE OBJETIVOS (AJAX)
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
            //Carga de provincias cantones y parroquias del ecuador
            ///////////////////////////////////////////////////////

            document.addEventListener('DOMContentLoaded', function() {

                // Referencias
                const selProvincia = document.getElementById('select_provincia');
                const selCanton = document.getElementById('select_canton');
                const selParroquia = document.getElementById('select_parroquia');

                let datosEcuador = {};

                // 1. CARGAR JSON
                fetch("{{ asset('json/ecuador.json') }}")
                    .then(r => r.json())
                    .then(data => {
                        console.log("✅ JSON cargado.");
                        datosEcuador = data;
                        cargarProvincias();
                    })
                    .catch(e => console.error("❌ Error:", e));


                // 2. LLENAR PROVINCIAS
                function cargarProvincias() {
                    selProvincia.innerHTML = '<option value="">-- Seleccione --</option>';

                    // Recorremos las llaves numéricas ("1", "2", etc.)
                    for (let idProv in datosEcuador) {
                        const infoProv = datosEcuador[idProv];

                        // Creamos la opción
                        let opcion = new Option(infoProv.provincia, infoProv.provincia);

                        // ¡TRUCO! Guardamos el ID numérico ("1") en un atributo escondido
                        opcion.setAttribute('data-id', idProv);

                        selProvincia.add(opcion);
                    }
                }


                // 3. CAMBIO DE PROVINCIA -> CARGAR CANTONES
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
                            opcion.setAttribute('data-id', idCant); // Guardamos ID Cantón

                            selCanton.add(opcion);
                        }
                    }
                });


                // 4. CAMBIO DE CANTÓN -> CARGAR PARROQUIAS
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

                            // Recorremos las parroquias ("10101": "BELLAVISTA")
                            for (let idParr in listaParroquias) {
                                const nombreParroquia = listaParroquias[idParr];

                                // Aquí el valor directo es el nombre
                                selParroquia.add(new Option(nombreParroquia, nombreParroquia));
                            }
                        }
                    }
                });
            });
            ///////////////////////////////////////
            //Cambiar nombre cuando sube un archivo
            function updateFileName(input) {
                var fileNameContainer = document.getElementById('file-name');
                var dropArea = document.getElementById('drop-area');

                if (input.files && input.files.length > 0) {
                    var count = input.files.length;

                    if (count === 1) {
                        // Si es uno solo, mostramos el nombre
                        fileNameContainer.textContent = "Archivo listo: " + input.files[0].name;
                    } else {
                        // Si son varios, mostramos la cantidad
                        fileNameContainer.textContent = "¡" + count + " archivos seleccionados listos para subir!";
                    }

                    // Estilos de éxito (Verde)
                    fileNameContainer.classList.remove('text-muted');
                    fileNameContainer.classList.add('text-success');

                    dropArea.classList.remove('border-dashed', 'bg-light');
                    dropArea.classList.add('border-success', 'bg-white', 'shadow-sm');

                    // Cambiamos el icono a check (opcional, requiere acceso al elemento i)
                    // dropArea.querySelector('i').className = "fas fa-check-circle fa-3x text-success mb-3";

                } else {
                    // Resetear si cancelan
                    fileNameContainer.textContent = "Haga clic o arrastre sus archivos aquí";
                    fileNameContainer.classList.add('text-muted');
                    fileNameContainer.classList.remove('text-success');

                    dropArea.classList.add('border-dashed', 'bg-light');
                    dropArea.classList.remove('border-success', 'bg-white', 'shadow-sm');
                }
            }
        </script>
    @endpush
@endsection
