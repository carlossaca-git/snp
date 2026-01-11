@extends('layouts.app')

@section('content')
    <style></style>

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

                    {{-- <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">
                            <span data-feather="share"></span> Share
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">
                            <span data-feather="download"></span> Export
                        </button>
                    </div> --}}

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
            <div class="bg-white shadow-md rounded-lg overflow-x-auto w-full">
                <table class="min-w-full leading-normal table table-hover">
                    <thead>
                        <tr
                            class="bg-gray-100 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="px-5 py-3">Identificación</th>
                            <th class="px-5 py-3">Usuario</th>
                            <th class="px-5 py-3">Correo</th>
                            <th class="px-5 py-3">Rol / Perfil</th>
                            <th class="px-5 py-3">Organizacion</th>
                            <th class="px-5 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($usuarios as $usuario)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-150 mobile-row">

                                {{-- Identificación --}}
                                <td class="px-5 py-4 text-sm" data-label="Identificación"> {{-- <--- OJO AQUÍ --}}
                                    <p class="text-gray-900 whitespace-no-wrap font-bold">{{ $usuario->identificacion }}</p>
                                </td>

                                {{-- Usuario --}}
                                <td class="px-5 py-4 text-sm" data-label="Usuario">
                                    <p class="text-gray-900 whitespace-no-wrap">
                                        {{ $usuario->nombres }} {{ $usuario->apellidos }}
                                    </p>
                                    <small class="text-muted">{{ $usuario->usuario }}</small>
                                </td>

                                {{-- Correo --}}
                                <td class="px-5 py-4 text-sm" data-label="Correo">
                                    <p class="text-gray-600 whitespace-no-wrap">{{ $usuario->correo_electronico }}</p>
                                </td>
                                {{-- Rol --}}
                                <td class="px-5 py-4 text-sm" data-label="Rol">
                                    @forelse ($usuario->roles as $rol)
                                        <span class="badge rounded-pill bg-primary bg-gradient shadow-sm">
                                            {{ $rol->nombre }}
                                        </span>
                                    @empty
                                        <span class="badge rounded-pill bg-secondary text-white-50">Sin Rol</span>
                                    @endforelse
                                </td>

                                {{-- Organizacion --}}
                                <td class="px-5 py-4 text-sm" data-label="Organización">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center text-primary">
                                            <i class="fas fa-building fa-xs" aria-hidden="true"></i>
                                        </div>
                                        <div>
                                            <span class="text-dark fw-bold d-block text-sm">

                                                {{ $usuario->organizacion->nom_organizacion ?? 'Sin Organización' }}
                                            </span>
                                            {{-- Opcional: mostrar el RUC o código si lo tienes --}}
                                            <span class="text-xs text-secondary">
                                                {{ $usuario->organizacion->ruc ?? '' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Acciones --}}
                                <td class="px-5 py-4 text-sm text-center" data-label="Acciones">
                                    <div class="d-flex justify-content-center gap-2">
                                        {{-- Tus botones de acción aquí... --}}
                                        <a href="{{ route('administracion.usuarios.edit', $usuario) }}"
                                            class="btn btn-sm btn-outline-warning"><span data-feather="edit"></span></a>
                                        <form action="{{ route('administracion.usuarios.destroy', $usuario) }}"
                                            method="POST" class="d-inline forms-delete">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><span
                                                    data-feather="trash-2"></span></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- ... --}}
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
