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


        <div class="container mx-auto py-5">

            {{-- 1. ENCABEZADO MODERNO --}}
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 class="h3 fw-bold text-slate-800 mb-1">Directorio de Entidades</h1>
                    <p class="text-slate-500 mb-0">Gestión y monitoreo de instituciones del sector público</p>
                </div>
                {{-- BARRA DE BÚSQUEDA Y ACCIONES --}}
                <div class="row mb-3 align-items-center">
                    {{-- COLUMNA IZQUIERDA: BUSCADOR --}}
                    <div class="col-md-6">
                        <form action="{{ route('institucional.organizaciones.index') }}" method="GET">
                            <div class="input-group shadow-sm">
                                {{-- Lupa --}}
                                <span class="input-group-text bg-white border-end-0 text-muted">
                                    <i class="fas fa-search"></i>
                                </span>
                                {{-- Input --}}
                                <input type="text" name="busqueda"
                                    class="form-control border-start-0 border-end-0 shadow-none"
                                    placeholder="Buscar institución, RUC o siglas..." value="{{ request('busqueda') }}">
                                {{--  Botón X (Solo si hay búsqueda) --}}
                                @if (request('busqueda'))
                                    <a href="{{ route('institucional.organizaciones.index') }}"
                                        class="btn bg-white border-top border-bottom border-end-0 text-danger"
                                        style="z-index: 5;" title="Limpiar filtro">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                                {{-- Botón Buscar --}}
                                <button class="btn btn-primary" type="submit">Buscar</button>
                            </div>
                        </form>
                    </div>
                    {{-- COLUMNA DERECHA: BOTÓN NUEVA INSTITUCIÓN --}}
                    <div class="col-md-6 text-end">
                        <a href="{{ route('institucional.organizaciones.create') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Nueva Institución
                        </a>
                    </div>

                </div>

            </div>

            {{-- 2. MENSAJES DE ALERTA --}}
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- 3. TARJETA DE LA TABLA --}}
            <div class="card card-clean rounded-3 overflow-hidden bg-white mb-5">
                {{-- Encabezado de la tarjeta limpio (sin fondo gris fuerte) --}}
                <div class="card-header py-3 bg-white border-bottom">
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
                        {{-- Tabla sin bordes verticales para limpieza visual --}}
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

                                        {{-- ID --}}
                                        <td class="ps-4 text-muted fw-bold small">
                                            {{ $organizacion->id_organizacion }}
                                        </td>

                                        {{-- NOMBRE Y SIGLAS --}}
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
                                                    Alinear
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
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" fill="currentColor" class="bi bi-trash3"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5" />
                                                        </svg>
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

                {{-- 4. PAGINACIÓN LIMPIA --}}
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
        //Para el mouse over en la tabla

        document.addEventListener('DOMContentLoaded', function() {
            // Selecciona todos los elementos que tengan data-bs-toggle="tooltip"
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))

            // Los inicializa uno por uno
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
        //Para verificar que no sea un boton o un link
        document.addEventListener('DOMContentLoaded', () => {
            const rows = document.querySelectorAll('.clickable-row');

            rows.forEach(row => {
                row.addEventListener('click', (e) => {
                    // Verificación extra de seguridad:
                    // Si lo que se clickeó fue un botón o un link dentro de la fila, no hacemos nada.
                    if (e.target.closest('a') || e.target.closest('button')) {
                        return;
                    }

                    const href = row.dataset.href;
                    if (href) {
                        window.location.href = href;
                    }
                });
            });
        });
    </script>
@endsection
