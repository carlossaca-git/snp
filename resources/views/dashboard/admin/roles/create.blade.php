@extends('layouts.app')
<style>
        .permission-card {
            transition: transform 0.2s;
            border: none;
            border-radius: 10px;
        }

        .permission-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

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
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Crear Nuevo Rol</h1>
            <a href="{{ route('administracion.roles.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver al listado
            </a>
        </div>

        <form action="{{ route('administracion.roles.store') }}" method="POST">
            @csrf

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Datos Generales</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">Nombre del Rol <span class="text-danger">*</span></label>
                                <input type="text" name="nombre"
                                    class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}"
                                    placeholder="Ej: Auditor Financiero" required autofocus>
                                @error('nombre')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="text-muted">El slug se generará automáticamente (ej:
                                    auditor-financiero).</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Descripción <span class="text-muted">(Opcional)</span></label>
                                <input type="text" name="descripcion" class="form-control"
                                    value="{{ old('descripcion') }}" placeholder="Breve descripción de funciones...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="mb-3 text-gray-800">Asignar Permisos Iniciales</h4>

            <div class="row">
                @foreach ($permisosAgrupados as $modulo => $permisos)
                    {{-- Lógica de iconos y colores --}}
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

                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h6 class="m-0 font-weight-bold text-{{ $color }}">
                                    <i class="fas {{ $icon }} me-2"></i> {{ $modulo }}
                                </h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input check-all" type="checkbox" title="Marcar todo">
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    @foreach ($permisos as $permiso)
                                        <label
                                            class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                                            <div class="ms-2">
                                                <span class="fw-bold text-dark">{{ $permiso->nombre }}</span>
                                                <br>
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    {{ $permiso->descripcion }}
                                                </small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                    name="permisos[]" value="{{ $permiso->id_permiso }}">
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-end mb-5">
                <a href="{{ route('administracion.roles.index') }}" class="btn btn-secondary btn-lg me-2">Cancelar</a>
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save"></i> Crear Rol
                </button>
            </div>

        </form>
    </div>
@endsection

@push('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const masterToggles = document.querySelectorAll('.check-all');
            masterToggles.forEach(master => {
                master.addEventListener('change', function() {
                    const card = this.closest('.card');
                    const childCheckboxes = card.querySelectorAll('.permission-checkbox');
                    childCheckboxes.forEach(child => child.checked = this.checked);
                });
            });
        });
    </script>
@endpush
