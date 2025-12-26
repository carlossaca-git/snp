@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Editar Usuario: {{ $user->nombres }}</h2>

        <form action="{{ route('admin.users.update', $user->id_usuario) }}" method="POST">
            @csrf
            @method('PUT') <!-- OBLIGATORIO PARA ACTUALIZAR -->

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Identificación --}}
                <div class="mb-3">
                    <label class="block text-gray-700">Identificación</label>
                    <input type="text" name="identificacion" value="{{ old('identificacion', $user->identificacion) }}" class="w-full border rounded p-2">
                </div>

                {{-- Rol --}}
                <div class="mb-3">
                    <label class="block text-gray-700">Rol</label>
                    <select name="id_rol" class="w-full border rounded p-2">
                        @foreach($roles as $role)
                            <option value="{{ $role->id_rol }}" {{ $rolActual == $role->id_rol ? 'selected' : '' }}>
                                {{ $role->nombre_rol }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Nombres --}}
                <div class="mb-3">
                    <label class="block text-gray-700">Nombres</label>
                    <input type="text" name="nombres" value="{{ old('nombres', $user->nombres) }}" class="w-full border rounded p-2">
                </div>

                {{-- Apellidos --}}
                <div class="mb-3">
                    <label class="block text-gray-700">Apellidos</label>
                    <input type="text" name="apellidos" value="{{ old('apellidos', $user->apellidos) }}" class="w-full border rounded p-2">
                </div>

                {{-- Correo (Ancho completo) --}}
                <div class="md:col-span-2 mb-3">
                    <label class="block text-gray-700">Correo Electrónico</label>
                    <input type="email" name="correo_electronico" value="{{ old('correo_electronico', $user->correo_electronico) }}" class="w-full border rounded p-2">
                </div>

                {{-- Password (Aclarar que es opcional) --}}
                <div class="md:col-span-2 bg-gray-50 p-4 rounded border">
                    <p class="text-sm text-gray-500 mb-2 font-bold italic">Dejar en blanco si no desea cambiar la contraseña</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="password" name="password" placeholder="Nueva contraseña" class="border rounded p-2">
                        <input type="password" name="password_confirmation" placeholder="Confirmar nueva contraseña" class="border rounded p-2">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <a href="{{ route('admin.users.index') }}" class="text-gray-600 underline">Cancelar</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Actualizar Datos</button>
            </div>
        </form>
    </div>
</div>
@endsection
