@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Registro de Usuarios</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="{{ route('dashboard') }}"
                    class="btn btn-sm btn-outline-secondary">
                    <span data-feather="arrow-left"></span> Volver al Listado
                </a>
                <button type="button" class="btn btn-sm btn-outline-secondary">
                    <span data-feather="calendar"></span> {{ date('d/m/Y') }}
                </button>
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary"></span
                            data-feather="share"></span>Share</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary"><span
                            data-feather=""></span>Export</button>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                    <span data-feather="calendar"></span>
                    Esta semana
                </button>
            </div>
        </div>

        <div class="container py-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0 fw-bold text-dark">
                        <span data-feather="user-plus" class="me-2"></span>Registrar Nuevo Usuario
                    </h6>
                </div>

                <div class="card-body p-4">
                    @if (session('status'))
                        <div id="alert-success" class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>¡Éxito!</strong> {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- CORRECCIÓN: Usamos 'admin.users.store' que es el nombre en tu web.php --}}
                    <form method="POST" action="{{ route('administracion.usuarios.store') }}">
                        @csrf

                        <div class="row g-3">
                            <!-- Nombres -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Nombres</label>
                                <input type="text" name="nombres" value="{{ old('nombres') }}"
                                    class="form-control text-capitalize @error('nombres') is-invalid @enderror" required>
                            </div>

                            <!-- Usuario -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Nombre de Usuario (Login)</label>
                                <input type="text" name="usuario" value="{{ old('usuario') }}"
                                    class="form-control @error('usuario') is-invalid @enderror" required>
                            </div>

                            <!-- Apellidos -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Apellidos</label>
                                <input type="text" name="apellidos" value="{{ old('apellidos') }}"
                                    class="form-control text-capitalize @error('apellidos') is-invalid @enderror" required>
                            </div>

                            <!-- Identificación -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Identificación (Cédula/RUC)</label>
                                <input type="text" name="identificacion" value="{{ old('identificacion') }}"
                                    class="form-control @error('identificacion') is-invalid @enderror" required>
                            </div>

                            <!-- Rol -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Rol del Sistema</label>
                                <select name="id_rol" class="form-select @error('id_rol') is-invalid @enderror" required>
                                    <option value="">Seleccione un perfil...</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id_rol }}"
                                            {{ old('id_rol') == $role->id_rol ? 'selected' : '' }}>
                                            {{ $role->nombre_rol }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Correo Electrónico -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Correo Electrónico</label>
                                <input type="email" name="correo_electronico" value="{{ old('correo_electronico') }}"
                                    class="form-control @error('correo_electronico') is-invalid @enderror" required>
                                @error('correo_electronico')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Contraseña -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Contraseña</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror" required>
                                    <button type="button" onclick="generatePassword()" class="btn btn-outline-secondary">
                                        <span data-feather="key"></span>
                                    </button>
                                </div>
                            </div>

                            <!-- Confirmación Contraseña -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Confirmar Contraseña</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-control" required>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-grid">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                <span data-feather="save" class="me-2"></span> Registrar Usuario en seg_usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function generatePassword() {
            const length = 10;
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
            let retVal = "";
            for (let i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            document.getElementById('password').value = retVal;
            document.getElementById('password_confirmation').value = retVal;
            // Cambiar tipo a texto para que el usuario vea la clave generada momentáneamente
            document.getElementById('password').type = 'text';
            setTimeout(() => {
                document.getElementById('password').type = 'password';
            }, 3000);
        }

        // Auto-ocultar alerta
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    </script>
@endsection
