@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Gestión de Roles" subtitulo="Gestion de roles y permisos del sistema">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('administracion.roles.create') }}"
                class="btn btn-sm  btn-secondary me-2 d-inline-flex align-items-center">
                <span data-feather="plus"></span> Nuevo Rol
            </a>
        </div>

    </x-layouts.header_content>
    @include('partials.mensajes')
    <div class="container mx-auto py-3">
        <div class="container-fluid">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-slate-800">
                            <i class="fas fa-list-ul me-2 text-muted"></i> Listado de Roles del Sistema
                        </h6>
                        <span class="badge bg-light text-dark border rounded-pill">Total:
                            {{ $roles->count() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class=" text-uppercase text-secondary small fw-bold">Nombre del Rol</th>
                                    <th class=" text-uppercase text-secondary small fw-bold">Descripcion</th>
                                    <th class=" text-uppercase text-secondary small fw-bold">Permisos</th>
                                    <th class=" text-uppercase text-secondary small fw-bold">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse($roles as $rol)
                                    <tr>
                                        <td>
                                            <strong>{{ $rol->nombre_corto }}</strong>
                                            @if ($rol->nombre_corto == 'Admin General')
                                                <span class="badge bg-danger text-white">Super Admin</span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">
                                            {{ Str::limit($rol->descripcion, 50) }}
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-white">
                                                {{ $rol->permisos_count }} permisos
                                            </span>
                                        </td>
                                        <td class="px-5 text-sm text-center">
                                            <div class="btn-group" role="group">
                                                {{-- BOTÓN EDITAR: Aquí pasamos el ID_ROL para evitar el error --}}
                                                <a href="{{ route('administracion.roles.edit', $rol->id_rol) }}"
                                                    class="btn btn-warning btn-sm" title="Editar Permisos">
                                                    <i class="fas fa-key"></i>
                                                </a>

                                                {{-- BOTÓN ELIMINAR --}}

                                                <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="confirmarEliminacion('{{ $rol->id_rol }}', '{{ $rol->nombre }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            No hay roles registrados. ¡Crea el primero!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- MODAL DE CONFIRMAR ELIMINACION DE ROL --}}
    <div class="modal fade" id="modalDelete" tabindex="-1">
        <div class="modal-dialog">
            <form id="formDelete" action="" method="POST" class="modal-content">
                @csrf
                @method('DELETE')

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Eliminar Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>¿Estás seguro que deseas eliminar el rol <strong id="rolNombre"></strong>?</p>
                    <p class="text-danger small">Esta acción no se puede deshacer.</p>

                    <div class="form-group">
                        <label>Confirma tu contraseña:</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Sí, Eliminar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function confirmarEliminacion(id, nombre) {
            // 1. Ponemos la ruta correcta en el formulario
            // OJO: Ajusta la ruta base según tu sistema ('/admin/roles/')
            let actionUrl = "{{ route('administracion.roles.destroy', ':id') }}";
            actionUrl = actionUrl.replace(':id', id);

            document.getElementById('formDelete').action = actionUrl;

            // 2. Ponemos el nombre en el texto
            document.getElementById('rolNombre').innerText = nombre;

            // 3. Abrimos el modal
            var myModal = new bootstrap.Modal(document.getElementById('modalDelete'));
            myModal.show();
        }
    </script>
@endpush
