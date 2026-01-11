@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Catálogo de ODS</h1>
            <p class="text-muted small mb-0">Objetivos de Desarrollo Sostenible - Agenda 2030</p>
        </div>
        <button type="button" class="btn btn-secondary shadow-sm d-inline-flex align-items-center" data-bs-toggle="modal"
            data-bs-target="#modalCrearOds">
            <i class="fas fa-plus" data-feather="plus"></i> Nuevo ODS
        </button>
    </div>
    @include('partials.mensajes')
    {{-- Div invisible para pasar datos de PHP a JS sin errores --}}
    <div id="estado-errores" data-hay-errores="{{ $errors->any() ? 'true' : 'false' }}"
        data-es-edicion="{{ old('_method') === 'PUT' ? 'true' : 'false' }}" data-id-temp="{{ old('id_temp') }}"
        style="display: none;">
    </div>
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th >Numero</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Pilar</th>
                            <th>Estado</th>
                            <th class="text-end px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ods as $item)
                            <tr>
                                <td class="text-center">
                                    {{-- Usamos el color_hex para el fondo del codigo --}}
                                    <span class="badge shadow-sm"
                                        style="background-color: {{ $item->color_hex }}; color: #fff; width: 60px; height: 35px; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                        {{ $item->codigo }}
                                    </span>
                                </td>
                                <td class="fw-bold px-3">
                                    <span style="color: {{ $item->color_hex }};">
                                        {{ $item->nombre }}
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    {{ Str::limit($item->descripcion, 50) }}

                                </td>
                                <td class="text-muted small">
                                    {{ Str::limit($item->pilar, 50) }}

                                </td>
                                <td class="text-muted small">
                                    @if ($item->estado == 1)
                                        <span class="badge rounded-pill bg-success">Activo</span>
                                    @else
                                        <span class="badge rounded-pill bg-warning text-dark">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-end px-4">
                                    <div class="btn-group shadow-sm">
                                        {{-- PASAR DATOS AL MODAL EDIT --}}
                                        <button type="button" class="btn btn-sm text-white btn-warning btnEditar"
                                            data-id="{{ $item->id_ods }}" data-codigo="{{ $item->codigo }}"
                                            data-nombre="{{ $item->nombre }}" data-pilar="{{ $item->pilar }}"
                                            data-descripcion="{{ $item->descripcion }}"
                                            data-color="{{ $item->color_hex }}" data-estado="{{ $item->estado }}"
                                            data-bs-toggle="modal" data-bs-target="#modalEditarOds">
                                            <i class="fas fa-edit" data-feather="edit-2" style="width: 16px; height: 16px;"></i>
                                        </button>

                                        {{-- BOTÓN ELIMINAR (Estilo SweetAlert) --}}
                                        <form action="{{ route('catalogos.ods.destroy', $item->id_ods) }}" method="POST"
                                            class="d-inline form-eliminar">
                                            @csrf
                                            @method('DELETE')

                                            <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                title="Eliminar">
                                                <i class="fas fa-trash-alt"data-feather="trash-2" style="width: 16px; height: 16px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                         @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No hay metas registradas para este
                                    nivel.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- MODALES PARA EDICION Y CREACION --}}
    @include('dashboard.configuracion.ods.crear')
    @include('dashboard.configuracion.ods.editar')
@endsection

@section('scripts')
    {{-- 1. IMPORTAR SWEETALERT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // =======================================================
            // LÓGICA DE EDICIÓN
            // =======================================================
            const botonesEditar = document.querySelectorAll('.btnEditar');

            botonesEditar.forEach(boton => {
                boton.addEventListener('click', function() {
                    // Datos del botón
                    const id = this.getAttribute('data-id');
                    const codigo = this.getAttribute('data-codigo');
                    const nombre = this.getAttribute('data-nombre');
                    const pilar = this.getAttribute('data-pilar');
                    const descripcion = this.getAttribute('data-descripcion');
                    const color = this.getAttribute('data-color');
                    const estado = this.getAttribute('data-estado');

                    // Llenar Modal
                    document.getElementById('edit_codigo').value = codigo;
                    document.getElementById('edit_nombre').value = nombre;
                    document.getElementById('edit_descripcion').value = descripcion;
                    document.getElementById('edit_color').value = color;

                    // Selects seguros
                    const inputPilar = document.getElementById('edit_pilar');
                    if (inputPilar) inputPilar.value = pilar || "";

                    const inputEstado = document.getElementById('edit_estado');
                    if (inputEstado) inputEstado.value = estado;

                    // ID temporal y Action
                    const inputIdTemp = document.getElementById('edit_id_temp');
                    if (inputIdTemp) inputIdTemp.value = id;

                    const form = document.getElementById('formEditarOds');
                    form.action = `/catalogos/ods/${id}`; // Forma corta de la ruta update
                });
            });

            // =======================================================
            // LÓGICA DE ELIMINAR
            // =======================================================
            // Usamos delegación de eventos para detectar clicks en .btn-delete
            document.addEventListener('click', function(event) {

                //Detectamos el clic
                // Buscamos si el click fue dentro de un botón con clase .btn-delete
                const btnDelete = event.target.closest('.btn-delete');

                if (btnDelete) {
                    event.preventDefault(); // Evitamos que se envie solo de inmediato

                    const form = btnDelete.closest('.form-eliminar'); // Buscamos el form padre

                    Swal.fire({
                        title: '¿Eliminar ODS?',
                        text: "Esta acción no se puede deshacer.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // Si confirma enviamos el formulario manualmente
                        }
                    });
                }
            });

            // =======================================================
            // LÓGICA DE ERRORES para recuperar modal
            // =======================================================
            const estadoDiv = document.getElementById('estado-errores');
            if (estadoDiv) {
                const hayErrores = estadoDiv.getAttribute('data-hay-errores') === 'true';
                const esEdicion = estadoDiv.getAttribute('data-es-edicion') === 'true';
                const idAntiguo = estadoDiv.getAttribute('data-id-temp');

                if (hayErrores) {
                    if (esEdicion) {
                        if (idAntiguo) {
                            let form = document.getElementById('formEditarOds');
                            form.action = `/catalogos/ods/${idAntiguo}`;
                        }
                        var myModalEl = document.getElementById('modalEditarOds');
                        var modal = bootstrap.Modal.getOrCreateInstance(myModalEl);
                        modal.show();
                    } else {
                        var myModalEl = document.getElementById('modalCrearOds');
                        var modal = bootstrap.Modal.getOrCreateInstance(myModalEl);
                        modal.show();
                    }
                }
            }
        });
    </script>
@endsection
