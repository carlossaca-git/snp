@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Catálogo de Objetivos Nacionales (PND)"
        subtitulo="Objetivos nacionales dal plan Nacional Desarrollo">

        <div class="btn-toolbar mb-2 mb-md-0">
            @if (Auth::user()->tienePermiso('objetivos.gestionar'))
                <button type="button" class="btn btn-sm btn-secondary me-2 d-inline-flex align-items-center"
                    data-bs-toggle="modal" data-bs-target="#modalCreatePnd">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Nuevo Objetivo
                </button>
            @endif
        </div>
    </x-layouts.header_content>
    @include('partials.mensajes')
    <div class="container mx-auto py-8">
        <div class="row justify-content-end align-items-end mb-2">
            <div class="col-md-4">
                <form action="{{ route('catalogos.objetivos.index') }}" method="GET">
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
                                        <span class="badge bg-secondary px-3 py-2 shadow-sm">
                                            {{ $item->codigo_objetivo }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-muted">{{ Str::limit($item->descripcion_objetivo, 100) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small"><span class="badge bg-info text-dark">
                                                @if ($item->eje)
                                                    {{ $item->eje->nombre_eje }}
                                                @else
                                                    <span class="text-muted">No asignado</span>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="small text-muted">
                                            <i class="far fa-calendar-alt"></i> Vigencia: {{ $item->eje?->plan?->periodo_inicio }} -
                                            {{ $item->eje?->plan?->periodo_fin }}
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
                                        {{-- ACCIONES --}}
                                        @if (Auth::user()->tienePermiso('objetivos.gestionar'))
                                            <div class="btn-group shadow-sm">
                                                <button type="button"
                                                    class="btn btn-sm btn-white text-warning border edit-pnd-btn"
                                                    data-bs-toggle="modal" data-bs-target="#modalEditPnd"
                                                    data-id="{{ $item->id_objetivo_nacional }}"
                                                    data-codigo="{{ $item->codigo_objetivo }}"
                                                    data-descripcion="{{ $item->descripcion_objetivo }}"
                                                    data-eje="{{ $item->id_eje }}"
                                                    data-inicio="{{ $item->periodo_inicio }}"
                                                    data-fin="{{ $item->periodo_fin }}" data-estado="{{ $item->estado }}">
                                                    <span data-feather="edit-2" style="width: 16px; height: 16px;"></span>
                                                </button>

                                                <form
                                                    action="{{ route('catalogos.objetivos.destroy', $item->id_objetivo_nacional) }}"
                                                    method="POST" class="d-inline form-eliminar">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-sm btn-white text-danger border btn-delete shadow-sm">
                                                        <span data-feather="trash-2"
                                                            style="width: 16px; height: 16px;"></span>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
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
                    <div class="d-flex justify-content-end mt-3">
                        {{ $pnd->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PARA EDICIÓN DE PND --}}
    @include('dashboard.configuracion.objetivos.editar')
    {{-- MODAL PARA CREAR NUEVO OBJETIVO --}}
    @include('dashboard.configuracion.objetivos.crear')

    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    @push('scripts')
        <script>
            document.addEventListener('click', function(event) {
                const btn = event.target.closest('.edit-pnd-btn');

                if (btn) {
                    // Extraer datos
                    const id = btn.getAttribute('data-id');
                    const codigo = btn.getAttribute('data-codigo');
                    const descripcion = btn.getAttribute('data-descripcion');

                    const inputDes = document.getElementById('edit_pnd_descripcion');
                    if (inputDes) {
                        inputDes.value = descripcion || '';
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
                    // Asignar datos al formulario del modal
                    document.getElementById('formEditPnd').action = "/catalogos/objetivos/" + id;
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
                    event.preventDefault(); // Evitar el envío inmediato del formulario
                    const form = deleteBtn.closest('.form-eliminar');

                    Swal.fire({
                        title: '¿Estás seguro que desea eliminar este objetivo?',
                        text: "El objetivo se marcará como eliminado.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e74a3b',
                        cancelButtonColor: '#858796',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });
            // 3. Cerrar alerta automáticamente
            document.addEventListener('DOMContentLoaded', function() {
                const alerta = document.getElementById('alerta-exito');
                if (alerta) {
                    setTimeout(() => {
                        alerta.style.transition = "opacity 0.5s ease";
                        alerta.style.opacity = "0";
                        setTimeout(() => {
                            alerta.remove();
                        }, 500);
                    }, 3000);
                }
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
                btnLimpiar.addEventListener('click', function() {
                    inputBusqueda.value = '';
                    toggleLimpiarButton();

                    inputBusqueda.closest('form').submit();
                });
            })
        </script>
    @endpush
@endsection
