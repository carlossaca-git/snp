@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Ejes Estratégicos</h1>
            <p class="text-muted small mb-0">Pilares fundamentales del Plan Nacional de Desarrollo</p>
        </div>
        <button type="button" class="btn btn-secondary btn-sm shadow-sm d-inline-flex align-items-center"
            data-bs-toggle="modal" data-bs-target="#modalCreateEje">
            <i class="fas fa-layer-group fa-sm text-white-50" data-feather="plus"></i> Nuevo Eje
        </button>
    </div>
    @include('partials.mensajes')
    {{-- PRUEBA TEMPORAL --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Nombre del Eje</th>
                            <th>Descripción</th>
                            <th class="text-center">Objetivos Asoc.</th>
                            <th class="text-center">Periodo</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ejes as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-primary">{{ $item->nombre_eje }}</div>
                                </td>
                                <td class="text-muted small">
                                    {{ Str::limit($item->descripcion, 50) }}
                                </td>
                                <td class="text-center">
                                    {{-- Usamos la relación para contar --}}
                                    <span class="badge bg-light text-dark border">
                                        {{ $item->objetivos_nacionales_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    {{ $item->periodo_inicio }} - {{ $item->periodo_fin }}
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill {{ $item->estado == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $item->estado == 1 ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>

                                <td class="text-end px-4">

                                    @if (!empty($item->url_documento))
                                        {{-- Si el campo tiene contenido, mostramos el botón rojo de PDF --}}
                                        <a href="{{ $item->url_documento }}" target="_blank" class="btn btn-sm btn-danger"
                                            title="Ver Documento">
                                            <i class="fas fa-file-pdf" data-feather="file-text"
                                                style="width: 16px; height: 16px;"></i>
                                        </a>
                                    @else
                                        {{-- Si está vacío, mostramos un icono gris que no hace nada --}}
                                        <span class="text-muted" title="No hay documento cargado">
                                            <i class="fas fa-file-slash opacity-50"></i>
                                        </span>
                                    @endif
                                    {{-- Botones eliminar y editar --}}
                                    <div class="btn-group shadow-sm">
                                        <button type="button" class="btn btn-sm btn-warning btn-edit-eje"
                                            data-id="{{ $item->id_eje }}" data-nombre="{{ $item->nombre_eje }}"
                                            data-descripcion="{{ $item->descripcion }}"
                                            data-url="{{ $item->url_documento }}" data-estado="{{ $item->estado }}"
                                            data-plan="{{ $item->plan->nombre ?? 'Sin Plan' }}">
                                            <i class="fas fa-edit" data-feather="edit-2"
                                                style="width: 16px; height: 16px;"></i>
                                        </button>

                                        <form action="{{ route('catalogos.ejes.destroy', $item->id_eje) }}" method="POST"
                                            class="d-inline form-eliminar">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn btn-sm btn-white text-danger border btn-delete">
                                                <i data-feather="trash-2" style="width: 16px; height: 16px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No hay ejes registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('dashboard.configuracion.ejes.crear')
    {{-- MODAL DE EDICION --}}
    @include('dashboard.configuracion.ejes.editar')
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('click', function(event) {

            // --- 1. LÓGICA PARA EL BOTÓN EDITAR ---
            const btnEdit = event.target.closest('.btn-edit-eje');
            if (btnEdit) {
                //console.log('Editando eje ID:', btnEdit.dataset.id);
                // 1. Extraemos los datos que Laravel imprimió en el botón desde la BD
                const id = btnEdit.getAttribute('data-id');
                const nombre = btnEdit.getAttribute('data-nombre');
                const descripcion = btnEdit.getAttribute('data-descripcion');
                const url = btnEdit.getAttribute('data-url');
                const estado = btnEdit.getAttribute('data-estado');
                const plan = btnEdit.getAttribute('data-plan');

                // 2. Localizamos los inputs por su ID y les asignamos el valor
                document.getElementById('edit_nombre_eje').value = nombre;
                document.getElementById('edit_descripcion_eje').value = descripcion;
                document.getElementById('edit_url_documento').value = url;
                document.getElementById('edit_estado_eje').value = estado;
                document.getElementById('edit_plan_nombre').value = plan;

                // Captura de datos
                const datos = {
                    id: btnEdit.dataset.id,
                    nombre: btnEdit.dataset.nombre,
                    descripcion: btnEdit.dataset.descripcion,
                    url: btnEdit.dataset.url,
                    estado: btnEdit.dataset.estado,
                    plan: btnEdit.dataset.plan
                };

                // Llenado de campos con validación
                const campos = {
                    'edit_nombre_eje': datos.nombre,
                    'edit_descripcion_eje': datos.descripcion,
                    'edit_url_documento': datos.url,
                    'edit_estado_eje': datos.estado,
                    'edit_plan_nombre': datos.plan
                };

                for (let id in campos) {
                    let el = document.getElementById(id);
                    if (el) el.value = campos[id] || '';
                }

                // Actualizar URL del Formulario
                const form = document.getElementById('formEditEje');
                if (form) {
                    form.action = '{{ url('catalogos/ejes') }}/' + datos.id;
                }

                // Abrir Modal (Compatibilidad BS4 y BS5)
                const modalEl = document.getElementById('modalEditEje');
                if (modalEl) {
                    try {
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    } catch (e) {
                        if (typeof jQuery !== 'undefined') $(modalEl).modal('show');
                    }
                }
            }

            // --- 2. LÓGICA PARA EL BOTÓN ELIMINAR ---
            const btnDelete = event.target.closest('.btn-delete');
            if (btnDelete) {
                event.preventDefault(); // Detenemos el envío automático

                const formEliminar = btnDelete.closest('.form-eliminar');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción marcará el eje como inactivo o lo eliminará permanentemente.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Si el usuario confirma, enviamos el formulario
                        formEliminar.submit();
                    }
                });
            }
            // Validamos fechas para que perido fin no sea menor al periodo actual
            document.getElementById('formCreateEje').addEventListener('submit', function(e) {
                const inicio = parseInt(document.getElementById('create_periodo_inicio').value);
                const fin = parseInt(document.getElementById('create_periodo_fin').value);

                if (fin < inicio) {
                    e.preventDefault(); // Detiene el envío del formulario
                    Swal.fire({
                        icon: 'error',
                        title: 'Periodo inválido',
                        text: 'El año de fin no puede ser menor al año de inicio.',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
            @if ($errors->any())
                document.addEventListener('DOMContentLoaded', function() {
                    const modalCreate = bootstrap.Modal.getOrCreateInstance(document.getElementById(
                        'modalCreateEje'));
                    modalCreate.show();
                });
            @endif
        });
    </script>
@endpush
