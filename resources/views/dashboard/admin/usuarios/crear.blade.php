@extends('layouts.app')

@section('content')
    <style>
        .text-slate-800 {
            color: #1e293b;
        }

        .card-clean {
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
        }
    </style>

    <div class="container mx-auto py-5">
        {{-- ENCABEZADO --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold text-slate-800 mb-1">Registro de Usuarios</h1>
                <p class="text-secondary mb-0">Creación de cuentas de acceso al sistema SIPeIP</p>
            </div>
            <a href="{{ route('administracion.usuarios.index') }}"
                class="btn btn-outline-secondary me-2 d-inline-flex align-items-center">
                <i data-feather="arrow-left" class="me-1"></i> Volver al Listado
            </a>
        </div>

        {{-- ALERTAS DE ERROR --}}
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm border-0 mb-4">
                <div class="fw-bold"><i data-feather="alert-circle" class="me-2"></i>Por favor, corrige los siguientes
                    errores:</div>
                <ul class="mb-0 mt-2 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card card-clean rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-slate-800">
                    <i data-feather="user-plus" class="me-2 text-primary"></i>Información del Nuevo Usuario
                </h6>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('administracion.usuarios.store') }}">
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 small">Identificación (Cédula/RUC)</label>
                            <input type="text" name="identificacion" value="{{ old('identificacion') }}"
                                class="form-control @error('identificacion') is-invalid @enderror" required maxlength="13">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 small">Organización / Entidad</label>
                            @if (auth()->user()->tieneRol('SUPER_ADMIN'))
                                <select name="id_organizacion"
                                    class="form-select @error('id_organizacion') is-invalid @enderror" required>
                                    <option value="">Seleccione la Entidad Pública...</option>
                                    @foreach ($organizaciones as $org)
                                        <option value="{{ $org->id_organizacion }}"
                                            {{ old('id_organizacion') == $org->id_organizacion ? 'selected' : '' }}>
                                            {{ $org->nom_organizacion }} ({{ $org->siglas }})
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" class="form-control"
                                    value="{{ auth()->user()->organizacion->nom_organizacion }}" disabled>
                                <input type="hidden" name="id_organizacion" value="{{ auth()->user()->id_organizacion }}">
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 small">Nombres</label>
                            <input type="text" name="nombres" value="{{ old('nombres') }}"
                                class="form-control @error('nombres') is-invalid @enderror" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 small">Apellidos</label>
                            <input type="text" name="apellidos" value="{{ old('apellidos') }}"
                                class="form-control @error('apellidos') is-invalid @enderror" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 small">Nombre de Usuario (Login)</label>
                            <input type="text" name="usuario" value="{{ old('usuario') }}"
                                class="form-control @error('usuario') is-invalid @enderror" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 small">Asignar Perfil / Rol</label>
                            <select name="roles[]" class="form-select @error('roles') is-invalid @enderror" required>
                                <option value="">Seleccione un perfil...</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id_rol }}"
                                        {{ is_array(old('roles')) && in_array($role->id_rol, old('roles')) ? 'selected' : '' }}>
                                        {{ $role->nombre_corto }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted small">Define los permisos de acceso al sistema.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 small">Correo Electrónico</label>
                            <input type="email" name="correo_electronico" value="{{ old('correo_electronico') }}"
                                class="form-control @error('correo_electronico') is-invalid @enderror" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 small d-block">Estado de Verificación</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="checkVerificado"
                                    name="verificado" value="1" {{ old('verificado') ? 'checked' : '' }}>
                                <label class="form-check-label text-slate-500 small" for="checkVerificado">
                                    Verificar cuenta inmediatamente
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 small">Contraseña</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" required>
                                <button type="button" onclick="generatePassword()" class="btn btn-outline-primary"
                                    title="Generar clave segura">
                                    <i data-feather="key" style="width: 16px;"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-slate-800 small">Confirmar Contraseña</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control" required>
                        </div>
                    </div>

                    <div class="mt-5 pt-3 border-top text-end  ">
                        <button type="submit" class="btn btn-primary shadow-sm  me-2 d-inline-flex align-items-center">
                            <i data-feather="save" class="me-2" style="width: 18px;"></i> Guardar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function generatePassword() {
            const length = 12; // Aumenté un poco la seguridad
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$";
            let retVal = "";
            for (let i = 0; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * charset.length));
            }
            const passField = document.getElementById('password');
            const confirmField = document.getElementById('password_confirmation');

            passField.value = retVal;
            confirmField.value = retVal;

            // Mostrar temporalmente para que el admin pueda copiarla si necesita enviarla
            passField.type = 'text';
            setTimeout(() => {
                passField.type = 'password';
            }, 5000); // 5 segundos
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') feather.replace();
        });
    </script>
@endsection
