@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/metas/metas-style.css') }}?v={{ time() }}">
@endpush
@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Metas Nacionales</h1>
            <p class="text-muted small mb-0">Compromisos cuantificables vinculados a los Objetivos Nacionales</p>
        </div>
        <button type="button" class="btn btn-secondary btn-sm d-inline-flex align-items-center" data-bs-toggle="modal"
            data-bs-target="#modalCrearMeta">
            <i class="fas fa-bullseye fa-sm text-white-50 me-1" data-feather="plus"></i> Nueva Meta
        </button>

    </div>
    @include('partials.mensajes')

    <div class="card shadow-sm border-0">
        <div class="row mb-3 justify-content-end">
            {{-- BUSCADOR --}}
            <div class="col-md-4">
                <form action="{{ route('catalogos.metas.index') }}" method="GET">
                    <div class="input-group shadow-sm">
                        {{-- Lupa --}}
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        {{-- Input --}}
                        <input type="text" name="busqueda" id="inputBusqueda"
                            class="form-control border-start-0 border-end-0 shadow-none"
                            placeholder="Buscar por nombre, codigo..." value="{{ request('busqueda') }}">
                        {{--  Botón X Solo si hay búsqueda --}}
                        <button type="button" id="btnLimpiarBusqueda"
                            class="btn bg-white border-top border-bottom border-end-0 text-danger"
                            style="display: none; z-index: 1000 !important;">
                            <i class="fas fa-times"></i>
                        </button>
                        {{-- Botón Buscar --}}
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablaMetas">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4" style="width: 80px;">Código</th>
                            <th style="width: 10%;">O. Nacional</th>
                            <th style="width: 30">Definición de la Meta</th>
                            <th class="text-center">Estado</th>
                            <th>Progreso</th>
                            <th class="text-center px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($metas as $item)
                            <tr>
                                <td class="px-4 fw-bold text-muted">{{ $item->codigo_meta ?? 'S/C' }}</td>
                                <td>
                                    <div class="small text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.7rem;">
                                        Objetivo Padre:</div>
                                    <div class="fw-bold text-primary small">
                                        {{ $item->objetivoNacional->codigo_objetivo ?? 'Sin Objetivo Asignado' }}</div>
                                </td>
                                <td>
                                    <div class="text-dark small">{{ $item->nombre_meta }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill {{ $item->estado == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $item->estado == 1 ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    {{-- CALCULOS PARA LAS BARRAS DE ESTADO DE AVANCE --}}
                                    @php
                                        // 1. Convertimos a números para operar
                                        $lb = (float) $item->linea_base;
                                        $mv = (float) $item->meta_valor;
                                        $va = (float) ($item->valor_actual ?? $lb);

                                        //  La meta es de REDUCCIÓN (Ej: Pobreza, Homicidios)
                                        // Es reducción si el valor meta es menor que el inicial
                                        $esReduccion = $mv < $lb;

                                        if ($esReduccion) {
                                            // Lógica de Reducción
                                            if ($va >= $lb) {
                                                $porcentaje = 0; // Si el valor subió, el progreso es cero
                                            } elseif ($va <= $mv) {
                                                $porcentaje = 100; // Si ya bajamos más de la meta, es 100%
                                            } else {
                                                // Calculo de cuanto hemos bajado hacia la meta
                                                $totalAReducir = $lb - $mv;
                                                $reducidoReal = $lb - $va;
                                                $porcentaje = ($reducidoReal / $totalAReducir) * 100;
                                            }
                                        } else {
                                            // Lógica de Incremento Ej: Empleo, Agua potable
                                            if ($va <= $lb) {
                                                $porcentaje = 0; // Si no ha subido nada, progreso cero
                                            } elseif ($va >= $mv) {
                                                $porcentaje = 100; // Si ya superamos la meta, es 100%
                                            } else {
                                                // Cálculo de cuánto hemos subido hacia la meta
                                                $totalAIncrementar = $mv - $lb;
                                                $incrementoReal = $va - $lb;
                                                $porcentaje = ($incrementoReal / $totalAIncrementar) * 100;
                                            }
                                        }

                                        // Colores del semáforo
                                        $colorBarra = 'bg-danger';
                                        if ($porcentaje >= 40) {
                                            $colorBarra = 'bg-warning';
                                        }
                                        if ($porcentaje >= 80) {
                                            $colorBarra = 'bg-success';
                                        }
                                    @endphp

                                    {{-- La Barra Visual --}}
                                    <div class="progress shadow-sm" style="height: 18px; border-radius: 10px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated {{ $colorBarra }}"
                                            role="progressbar" style="width: {{ $porcentaje }}%;"
                                            aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ number_format($porcentaje, 0) }}%
                                        </div>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.7rem;">
                                        VA: {{ number_format($va, 2) }} / VF: {{ number_format($mv, 2) }}
                                    </small>
                                    {{-- Contenedor de ODS vinculados --}}
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        @if ($item->ods->count() > 0)
                                            @foreach ($item->ods as $ods_vinculado)
                                                <span
                                                    class="badge d-flex align-items-center justify-content-center shadow-sm"
                                                    style="background-color: {{ $ods_vinculado->color_hex }};
                                                    width: 40px;
                                                    height: 24px;
                                                    font-size: 0.65rem;
                                                    color: white;
                                                    border-radius: 4px;"
                                                    title="{{ $ods_vinculado->nombre }}">
                                                    {{ $ods_vinculado->codigo }}
                                                </span>
                                            @endforeach
                                        @else
                                            <small class="text-muted" style="font-size: 0.6rem; font-style: italic;">
                                                Sin ODS vinculados
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                {{-- GRUPO : ELIIMINAR - EDITAR- DCUMENTO- VALOR ACTUAL --}}
                                <td class="text-end px-4">
                                    <div class="btn-group shadow-sm">
                                        {{-- BOTON ABRIR VINCULACION METAS-ODS --}}
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="abrirVinculacionOds(this)" data-id="{{ $item->id_meta_nacional }}"
                                            data-ods="{{ $item->ods->pluck('id_ods')->implode(',') }}"
                                            data-nombre="{{ $item->nombre_meta }}">
                                            <i class="fas fa-globe"></i> ODS
                                        </button>
                                        {{-- BOTON ABRIR MODAL SEGUIMIENTO --}}
                                        <button type="button" class="btn btn-sm btn-info" onclick="abrirSeguimiento(this)"
                                            data-id="{{ $item->id_meta_nacional }}"
                                            data-unidad="{{ $item->unidad_medida }}"
                                            data-valor="{{ $item->valor_actual ?? $item->linea_base }}">
                                            <i class="fas fa-chart-line"></i>
                                        </button>
                                        {{-- BOTON MOSTRAR DOCUMENTO DE RESPALDO --}}
                                        @if ($item->url_documento)
                                            <a href="{{ $item->url_documento }}" target="_blank"
                                                class="btn btn-sm btn-white text-danger border" title="Ver PDF">
                                                <i class="fas fa-file-pdf " data-feather="file-text"></i>
                                            </a>
                                        @endif
                                        {{-- BOTON ABRIR MODAL DE EDICION --}}
                                        <button type="button"
                                            class="btn btn-sm btn-white text-warning border edit-meta-btn"
                                            onclick="abrirEditarMeta(this)" data-bs-toggle="modal"
                                            data-bs-target="#modalEditMeta" data-id="{{ $item->id_meta_nacional }}"
                                            data-objetivo="{{ $item->id_objetivo_nacional }}"
                                            data-codigo="{{ $item->codigo_meta }}" data-nombre="{{ $item->nombre_meta }}"
                                            data-descripcion="{{ $item->descripcion_meta }}"
                                            data-indicador="{{ $item->nombre_indicador }}"
                                            data-unidad="{{ $item->unidad_medida }}"
                                            data-linea-base="{{ $item->linea_base }}"
                                            data-meta-valor="{{ $item->meta_valor }}"
                                            data-valor-actual="{{ $item->valor_actual }}"
                                            data-url="{{ $item->url_documento }}" data-estado="{{ $item->estado }}">
                                            <i class="fas fa-edit" data-feather="edit-2"></i>
                                        </button>
                                        {{-- BOTON ELIMINAR --}}
                                        <form action="{{ route('catalogos.metas.destroy', $item->id_meta_nacional) }}"
                                            method="POST" class="d-inline form-eliminar">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn btn-sm btn-white text-danger border btn-eliminar-meta">
                                                <i class="fas fa-trash-alt" data-feather="trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No hay metas registradas para este
                                    nivel.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center mt-4 px-2">
                    {{-- Información de resultados --}}
                    <div class="text-muted small">
                        Mostrando {{ $metas->firstItem() }} al {{ $metas->lastItem() }} de {{ $metas->total() }} metas
                    </div>

                    {{-- Enlaces de páginas --}}
                    <div class="pagination-clean pagination-custom mt-4">
                        {{ $metas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CREAR --}}
    @include('dashboard.configuracion.metas.crear')

    {{-- MODAL EDITAR --}}
    @include('dashboard.configuracion.metas.editar')
    {{-- MODAL ACTUALIZAR VALOR Y VINCULAR ODS --}}
    @include('dashboard.configuracion.metas.partials.modals')
@endsection
@push('scripts')
    <script>
        // Puente entre Laravel y el JS externo
        const CONFIG_METAS = {
            urlIndex: "{{ route('catalogos.metas.index') }}",
            msgSuccess: "{{ session('success') }}" // Pasamos el mensaje de sesión
        };

        // Mostrar alerta de éxito si existe
        if (CONFIG_METAS.msgSuccess) {
            Swal.fire({
                icon: 'success',
                title: '¡Logrado!',
                text: CONFIG_METAS.msgSuccess,
                timer: 3000,
                showConfirmButton: false
            });
        }
    </script>

    <script src="{{ asset('js/metas/metas-logic.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
