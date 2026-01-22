@extends('layouts.app')

@section('content')
    <style></style>
    <x-layouts.header_content titulo="Gestion de Usuarios" subtitulo="Catalogo de usuarios">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary me-2 d-inline-flex align-items-center">
                <span class="fas fa-home"></span> Inicio
            </a>
            <a href="{{ route('administracion.usuarios.create') }}"
                class="btn btn-sm btn-secondary me-2 d-inline-flex align-items-center">
                <span data-feather="plus"></span> Nuevo Usuario
            </a>
        </div>
    </x-layouts.header_content>

    <div class="container-fluid">
        @include('partials.mensajes')
        <div class="container mx-auto py-8">
            <div class="row justify-content-end align-items-end mb-2">
                <div class="col-md-4">
                    <form action="{{ route('administracion.usuarios.index') }}" method="GET">
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
            {{-- TABLA DE DATOS --}}
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light border-bottom">
                                <tr>
                                    <th class="ps-4 text-uppercase text-secondary small fw-bold" width="35%">Usuario /
                                        Identidad</th>
                                    <th class="text-uppercase text-secondary small fw-bold" width="20%">Rol Asignado</th>
                                    <th class="text-uppercase text-secondary small fw-bold" width="30%">Organización</th>
                                    <th class="text-end pe-4 text-uppercase text-secondary small fw-bold" width="15%">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($usuarios as $usuario)
                                    <tr>
                                        {{-- COLUMNA 1: PERFIL (Avatar + Datos) --}}
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                {{-- Avatar (Generado con iniciales) --}}
                                                <div class="me-3">
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($usuario->nombres) }}&background=primary&color=fff&size=45&font-size=0.4"
                                                        class="rounded-circle" alt="Avatar" width="45" height="45">
                                                </div>
                                                <div>
                                                    {{-- Nombre y Apellido --}}
                                                    <div class="fw-bold text-dark mb-0">
                                                        {{ $usuario->nombres }} {{ $usuario->apellidos }}
                                                    </div>
                                                    {{-- Correo --}}
                                                    <div class="text-muted small">
                                                        {{ $usuario->correo_electronico }}
                                                    </div>
                                                    {{-- Cédula / Usuario (Badge pequeño) --}}
                                                    <div class="d-flex align-items-center mt-1">
                                                        <span
                                                            class="badge bg-light text-secondary border border-light rounded-pill fw-normal px-2 me-2">
                                                            <i class="fas fa-id-card me-1"></i>
                                                            {{ $usuario->identificacion }}
                                                        </span>
                                                        <small class="text-muted fst-italic">{{ $usuario->usuario }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{--  ROL --}}
                                        <td>
                                            @forelse ($usuario->roles as $rol)
                                                {{-- Estilo idéntico a tu referencia (fondo suave) --}}
                                                <span
                                                    class="badge bg-primary bg-opacity-10 text-primary border border-primary fw-bold px-3 py-2">
                                                    {{ $rol->nombre_corto }}
                                                </span>
                                            @empty
                                                <span
                                                    class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-2">
                                                    Sin Rol
                                                </span>
                                            @endforelse
                                        </td>

                                        {{--  ORGANIZACIÓN --}}
                                        <td>
                                            @if ($usuario->organizacion)
                                                <div class="d-flex align-items-start">
                                                    <div class="me-2 mt-1">
                                                        <i class="fas fa-building text-secondary opacity-50"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark" style="font-size: 0.9rem;">
                                                            {{ Str::limit($usuario->organizacion->nom_organizacion, 35) }}
                                                        </div>
                                                        <div class="text-muted small">
                                                            RUC: {{ $usuario->organizacion->ruc }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted small fst-italic">No asignada</span>
                                            @endif
                                        </td>

                                        {{--  ACCIONES --}}
                                        <td class="text-end pe-4">
                                            <div class="btn-group" role="group">
                                                {{-- Ver --}}
                                                <a href="{{ route('administracion.usuarios.show', $usuario->id_usuario) }}"
                                                    class="btn btn-sm btn-light border text-info shadow-sm"
                                                    title="Ver Perfil">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                {{-- Editar --}}
                                                <a href="{{ route('administracion.usuarios.edit', $usuario) }}"
                                                    class="btn btn-sm btn-light border text-warning shadow-sm"
                                                    title="Editar">
                                                    <i class="fas fa-pen"></i>
                                                </a>

                                                {{-- Eliminar --}}
                                                <form action="{{ route('administracion.usuarios.destroy', $usuario) }}"
                                                    method="POST" class="d-inline forms-delete"
                                                    onsubmit="return confirm('¿Está seguro de eliminar este usuario?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-light border text-danger shadow-sm"
                                                        title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div
                                                class="d-flex flex-column align-items-center justify-content-center text-muted">
                                                <i class="fas fa-users-slash fa-3x mb-3 opacity-25"></i>
                                                <h6 class="fw-bold">No se encontraron usuarios</h6>
                                                <p class="small mb-0">Intente ajustar los filtros de búsqueda.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 py-3 border-top bg-light d-flex justify-content-between align-items-center">
                        <div class="small text-muted">
                            @if ($usuarios->total() > 0)
                                Mostrando {{ $usuarios->firstItem() }} - {{ $usuarios->lastItem() }} de
                                <strong>{{ $usuarios->total() }}</strong> usuarios
                            @endif
                        </div>
                        <div>
                            {{ $usuarios->appends(request()->query())->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
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
            toggleLimpiarButton();
            inputBusqueda.addEventListener('input', toggleLimpiarButton);
            btnLimpiar.addEventListener('click', function() {
                inputBusqueda.value = '';
                toggleLimpiarButton();

                inputBusqueda.closest('form').submit();
            });
        });
    </script>
@endpush
