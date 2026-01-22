@extends('layouts.app')

@section('titulo', 'Alineación Estratégica')

@section('content')
    <style>
        .text-slate-800 {
            color: #1e293b;
        }

        .text-slate-500 {
            color: #64748b;
        }

        .bg-light-gray {
            background-color: #f8fafc;
        }

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
            color: #64748b;
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

        .ods-bloqueado {
            cursor: default;
            overflow-y: auto;
            background-color: #f8fafc;
        }

        .ods-checkbox:checked+.ods-card {
            opacity: 1 !important;
            transform: scale(1.05);
            border: 2px solid #0f172a;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .ods-bloqueado .ods-card-container {
            pointer-events: none;
        }

        .ods-card {
            aspect-ratio: 1/1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
    </style>
    <x-layouts.header_content titulo="Planificación y Alineación" subtitulo="Gestión de Objetivos Institucionales">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('institucional.organizaciones.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>

    </x-layouts.header_content>
    <div class="container-fluid py-4">
        @include('partials.mensajes')
        <div class="row justify-content-end align-items-end mb-2">
            <div class="col-md-4">
                <form action="{{ route('estrategico.alineacion.general.index') }}" method="GET">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="busqueda" id="inputBusqueda"
                            class="form-control border-start-0 border-end-0 shadow-none"
                            placeholder="Buscar por nombre, código..." value="{{ request('busqueda') }}">

                        <button type="button" id="btnLimpiarBusqueda"
                            class="btn bg-white border border-start-0 text-danger"
                            style="display: none; z-index: 1000 !important;">
                            <i class="fas fa-times"></i>
                        </button>
                        <button class="btn btn-secondary" type="submit">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
        <p class="text-slate-500 mb-0 small">
            Gestión para: <strong class="text-dark">{{ $organizacion->nom_organizacion }}</strong>
        </p>

        <div class="row g-4">
            {{--  TABLA DE ALINEACIONES --}}
            <div class="col-lg-12">
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
                                        <th class="ps-4 py-3 text-secondary small text-uppercase">Usuario
                                        </th>
                                        <th class="ps-4 py-3 text-secondary small text-uppercase">Objetivo Estrategico
                                        </th>
                                        <th class="py-3 text-secondary small text-uppercase">Meta Nacional</th>
                                        <th class="text-center py-3 text-secondary small text-uppercase">Vinculacion ODS
                                        </th>
                                        <th class="text-end pe-4 py-3 text-secondary small text-uppercase">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($alineaciones as $item)
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <div class="fw-bold text-slate-800 mb-1" style="font-size: 0.9rem;">
                                                    {{ $item->usuario->nombres ?? 'NN' }}
                                                </div>
                                            </td>
                                            <td class="ps-4 py-3">
                                                <div class="fw-bold text-slate-800 mb-1" style="font-size: 0.9rem;">
                                                    {{ $item->objetivoEstrategico->codigo ?? 'OE' }}
                                                </div>
                                                <div class="text-slate-500 small" style="line-height: 1.3;">
                                                    {{ Str::limit($item->objetivoEstrategico->nombre ?? 'Sin Nombre', 50) }}
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <div
                                                    class="badge bg-success bg-opacity-10 text-success border border-success mb-1">
                                                    {{ $item->metaNacional->codigo_meta ?? 'N/A' }}
                                                </div>
                                                <div class="text-muted small fst-italic"
                                                    style="font-size: 0.9rem; line-height: 1.3;">
                                                    {{ Str::limit($item->metaNacional->nombre_meta ?? 'Meta no especificada', 50) }}
                                                </div>
                                            </td>
                                            <td class="text-center py-3">
                                                @if ($item->metaNacional && $item->metaNacional->ods->count() > 0)
                                                    @foreach ($item->metaNacional->ods as $odsitem)
                                                        <span class="badge rounded-pill text-white"
                                                            style="background-color: {{ $odsitem->color_hex }}; font-weight: normal;">
                                                            {{ $odsitem->codigo }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                            {{-- ACCIONES --}}
                                            <td class="text-end pe-4 py-3">
                                                <div class="btn-group">
                                                    <a href="{{ route('estrategico.alineacion.show', $item->id) }}"
                                                        class="btn btn-sm btn-light border text-info"
                                                        title="Ver Ficha Técnica" data-bs-toggle="tooltip">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button"
                                                        class="btn btn-sm btn-light border btn-editar text-primary"
                                                        title="Editar" data-id="{{ $item->id }}"
                                                        data-nombre-obj="{{ $item->objetivoEstrategico->nombre }}"
                                                        data-meta-id="{{ $item->meta_nacional_id }}" data-bs-toggle="modal"
                                                        data-bs-target="#modalEditarAlineacion">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>
                                                    <form
                                                        action="{{ route('estrategico.alineacion.eliminar', $item->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('¿Confirma eliminar esta alineación?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-light border text-danger"
                                                            title="Desvincular">
                                                            <i class="fas fa-unlink"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <div class="text-muted mb-2"><i class="far fa-folder-open fa-3x"></i>
                                                </div>
                                                <p class="text-slate-500 small mb-0">No hay alineaciones registradas para
                                                    esta organización.</p>
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
    {{-- MODAL EDITAR ALINEACIÓN  --}}
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
                            <div class="mb-3">
                                <label class="form-label fw-bold">Objetivo Estratégico</label>
                                <input type="text" id="modal_nombre_objetivo" class="form-control bg-light" readonly>
                                <div class="form-text">Esta editando la alineación para este objetivo.</div>
                            </div>
                            {{-- Meta Nacional --}}
                            <div class="mb-4">
                                <select name="meta_nacional_id" id="edit_meta_pnd" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    @foreach ($metas as $meta)
                                        <option value="{{ $meta->id_meta_nacional }}"
                                            data-ods-ids="{{ $meta->ods->pluck('id_ods') }}">
                                            {{ $meta->codigo_meta }}--{{ Str::limit($meta->nombre_meta, 90) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{--  ODS Visual --}}
                            <div>
                                <label class="form-label">Impacto ODS (Vinculado)</label>
                                <input type="hidden" id="edit_ods_id">
                                <div class="row row-cols-6 g-2 p-2 bg-light border rounded"
                                    style="max-height: 150px; overflow-y: auto;">
                                    @foreach ($ods as $od)
                                        <div class="col">
                                            <div class="ods-card-edit p-1 rounded d-flex align-items-center justify-content-center"
                                                id="edit_ods_card_{{ $od->id_ods }}" title="{{ $od->nombre }}"
                                                data-bs-toggle="tooltip"
                                                style="background-color: {{ $od->color_hex }}; opacity: 0.2; height: 50px; transition: all 0.3s;">
                                                <small class="text-white fw-bold">{{ $od->codigo }}</small>
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

                // --- FUNCIÓN PARA ILUMINAR MÚLTIPLES ODS ---
                function iluminarOdsModal(idsOds) {
                    $('.ods-card-edit').css({
                        opacity: '0.2',
                        transform: 'scale(1)',
                        border: 'none',
                        boxShadow: 'none'
                    });

                    // Iluminar SOLO los que están en el array
                    if (idsOds && Array.isArray(idsOds)) {
                        idsOds.forEach(function(id) {
                            const $card = $('#edit_ods_card_' + id);
                            $card.css({
                                opacity: '1',
                                transform: 'scale(1.1)',
                                border: '2px solid #333',
                                boxShadow: '0 4px 6px rgba(0,0,0,0.1)'
                            });
                        });
                    }
                }

                // ---EVENTO AL CAMBIAR LA META (Desplegable) ---
                $('#edit_meta_pnd').on('change', function() {
                    // Obtenemos el array de IDs. jQuery lo parsea automáticamente si es JSON válido
                    const odsIds = $(this).find(':selected').data('ods-ids');
                    iluminarOdsModal(odsIds);
                });

                // ---  EVENTO AL ABRIR EL MODAL (Botón Editar) ---
                $('.btn-editar').click(function() {
                    // Obtener datos del botón
                    var idAlineacion = $(this).data('id');
                    var nombreObjetivo = $(this).data('nombre-obj');
                    var idMetaActual = $(this).data('meta-id');

                    // Actualizar Action del Formulario
                    const urlBase = "{{ route('estrategico.alineacion.actualizar', 0) }}";
                    $('#formEditarAlineacion').attr('action', urlBase.replace('0', idAlineacion));

                    // Llenar campos visuales
                    $('#modal_nombre_objetivo').val(nombreObjetivo);

                    // Seleccionar la meta actual y disparar el cambio para que se iluminen los ODS
                    //  Si la meta no existe en la lista, el select se quedará en blanco
                    $('#edit_meta_pnd').val(idMetaActual).trigger('change');
                });

            });
            document.addEventListener('DOMContentLoaded', function() {
                const inputBusqueda = document.getElementById('inputBusqueda');
                const btnLimpiar = document.getElementById('btnLimpiarBusqueda');

                // Función para mostrar/ocultar el botón
                function toggleLimpiarButton() {
                    if (inputBusqueda.value.trim() !== '') {
                        btnLimpiar.style.display = 'block';
                    } else {
                        btnLimpiar.style.display = 'none';
                    }
                }

                // Ejecutar al cargar (por si ya vienes de una búsqueda)
                toggleLimpiarButton();

                // Ejecutar cada vez que el usuario escribe
                inputBusqueda.addEventListener('input', toggleLimpiarButton);

                // Acción al hacer clic en la "X"
                btnLimpiar.addEventListener('click', function() {
                    inputBusqueda.value = '';
                    toggleLimpiarButton();

                    inputBusqueda.closest('form').submit();
                });
            });
        </script>
    @endsection
@endsection
