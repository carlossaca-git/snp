<div class="max-w-4xl mx-auto mt-8">
    <!-- Contenedor Principal: Fondo blanco, bordes redondeados y sombra suave -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">

        <!-- Encabezado del Formulario -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">
                {{ __('Registro de Usuarios') }}
            </h3>
            <p class="text-sm text-gray-600">Rellene todos los campos requeridos.</p>
        </div>
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                {{ session('success') }}
            </div>
        @endif
        <!-- Cuerpo del Formulario -->
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm border-0">
                <strong>¡Atención! No se pudo guardar:</strong>
                <ul class="mt-2 mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('register') }}" class="p-8 space-y-6">
            @csrf

            <!-- Usamos un Grid para poner campos uno al lado del otro en pantallas medianas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                <!-- Campo: Identificación -->
                <div class="space-y-1">
                    <label for="identificacion" class="block text-sm font-semibold text-gray-700">Número de
                        Identificación</label>
                    <input type="text" name="identificacion" id="identificacion"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 @error('identificacion') border-red-500 @enderror"
                        placeholder="Ej: 17263544" value="{{ old('identificacion') }}" required>
                    @error('identificacion')
                        <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Campo: Usuario -->
                <div class="space-y-1">
                    <label for="usuario" class="block text-sm font-semibold text-gray-700">Nombre de Usuario</label>
                    <input type="text" name="usuario" id="usuario"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200"
                        placeholder="Ej: jsmith" value="{{ old('usuario') }}" required>
                </div>

                <!-- Campo: Nombres -->
                <div class="space-y-1">
                    <label for="nombres" class="block text-sm font-semibold text-gray-700">Nombres</label>
                    <input type="text" name="nombres" id="nombres"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200"
                        placeholder="Nombres completos" value="{{ old('nombres') }}" required>
                </div>

                <!-- Campo: Apellidos -->
                <div class="space-y-1">
                    <label for="apellidos" class="block text-sm font-semibold text-gray-700">Apellidos</label>
                    <input type="text" name="apellidos" id="apellidos"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200"
                        placeholder="Apellidos completos" value="{{ old('apellidos') }}" required>
                </div>

                <!-- Campo: Email (Ocupa las dos columnas en tablets/desktop) -->
                <div class="space-y-1 md:col-span-2">
                    <label for="correo_electronico" class="block text-sm font-semibold text-gray-700">Correo Electrónico
                        Corporativo</label>
                    <input type="email" name="correo_electronico" id="correo_electronico"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200"
                        placeholder="usuario@empresa.com" value="{{ old('correo_electronico') }}" required>
                    <p class="text-xs text-gray-500 italic">Este correo será verificado por el sistema.</p>
                </div>

                <!-- Campo: Contraseña -->
                <div class="space-y-1">
                    <label for="password" class="block text-sm font-semibold text-gray-700">Contraseña Temporal</label>
                    <input type="password" name="password" id="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200"
                        required>
                </div>

                <!-- Campo: Confirmar Contraseña -->
                <div class="space-y-1">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">Confirmar
                        Contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200"
                        required>
                </div>
                <div class="mb-3">
                    <label for="id_rol" class="form-label">Rol del Usuario</label>
                    <select name="id_rol" id="id_rol" class="form-select" required>
                        <option value="" selected disabled>Seleccione un rol...</option>
                        @foreach ($roles as $rol)
                            <option value="{{ $rol->id_rol }}">{{ $rol->nombre_rol }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- Botón de Envío: Estilo botón primario de Laravel -->
            <div class="flex items-center justify-end pt-6 border-t border-gray-100">
                <button type="reset"
                    class="mr-4 text-sm font-semibold text-gray-600 hover:text-gray-900 transition underline">
                    Limpiar Campos
                </button>
                <button type="submit"
                    class="px-6 py-2 bg-indigo-600 border border-transparent rounded-lg font-bold text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 active:bg-indigo-800 transition duration-150 ease-in-out shadow-lg">
                    Guardar Usuario
                </button>
            </div>
        </form>
    </div>
</div>
