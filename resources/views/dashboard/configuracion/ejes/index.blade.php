@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Ejes Estratégicos" subtitulo="Pilares fundamentales del Plan Nacional de Desarrollo">
        @if (Auth::user()->tienePermiso('ejes.gestionar'))
            <button type="button" class="btn btn-secondary btn-sm shadow-sm d-inline-flex align-items-center"
                data-bs-toggle="modal" data-bs-target="#modalCreateEje">
                <i class="fas fa-layer-group fa-sm text-white-50" data-feather="plus"></i> Nuevo Eje
            </button>
        @endif
    </x-layouts.header_content>

    @include('partials.mensajes')
    {{-- FORMULARIO DE BUSQUEDA --}}
    <div class="container mx-auto py-8">
        <div class="row justify-content-end align-items-end mb-2">
            <div class="col-md-4">
                <form action="{{ route('catalogos.ejes.index') }}" method="GET">
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
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
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
                                        <span
                                            class="badge rounded-pill {{ $item->estado == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $item->estado == 1 ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    {{-- ACCIONES --}}
                                    <td class="text-end px-4">

                                        @if (!empty($item->url_documento))
                                            {{-- Si el campo tiene contenido, mostramos el botón rojo de PDF --}}
                                            <a href="{{ $item->url_documento }}" target="_blank"
                                                class="btn btn-sm btn-danger" title="Ver Documento">
                                                <i class="fas fa-file-pdf" data-feather="file-text"
                                                    style="width: 16px; height: 16px;"></i>
                                            </a>
                                        @else
                                            {{-- Si está vacío, mostramos un icono gris que no hace nada --}}
                                            <span class="text-muted" title="No hay documento cargado">
                                                <i class="fas fa-file-slash opacity-80" data-feather="file"></i>
                                            </span>
                                        @endif
                                        {{-- Botones eliminar y editar --}}
                                        @if (Auth::user()->tienePermiso('ejes.gestionar'))
                                            <div class="btn-group shadow-sm">
                                                @can('ejes.ver')
                                                    <button type="button" class="btn btn-sm btn-warning btn-edit-eje"
                                                        data-id="{{ $item->id_eje }}" data-nombre="{{ $item->nombre_eje }}"
                                                        data-descripcion="{{ $item->descripcion }}"
                                                        data-url="{{ $item->url_documento }}"
                                                        data-estado="{{ $item->estado }}"
                                                        data-plan="{{ $item->plan->nombre ?? 'Sin Plan' }}">
                                                        <i class="fas fa-edit" data-feather="edit-2"
                                                            style="width: 16px; height: 16px;"></i>
                                                    </button>
                                                @endcan
                                                <form action="{{ route('catalogos.ejes.destroy', $item->id_eje) }}"
                                                    method="POST" class="d-inline form-eliminar">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-sm btn-white text-danger border btn-delete">
                                                        <i class="fas fa-trash" style="width: 16px; height: 16px;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
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
    </div>
    @include('dashboard.configuracion.ejes.crear')
    {{-- MODAL DE EDICION --}}
    @include('dashboard.configuracion.ejes.editar')
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ==========================================
            // 1. LÓGICA DEL BUSCADOR (Corregido)
            // ==========================================
            const inputBusqueda = document.getElementById('inputBusqueda');
            const btnLimpiar = document.getElementById('btnLimpiarBusqueda');

            if (inputBusqueda && btnLimpiar) {
                // Función para mostrar/ocultar
                const toggleLimpiarButton = () => {
                    btnLimpiar.style.display = inputBusqueda.value.trim() !== '' ? 'block' : 'none';
                };

                // Ejecutar al inicio
                toggleLimpiarButton();

                // Ejecutar al escribir
                inputBusqueda.addEventListener('input', toggleLimpiarButton);

                // Ejecutar al dar clic en la X
                btnLimpiar.addEventListener('click', function() {
                    inputBusqueda.value = '';
                    toggleLimpiarButton();
                    inputBusqueda.closest('form').submit();
                });
            }

            // ==========================================
            // 2. VALIDACIÓN FORMULARIO CREAR
            // ==========================================
            const formCreate = document.getElementById('formCreateEje');
            if (formCreate) {
                formCreate.addEventListener('submit', function(e) {
                    const inicio = parseInt(document.getElementById('create_periodo_inicio').value);
                    const fin = parseInt(document.getElementById('create_periodo_fin').value);

                    // Corregido el error de sintaxis "script script"
                    if (fin < inicio) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Periodo inválido',
                            text: 'El año de fin no puede ser menor al año de inicio.',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
            }

            // ==========================================
            // 3. ERRORES DE LARAVEL (Abrir Modal)
            // ==========================================
            @if ($errors->any())
                const modalCreateEl = document.getElementById('modalCreateEje');
                if(modalCreateEl){
                    const modalCreate = bootstrap.Modal.getOrCreateInstance(modalCreateEl);
                    modalCreate.show();
                }
            @endif

            // ==========================================
            // 4. DELEGACIÓN DE EVENTOS (Clicks dinámicos)
            // ==========================================
            document.addEventListener('click', function(event) {

                // --- A. BOTÓN EDITAR ---
                const btnEdit = event.target.closest('.btn-edit-eje');
                if (btnEdit) {
                    // Mapeo de datos
                    const datos = {
                        id: btnEdit.dataset.id,
                        nombre: btnEdit.dataset.nombre,
                        descripcion: btnEdit.dataset.descripcion,
                        url: btnEdit.dataset.url,
                        estado: btnEdit.dataset.estado,
                        plan: btnEdit.dataset.plan
                    };

                    // Asignar valores
                    document.getElementById('edit_nombre_eje').value = datos.nombre;
                    document.getElementById('edit_descripcion_eje').value = datos.descripcion;
                    document.getElementById('edit_url_documento').value = datos.url;
                    document.getElementById('edit_estado_eje').value = datos.estado;
                    document.getElementById('edit_plan_nombre').value = datos.plan;

                    // Actualizar Action del Form
                    const form = document.getElementById('formEditEje');
                    if (form) form.action = '{{ url('catalogos/ejes') }}/' + datos.id;

                    // Mostrar Modal
                    const modalEl = document.getElementById('modalEditEje');
                    if (modalEl) bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }

                // --- B. BOTÓN ELIMINAR ---
                const btnDelete = event.target.closest('.btn-delete');
                if (btnDelete) {
                    event.preventDefault();
                    const formEliminar = btnDelete.closest('.form-eliminar');

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Esta acción marcará el eje como inactivo o lo eliminará permanentemente.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) formEliminar.submit();
                    });
                }
            });
        });
    </script>
@endpush
