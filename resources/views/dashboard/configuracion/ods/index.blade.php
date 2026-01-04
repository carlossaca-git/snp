@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Catálogo de ODS</h1>
            <p class="text-muted small mb-0">Objetivos de Desarrollo Sostenible - Agenda 2030</p>
        </div>
        <a href="{{ route('configuracion.ods.create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nuevo ODS
        </a>
    </div>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <span data-feather="check-circle" class="me-2"></span>
                <div>
                    {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 " style="width: 80px;">Numero</th>
                            <th>Nombre del Objetivo</th>
                            <th>Descripción</th>
                            <th class="text-end px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ods as $item)
                            <tr>
                                <td class="text-center">
                                    {{-- Usamos el color_hex para el fondo del número --}}
                                    <span class="badge shadow-sm"
                                        style="background-color: {{ $item->color_hex }}; color: #fff; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                        {{ $item->numero }}
                                    </span>
                                </td>
                                <td class="fw-bold px-3">
                                    <span style="color: {{ $item->color_hex }};">
                                        {{ $item->nombre_corto }}
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    {{ Str::limit($item->descripcion, 150) }}
                                </td>

                                <td class="text-end px-4">
                                    <div class="btn-group shadow-sm">
                                        <button type="button" class="btn btn-sm btn-white text-warning border edit-ods-btn"
                                            data-bs-toggle="modal" data-bs-target="#modalEditOds"
                                            data-id="{{ $item->id_ods }}" data-numero="{{ $item->numero }}"
                                            data-nombre="{{ $item->nombre_corto }}"
                                            data-descripcion="{{ $item->descripcion }}"
                                            data-color="{{ $item->color_hex }}">
                                            <span data-feather="edit-2" style="width: 16px; height: 16px;"></span>
                                        </button>
                                        <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- <form action="{{ route('config.ods.destroy', $item->id) }}" method="POST" class="d-inline"> --}}
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-white text-danger border"
                                            onclick="return confirm('¿Está seguro de eliminar este ODS?')" title="Eliminar">
                                            <i class="fas fa-trash" data-feather="trash-2"></i>
                                        </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-info-circle fa-2x mb-3"></i><br>
                                    No hay ODS registrados en la base de datos.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- MODAL PARA EDICION DE ODS --}}
    <div class="modal fade" id="modalEditOds" tabindex="-1" aria-labelledby="modalEditOdsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="modalEditOdsLabel">Editar Objetivo de Desarrollo Sostenible
                        <span id="cuadrito_numero_ods" class="badge ms-2"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditOds" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Número</label>
                                <input type="number" name="numero" id="edit_numero" class="form-control" required>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label fw-bold">Color Oficial</label>
                                <div class="input-group">
                                    <input type="color" name="color_hex" id="edit_color_picker"
                                        class="form-control form-control-color w-25">
                                    <input type="text" id="edit_color_text" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre del Objetivo</label>
                            <input type="text" name="nombre_corto" id="edit_nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('click', function(event) {
            // Buscamos el botón de editar
            const btn = event.target.closest('.edit-ods-btn');

            if (btn) {
                // 1. Extraer con getAttribute (es más seguro que dataset en algunos navegadores)
                const id = btn.getAttribute('data-id');
                const numero = btn.getAttribute('data-numero');
                const nombre = btn.getAttribute('data-nombre');
                const descripcion = btn.getAttribute('data-descripcion');
                const color = btn.getAttribute('data-color');

                console.log("ID recuperado:", id); // DEBE SALIR UN NÚMERO AQUÍ

                // 2. Referencias del Modal
                const form = document.getElementById('formEditOds');
                const inNum = document.getElementById('edit_numero');
                const inNom = document.getElementById('edit_nombre');
                const inDes = document.getElementById('edit_descripcion');
                const inCol = document.getElementById('edit_color_picker');
                const inTex = document.getElementById('edit_color_text');

                // 3. Inyectar datos
                if (form) form.action = "/configuracion/ods/" + id;
                if (inNum) inNum.value = numero;
                if (inNom) inNom.value = nombre;
                if (inDes) inDes.value = descripcion;
                if (inCol) inCol.value = color;
                if (inTex) inTex.value = color ? color.toUpperCase() : '';
            }
        });
    </script>
@endsection
