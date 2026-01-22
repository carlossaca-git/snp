@extends('layouts.app')

@section('content')
    <style>
        .text-slate-800 {
            color: #1e293b;
        }

        .text-slate-500 {
            color: #64748b;
        }

        .card-clean {
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            background-color: #fff;
        }

        .fila-clickable {
            cursor: pointer;
            transition: all 0.2s;
        }

        .fila-clickable:hover td {
            background-color: #f8fafc !important;
        }

        .table thead th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
    </style>
    <x-layouts.header_content titulo="Gestión de Planes y Programas" subtitulo="Catalogo de programas de inversion">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('inversion.programas.create') }}" id="btnNuevoPrograma"
                class="btn btn-secondary shadow-sm px-3 fw-bold d-inline-flex align-items-center"
                data-tiene-objetivo="{{ $tieneObjetivos ? '1' : '0' }}"
                data-url-crear="{{ route('estrategico.objetivos.create') }}">
                <i class="fas fa-plus me-1"></i> Nuevo Programa
            </a>
        </div>
    </x-layouts.header_content>
    @include('partials.mensajes')
    <div class="container-fluid py-2">
        <div class="row justify-content-end align-items-end mb-2">
                <div class="col-md-4">
                    <form action="{{ route('inversion.programas.index') }}" method="GET">
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
        <div class="card card-clean rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-slate-800">
                    <i class="fas fa-list-ul me-2 text-secondary"></i>Listado Maestro de Programas
                </h6>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Código / Estado</th>
                                <th>Nombre del Programa</th>
                                <th class="text-center">Vigencia</th>
                                <th class="text-center">Ejecución</th>
                                <th class="text-end">Presupuesto (PAI)</th>
                                <th class="text-center pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($programas as $programa)
                                <tr class="fila-clickable"
                                    onclick="handleRowClick(event, '{{ route('inversion.programas.edit', $programa->id) }}')">
                                    {{--  CÓDIGO Y ESTADO --}}
                                    <td class="ps-4">
                                        <div class="fw-bold text-slate-800">{{ $programa->codigo_cup }}</div>
                                        {{-- Badge dinámico según el estado --}}
                                        @php
                                            $statusColor =
                                                [
                                                    'POSTULADO' => 'bg-info',
                                                    'PRIORIZADO' => 'bg-primary',
                                                    'EJECUCION' => 'bg-success',
                                                    'CERRADO' => 'bg-secondary',
                                                ][$programa->estado] ?? 'bg-light text-dark';
                                        @endphp
                                        <span class="badge {{ $statusColor }} " style="font-size: 0.65rem;">
                                            {{ $programa->estado ?? 'POSTULADO' }}
                                        </span>
                                    </td>

                                    {{-- NOMBRE Y FUENTE --}}
                                    <td>
                                        <div class="fw-bold text-slate-800 mb-0">
                                            {{ Str::limit($programa->nombre_programa, 50) }}</div>
                                        <small class="text-primary fw-bold" style="font-size: 0.7rem;">
                                            Fuente: {{ $programa->fuente_financiamiento ?? '001' }}
                                        </small>
                                    </td>

                                    {{--  VIGENCIA --}}
                                    <td class="text-center">
                                        <div class="small fw-bold">{{ $programa->anio_inicio }} -
                                            {{ $programa->anio_fin }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Plurianual</div>
                                    </td>

                                    {{-- BARRA DE EJECUCIÓN --}}
                                    <td class="text-center" style="min-width: 120px;">
                                        @php
                                            $porcentaje =
                                                $programa->monto_planificado > 0
                                                    ? ($programa->presupuesto_asignado / $programa->monto_planificado) *
                                                        100
                                                    : 0;
                                        @endphp
                                        <div class="small mb-1 fw-bold">{{ number_format($porcentaje, 1) }}%</div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $porcentaje }}%"></div>
                                        </div>
                                    </td>

                                    {{-- PRESUPUESTO --}}
                                    <td class="text-end">
                                        <div class="fw-bold text-slate-800">
                                            ${{ number_format($programa->monto_planificado, 2) }}</div>
                                        <small class="text-muted" style="font-size: 0.7rem;">Planificado PAI</small>
                                    </td>
                                    {{-- ACCIONES --}}
                                    <td class="text-end pe-4 py-3">
                                        <div class="btn-group shadow-sm">
                                            <a href="{{ route('inversion.programas.edit', $programa->id) }}"
                                                class="btn btn-sm btn-white border text-primary" title="Editar">
                                                <i class="fas fa-edit" data-feather="edit"></i>
                                            </a>
                                            <form action="{{ route('inversion.programas.destroy', $programa->id) }}"
                                                method="POST" class="d-inline form-delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-white border text-danger"
                                                    title="Eliminar"
                                                    onclick="confirmDelete(event, '{{ $programa->codigo_cup }}')">
                                                    <i class="fas fa-trash-alt" data-feather="trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="small text-slate-500 mb-0">
                        Mostrando {{ $programas->count() }} programas de esta página.
                    </p>
                    <div>
                        {{ $programas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        /**
         * Permite que la fila sea cliqueable sin interferir con los botones
         */
        function handleRowClick(event, url) {
            // Si el clic fue en un botón, enlace o icono dentro de ellos, no redireccionar
            if (event.target.closest('button') || event.target.closest('a')) {
                return;
            }
            window.location.href = url;
        }

        function confirmDelete(event, cup) {
            event.stopPropagation(); // Evita que la fila se redireccione
            if (confirm('¿Está seguro de eliminar el programa ' + cup + '? Esta acción no se puede deshacer.')) {
                event.target.closest('form').submit();
            }
        }
        //Confirmacion para ir a crear objetivos estrategicos
        document.getElementById('btnNuevoPrograma').addEventListener('click', function(e) {
            // Obtener datos
            var valor = this.getAttribute('data-tiene-objetivo');
            var urlCrear = this.getAttribute('data-url-crear');

            var tieneObjetivos = (valor == '1');

            if (!tieneObjetivos) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Requisito Previo',
                        text: 'Para crear un Programa, primero necesita definir Objetivos Estratégicos. ¿Desea crearlos ahora?',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, ir a crear',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = urlCrear;
                        }
                    });
                }
                else {
                    if (confirm('Necesitas objetivos estratégicos. ¿Quieres ir a crearlos ahora?')) {
                        window.location.href = urlCrear;
                    }
                }
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

            // Acción al hacer clic en la "X"
            btnLimpiar.addEventListener('click', function() {
                inputBusqueda.value = '';
                toggleLimpiarButton();

                inputBusqueda.closest('form').submit();
            });
        });
    </script>
@endsection
