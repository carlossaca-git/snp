@extends('layouts.app') {{-- Ajusta a tu layout --}}

@section('content')
    <x-layouts.header_content titulo="Gestión de Unidades Ejecutoras"
        subtitulo="{{ Auth::user()->organizacion->nom_organizacion }}">
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalCrear">
                <i class="fas fa-plus me-2"></i> Nueva Unidad
            </button>
        </div>

    </x-layouts.header_content>

    <div class="container-fluid py-4">
        @include('partials.mensajes')
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Nombre Unidad / Área</th>
                                <th>Código</th>
                                <th>Responsable</th>
                                <th>Estado</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($unidades as $item)
                                <tr>
                                    <td class="ps-4 fw-bold">{{ $item->nombre_unidad }}</td>
                                    <td>{{ $item->codigo_unidad ?? '-' }}</td>
                                    <td>
                                        @if ($item->nombre_responsable)
                                            <i class="fas fa-user-tie text-muted me-1"></i> {{ $item->nombre_responsable }}
                                        @else
                                            <span class="text-muted small">No asignado</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->estado)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        {{-- EDITAR  --}}
                                        <button type="button" class="btn btn-sm btn-warning me-1 btn-editar"
                                            data-id="{{ $item->id }}" data-nombre="{{ $item->nombre_unidad }}"
                                            data-codigo="{{ $item->codigo_unidad }}"
                                            data-responsable="{{ $item->nombre_responsable }}"
                                            data-estado="{{ $item->estado }}" data-bs-toggle="modal"
                                            data-bs-target="#modalEditar">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        {{--  ELIMINAR () --}}
                                        <form action="{{ route('institucional.unidades.destroy', $item->id) }}"
                                            method="POST" class="d-inline form-eliminar">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No hay unidades registradas. ¡Crea la primera!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CREAR --}}
    @include('dashboard.estrategico.organizaciones.unidades.create')

    {{-- MODAL EDITAR --}}
    @include('dashboard.estrategico.organizaciones.unidades.edit')
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const botonesEditar = document.querySelectorAll('.btn-editar');
            const formEditar = document.getElementById('formEditar');

            botonesEditar.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Obtenemos datos del botón
                    const id = this.dataset.id;
                    const nombre = this.dataset.nombre;
                    const codigo = this.dataset.codigo;
                    const resp = this.dataset.responsable;
                    const estado = this.dataset.estado;
                    let url = "{{ route('institucional.unidades.update', ':id') }}";
                    url = url.replace(':id', id);
                    formEditar.action = url;

                    // Llenamos los inputs
                    document.getElementById('edit_nombre').value = nombre;
                    document.getElementById('edit_codigo').value = codigo;
                    document.getElementById('edit_responsable').value = resp;
                    document.getElementById('edit_estado').value = estado;
                });
            });

            const mensajeStatus = @json(session('status'));
            if (mensajeStatus) {
                Swal.fire('Éxito', mensajeStatus, 'success');
            }

            // Confirmación para eliminar

            // Seleccionar todos los formularios de eliminar
            const formularios = document.querySelectorAll('.form-eliminar');

            formularios.forEach(form => {
                form.addEventListener('submit', function(e) {
                    //  DETENER el envío automático
                    e.preventDefault();

                    //  Mostrar la alerta
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡Este plan se moverá a la papelera!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminarlo',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });

        });
    </script>
@endsection
