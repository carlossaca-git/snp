@extends('layouts.app')

@section('content')

    <div class="container-fluid">

        <div class="container mx-auto py-8">
            {{-- CABECERA --}}
            <div
                class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Usuarios Registrados</h1>

                <div class="btn-toolbar mb-2 mb-md-0">
                    {{-- 1. BOTÓN VOLVER (Actualizado) --}}
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary me-2">
                        <span data-feather="arrow-left"></span> Volver al Resumen
                    </a>

                    {{-- 2. BOTÓN NUEVO USUARIO (Añadido para que puedas crear) --}}
                    <a href="{{ route('administracion.usuarios.create') }}" class="btn btn-sm btn-primary me-2">
                        <span data-feather="plus"></span> Nuevo Usuario
                    </a>

                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">
                            <span data-feather="share"></span> Share
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">
                            <span data-feather="download"></span> Export
                        </button>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                        <span data-feather="calendar"></span> {{ date('d/m/Y') }}
                    </button>
                </div>
            </div>

            {{-- MENSAJES DE ÉXITO --}}
            @if (session('status'))
                {{-- Usamos clases de Bootstrap alert por si Tailwind no carga bien, o mantenemos las tuyas si prefieres --}}
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <span data-feather="check-circle" class="me-2"></span>
                    {{ session('status') }}
                </div>
            @endif

            {{-- TABLA DE DATOS --}}
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal table table-hover"> {{-- Agregué table-hover de bootstrap --}}
                    <thead>
                        <tr
                            class="bg-gray-100 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="px-5 py-3">Identificación</th>
                            <th class="px-5 py-3">Usuario</th>
                            <th class="px-5 py-3">Correo</th>
                            <th class="px-5 py-3">Rol / Perfil</th>
                            <th class="px-5 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($usuarios as $usuario)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-150">

                                {{-- Identificación --}}
                                <td class="px-5 py-4 text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap font-bold">{{ $usuario->identificacion }}</p>
                                </td>

                                {{-- Nombre Completo --}}
                                <td class="px-5 py-4 text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">
                                        {{ $usuario->nombres }} {{ $usuario->apellidos }}
                                    </p>
                                    <small class="text-muted">{{ $usuario->usuario }}</small>
                                </td>

                                {{-- Correo --}}
                                <td class="px-5 py-4 text-sm">
                                    <p class="text-gray-600 whitespace-no-wrap">{{ $usuario->correo_electronico }}</p>
                                </td>

                                {{-- Roles --}}
                                <td class="px-5 py-4 text-sm">
                                    @foreach ($usuario->roles as $rol)
                                        <span class="badge bg-primary text-white">
                                            {{ $rol->nombre_rol }}
                                        </span>
                                    @endforeach
                                </td>

                                {{-- ACCIONES (Rutas Actualizadas) --}}
                                <td class="px-5 py-4 text-sm text-center">
                                    <div class="d-flex justify-content-center gap-2">

                                        {{-- Botón Editar --}}
                                        {{-- Laravel inyecta automáticamente el ID si pasas el objeto $usuario --}}
                                        <a href="{{ route('administracion.usuarios.edit', $usuario) }}"
                                            class="btn btn-sm btn-outline-warning" title="Editar">
                                            <span data-feather="edit"></span>
                                        </a>

                                        {{-- Botón Eliminar --}}
                                        <form action="{{ route('administracion.usuarios.destroy', $usuario) }}"
                                            method="POST" class="d-inline forms-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                                onclick="return confirm('¿Estás seguro de que deseas eliminar a {{ $usuario->usuario }}?');">
                                                <span data-feather="trash-2"></span>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    No se encontraron usuarios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- PAGINACIÓN --}}
                <div class="px-5 py-5 bg-white border-t d-flex justify-content-center">
                    {{ $usuarios->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
