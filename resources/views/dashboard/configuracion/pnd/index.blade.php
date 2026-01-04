@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Catálogo de Objetivos Nacionales (PND)</h1>
            <p class="text-muted small mb-0">Plan Nacional de Desarrollo </p>
        </div>
        {{-- Boton nuevo Objetvivo --}}
        <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreatePnd">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nuevo Objetivo
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert"
            id="success-alert">
            <div class="d-flex align-items-center">
                {{-- Icono dinámico --}}
                <i data-feather="check-circle" class="me-2"></i>
                <div>
                    {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <script>
            // Hacer que el mensaje desaparezca solo tras 4 segundos
            setTimeout(function() {
                var alert = document.getElementById('success-alert');
                if (alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 4000);
        </script>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4" style="width: 100px;">Código</th>
                            <th>Objetivo / Descripción</th>
                            <th>Eje / Periodo</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pnd as $item)
                            <tr>
                                <td class="px-4">
                                    <span class="badge bg-dark px-3 py-2 shadow-sm">
                                        {{ $item->codigo_objetivo }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ Str::limit($item->descripcion_objetivo, 100) }}</div>
                                    {{-- <small class="text-muted">ID: {{ $item->id_objetivo_nacional }}</small> --}}
                                </td>
                                <td>
                                    <div class="small"><span class="badge bg-info text-dark">
                                            @if ($item->relEje)
                                                {{ $item->relEje->nombre_eje }}
                                            @else
                                                <span class="text-muted">No asignado</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="small text-muted">
                                        <i class="far fa-calendar-alt"></i> Vigencia: {{ $item->periodo_inicio }} -
                                        {{ $item->periodo_fin }}
                                    </div>
                                </td>

                                <td class="text-center">
                                    @if ($item->estado == '1')
                                        <span class="badge rounded-pill bg-success" style="opacity: 0.8;">Activo</span>
                                    @else
                                        <span class="badge rounded-pill bg-danger" style="opacity: 0.8;">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-end px-4">
                                    <div class="btn-group shadow-sm">
                                        <button type="button" class="btn btn-sm btn-white text-warning border edit-pnd-btn"
                                            data-bs-toggle="modal" data-bs-target="#modalEditPnd"
                                            data-id="{{ $item->id_objetivo_nacional }}"
                                            data-codigo="{{ $item->codigo_objetivo }}"
                                            data-descripcion="{{ $item->descripcion_objetivo }}"
                                            data-eje="{{ $item->id_eje }}" data-inicio="{{ $item->periodo_inicio }}"
                                            data-fin="{{ $item->periodo_fin }}" data-estado="{{ $item->estado }}">
                                            <span data-feather="edit-2" style="width: 16px; height: 16px;"></span>
                                        </button>

                                        <form
                                            action="{{ route('configuracion.pnd.destroy', $item->id_objetivo_nacional) }}"
                                            method="POST" class="d-inline form-eliminar">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn btn-sm btn-white text-danger border btn-delete shadow-sm">
                                                <span data-feather="trash-2" style="width: 16px; height: 16px;"></span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    No hay Objetivos Nacionales registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL PARA EDICIÓN DE PND --}}
    <div class="modal fade" id="modalEditPnd" tabindex="-1" aria-labelledby="modalEditPndLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="modalEditPndLabel">Editar Objetivo Nacional
                        <span id="label_codigo_pnd" class="badge bg-primary ms-2"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditPnd" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Código del Objetivo</label>
                                <input type="text" name="codigo_objetivo" id="edit_pnd_codigo" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Eje Estratégico</label>
                                <select name="id_eje" id="edit_pnd_eje" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    @foreach ($ejes as $eje)
                                        <option value="{{ $eje->id_eje }}">{{ $eje->nombre_eje }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Año Inicio</label>
                                <input type="number" name="periodo_inicio" id="edit_pnd_inicio" class="form-control"
                                    placeholder="Ej: 2021">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Año Fin</label>
                                <input type="number" name="periodo_fin" id="edit_pnd_fin" class="form-control"
                                    placeholder="Ej: 2025">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción del Objetivo</label>
                            <textarea name="descripcion_objetivo" id="edit_pnd_descripcion" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Estado</label>
                            <select name="estado" id="edit_pnd_estado" class="form-select">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- MODAL PARA CREAR NUEVO OBJETIVO --}}
    <div class="modal fade" id="modalCreatePnd" tabindex="-1" aria-labelledby="modalCreatePndLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalCreatePndLabel">
                        <i class="fas fa-plus-circle me-2"></i>Registrar Nuevo Objetivo Nacional
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('configuracion.pnd.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Código del Objetivo</label>
                                <input type="text" name="codigo_objetivo" class="form-control"
                                    placeholder="Ej: OBJ-01" required>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">Eje Estratégico</label>
                                <select name="id_eje" class="form-select" required>
                                    <option value="" selected disabled>Seleccione un eje...</option>
                                    @foreach ($ejes as $eje)
                                        <option value="{{ $eje->id_eje }}">{{ $eje->nombre_eje }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Año Inicio</label>
                                <input type="number" name="periodo_inicio" class="form-control" placeholder="2021"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Año Fin</label>
                                <input type="number" name="periodo_fin" class="form-control" placeholder="2025"
                                    required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción del Objetivo</label>
                            <textarea name="descripcion_objetivo" class="form-control" rows="4"
                                placeholder="Escriba la descripción detallada del objetivo..." required></textarea>
                        </div>

                        {{-- El estado por defecto será Activo (1) --}}
                        <input type="hidden" name="estado" value="1">
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar Objetivo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('click', function(event) {
            const btn = event.target.closest('.edit-pnd-btn');

            if (btn) {
                // 1. Extraer datos
                const id = btn.getAttribute('data-id');
                const codigo = btn.getAttribute('data-codigo');
                const descripcion = btn.getAttribute('data-descripcion');

                const inputDes = document.getElementById('edit_pnd_descripcion');
                if (inputDes) {
                    // Probamos primero con .value, y si no, con .innerHTML
                    inputDes.value = descripcion || '';
                    //console.log("Descripción cargada:", descripcion); // Mira si esto sale en F12
                }
                const eje = btn.getAttribute('data-eje');
                const inicio = btn.getAttribute('data-inicio');
                const fin = btn.getAttribute('data-fin');
                const estado = btn.getAttribute('data-estado');
                const inEst = document.getElementById('edit_pnd_estado');
                if (inEst) inEst.value = estado;

                const ejeId = btn.getAttribute('data-eje');
                const selectEje = document.getElementById('edit_pnd_eje');

                if (selectEje) {
                    selectEje.value = ejeId;
                }
                // 2. Referencias e Inyección
                document.getElementById('formEditPnd').action = "/configuracion/pnd/" + id;
                document.getElementById('edit_pnd_codigo').value = codigo;
                document.getElementById('edit_pnd_descripcion').value = descripcion;
                document.getElementById('edit_pnd_eje').value = eje;
                document.getElementById('edit_pnd_inicio').value = inicio;
                document.getElementById('edit_pnd_fin').value = fin;
                document.getElementById('edit_pnd_estado').value = estado;


                // Actualizar etiqueta del código en el título si la tienes
                const label = document.getElementById('label_codigo_pnd');
                if (label) label.innerText = codigo;
            }
        });
        document.addEventListener('click', function(event) {
            const deleteBtn = event.target.closest('.btn-delete');

            if (deleteBtn) {
                event.preventDefault(); // Evita el envío inmediato
                const form = deleteBtn.closest('.form-eliminar');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "El registro se marcará como eliminado.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b', // Rojo Bootstrap
                    cancelButtonColor: '#858796', // Gris Bootstrap
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Envía el formulario si confirmó
                    }
                });
            }
        });
    </script>
    @if (session('success'))
        Swal.fire({
        icon: 'success',
        title: '¡Hecho!',
        text: "{{ session('success') }}",
        timer: 3000,
        showConfirmButton: false
        });
    @endif
@endsection
