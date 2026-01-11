@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Indicadores de Desempeño</h1>
            <p class="text-muted small mb-0">Seguimiento técnico y medición de Metas Nacionales</p>
        </div>
        <button type="button" class="btn btn-dark btn-sm shadow-sm d-inline-flex align-items-center" data-bs-toggle="modal"
            data-bs-target="#modalCrearIndicador">
            <i class="fas fa-chart-line fa-sm text-white-50 me-1" data-feather="plus"></i> Nuevo Indicador
        </button>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div id="alerta-exito" class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert"
            style="border-left: 5px solid #198754;">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-alert="alert" aria-label="Close"></button>
        </div>
    @endif
    {{-- Alertas de Éxito y Errores (Mismo estilo que Ejes) --}}
    {{-- @include('layouts.alerts') --}}

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4">Indicador</th>
                            <th>Meta Vinculada</th>
                            <th class="text-center">Línea Base</th>
                            <th class="text-center">Meta Final</th>
                            <th class="text-center">Frecuencia</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($indicadores as $item)
                            <tr>
                                <td class="px-4">
                                    <div class="fw-bold text-dark">{{ $item->nombre_indicador }}</div>
                                    <small class="text-muted text-uppercase">{{ $item->unidad_medida }}</small>
                                </td>
                                <td>
                                    <div class="small text-muted" title="{{ $item->meta->nombre_meta ?? 'Sin meta' }}">
                                        {{ Str::limit($item->meta->nombre_meta ?? 'No asignada', 50) }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="fw-bold text-secondary">{{ number_format($item->linea_base, 2) }}</div>
                                    <small class="text-muted">Año: {{ $item->anio_linea_base }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="fw-bold text-primary">{{ number_format($item->meta_final, 2) }}</div>
                                    <small class="text-primary small fw-bold">Objetivo</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info text-dark">{{ $item->frecuencia }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill {{ $item->estado == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $item->estado == 1 ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="text-end px-4">
                                    <div class="btn-group shadow-sm">
                                        {{-- Botón de Fórmula (Tooltip con método de cálculo) --}}
                                        <button type="button" class="btn btn-sm btn-white border text-info"
                                            title="Fórmula: {{ $item->metodo_calculo }}">
                                            <i class="fas fa-calculator "></i>
                                        </button>

                                        <button type="button"
                                            class="btn btn-sm btn-white border text-warning edit-indicador-btn"
                                            data-bs-toggle="modal" data-bs-target="#modalEditIndicador"
                                            data-id="{{ $item->id_indicador }}" data-meta="{{ $item->id_meta }}"
                                            data-nombre="{{ $item->nombre_indicador }}"
                                            data-linea="{{ $item->linea_base }}" data-anio="{{ $item->anio_linea_base }}"
                                            data-final="{{ $item->meta_final }}" data-unidad="{{ $item->unidad_medida }}"
                                            data-frecuencia="{{ $item->frecuencia }}"
                                            data-metodo="{{ $item->metodo_calculo }}"
                                            data-descripcion="{{ $item->descripcion_indicador }}"
                                            data-fuente="{{ $item->fuente_informacion }}"
                                            data-estado="{{ $item->estado }}">
                                            <i class="fas fa-edit" data-feather="edit-2"></i>
                                        </button>

                                        <form
                                            action="{{ route('catalogos.indicadores.destroy', $item->id_indicador) }}"
                                            method="POST" class="d-inline form-eliminar">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn btn-sm btn-white border text-danger btn-delete-indicador"
                                                title="Elimnar Indicador">
                                                <i class="fas fa-trash-alt" data-feather="trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">No hay indicadores registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('dashboard.configuracion.indicadores.crear')
    @include('dashboard.configuracion.indicadores.editar')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('click', function(event) {
            const btn = event.target.closest('.edit-indicador-btn');
            if (btn) {
                const id = btn.getAttribute('data-id');
                const form = document.getElementById('formEditIndicador');
                form.action = `/configuracion/indicadores/${id}`;

                setTimeout(() => {
                    document.getElementById('edit_id_meta').value = btn.getAttribute('data-meta');
                    document.getElementById('edit_nombre_indicador').value = btn.getAttribute(
                        'data-nombre');
                    document.getElementById('edit_linea_base').value = btn.getAttribute('data-linea');
                    document.getElementById('edit_anio_linea_base').value = btn.getAttribute('data-anio');
                    document.getElementById('edit_meta_final').value = btn.getAttribute('data-final');
                    document.getElementById('edit_unidad_medida').value = btn.getAttribute('data-unidad');
                    document.getElementById('edit_frecuencia').value = btn.getAttribute('data-frecuencia');
                    document.getElementById('edit_metodo_calculo').value = btn.getAttribute('data-metodo');
                    document.getElementById('edit_descripcion_indicador').value = btn.getAttribute(
                        'data-descripcion');
                    document.getElementById('edit_fuente_informacion').value = btn.getAttribute(
                        'data-fuente');
                    document.getElementById('edit_estado_indicador').value = btn.getAttribute(
                        'data-estado');
                }, 200);
            }
            //Logica de eliminacion
            const btnDelete = event.target.closest('.btn-delete-indicador');
            if (btnDelete) {
                const formulario = btnDelete.closest('.form-eliminar');
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede deshacer. Se eliminará el indicador y su historial.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        formulario.submit(); // Solo se envía si el usuario confirma
                    }
                });
            }
        });
        // 3. Cerrar alerta automáticamente
        document.addEventListener('DOMContentLoaded', function() {
            const alerta = document.getElementById('alerta-exito');
            if (alerta) {
                setTimeout(() => {
                    alerta.style.transition = "opacity 0.5s ease";
                    alerta.style.opacity = "0";
                    setTimeout(() => {
                        alerta.remove();
                    }, 500);
                }, 3000);
            }
        });
    </script>
@endpush
