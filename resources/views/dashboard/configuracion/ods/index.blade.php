@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Catálogo de ODS" subtitulo="Objetivos de Desarrollo Sostenible (Agenda 2030)">
        @if (Auth::user()->tienePermiso('ods.gestionar'))
            <button type="button" class="btn btn-secondary shadow-sm d-inline-flex align-items-center" data-bs-toggle="modal"
                data-bs-target="#modalCrearOds">
                <i class="fas fa-plus me-1"></i> Nuevo ODS
            </button>
        @endif
    </x-layouts.header_content>

    @include('partials.mensajes')

    <div class="container-fluid py-4">
        <div class="row justify-content-end align-items-end mb-4">
            <div class="col-md-4">
                <form action="{{ route('catalogos.ods.index') }}" method="GET">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="busqueda" id="inputBusqueda"
                            class="form-control border-start-0 border-end-0 shadow-none" placeholder="Buscar ODS..."
                            value="{{ request('busqueda') }}">

                        <button type="button" id="btnLimpiarBusqueda"
                            class="btn bg-white border-top border-bottom border-end-0 text-danger"
                            style="display: none; z-index: 1000 !important;">
                            <i class="fas fa-times"></i>
                        </button>
                        <button class="btn btn-secondary" type="submit">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
        {{-- Errores --}}
        <div id="estado-errores" data-hay-errores="{{ $errors->any() ? 'true' : 'false' }}"
            data-es-edicion="{{ old('_method') === 'PUT' ? 'true' : 'false' }}" data-id-temp="{{ old('id_temp') }}"
            style="display: none;">
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
            @forelse($ods as $item)
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm hover-elevate transition-all">
                        <a href="{{ route('catalogos.ods.show', $item->id_ods) }}">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center text-white p-3"
                            style="background-color: {{ $item->color_hex ?? '#777' }}; border-radius: 0.5rem 0.5rem 0 0;">

                            <div class="d-flex align-items-center">
                                <span class="fw-bold fs-4 me-2">ODS {{ $item->codigo }}</span>
                            </div>

                            @if ($item->estado == 1)
                                <span class="badge bg-white text-success rounded-pill shadow-sm" style="font-size: 0.7rem;">
                                    <i class="fas fa-check-circle me-1"></i> Activo
                                </span>
                            @else
                                <span class="badge bg-white text-danger rounded-pill shadow-sm" style="font-size: 0.7rem;">
                                    <i class="fas fa-ban me-1"></i> Inactivo
                                </span>
                            @endif
                        </div>

                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title fw-bold text-dark mb-2"
                                    style="color: {{ $item->color_hex }}; min-height: 40px;">
                                    {{ $item->nombre }}
                                </h6>
                                <p class="card-text text-muted small flex-grow-1" style="font-size: 0.85rem;">
                                    {{ Str::limit($item->descripcion, 90) }}
                                </p>

                                <hr class="my-2 opacity-10">
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="fas fa-columns me-2 text-secondary"></i>
                                    <span>Pilar: <strong>{{ Str::limit($item->pilar, 20) }}</strong></span>
                                </div>
                            </div>
                        </a>
                        {{-- ACCIONES --}}
                        @if (Auth::user()->tienePermiso('ods.gestionar'))
                            <div
                                class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center pb-3 pt-0">
                                <small class="text-muted" style="font-size: 0.7rem;">Acciones</small>
                                <div class="btn-group">
                                    {{-- BOTÓN EDITAR --}}
                                    <button type="button" class="btn btn-sm btn-outline-warning btnEditar" title="Editar"
                                        data-id="{{ $item->id_ods }}" data-codigo="{{ $item->codigo }}"
                                        data-nombre="{{ $item->nombre }}" data-pilar="{{ $item->pilar }}"
                                        data-descripcion="{{ $item->descripcion }}" data-color="{{ $item->color_hex }}"
                                        data-estado="{{ $item->estado }}" data-bs-toggle="modal"
                                        data-bs-target="#modalEditarOds">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    {{-- BOTÓN ELIMINAR --}}
                                    <form action="{{ route('catalogos.ods.destroy', $item->id_ods) }}" method="POST"
                                        class="d-inline form-eliminar">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                            title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light text-center border shadow-sm py-5" role="alert">
                        <div class="mb-3">
                            <i class="fas fa-globe-americas fa-3x text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted">No se encontraron ODS</h5>
                        <p class="text-muted small mb-0">Intenta ajustar tu búsqueda o agrega un nuevo objetivo.</p>
                    </div>
                </div>
            @endforelse
        </div>
        @if (method_exists($ods, 'links'))
            <div class="d-flex justify-content-center mt-4">
                {{ $ods->links() }}
            </div>
        @endif

    </div>

    {{-- MODALES --}}
    @include('dashboard.configuracion.ods.create')
    @include('dashboard.configuracion.ods.edit')
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // LÓGICA DE EDICIÓN
            const botonesEditar = document.querySelectorAll('.btnEditar');

            botonesEditar.forEach(boton => {
                boton.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const codigo = this.getAttribute('data-codigo');
                    const nombre = this.getAttribute('data-nombre');
                    const pilar = this.getAttribute('data-pilar');
                    const descripcion = this.getAttribute('data-descripcion');
                    const color = this.getAttribute('data-color');
                    const estado = this.getAttribute('data-estado');

                    document.getElementById('edit_codigo').value = codigo;
                    document.getElementById('edit_nombre').value = nombre;
                    document.getElementById('edit_descripcion').value = descripcion;
                    document.getElementById('edit_color').value = color;

                    const inputPilar = document.getElementById('edit_pilar');
                    if (inputPilar) inputPilar.value = pilar || "";

                    const inputEstado = document.getElementById('edit_estado');
                    if (inputEstado) inputEstado.value = estado;

                    const inputIdTemp = document.getElementById('edit_id_temp');
                    if (inputIdTemp) inputIdTemp.value = id;

                    const form = document.getElementById('formEditarOds');
                    form.action = `/catalogos/ods/${id}`;
                });
            });

            // LÓGICA DE ELIMINAR
            document.addEventListener('click', function(event) {
                const btnDelete = event.target.closest('.btn-delete');
                if (btnDelete) {
                    event.preventDefault();
                    const form = btnDelete.closest('.form-eliminar');
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
                            form.submit();
                        }
                    });
                }
            });

            // LÓGICA DE ERRORES para recuperar modal
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

            // BUSCADOR
            const inputBusqueda = document.getElementById('inputBusqueda');
            const btnLimpiar = document.getElementById('btnLimpiarBusqueda');

            function toggleLimpiarButton() {
                if (inputBusqueda.value.trim() !== '') {
                    btnLimpiar.style.display = 'block';
                } else {
                    btnLimpiar.style.display = 'none';
                }
            }
            toggleLimpiarButton();
            inputBusqueda.addEventListener('input', toggleLimpiarButton);
            btnLimpiar.addEventListener('click', function() {
                inputBusqueda.value = '';
                toggleLimpiarButton();
                inputBusqueda.closest('form').submit();
            });
        });
    </script>

    {{-- CSS EXTRA PARA EFECTO HOVER SUAVE --}}
    <style>
        .hover-elevate {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-elevate:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }
    </style>
@endsection
