@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Gestión de Usuarios</h2>
            <a href="{{ route('admin.users.create') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow">
                + Nuevo Usuario
            </a>
        </div>

        @if (session('status'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
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
                    @foreach ($usuarios as $usuario)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-150">
                            <td class="px-5 py-4 text-sm">
                                <p class="text-gray-900 whitespace-no-wrap font-bold">{{ $usuario->identificacion }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $usuario->nombres }}
                                    {{ $usuario->apellidos }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm">
                                <p class="text-gray-600 whitespace-no-wrap">{{ $usuario->correo_electronico }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm">
                                @foreach ($usuario->roles as $rol)
                                    <span
                                        class="relative inline-block px-3 py-1 font-semibold text-indigo-900 leading-tight">
                                        <span aria-hidden
                                            class="absolute inset-0 bg-indigo-200 opacity-50 rounded-full"></span>
                                        <span class="relative text-xs">{{ $rol->nombre_rol }}</span>
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-5 py-4 text-sm text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.users.edit', $usuario->id_usuario) }}">Editar</a>

                                    <!-- Formulario Eliminar -->
                                    <form action="{{ route('admin.users.destroy', $usuario->id_usuario) }}" method="POST"
                                        onsubmit="return confirm('¿Estás seguro de que deseas eliminar permanentemente a este usuario? Esta acción no se puede deshacer.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Paginación de Laravel -->
            <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
                {{ $usuarios->links() }}
            </div>
        </div>
    </div>
@endsection
