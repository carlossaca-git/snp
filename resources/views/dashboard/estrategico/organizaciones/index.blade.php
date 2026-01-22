@extends('layouts.app')

@section('content')
    {{-- Estilos personalizados para este archivo --}}
    <style>
        /* Paleta de colores Institucional */
        .text-slate-800 {
            color: #1e293b;
        }

        .text-slate-500 {
            color: #64748b;
        }

        .bg-slate-50 {
            background-color: #f8fafc;
        }

        /* Hover suave en la fila */
        .clickable-row:hover {
            background-color: #f1f5f9 !important;
            /* Gris muy suave al pasar el mouse */
            cursor: pointer;
        }

        /* Botón principal */
        .btn-institutional {
            background-color: #2563eb;
            /* Azul Real */
            border-color: #2563eb;
            color: white;
        }

        .btn-institutional:hover {
            background-color: #1d4ed8;
            color: white;
        }

        /* Tarjeta limpia */
        .card-clean {
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }
    </style>
    <x-layouts.header_content titulo="Directorio de Entidades"
        subtitulo="Gestión y monitoreo de instituciones del sector público">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('institucional.organizaciones.create') }}"
                class="btn btn-sm btn-outline-secondary me-2 d-inline-flex align-items-center">
                <i class="fas fa-plus"></i> Nueva Institución
            </a>
        </div>
    </x-layouts.header_content>
    @include('partials.mensajes')
    <div class="container mx-auto py-2">
        <div class="row justify-content-end align-items-end mb-2">
            <div class="col-md-5">
                <form action="{{ route('institucional.organizaciones.index') }}" method="GET">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="busqueda" id="inputBusqueda"
                            class="form-control border-start-0 border-end-0 shadow-none"
                            placeholder="Buscar institución, RUC o siglas..." value="{{ request('busqueda') }}">
                        <button type="button" id="btnLimpiarBusqueda"
                            class="btn bg-white border-top border-bottom border-start-0 text-danger"
                            style="display: none; z-index: 5;">
                            <i class="fas fa-times"></i>
                        </button>
                        <button class="btn btn-secondary" type="submit">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card card-clean rounded-3 overflow-hidden bg-white mb-5">
            <div class="card-header py-3 bg-white border-bottom">
                <small class="text-primary fst-italic">
                    <i class="fas fa-info-circle fa-sm me-1"></i>
                    Clic en el nombre para ver el perfil
                </small>
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-slate-800">
                        <i class="fas fa-list-ul me-2 text-muted"></i> Listado Registrado
                    </h6>
                    <span class="badge bg-light text-dark border rounded-pill">Total:
                        {{ $organizaciones->count() }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 ps-4 text-uppercase text-secondary small fw-bold"
                                    style="letter-spacing: 0.5px;">ID</th>
                                <th class="py-3 text-uppercase text-secondary small fw-bold">Entidad</th>
                                <th class="py-3 text-uppercase text-secondary small fw-bold">Sectorización</th>
                                <th class="py-3 text-uppercase text-secondary small fw-bold">RUC</th>
                                <th class="py-3 text-uppercase text-secondary small fw-bold">Estado</th>
                                <th class="py-3 text-end pe-4 text-uppercase text-secondary small fw-bold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse ($organizaciones as $organizacion)
                                <tr class="clickable-row border-bottom transition-all"
                                    data-href="{{ route('institucional.organizaciones.show', $organizacion->id_organizacion) }}"
                                    data-bs-placement="top"
                                    title="Clic para mas informacion de: {{ $organizacion->nom_organizacion }}">


                                    <td class="ps-4 text-muted fw-bold small">
                                        {{ $organizacion->id_organizacion }}
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span
                                                class="fw-bold text-slate-800">{{ $organizacion->nom_organizacion }}</span>
                                            <span class="small text-slate-500">{{ $organizacion->siglas }}</span>
                                        </div>
                                    </td>

                                    {{-- RELACIONES --}}
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            {{-- Badge sutil para el Tipo --}}
                                            <div>
                                                <span
                                                    class="badge bg-blue-50 text-primary border border-primary-subtle rounded-pill fw-normal">
                                                    {{ $organizacion->tipo->nombre ?? 'N/A' }}
                                                </span>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-folder-tree me-1 text-secondary"></i>
                                                {{ $organizacion->subsector->nombre ?? 'General' }}
                                            </small>
                                        </div>
                                    </td>

                                    {{-- RUC --}}
                                    <td class="text-secondary font-monospace small">
                                        {{ $organizacion->ruc }}
                                    </td>

                                    {{-- ESTADO --}}
                                    <td>
                                        @if ($organizacion->estado == 1)
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">
                                                <i class="fas fa-check-circle me-1"></i> Activo
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3">
                                                <i class="fas fa-ban me-1"></i> Inactivo
                                            </span>
                                        @endif
                                    </td>

                                    {{-- ACCIONES --}}
                                    <td class="text-end align-middle">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('estrategico.alineacion.gestionar', $organizacion->id_organizacion) }}"
                                                class="btn btn-sm btn-outline-primary d-flex align-items-center fw-bold shadow-sm px-3"
                                                data-bs-toggle="tooltip" title="Gestionar Metas y Objetivos">
                                                <i class="fas fa-bullseye me-2"></i>
                                                {{-- EDITAR --}}
                                            </a>
                                            <a href="{{ route('institucional.organizaciones.edit', $organizacion->id_organizacion) }}"
                                                class="btn btn-sm btn-white border" title="Editar">
                                                <span data-feather="edit" class="text-primary"></span>
                                            </a>
                                            <form
                                                action="{{ route('institucional.organizaciones.destroy', $organizacion->id_organizacion) }} method="POST"
                                                onsubmit="return confirm('¿Estás seguro? Se borrará todo el historial.');"
                                                style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-white border text-danger shadow-sm hover-danger"
                                                    data-bs-toggle="tooltip" title="Eliminar registro">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <div class="bg-light rounded-circle p-4 mb-3">
                                                <i class="fas fa-inbox fa-3x text-secondary"></i>
                                            </div>
                                            <h6 class="text-muted fw-bold">No se encontraron registros</h6>
                                            <p class="text-muted small mb-0">Comienza creando una nueva entidad
                                                institucional.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PAGINACIÓN LIMPIA --}}
            <div class="card-footer bg-white border-top-0 py-4">
                <div class="d-flex justify-content-end">
                    {{ $organizaciones->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
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
