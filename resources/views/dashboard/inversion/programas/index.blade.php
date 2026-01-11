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

    <div class="container-fluid py-4">
        {{-- CABECERA --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold text-slate-800 mb-1">Gestión de Planes y Programas</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"
                                class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item text-slate-500">Gestión de Inversión</li>
                        <li class="breadcrumb-item active fw-bold text-primary">Programas</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('inversion.programas.create') }}"
                class="btn btn-secondary shadow-sm px-3 fw-bold d-inline-flex align-items-center"
                data-tiene-objetivo="{{ $tieneObjetivos ? 'true' : 'false' }}" id="btnNuevoPrograma">
                <i class="fas fa-plus me-1" data-feather="plus"></i> Nuevo Programa
            </a>
        </div>

        {{-- ALERTAS --}}
        @include('partials.mensajes')

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
                                    {{-- 1. CÓDIGO Y ESTADO --}}
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

                                    {{-- 2. NOMBRE Y FUENTE --}}
                                    <td>
                                        <div class="fw-bold text-slate-800 mb-0">
                                            {{ Str::limit($programa->nombre_programa, 50) }}</div>
                                        <small class="text-primary fw-bold" style="font-size: 0.7rem;">
                                            Fuente: {{ $programa->fuente_financiamiento ?? '001' }}
                                        </small>
                                    </td>

                                    {{-- 3. VIGENCIA --}}
                                    <td class="text-center">
                                        <div class="small fw-bold">{{ $programa->anio_inicio }} -
                                            {{ $programa->anio_fin }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Plurianual</div>
                                    </td>

                                    {{-- 4. BARRA DE EJECUCIÓN (Visualmente muy impactante) --}}
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

                                    {{-- 5. PRESUPUESTO --}}
                                    <td class="text-end">
                                        <div class="fw-bold text-slate-800">
                                            ${{ number_format($programa->monto_planificado, 2) }}</div>
                                        <small class="text-muted" style="font-size: 0.7rem;">Planificado PAI</small>
                                    </td>
                                    {{-- 6. ACCIONES --}}
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

        /**
         * Confirmación personalizada para eliminar
         */
        function confirmDelete(event, cup) {
            event.stopPropagation(); // Evita que la fila se redireccione
            if (confirm('¿Está seguro de eliminar el programa ' + cup + '? Esta acción no se puede deshacer.')) {
                event.target.closest('form').submit();
            }
        }
        //Confirmacion para ir a crear objetivos estrategicos
        document.addEventListener('DOMContentLoaded', function() {

        const btnNuevo = document.getElementById('btnNuevoPrograma');

        if(btnNuevo){
            btnNuevo.addEventListener('click', function(e) {
                // 1. IMPORTANTE: Prevenir cualquier comportamiento automático
                e.preventDefault();
                e.stopPropagation();

                // 2. Leemos el atributo (Asegúrate que coincida la escritura)
                const tieneObjetivos = this.getAttribute('data-tiene-objetivos') === 'true';

                // Debug: Mira la consola (F12) para ver si esto imprime true o false
                console.log('¿Tiene objetivos?:', tieneObjetivos);

                if (tieneObjetivos) {
                    // ESCENARIO A: ABRIR MODAL
                    const modalEl = document.getElementById('modalCrearPrograma');

                    if(modalEl) {
                        // Usamos getOrCreateInstance para evitar duplicados que cierran el modal
                        const myModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        myModal.show();
                    } else {
                        console.error('Error: No encuentro el modal con ID "modalCrearPrograma"');
                        alert('Error interno: El ID del modal no coincide.');
                    }

                } else {
                    // ESCENARIO B: MOSTRAR ALERTA
                    Swal.fire({
                        title: 'Faltan Objetivos Estratégicos',
                        text: "Para crear un Programa, primero necesitas definir los Objetivos Estratégicos de la institución.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#74788d',
                        confirmButtonText: 'Ir a crear Objetivos',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('estrategico.objetivos.create') }}";
                        }
                    });
                }
            });
        }
    });
    </script>
@endsection
