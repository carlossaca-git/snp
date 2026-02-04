@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Plan Anual de Inversiones (PAI)" subtitulo="Catalogo de programas de inversion">
        <div class="btn-toolbar mb-2 mb-md-0">
            <button class="btn btn-secondary shadow-sm px-3 fw-bold d-inline-flex align-items-center" data-bs-toggle="modal"
                data-bs-target="#modalCrearPlan">
                <i class="fas fa-plus me-2"></i> Nuevo Plan Anual
            </button>
        </div>
    </x-layouts.header_content>
    <div class="container-fluid py-4">
        @include('partials.mensajes')
        <div class="row justify-content-end align-items-end mb-2">
            <div class="col-md-4">
                <form action="{{ route('inversion.planes.index') }}" method="GET">
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
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Año Fiscal</th>
                                <th>Resolución</th>
                                <th>Nombre del Plan</th>
                                <th>Monto Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($planes as $item)
                                <tr>
                                    <td class="fw-bold">{{ $item->anio }}</td>
                                    <td>
                                        {{ $item->numero_resolucion }}
                                    </td>
                                    <td>
                                        <span class="text-dark">{{ $item->nombre }}</span>
                                        <br>
                                        <span class="badge bg-info text-dark" style="font-size: 0.75rem;">
                                            v{{ $item->version }}.0
                                        </span>
                                    </td>
                                                                    <td class="text-success fw-bold">
                                        ${{ number_format($item->monto_total, 2) }}
                                    </td>
                                    <td>
                                        @php
                                            $badges = [
                                                'FORMULACION' => 'bg-secondary',
                                                'APROBADO' => 'bg-info text-dark',
                                                'EJECUCION' => 'bg-success',
                                                'CERRADO' => 'bg-danger',
                                            ];
                                        @endphp
                                        <span class="badge {{ $badges[$item->estado] ?? 'bg-light text-dark' }}">
                                            {{ $item->estado }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-inline-block me-2">
                                            @if ($item->ruta_documento)
                                                <a href="{{ Storage::url($item->ruta_documento) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    title="Ver Documento Habilitante">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-outline-secondary" title="Sin Documento"
                                                    disabled>
                                                    <i class="fas fa-file-pdf"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <button class="btn btn-sm btn-warning btnEditarPlan" data-id="{{ $item->id }}"
                                            data-nombre="{{ $item->nombre }}" data-monto="{{ $item->monto_total }}"
                                            data-estado="{{ $item->estado }}" data-anio="{{ $item->anio }}"
                                            data-descripcion="{{ $item->descripcion }}"
                                            data-resolucion="{{ $item->numero_resolucion }}"
                                            data-version="{{ $item->version }}"
                                            data-tiene-documento="{{ $item->ruta_documento ? 'si' : 'no' }}"
                                            data-nombre-archivo="{{ $item->nombre_archivo ? basename($item->nombre_archivo) : '' }}"
                                            data-url-archivo="{{ $item->ruta_documento ? Storage::url($item->ruta_documento) : '#' }}"
                                            data-bs-toggle="modal" data-bs-target="#modalEditarPlan">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="#" class="btn btn-sm btn-primary" title="Ver Programas y Proyectos">
                                            <i class="fas fa-folder-open"></i>
                                        </a>
                                        <form action="{{ route('inversion.planes.destroy', $item->id) }}" method="POST"
                                            class="d-inline form-eliminar">

                                            @csrf
                                            @method('DELETE') <button type="submit" class="btn btn-sm btn-danger"
                                                title="Eliminar Plan">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <div
                                            class="d-flex flex-column align-items-center justify-content-center text-muted">
                                            <i class="fas fa-folder-open fa-3x mb-3 opacity-50"></i>
                                            <h6 class="fw-bold">No se encontraron Planes de Inversión</h6>
                                            <p class="small mb-0">Comience creando un nuevo plan de inversión.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Incluir Modales --}}
    @include('dashboard.inversion.planes.crear')
    @include('dashboard.inversion.planes.editar')
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const botonesEditar = document.querySelectorAll('.btnEditarPlan');
            botonesEditar.forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nombre = this.getAttribute('data-nombre');
                    const monto = this.getAttribute('data-monto');
                    const estado = this.getAttribute('data-estado');
                    const anio = this.getAttribute('data-anio');
                    const resolucion = this.getAttribute('data-resolucion');
                    const version = this.getAttribute('data-version');
                    const descripcion = this.getAttribute('data-descripcion');
                    const documento = this.getAttribute('data-tiene-documento');


                    const nombreArchivo = this.getAttribute('data-nombre-archivo');
                    const urlArchivo = this.getAttribute('data-url-archivo');


                    document.getElementById('edit_nombre').value = nombre;
                    document.getElementById('edit_monto').value = monto;
                    document.getElementById('edit_estado').value = estado;
                    document.getElementById('edit_anio').value = anio;
                    document.getElementById('edit_descripcion').value = descripcion;
                    document.getElementById('edit_resolucion').value = resolucion;
                    document.getElementById('span_version_actual').textContent = 'v' + version;

                    // Actualizar enlace de descarga del archivo
                    const aviso = document.getElementById('aviso_archivo_actual');
                    const link = document.getElementById('link_archivo_descarga');
                    const textoSpan = document.getElementById('texto_nombre_archivo');
                    if (documento === 'si') {
                        aviso.style.display = 'block';
                        link.href = urlArchivo;
                        textoSpan.textContent = nombreArchivo;
                    } else {
                        aviso.style.display = 'none';
                        link.href = '#';
                        textoSpan.textContent = '';
                    }

                    // Actualizar action del form
                    const form = document.getElementById('formEditarPlan');
                    form.action = `/inversion/planes/${id}`;
                });
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

        // Confirmación para eliminar
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionar todos los formularios de eliminar
            const formularios = document.querySelectorAll('.form-eliminar');

            formularios.forEach(form => {
                form.addEventListener('submit', function(e) {
                    //  DETENER el envío automático
                    e.preventDefault();

                    //  Mostrar la alerta bonita
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡Este plan se moverá a la papelera!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33', // Rojo para confirmar
                        cancelButtonColor: '#3085d6', // Azul para cancelar
                        confirmButtonText: 'Sí, eliminarlo',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        // Si el usuario confirma, enviamos el formulario manualmente
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
