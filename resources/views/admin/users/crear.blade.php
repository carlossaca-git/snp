@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto py-6">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-gray-100 px-6 py-4 border-b">
                <h3 class="text-xl font-bold text-gray-700">Registrar Nuevo Usuario</h3>
            </div>

            <div class="p-6">
                @if (session('status'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm rounded"
                        role="alert">
                        <p class="font-bold">¡Registro Exitoso!</p>
                        <p>{{ session('status') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nombres -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombres</label>
                            <input type="text" name="nombres"
                                class="text-capitalize mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <!-- Identificación -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Usuario</label>
                            <input type="text" name="usuario"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                required>
                        </div>
                        <!-- Apellidos -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Apellidos</label>
                            <input type="text" name="apellidos"
                                class="text-capitalize mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <!-- Identificación -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Identificación</label>
                            <input type="text" name="identificacion"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                required>
                        </div>

                        <!-- Rol -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Rol</label>
                            <select name="id_rol" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Seleccione un perfil...</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id_rol }}">{{ $role->nombre_rol }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- CORREO: Ancho completo con col-span-2 -->
                        <!-- Sección de Correo y Confirmación con Grid de Tailwind -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- Correo Electrónico -->
                            <div class="mb-4">
                                <label for="correo_electronico" class="block text-sm font-medium text-gray-700">
                                    {{ __('Correo Electrónico') }}
                                </label>
                                <input type="email" name="correo_electronico" id="correo_electronico"
                                    value="{{ old('correo_electronico') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('correo_electronico') border-red-500 @enderror"
                                    required>
                                @error('correo_electronico')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirmar Correo Electrónico -->
                            <div class="mb-4">
                                <label for="correo_electronico_confirmation"
                                    class="block text-sm font-medium text-gray-700">
                                    {{ __('Confirmar Correo Electrónico') }}
                                </label>
                                <input type="email" name="correo_electronico_confirmation"
                                    id="correo_electronico_confirmation"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                            </div>

                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Contraseña</label>
                            <div class="flex mt-1">
                                <input type="password" id="password" name="password"
                                    class="block w-full border-gray-300 rounded-l-md shadow-sm" required>
                                <button type="button" onclick="generatePassword()"
                                    class="bg-gray-200 px-4 py-2 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-300">Gen</button>
                            </div>
                        </div>

                        <!-- Confirmación -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                    </div>

                    <div class="mt-8">
                        <button type="submit"
                            class="w-full bg-gray-700 text-white font-bold py-3 px-4 rounded-md hover:bg-gray-800 transition duration-300">
                            Registrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @if(session('status'))
    <script>
        setTimeout(() => {
            const alert = document.getElementById('alert-success');
            if(alert) {
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000); // Se oculta tras 5 segundos
    </script>
@endif
@endsection


