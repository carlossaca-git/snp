@extends('layouts.app')

@section('content')
    <style>
        .text-slate-800 { color: #1e293b; }
        .card-clean { border: 1px solid #e2e8f0; box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1); }
    </style>

    <div class="container mx-auto py-5">
        {{-- ENCABEZADO --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold text-slate-800 mb-1">Editar Usuario</h1>
                <p class="text-secondary mb-0">Actualización de datos para: <strong>{{ $usuario->usuario }}</strong></p>
            </div>
            <a href="{{ route('administracion.usuarios.index') }}" class="btn btn-outline-secondary shadow-sm d-inline-flex align-items-center">
                <i data-feather="arrow-left" class="me-1"></i> Volver al Listado
            </a>
        </div>

        {{-- ALERTAS DE ERROR --}}
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm border-0 mb-4">
                <div class="fw-bold"><i data-feather="alert-circle" class="me-2"></i>No se pudo actualizar:</div>
                <ul class="mb-0 mt-2 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card card-clean rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-slate-800">
                    <i data-feather="edit-3" class="me-2 text-warning"></i>Formulario de Edición
                </h6>
                <span class="badge {{ $usuario->estado ? 'bg-success' : 'bg-danger' }}">
                    {{ $usuario->estado ? 'ACTIVO' : 'INACTIVO' }}
                </span>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('administracion.usuarios.update', $usuario->id_usuario) }}">
                    @csrf
                    @method('PUT') {{-- IMPORTANTE: Directiva para actualizar --}}

                    <div class="row g-4">

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 small">Identificación</label>
                            <input type="text" name="identificacion" value="{{ old('identificacion', $usuario->identificacion) }}"
                                class="form-control @error('identificacion') is-invalid @enderror" required maxlength="13">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 small">Organización / Entidad</label>
                            <select name="id_organizacion" class="form-select @error('id_organizacion') is-invalid @enderror" required>
                                <option value="">Seleccione...</option>
                                @foreach ($organizaciones as $org)
                                    <option value="{{ $org->id_organizacion }}"
                                        {{ old('id_organizacion', $usuario->id_organizacion) == $org->id_organizacion ? 'selected' : '' }}>
                                        {{ $org->nom_organizacion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 small">Nombres</label>
                            <input type="text" name="nombres" value="{{ old('nombres', $usuario->nombres) }}"
                                class="form-control @error('nombres') is-invalid @enderror" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 small">Apellidos</label>
                            <input type="text" name="apellidos" value="{{ old('apellidos', $usuario->apellidos) }}"
                                class="form-control @error('apellidos') is-invalid @enderror" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 small">Nombre de Usuario</label>
                            <input type="text" value="{{ $usuario->usuario }}" class="form-control bg-light" readonly disabled
                                title="El nombre de usuario no se puede modificar">
                        </div>

                         <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 small">Perfil / Rol Asignado</label>
                            <select name="id_rol" class="form-select @error('id_rol') is-invalid @enderror" required>
                                <option value="">Seleccione...</option>
                                @php
                                    // Obtenemos el ID del rol actual (si tiene)
                                    $rolActualId = $usuario->roles->first()?->id_rol;
                                @endphp
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id_rol }}"
                                        {{ old('id_rol', $rolActualId) == $role->id_rol ? 'selected' : '' }}>
                                        {{ $role->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 small">Correo Electrónico</label>
                            <input type="email" name="correo_electronico" value="{{ old('correo_electronico', $usuario->correo_electronico) }}"
                                class="form-control @error('correo_electronico') is-invalid @enderror" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold text-slate-800 small">Estado del Usuario</label>
                            <select name="estado" class="form-select">
                                <option value="1" {{ $usuario->estado == 1 ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ $usuario->estado == 0 ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold text-slate-800 small d-block">Verificación Email</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="checkVerificado"
                                    name="verificado" value="1"
                                    {{ old('verificado', $usuario->email_verified_at) ? 'checked' : '' }}>
                                <label class="form-check-label text-slate-500 small" for="checkVerificado">
                                    Cuenta Verificada
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr class="my-4 text-muted">
                            <h6 class="text-primary fw-bold"><i data-feather="lock" class="me-2" style="width:16px"></i>Seguridad</h6>
                            <div class="alert alert-light border small text-muted">
                                <i data-feather="info" class="me-1" style="width:14px"></i>
                                Deje los campos de contraseña <strong>vacíos</strong> si desea mantener la clave actual.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 small">Nueva Contraseña</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
                                <button type="button" onclick="generatePassword()" class="btn btn-outline-secondary">
                                    <i data-feather="refresh-cw" style="width: 16px;"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 small">Confirmar Nueva Contraseña</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="mt-5 pt-3 border-top text-end">
                        <button type="submit" class="btn btn-warning px-5 shadow-sm text-dark fw-bold d-inline-flex align-items-center">
                            <i data-feather="save" class="me-2" style="width: 18px;"></i> Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Reutilizamos el script de generación de clave
        function generatePassword() {
            const length = 12;
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$";
            let retVal = "";
            for (let i = 0; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * charset.length));
            }
            document.getElementById('password').value = retVal;
            document.getElementById('password_confirmation').value = retVal;

            const passField = document.getElementById('password');
            passField.type = 'text';
            setTimeout(() => { passField.type = 'password'; }, 5000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') feather.replace();
        });
    </script>
@endsection
