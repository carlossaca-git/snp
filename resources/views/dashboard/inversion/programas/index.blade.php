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
                                {{--  CODIGO Y ESTADO --}}
                                <td class="ps-4">
                                    <div class="fw-bold text-slate-800">{{ $programa->codigo_cup }}</div>
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
                                {{-- NOMBRE --}}
                                <td>
                                    <div class="fw-bold text-slate-800 mb-0">
                                    <a href="{{ route('inversion.programas.show', $programa->id) }}">{{ Str::limit($programa->nombre_programa, 50) }}</a></div>
                                    <small class="text-primary fw-bold" style="font-size: 0.7rem;">
                                        Codigo: {{ $programa->codigo_programa ?? '001' }}
                                    </small>
                                </td>
                                {{--  VIGENCIA --}}
                                <td>
                                    @php
                                        $inicio = \Carbon\Carbon::parse($programa->fecha_inicio);
                                        $fin = \Carbon\Carbon::parse($programa->fecha_fin);
                                        $hoy = now();
                                    @endphp
                                    @if ($programa->estado == 'POSTULADO')
                                        <span class="badge bg-warning text-dark mb-1">
                                            <i class="fas fa-file-signature me-1"></i> Postulado / Revisión
                                        </span>
                                        <div class="small text-muted">Esperando aprobación</div>
                                    @elseif ($programa->estado == 'SUSPENDIDO')
                                        <span class="badge bg-danger mb-1">
                                            <i class="fas fa-ban me-1"></i> Suspendido
                                        </span>
                                    @elseif ($programa->estado == 'CERRADO')
                                        <span class="badge bg-dark mb-1">
                                            <i class="fas fa-archive me-1"></i> Cerrado Legalmente
                                        </span>
                                    @else
                                        @if ($hoy->lt($inicio))
                                            <span class="badge bg-info text-dark mb-1">
                                                <i class="fas fa-hourglass-start me-1"></i> Aprobado - Por Iniciar
                                            </span>
                                            <div class="small text-muted">Faltan {{ $hoy->diffInDays($inicio) }} días</div>
                                        @elseif ($hoy->between($inicio, $fin))
                                            <span class="badge bg-success mb-1">
                                                <i class="fas fa-running me-1"></i> En Ejecución
                                            </span>
                                        @else
                                            <span class="badge bg-secondary mb-1">
                                                <i class="fas fa-calendar-times me-1"></i> Plazo Vencido
                                            </span>
                                            <div class="small text-danger" style="font-size: 0.75rem;">
                                                Requiere Cierre o Prórroga
                                            </div>
                                        @endif
                                    @endif
                                    <div class="mt-1 border-top pt-1" style="font-size: 0.8rem; color: #666;">
                                        {{ $inicio->format('d/m/Y') }} - {{ $fin->format('d/m/Y') }}
                                    </div>
                                </td>
                                {{-- EJECUCIÓN --}}
                                <td style="min-width: 150px; vertical-align: middle;">
                                    @php
                                        // Definir variables para que el código sea legible
                                        $techo = $programa->monto_asignado;
                                        // El dinero total disponible (Techo presupuestario)
                                        $uso = $programa->monto_planificado;
                                        // El dinero usado (Lo que se ha gastado)
                                        $porcentaje = $techo > 0 ? ($uso / $techo) * 100 : 0;
                                        $color = 'bg-primary';
                                        if ($porcentaje == 100) {
                                            $color = 'bg-success';
                                        }
                                        if ($porcentaje > 100) {
                                            $color = 'bg-danger';
                                        }
                                    @endphp
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small text-muted fw-bold" style="font-size: 0.75rem;">Ocupación</span>
                                        <span class="small fw-bold {{ $porcentaje > 100 ? 'text-danger' : 'text-dark' }}">
                                            {{ number_format($porcentaje, 1) }}%
                                        </span>
                                    </div>
                                    <div class="progress shadow-sm" style="height: 6px; background-color: #e9ecef;">
                                        <div class="progress-bar {{ $color }}" role="progressbar"
                                            style="width: {{ $porcentaje > 100 ? 100 : $porcentaje }}%">
                                        </div>
                                    </div>
                                    <div class="mt-1 text-muted text-end" style="font-size: 0.7rem;">
                                        ${{ number_format($uso, 2) }} / ${{ number_format($techo, 2) }}
                                    </div>
                                </td>

                                {{-- PRESUPUESTO --}}
                                <td class="text-end">
                                    <div class="fw-bold text-slate-800">
                                        ${{ number_format($programa->monto_planificado, 2) }}</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">Planificado PAI</small>
                                </td>
                                {{-- ACCIONES --}}
                                <td class="text-end pe-3">
                                    <div class="btn-group shadow-sm">
                                        @if ($programa->nombre_archivo)
                                            <a href="{{ asset('storage/' . $programa->url_documento) }}"
                                                target="_blank" class="btn btn-sm btn-outline-danger"
                                                title="Ver Documento Habilitante">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        @else
                                            <span class="text-muted border">
                                                <i class="fas fa-file-pdf"></i>
                                            </span>
                                        @endif
                                        {{-- Show --}}
                                        <a href="{{ route('inversion.programas.show', $programa->id) }}"
                                                class="btn btn-sm btn-light border text-info" title="Ver Detalle y ODS">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            {{-- Editar --}}
                                        <a href="{{ route('inversion.programas.edit', $programa->id) }}"
                                            class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('inversion.programas.destroy', $programa->id) }}"
                                            method="POST" class="form-eliminar">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i>
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
    <script>
        //Función para hacer clic en la fila
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
                } else {
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

            // Ejecutar al cargar
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

        // Seleccionamos todos los formularios que tengan la clase 'form-eliminar'

        document.addEventListener('DOMContentLoaded', function() {

            // CONFIRMACIÓN DE ELIMINAR
            const formularios = document.querySelectorAll('.form-eliminar');

            if (formularios.length > 0) {
                console.log('Se encontraron ' + formularios.length + ' formularios.');
            } else {
                console.error('No se encontró ningún formulario con la clase .form-eliminar');
            }

            formularios.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // Detiene el envío real

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡No podrás revertir esto! Se eliminará el programa.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminarlo',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit(); // Envía el formulario si se confirma
                        }
                    });
                });
            });

            // MENSAJE DE ÉXITO (AL VOLVER DEL CONTROLADOR)
            @if (session('eliminar') == 'ok')
                Swal.fire(
                    '¡Eliminado!',
                    'El registro ha sido eliminado correctamente.',
                    'success'
                )
            @endif

            @if (session('error'))
                Swal.fire(
                    'Error',
                    '{{ session('error') }}',
                    'error'
                )
            @endif
        });
    </script>
@endsection
