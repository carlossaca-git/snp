@extends('layouts.app')

@section('titulo', 'Alineación Estratégica')

@section('content')
    <div class="container-fluid py-4">

        {{-- 1. ENCABEZADO --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Planificación y Alineación</h1>
                <p class="text-muted mb-0">
                    Institución: <strong class="text-primary">{{ $organizacion->nom_organizacion }}</strong>
                </p>
            </div>
            <a href="{{ route('estrategico.organizaciones.index') }}" class="btn btn-outline-secondary>
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        {{-- MENSAJES DE SESIÓN --}}
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">

            {{-- ============================================================== --}}
            {{-- COLUMNA IZQUIERDA: FORMULARIO DE ALINEACIÓN --}}
            {{-- ============================================================== --}}
            <div class="col-lg-5 mb-4">
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="m-0 fw-bold"><i class="fas fa-link me-1"></i> Nueva Alineación</h6>
                    </div>
                    <div class="card-body bg-light">
                        {{-- Formulario apunta al método STORE del controlador de Alineación --}}
                        <form action="{{ route('estrategico.alineacion.guardar', $organizacion->id_organizacion) }}"
                            method="POST" id="formAlineacion">
                            @csrf

                            {{-- PASO 1: SELECCIONAR OBJETIVO INSTITUCIONAL --}}
                            <div class="mb-4 bg-white p-3 rounded border">
                                <h6 class="text-primary fw-bold small text-uppercase mb-3">1. Objetivo Institucional</h6>

                                <label class="form-label fw-bold small">Seleccione su Objetivo Estratégico:</label>
                                <div class="input-group">
                                    <select name="objetivo_estrategico_id" id="select-obj-estrategico" class="form-select"
                                        required>
                                        <option value="">Seleccione un objetivo...</option>
                                        @foreach ($objetivosEstrategicos as $objEst)
                                            <option value="{{ $objEst->id_objetivo_estrategico }}">
                                                {{-- Ajusta si tu columna de código se llama diferente --}}
                                                {{ $objEst->codigo ?? 'OE' }} - {{ Str::limit($objEst->nombre, 60) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    {{-- Botón para abrir Modal --}}
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalCrearObjetivo">
                                        <i class="fas fa-plus"></i> Nuevo
                                    </button>
                                </div>
                                @error('objetivo_estrategico_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- PASO 2: SELECCIONAR OBJETIVO NACIONAL (PND) --}}
                            <div class="mb-4 bg-white p-3 rounded border">
                                <h6 class="text-success fw-bold small text-uppercase mb-3">2. Alineación Nacional (PND)</h6>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Objetivo Nacional PND <span
                                            class="text-danger">*</span>:</label>
                                    <select name="objetivo_nacional_id" id="select-nacional" class="form-select" required>
                                        <option value="">- Seleccione un Objetivo -</option>
                                        @foreach ($objetivosNacionales as $obj)
                                            <option value="{{ $obj->id_objetivo_nacional }}"
                                                data-ods-id="{{ $obj->id_ods }}"
                                                {{ old('objetivo_nacional_id') == $obj->id_objetivo_nacional ? 'selected' : '' }}>
                                                {{ $obj->codigo_objetivo }} -
                                                {{ Str::limit($obj->descripcion_objetivo, 80) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- PASO 3: Seleccionar una meta --}}
                                <div class="mb-2">
                                    <label for="select-meta" class="form-label fw-bold small">Meta Nacional Asociada <span
                                            class="text-danger">*</span>:</label>
                                    <select name="id_meta_pnd" id="select-meta" class="form-select" disabled required>
                                        <option value="">Seleccione una Meta...</option>

                                        @foreach ($metasNacionales as $meta)
                                            <option value="{{ $meta->id_meta_pnd }}"
                                                data-objetivo-id="{{ $meta->id_objetivo_nacional }}" {{-- AGREGAMOS ESTOS DATOS PARA EL JAVASCRIPT --}}
                                                data-ods-id="{{ $meta->id_ods }}"
                                                data-ods-nombre="{{ $meta->ods->descripcion ?? 'Sin descripción' }}">

                                                {{ Str::limit($meta->descripcion, 90) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- PASO 4: ODS (AUTOMÁTICO) --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="text-warning fw-bold small text-uppercase mb-0">3. Impacto ODS (Automático)
                                    </h6>
                                </div>

                                {{-- Input oculto para enviar el ID del ODS al servidor --}}
                                <input type="hidden" name="ods_id" id="input_ods_oculto" value="">

                                <div class="row row-cols-4 g-2 p-2 bg-white border rounded shadow-sm ods-bloqueado"
                                    style="max-height: 250px; overflow-y: auto;">
                                    @foreach ($ods as $od)
                                        <div class="col">
                                            {{-- USAMOS EL ID REAL DE TU BASE DE DATOS (id_ods) --}}
                                            <label class="ods-card-container w-100" for="ods_{{ $od->id_ods ?? $od->id }}">

                                                {{-- 1. IMPORTANTE: Quitamos "disabled" para que se guarde --}}
                                                {{-- 2. IMPORTANTE: El ID debe coincidir con lo que busca el JS --}}
                                                <input class="form-check-input ods-checkbox d-none" type="radio"
                                                    name="ods_id" value="{{ $od->id_ods ?? $od->id }}"
                                                    id="ods_{{ $od->id_ods ?? $od->id }}">

                                                <div class="ods-card text-center p-1 rounded"
                                                    style="background-color: {{ $od->color_hex ?? '#ccc' }}; opacity: 0.3; transition: all 0.3s; min-height: 70px; display: flex; align-items: center; justify-content: center;">

                                                    <div class="w-100">
                                                        {{-- Número --}}
                                                        <div class="fw-bold text-white" style="font-size: 0.8rem;">
                                                            {{ $od->numero }}</div>

                                                        {{-- Nombre --}}
                                                        <div class="text-white px-1"
                                                            style="font-size: 0.55rem; font-weight: 500; line-height: 1;">
                                                            {{ $od->nombre_corto }}
                                                        </div>
                                                    </div>

                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                    <i class="fas fa-save me-1"></i> Guardar Alineación
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ============================================================== --}}
            {{-- COLUMNA DERECHA: TABLA DE ALINEACIONES --}}
            {{-- ============================================================== --}}
            <div class="col-lg-7 mb-4">
                <div class="card shadow border-0 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="m-0 fw-bold text-dark"><i class="fas fa-list me-1"></i> Alineaciones Registradas</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="bg-light text-uppercase small fw-bold">
                                    <tr>
                                        <th class="ps-4">Objetivo Institucional</th>
                                        <th>PND / Meta</th>
                                        <th class="text-center">ODS</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{--  Iteramos sobre $alineaciones, no sobre objetivos sueltos --}}
                                    @forelse($alineaciones as $item)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-primary small">
                                                    {{ $item->objetivoEstrategico->codigo ?? 'OE' }}
                                                </div>
                                                <small class="text-muted d-block" style="line-height: 1.2;">
                                                    {{ Str::limit($item->objetivoEstrategico->nombre ?? 'Sin Nombre', 60) }}
                                                </small>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success mb-1">
                                                    {{ $item->objetivoNacional->codigo_objetivo ?? 'N/A' }}
                                                </span>
                                                <div class="small text-muted fst-italic" style="font-size: 0.75rem;">
                                                    Meta:
                                                    {{ Str::limit($item->metaPnd->descripcion ?? 'No especificada', 50) }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if ($item->ods)
                                                    <span class="badge"
                                                        style="background-color: {{ $item->ods->color_hex }};">
                                                        ODS {{ $item->ods->numero }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center" style="width: 120px;">
                                                <div class="d-flex justify-content-center gap-2">

                                                    {{-- BOTÓN EDITAR (Lápiz) --}}
                                                    {{-- Fíjate en los 'data-attributes': Cargan los datos para el JS --}}
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-primary btn-editar"
                                                        title="Editar Alineación" data-id="{{ $item->id }}"
                                                        data-obj-est="{{ $item->objetivo_estrategico_id }}"
                                                        data-obj-nac="{{ $item->objetivo_nacional_id }}"
                                                        data-meta="{{ $item->meta_pnd_id }}"
                                                        data-ods="{{ $item->ods_id }}" data-bs-toggle="modal"
                                                        data-bs-target="#modalEditarAlineacion">
                                                        <i class="fas fa-edit">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" fill="currentColor" class="bi bi-pencil"
                                                                viewBox="0 0 16 16">
                                                                <path
                                                                    d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325" />
                                                            </svg>
                                                        </i>
                                                    </button>

                                                    {{-- BOTÓN ELIMINAR (Basurero) --}}
                                                    {{-- Usa un formulario para seguridad (DELETE method) --}}
                                                    <form
                                                        action="{{ route('estrategico.alineacion.eliminar', $item->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('¿Estás seguro de eliminar esta alineación? Esta acción no se puede deshacer.');">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            title="Eliminar">
                                                            <i class="fas fa-trash-alt">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                    height="16" fill="currentColor"
                                                                    class="bi bi-trash3" viewBox="0 0 16 16">
                                                                    <path
                                                                        d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5" />
                                                                </svg>
                                                            </i>
                                                        </button>
                                                    </form>

                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="fas fa-folder-open fa-2x mb-3 d-block"></i>
                                                No hay alineaciones registradas aún.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL PARA CREAR OBJETIVO --}}
    <div class="modal fade" id="modalCrearObjetivo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Nuevo Objetivo Estratégico</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-crear-objetivo">
                        @csrf
                        <input type="hidden" name="id_organizacion" value="{{ $organizacion->id_organizacion }}">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Código</label>
                            <input type="text" name="codigo" class="form-control" placeholder="Ej: OE-01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre del Objetivo</label>
                            <textarea name="nombre" class="form-control" rows="3" required
                                placeholder="Ej: Incrementar la eficiencia..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Indicador</label>
                            <input type="text" name="indicador" class="form-control" placeholder="Ej: % de gestión">
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Inicio</label>
                                <input type="date" name="fecha_inicio" class="form-control"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Fin</label>
                                <input type="date" name="fecha_fin" class="form-control">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn-guardar-modal">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal para editar alineaciones --}}
    <div class="modal fade" id="modalEditarAlineacion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formEditarAlineacion" action="#" method="POST">
                    @csrf
                    @method('PUT') {{-- Convierte el formulario para Actualizar --}}

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Editar Alineación</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        {{-- 1. Objetivo Estratégico --}}
                        <div class="mb-3">
                            <label class="form-label">Objetivo Estratégico</label>
                            <select name="objetivo_estrategico_id" id="edit_objetivo_estrategico" class="form-select"
                                required>
                                @foreach ($objetivosEstrategicos as $objEst)
                                    <option value="{{ $objEst->id_objetivo_estrategico }}">{{ $objEst->codigo }} -
                                        {{ Str::limit($objEst->nombre, 50) }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 2. Objetivo Nacional --}}
                        <div class="mb-3">
                            <label class="form-label">Objetivo Nacional (PND)</label>
                            <select name="objetivo_nacional_id" id="edit_objetivo_nacional" class="form-select" required>
                                @foreach ($objetivosNacionales as $objNac)
                                    <option value="{{ $objNac->id_objetivo_nacional }}">
                                        {{ Str::limit($objNac->descripcion_objetivo, 80) }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 3. Meta Nacional  --}}
                        <div class="mb-3">
                            <label class="form-label">Meta PND</label>
                            <select name="id_meta_pnd" id="edit_meta_pnd" class="form-select" required>
                                <option value="">Seleccione una meta...</option>
                                @foreach ($metasNacionales as $meta)
                                    <option value="{{ $meta->id_meta_pnd }}"
                                        data-objetivo-id="{{ $meta->id_objetivo_nacional }}"
                                        data-ods-id="{{ $meta->id_ods }}">
                                        {{ Str::limit($meta->descripcion, 90) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        {{-- ODS EN EL MODAL VISTA DE TARJETAS --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-warning">Impacto ODS
                                Vinculado</label>

                            {{-- Input oculto para el formulario de edición --}}
                            <input type="hidden" name="ods_id" id="edit_ods_id">

                            <div class="row row-cols-5 g-1 p-2 bg-light border rounded shadow-sm"
                                style="max-height: 250px; overflow-y: auto;">
                                @foreach ($ods as $od)
                                    <div class="col">
                                        <div class="ods-card-edit text-center p-2 rounded d-flex flex-column align-items-center justify-content-center"
                                            id="edit_ods_card_{{ $od->id_ods }}"
                                            style="background-color: {{ $od->color_hex }}; opacity: 0.2; transition: all 0.3s; min-height: 80px; cursor: default; border: 1px solid transparent;">

                                            {{-- Número del ODS --}}
                                            <div class="fw-bold text-white small"
                                                style="font-size: 0.8rem; line-height: 1;">
                                                {{ $od->numero }}
                                            </div>

                                            {{-- Nombre del ODS --}}
                                            <div class="text-white fw-normal mt-1"
                                                style="font-size: 0.6rem; line-height: 1.1; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                                {{ $od->nombre_corto }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- ESTILOS CSS --}}
    <style>
        .ods-bloqueado {
            cursor: default;
            overflow-y: auto;

        }

        .ods-checkbox:checked+.ods-card {
            opacity: 1 !important;
            transform: scale(1.1);
            border: 2px solid #333;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Bloqueamos la interacción SOLO en las tarjetas internas */
        .ods-bloqueado .ods-card-container {
            pointer-events: none;
            /* Aquí sí bloqueamos el clic en la opción */
        }

        .ods-card {
            aspect-ratio: 1/1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    {{-- SCRIPTS JS --}}
    @section('scripts')
        <script>
            $(document).ready(function() {
                // Usamos JQuery para asegurar compatibilidad con el resto de tus scripts
                $('#btn-guardar-modal').on('click', function(e) {
                    e.preventDefault(); // Bloqueamos cualquier acción por defecto

                    console.log("Iniciando guardado por AJAX...");

                    const $btn = $(this);
                    const $form = $('#form-crear-objetivo');
                    const formData = $form.serialize(); // JQuery captura todos los campos

                    // 1. Feedback visual inmediato
                    $btn.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm"></span> Guardando...');

                    $.ajax({
                        url: "{{ route('estrategico.alineacion.objetivos-estrategicos.store-ajax') }}",
                        method: 'POST',
                        data: formData,
                        // ... tus headers ...
                        success: function(response) {
                            // console.log("Respuesta:", response); // Descomenta si necesitas depurar

                            if (response.success) {
                                // 1. Cerrar modal y limpiar
                                $('#modalCrearObjetivo').modal('hide');
                                $('#form-crear-objetivo')[0].reset();

                                // 2. AGREGAR AL SELECT (CORREGIDO PARA TU RESPUESTA)
                                // Usamos response.id directamente, no response.data.id
                                var nuevoId = response.id;

                                // Usamos response.codigo
                                var codigo = response.codigo || 'OE';

                                // IMPORTANTE: Tu servidor devuelve 'descripcion', no 'nombre'
                                var textoNombre = response.descripcion || response.nombre ||
                                    'Nuevo Objetivo';

                                // Construimos el texto:  Cubrir la generación..."
                                var nuevoTexto = codigo + ' - ' + textoNombre;

                                // 3. Crear la opción y seleccionarla automáticamente
                                var nuevaOpcion = new Option(nuevoTexto, nuevoId, true, true);
                                $('#select-obj-estrategico').append(nuevaOpcion).trigger('change');

                                alert('Guardado y seleccionado con éxito');
                            }
                        },
                        error: function(xhr) {
                            // Lógica de errores (validaciones 422 o errores 500)
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                let msg = Object.values(errors).flat().join('\n');
                                alert("Validación:\n" + msg);
                            } else {
                                alert('Error en el servidor');
                            }
                        },
                        complete: function() {
                            // ESTO ES LO MÁS IMPORTANTE:
                            // Se ejecuta SIEMPRE al terminar la petición.
                            // Aquí devolvemos el botón a su estado original.
                            $('#btn-guardar-modal').prop('disabled', false).text('Guardar');
                        }
                    });
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                const nacionalSelect = document.getElementById('select-nacional');
                const metaSelect = document.getElementById('select-meta');
                const opcionesMetasOriginales = Array.from(metaSelect.options);

                // --- FUNCIÓN PARA ILUMINAR LAS TARJETAS ODS (Formulario Principal) ---
                function iluminarTarjetaOds(odsId) {
                    // Limpiamos todas primero
                    document.querySelectorAll('.ods-checkbox').forEach(input => input.checked = false);
                    document.querySelectorAll('.ods-card').forEach(card => {
                        card.style.opacity = '0.3';
                        card.style.transform = 'scale(1)';
                        card.style.border = 'none';
                    });

                    if (odsId) {
                        document.getElementById('input_ods_oculto').value = odsId;
                        const targetInput = document.getElementById('ods_' + odsId);
                        if (targetInput) {
                            targetInput.checked = true;
                            const cardVisual = targetInput.nextElementSibling;
                            cardVisual.style.opacity = '1';
                            cardVisual.style.transform = 'scale(1.1)';
                            cardVisual.style.border = '2px solid #333';
                        }
                    }
                }

                // --- FILTRAR METAS EN FORMULARIO PRINCIPAL ---
                nacionalSelect.addEventListener('change', function() {
                    const idObjetivo = this.value;
                    metaSelect.innerHTML = '<option value="">Seleccione una Meta...</option>';
                    metaSelect.disabled = !idObjetivo;

                    if (idObjetivo) {
                        opcionesMetasOriginales.forEach(opcion => {
                            if (opcion.getAttribute('data-objetivo-id') == idObjetivo) {
                                metaSelect.add(opcion.cloneNode(true));
                            }
                        });
                    }
                    iluminarTarjetaOds(null); // Limpiar ODS al cambiar de objetivo
                });

                // --- AL SELECCIONAR META, SE MARCA EL ODS (Formulario Principal) ---
                metaSelect.addEventListener('change', function() {
                    const odsId = this.options[this.selectedIndex].getAttribute('data-ods-id');
                    iluminarTarjetaOds(odsId);
                });

                // =========================================================
                // LÓGICA DEL MODAL DE EDICIÓN
                // =========================================================

                // Al abrir el modal (clic en editar)
                $('.btn-editar').click(function() {
                    const id = $(this).data('id');
                    const objEst = $(this).data('obj-est');
                    const objNac = $(this).data('obj-nac');
                    const meta = $(this).data('meta');
                    const ods = $(this).data('ods');

                    const url = "{{ route('estrategico.alineacion.actualizar', 0) }}";
                    $('#formEditarAlineacion').attr('action', url.replace('0', id));

                    $('#edit_objetivo_estrategico').val(objEst);
                    $('#edit_objetivo_nacional').val(objNac).trigger('change');

                    // Esperamos a que el filtro de abajo termine para poner la meta
                    setTimeout(() => {
                        $('#edit_meta_pnd').val(meta);
                        $('#edit_ods_id').val(ods);
                    }, 200);
                });

                // Filtrar metas dentro del MODAL
                $('#edit_objetivo_nacional').on('change', function() {
                    const objetivoId = $(this).val();
                    $('#edit_meta_pnd option').each(function() {
                        const padreId = $(this).data('objetivo-id');
                        if (padreId == objetivoId || $(this).val() == "") {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                    $('#edit_meta_pnd').val(''); // Resetear meta al cambiar objetivo
                });

                // Al cambiar la meta en el MODAL, actualizar el ID del ODS oculto
                $('#edit_meta_pnd').on('change', function() {
                    const odsId = $(this).find(':selected').data('ods-id');
                    $('#edit_ods_id').val(odsId);
                });
            });

            //Para activar el modal de edicion de alineaciones
            // --- FUNCIÓN PARA ILUMINAR TARJETAS EN EL MODAL ---
            function iluminarOdsModal(odsId) {
                // 1. Resetear todas las tarjetas del modal (clase .ods-card-edit)
                document.querySelectorAll('.ods-card-edit').forEach(card => {
                    card.style.opacity = '0.2';
                    card.style.transform = 'scale(1)';
                    card.style.border = '1px solid transparent';
                    card.style.boxShadow = 'none';
                });

                // 2. Iluminar la tarjeta correspondiente
                if (odsId) {
                    $('#edit_ods_id').val(odsId); // Seteamos el valor al input oculto
                    const card = document.getElementById('edit_ods_card_' + odsId);

                    if (card) {
                        card.style.opacity = '1';
                        card.style.transform = 'scale(1.05)';
                        card.style.border = '2px solid #333';
                        card.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';

                        // Hacer scroll automático dentro del modal hacia el ODS seleccionado
                        card.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest'
                        });
                    }
                }
            }

            // Evento: Cuando el usuario cambia la Meta en el Modal
            $('#edit_meta_pnd').on('change', function() {
                const odsId = $(this).find(':selected').data('ods-id');
                iluminarOdsModal(odsId);
            });

            // --- ACTUALIZAR EVENTOS DEL MODAL ---

            // A. Cuando se abre el modal (clic en el lápiz)
            $('.btn-editar').click(function() {
                const id = $(this).data('id');
                const objEst = $(this).data('obj-est');
                const objNac = $(this).data('obj-nac');
                const meta = $(this).data('meta');
                const ods = $(this).data('ods');

                const url = "{{ route('estrategico.alineacion.actualizar', 0) }}";
                $('#formEditarAlineacion').attr('action', url.replace('0', id));

                $('#edit_objetivo_estrategico').val(objEst);

                // Al cambiar objetivo nacional, disparamos el filtro de metas
                $('#edit_objetivo_nacional').val(objNac).trigger('change');

                setTimeout(() => {
                    $('#edit_meta_pnd').val(meta);
                    iluminarOdsModal(ods); // Iluminamos el ODS que ya tenía guardado
                }, 300);
            });

            // B. Cuando el usuario cambia la meta MANUALMENTE en el modal
            $('#edit_meta_pnd').on('change', function() {
                const odsId = $(this).find(':selected').data('ods-id');
                iluminarOdsModal(odsId); // Se ilumina automáticamente al cambiar la meta
            });
        </script>
    @endsection
@endsection
