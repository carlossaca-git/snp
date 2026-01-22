@extends('layouts.app')
<style>
    .permission-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
        border-radius: 10px;
    }

    .permission-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .card-header-custom {
        border-radius: 10px 10px 0 0 !important;
        background: linear-gradient(45deg, #f8f9fc, #ffffff);
        border-bottom: 2px solid #e3e6f0;
    }

    .module-icon {
        font-size: 1.2rem;
        opacity: 0.8;
    }

    /* Colores por módulo */
    .border-left-Institucional {
        border-left: 4px solid #4e73df !important;
    }

    .border-left-Inversión {
        border-left: 4px solid #1cc88a !important;
    }

    .border-left-Planificación {
        border-left: 4px solid #36b9cc !important;
    }

    .border-left-Seguimiento {
        border-left: 4px solid #f6c23e !important;
    }
</style>

@section('content')
    <x-layouts.header_content titulo="Gestion de permisos" subtitulo="Permisos segun rol de usuario">

        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('administracion.roles.index') }}"
                class="btn btn-sm btn-outline-secondary me-2 d-inline-flex align-items-center">
                <span data-feather="arrow-left"></span> Roles
            </a>
        </div>

    </x-layouts.header_content>
    <div class="container mx-auto py-1">

        @include('partials.mensajes')
        <form action="{{ route('administracion.roles.update', $rol->id_rol) }}" method="POST" id="formEditarRol">
            @csrf
            @method('PUT')
            <div class="row justify-content-start align-items-end mb-2">
                <div class="col-md-5">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Nombre del Rol</label>
                        <div class="input-group">
                            {{-- Input bloqueado con  --}}
                            <input type="text" name="nombre" id="inputNombreRol" value="{{ $rol->nombre_corto }}"
                                class="form-control bg-light" readonly>
                            <button class="btn btn-outline-secondary" type="button" id="btnUnlock"
                                title="Desbloquear edición">
                                <i class="fas fa-lock" id="iconLock"></i>
                            </button>
                        </div>
                        <small class="text-muted" id="textHelp">
                            <i class="fas fa-info-circle"></i> El nombre está bloqueado por seguridad. Click en el candado
                            para editar.
                        </small>
                    </div>
                </div>
            </div>
            <h4 class="mb-3">Asignación de Permisos</h4>
            <div class="row">
                @foreach ($permisosAgrupados as $modulo => $permisos)
                    {{-- Logica para iconos y colores según el módulo --}}
                    @php
                        $icon = 'fa-cogs';
                        $color = 'primary';
                        if (str_contains($modulo, 'Institucional')) {
                            $icon = 'fa-building';
                            $color = 'primary';
                        }
                        if (str_contains($modulo, 'Planificación')) {
                            $icon = 'fa-chart-line';
                            $color = 'info';
                        }
                        if (str_contains($modulo, 'Inversión')) {
                            $icon = 'fa-coins';
                            $color = 'success';
                        }
                        if (str_contains($modulo, 'Seguimiento')) {
                            $icon = 'fa-tasks';
                            $color = 'warning';
                        }
                    @endphp

                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card shadow-sm h-100 permission-card border-left-{{ $modulo }}">

                            {{--  Icono  Titulo  Toggle Todo --}}
                            <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-{{ $color }}">
                                    <i class="fas {{ $icon }} me-2 module-icon"></i> {{ $modulo }}
                                </h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input check-all" type="checkbox"
                                        id="checkAll_{{ Str::slug($modulo) }}" title="Marcar todo en {{ $modulo }}">
                                </div>
                            </div>

                            {{--  Lista de Switches --}}
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    @foreach ($permisos as $permiso)
                                        <label
                                            class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                                            <div class="ms-2">
                                                <span class="fw-bold text-dark">{{ $permiso->nombre_corto }}</span>
                                                <br>
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    {{ $permiso->descripcion ?? 'Sin descripción' }}
                                                </small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                    name="permisos[]" value="{{ $permiso->id_permiso }}"
                                                    {{ in_array($permiso->id_permiso, $permisosAsignados) ? 'checked' : '' }}>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                {{-- BOOTON GUARDAR  --}}
                <div class="row justify-content-end align-items-end mb-2">
                    <div class="col-md-3">
                        <div class="card-footer py-3 d-flex justify-content-end">
                            <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal"
                                data-bs-target="#modalConfirmacion">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- MODAL PARA INGRESAR CONTRASENIA --}}
            <div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">

                        <div class="modal-header bg-warning text-dark border-0">
                            <h5 class="modal-title fw-bold" id="modalLabel">
                                <i class="fas fa-shield-alt me-2"></i> Confirmación de Seguridad
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body p-4">
                            <p class="mb-3 text-muted">
                                Para modificar los permisos de acceso para el rol <strong>{{ $rol->nombre }}</strong>.
                                Por seguridad, debe ingresar su contraseña.
                            </p>

                            <div class="form-group">
                                <label class="form-label fw-bold">Su Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-lock text-muted"></i></span>
                                    <input type="password" name="current_password" id="passwordInput"
                                        class="form-control form-control-lg @error('current_password') is-invalid @enderror"
                                        placeholder="******" required>
                                </div>
                                @error('current_password')
                                    <small class="text-danger d-block mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="modal-footer border-0 bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-dark px-4" form="formEditarRol">
                                Confirmar y Guardar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('scripts')
    <script>
        document.querySelectorAll('.btn-check-all').forEach(btn => {
            btn.addEventListener('click', function() {
                // Buscar todos los checkboxes dentro de ESTA tarjeta
                const cardBody = this.closest('.card').querySelector('.card-body');
                const checkboxes = cardBody.querySelectorAll('input[type="checkbox"]');

                // Ver si están todos marcados o no para alternar
                let allChecked = true;
                checkboxes.forEach(cb => {
                    if (!cb.checked) allChecked = false;
                });

                checkboxes.forEach(cb => cb.checked = !allChecked);
            });
        });
        //Encedemos todos los permismos

        document.addEventListener('DOMContentLoaded', function() {
            // Buscar todos los "check-all" (los de la cabecera)
            const masterToggles = document.querySelectorAll('.check-all');

            masterToggles.forEach(master => {
                master.addEventListener('change', function() {
                    // Encontrar la tarjeta padre
                    const card = this.closest('.card');
                    // Encontrar todos los switches hijos dentro de esa tarjeta
                    const childCheckboxes = card.querySelectorAll('.permission-checkbox');

                    // Ponerlos todos igual que el maestro
                    childCheckboxes.forEach(child => {
                        child.checked = this.checked;
                    });
                });
            });
        });

        // Código para Bootstrap
        var myModal = document.getElementById('modalConfirmacion')
        var myInput = document.getElementById('passwordInput')

        if (myModal) {
            myModal.addEventListener('shown.bs.modal', function() {
                myInput.focus()
            })
        }
        //Script para manejar el candado

        document.getElementById('btnUnlock').addEventListener('click', function() {
            const input = document.getElementById('inputNombreRol');
            const icon = document.getElementById('iconLock');
            const btn = this;

            if (input.hasAttribute('readonly')) {
                // DESBLOQUEAR
                input.removeAttribute('readonly');
                input.classList.remove('bg-light');
                input.focus();

                // Cambiar icono a candado abierto
                icon.classList.remove('fa-lock');
                icon.classList.add('fa-lock-open');
                btn.classList.replace('btn-outline-secondary', 'btn-outline-danger');
            } else {
                // VOLVER A BLOQUEAR
                input.setAttribute('readonly', 'readonly');
                input.classList.add('bg-light');
                icon.classList.remove('fa-lock-open');
                icon.classList.add('fa-lock');
                btn.classList.replace('btn-outline-danger', 'btn-outline-secondary');
            }
        });
    </script>
@endpush
