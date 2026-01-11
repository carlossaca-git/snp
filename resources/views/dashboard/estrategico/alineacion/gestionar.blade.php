@extends('layouts.app')

@section('titulo', 'Alineación Estratégica')

@section('content')
    <style>
        .text-slate-800 { color: #1e293b; }
        .text-slate-500 { color: #64748b; }
        .bg-light-gray { background-color: #f8fafc; }

        .card-clean {
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            background-color: #fff;
        }

        .form-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            color: #64748b; /* Slate 500 */
            margin-bottom: 0.25rem;
        }

        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background-color: #e2e8f0;
            color: #475569;
            border-radius: 50%;
            font-size: 0.75rem;
            font-weight: bold;
            margin-right: 0.5rem;
        }

        /* ODS Styles */
        .ods-bloqueado { cursor: default; overflow-y: auto; background-color: #f8fafc; }
        .ods-checkbox:checked + .ods-card {
            opacity: 1 !important;
            transform: scale(1.05);
            border: 2px solid #0f172a;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .ods-bloqueado .ods-card-container { pointer-events: none; }
        .ods-card { aspect-ratio: 1/1; display: flex; align-items: center; justify-content: center; border-radius: 8px; }
    </style>

    <div class="container-fluid py-4">

        {{-- 1. ENCABEZADO --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold text-slate-800 mb-0">Planificación y Alineación</h1>
                <p class="text-slate-500 mb-0 small">
                    Gestión para: <strong class="text-dark">{{ $organizacion->nom_organizacion }}</strong>
                </p>
            </div>
            <a href="{{ route('institucional.organizaciones.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>

        {{-- MENSAJES DE SESIÓN --}}
        @if (session('status'))
            <div class="alert alert-success border-0 shadow-sm d-flex align-items-center" role="alert">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <div>{{ session('status') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">

            {{-- ============================================================== --}}
            {{-- COLUMNA IZQUIERDA: FORMULARIO DE ALINEACIÓN --}}
            {{-- ============================================================== --}}
            <div class="col-lg-5">
                <div class="card card-clean h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="m-0 fw-bold text-slate-800">
                            <i class="fas fa-plus-circle me-2 text-primary"></i>Nueva Alineación
                        </h6>
                    </div>
                    <div class="card-body p-4 bg-light-gray">
                        <form action="{{ route('estrategico.alineacion.guardar', $organizacion->id_organizacion) }}" method="POST" id="formAlineacion">
                            @csrf

                            {{-- PASO 1 --}}
                            <div class="card border mb-3 shadow-sm">
                                <div class="card-body p-3">
                                    <div class="mb-2 d-flex align-items-center">
                                        <span class="step-number">1</span>
                                        <span class="fw-bold text-dark small text-uppercase">Objetivo Institucional</span>
                                    </div>

                                    <label class="form-label">Seleccione Objetivo Estratégico</label>
                                    <div class="input-group">
                                        <select name="objetivo_estrategico_id" id="select-obj-estrategico" class="form-select bg-white" required>
                                            <option value="">Seleccione...</option>
                                            @foreach ($objetivosEstrategicos as $objEst)
                                                <option value="{{ $objEst->id_objetivo_estrategico }}">
                                                    {{ $objEst->codigo ?? 'OE' }} - {{ Str::limit($objEst->nombre, 50) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-light border" data-bs-toggle="modal" data-bs-target="#modalCrearObjetivo" title="Crear Nuevo">
                                            <i class="fas fa-plus text-primary"></i>
                                        </button>
                                    </div>
                                    @error('objetivo_estrategico_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            {{-- PASO 2 --}}
                            <div class="card border mb-3 shadow-sm">
                                <div class="card-body p-3">
                                    <div class="mb-2 d-flex align-items-center">
                                        <span class="step-number">2</span>
                                        <span class="fw-bold text-dark small text-uppercase">Alineación Nacional (PND)</span>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Objetivo Nacional PND <span class="text-danger">*</span></label>
                                        <select name="objetivo_nacional_id" id="select-nacional" class="form-select bg-white" required>
                                            <option value="">- Seleccione -</option>
                                            @foreach ($objetivosNacionales as $obj)
                                                <option value="{{ $obj->id_objetivo_nacional }}" data-ods-id="{{ $obj->id_ods }}" {{ old('objetivo_nacional_id') == $obj->id_objetivo_nacional ? 'selected' : '' }}>
                                                    {{ $obj->codigo_objetivo }} - {{ Str::limit($obj->descripcion_objetivo, 60) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="form-label">Meta Nacional Asociada <span class="text-danger">*</span></label>
                                        <select name="id_meta_pnd" id="select-meta" class="form-select bg-white" disabled required>
                                            <option value="">Primero seleccione PND...</option>
                                            @foreach ($metasNacionales as $meta)
                                                <option value="{{ $meta->id_meta_pnd }}"
                                                    data-objetivo-id="{{ $meta->id_objetivo_nacional }}"
                                                    data-ods-id="{{ $meta->id_ods }}"
                                                    data-ods-nombre="{{ $meta->ods->descripcion ?? 'Sin descripción' }}">
                                                    {{ Str::limit($meta->descripcion, 80) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- PASO 3 (ODS) --}}
                            <div class="card border mb-4 shadow-sm">
                                <div class="card-body p-3">
                                    <div class="mb-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <span class="step-number">3</span>
                                            <span class="fw-bold text-dark small text-uppercase">Impacto ODS (Automático)</span>
                                        </div>
                                    </div>

                                    <input type="hidden" name="ods_id" id="input_ods_oculto" value="">

                                    <div class="row row-cols-5 g-2 p-2 bg-light border rounded ods-bloqueado">
                                        @foreach ($ods as $od)
                                            <div class="col">
                                                <label class="ods-card-container w-100 mb-0" for="ods_{{ $od->id_ods }}">
                                                    <input class="form-check-input ods-checkbox d-none" type="radio" name="ods_id" value="{{ $od->id_ods }}" id="ods_{{ $od->id_ods }}">
                                                    <div class="ods-card" style="background-color: {{ $od->color_hex ?? '#ccc' }}; opacity: 0.3; transition: all 0.3s;">
                                                        <div class="text-center w-100">
                                                            <div class="fw-bold text-white small">{{ $od->numero }}</div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-secondary shadow-sm py-2 fw-bold">
                                    <i class="fas fa-save me-2"></i> Guardar Alineación
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ============================================================== --}}
            {{-- COLUMNA DERECHA: TABLA DE ALINEACIONES --}}
            {{-- ============================================================== --}}
            <div class="col-lg-7">
                <div class="card card-clean h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-slate-800">
                            <i class="fas fa-list-ul me-2 text-secondary"></i>Alineaciones Registradas
                        </h6>
                        <span class="badge bg-light text-dark border">{{ $alineaciones->count() }} Registros</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light-gray">
                                    <tr>
                                        <th class="ps-4 py-3 text-secondary small text-uppercase">Objetivo Institucional</th>
                                        <th class="py-3 text-secondary small text-uppercase">PND / Meta</th>
                                        <th class="text-center py-3 text-secondary small text-uppercase">ODS</th>
                                        <th class="text-end pe-4 py-3 text-secondary small text-uppercase">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($alineaciones as $item)
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <div class="fw-bold text-slate-800 mb-1" style="font-size: 0.9rem;">
                                                    {{ $item->objetivoEstrategico->codigo ?? 'OE' }}
                                                </div>
                                                <div class="text-slate-500 small" style="line-height: 1.3;">
                                                    {{ Str::limit($item->objetivoEstrategico->nombre ?? 'Sin Nombre', 50) }}
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <div class="badge bg-success bg-opacity-10 text-success border border-success mb-1">
                                                    {{ $item->objetivoNacional->codigo_objetivo ?? 'N/A' }}
                                                </div>
                                                <div class="text-muted small fst-italic" style="font-size: 0.8rem; line-height: 1.3;">
                                                    {{ Str::limit($item->metaPnd->descripcion ?? 'Meta no especificada', 50) }}
                                                </div>
                                                <small class="text-danger">ID Meta: {{ $item->id_meta}}</small>
                                            </td>
                                            <td class="text-center py-3">
                                                @if ($item->ods)
                                                    <span class="badge rounded-pill text-white" style="background-color: {{ $item->ods->color_hex }}; font-weight: normal;">
                                                        ODS {{ $item->ods->numero }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4 py-3">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-light border btn-editar text-primary"
                                                        title="Editar"
                                                        data-id="{{ $item->id }}"
                                                        data-obj-est="{{ $item->objetivo_estrategico_id }}"
                                                        data-obj-nac="{{ $item->objetivo_nacional_id }}"
                                                        data-meta="{{ $item->meta_pnd_id }}"
                                                        data-ods="{{ $item->ods_id }}"
                                                        data-bs-toggle="modal" data-bs-target="#modalEditarAlineacion">
                                                        <i class="fas fa-pencil-alt" data-feather="edit-2"></i>
                                                    </button>
                                                    <form action="{{ route('estrategico.alineacion.eliminar', $item->id) }}" method="POST" class="d-inline"
                                                        onsubmit="return confirm('¿Confirma eliminar esta alineación?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Eliminar">
                                                            <i class="fas fa-trash-alt" data-feather="trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <div class="text-muted mb-2"><i class="far fa-folder-open fa-3x"></i></div>
                                                <p class="text-slate-500 small mb-0">No hay alineaciones registradas para esta organización.</p>
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

    {{-- MODAL CREAR OBJETIVO (Estilo Limpio) --}}
    <div class="modal fade" id="modalCrearObjetivo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title fw-bold text-slate-800">Nuevo Objetivo Estratégico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light-gray">
                    <form id="form-crear-objetivo">
                        @csrf
                        <input type="hidden" name="id_organizacion" value="{{ $organizacion->id_organizacion }}">
                        <div class="bg-white p-3 rounded border shadow-sm">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Código</label>
                                    <input type="text" name="codigo" class="form-control" placeholder="Ej: OE-01" required>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Indicador</label>
                                    <input type="text" name="indicador" class="form-control" placeholder="Ej: % de gestión">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Nombre del Objetivo</label>
                                    <textarea name="nombre" class="form-control" rows="3" required placeholder="Descripción del objetivo..."></textarea>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Inicio</label>
                                    <input type="date" name="fecha_inicio" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Fin</label>
                                    <input type="date" name="fecha_fin" class="form-control">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-white border-top">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary fw-bold" id="btn-guardar-modal">Guardar Objetivo</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL EDITAR ALINEACIÓN (Estilo Limpio) --}}
    <div class="modal fade" id="modalEditarAlineacion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formEditarAlineacion" action="#" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header bg-white border-bottom">
                        <h5 class="modal-title fw-bold text-slate-800">Editar Alineación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body bg-light-gray">
                        <div class="bg-white p-4 rounded border shadow-sm">
                            {{-- 1. Objetivo Estratégico --}}
                            <div class="mb-3">
                                <label class="form-label">Objetivo Estratégico</label>
                                <select name="objetivo_estrategico_id" id="edit_objetivo_estrategico" class="form-select" required>
                                    @foreach ($objetivosEstrategicos as $objEst)
                                        <option value="{{ $objEst->id_objetivo_estrategico }}">
                                            {{ $objEst->codigo }} - {{ Str::limit($objEst->nombre, 60) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 2. Objetivo Nacional --}}
                            <div class="mb-3">
                                <label class="form-label">Objetivo Nacional (PND)</label>
                                <select name="objetivo_nacional_id" id="edit_objetivo_nacional" class="form-select" required>
                                    @foreach ($objetivosNacionales as $objNac)
                                        <option value="{{ $objNac->id_objetivo_nacional }}">
                                            {{ Str::limit($objNac->descripcion_objetivo, 80) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 3. Meta Nacional --}}
                            <div class="mb-4">
                                <label class="form-label">Meta PND</label>
                                <select name="id_meta_pnd" id="edit_meta_pnd" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    @foreach ($metasNacionales as $meta)
                                        <option value="{{ $meta->id_meta_pnd }}" data-objetivo-id="{{ $meta->id_objetivo_nacional }}" data-ods-id="{{ $meta->id_ods }}">
                                            {{ Str::limit($meta->descripcion, 90) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 4. ODS Visual --}}
                            <div>
                                <label class="form-label">Impacto ODS (Vinculado)</label>
                                <input type="hidden" name="ods_id" id="edit_ods_id">
                                <div class="row row-cols-6 g-2 p-2 bg-light border rounded" style="max-height: 150px; overflow-y: auto;">
                                    @foreach ($ods as $od)
                                        <div class="col">
                                            <div class="ods-card-edit p-1 rounded d-flex align-items-center justify-content-center"
                                                id="edit_ods_card_{{ $od->id_ods }}"
                                                style="background-color: {{ $od->color_hex }}; opacity: 0.2; height: 50px; transition: all 0.3s;">
                                                <small class="text-white fw-bold">{{ $od->numero }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-top">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning fw-bold text-dark">Actualizar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPTS JS --}}
    @section('scripts')
        <script>
            $(document).ready(function() {
                // (Tu script AJAX para crear objetivo se mantiene igual)
                $('#btn-guardar-modal').on('click', function(e) {
                    e.preventDefault();
                    const $btn = $(this);
                    const $form = $('#form-crear-objetivo');
                    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Guardando...');

                    $.ajax({
                        url: "{{ route('estrategico.alineacion.objetivos-ajax') }}",
                        method: 'POST',
                        data: $form.serialize(),
                        success: function(response) {
                            if (response.success) {
                                $('#modalCrearObjetivo').modal('hide');
                                $form[0].reset();

                                var nuevoTexto = (response.codigo || 'OE') + ' - ' + (response.descripcion || response.nombre || 'Nuevo');
                                var nuevaOpcion = new Option(nuevoTexto, response.id, true, true);
                                $('#select-obj-estrategico').append(nuevaOpcion).trigger('change');
                            }
                        },
                        error: function(xhr) { alert('Error al guardar'); },
                        complete: function() { $btn.prop('disabled', false).text('Guardar Objetivo'); }
                    });
                });

                // ... (Scripts de Filtrado de Metas y ODS)
                const opcionesMetasOriginales = $('#select-meta option').clone();

                // Función iluminar tarjeta principal
                function iluminarTarjetaOds(odsId) {
                    $('.ods-checkbox').prop('checked', false);
                    $('.ods-card').css({opacity: '0.3', transform: 'scale(1)', border: 'none'});

                    if(odsId) {
                        $('#input_ods_oculto').val(odsId);
                        const $input = $('#ods_' + odsId);
                        $input.prop('checked', true);
                        $input.next('.ods-card').css({
                            opacity: '1', transform: 'scale(1.05)', border: '2px solid #0f172a'
                        });
                    }
                }

                // Filtrar Metas
                $('#select-nacional').on('change', function() {
                    const idObj = $(this).val();
                    const $metaSelect = $('#select-meta');
                    $metaSelect.empty().append('<option value="">Seleccione Meta...</option>');
                    $metaSelect.prop('disabled', !idObj);

                    if(idObj) {
                        opcionesMetasOriginales.each(function() {
                            if($(this).data('objetivo-id') == idObj) $metaSelect.append($(this).clone());
                        });
                    }
                    iluminarTarjetaOds(null);
                });

                // Seleccionar ODS al cambiar Meta
                $('#select-meta').on('change', function() {
                    iluminarTarjetaOds($(this).find(':selected').data('ods-id'));
                });

                // ... (Lógica del Modal de Edición - Mismo script que tenías, solo ajustando selectores si cambió algo)
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

                    setTimeout(() => {
                        $('#edit_meta_pnd').val(meta);
                        iluminarOdsModal(ods);
                    }, 300);
                });

                // Filtro dentro del modal
                $('#edit_objetivo_nacional').change(function(){
                    const id = $(this).val();
                    $('#edit_meta_pnd option').each(function(){
                        const pid = $(this).data('objetivo-id');
                        (pid == id || $(this).val() == "") ? $(this).show() : $(this).hide();
                    });
                    $('#edit_meta_pnd').val('');
                });

                $('#edit_meta_pnd').change(function(){
                    iluminarOdsModal($(this).find(':selected').data('ods-id'));
                });

                function iluminarOdsModal(id) {
                    $('.ods-card-edit').css({opacity: '0.2', transform: 'scale(1)', border: 'none'});
                    if(id) {
                        $('#edit_ods_id').val(id);
                        const $card = $('#edit_ods_card_' + id);
                        $card.css({opacity: '1', transform: 'scale(1.1)', border: '2px solid #333'});
                    }
                }
            });
        </script>
    @endsection
@endsection
