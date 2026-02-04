@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/metas/metas-style.css') }}?v={{ time() }}">
@endpush
@section('content')
    <x-layouts.header_content titulo="Metas Nacionales"
        subtitulo="Compromisos cuantificables vinculados a los Objetivos Nacionales">
        <div class="btn-toolbar mb-2 mb-md-0">
            @if (Auth::user()->tienePermiso('metas_pnd.gestionar'))
                <button type="button" class="btn btn-secondary btn-sm d-inline-flex align-items-center" data-bs-toggle="modal"
                    data-bs-target="#modalCrearMeta">
                    <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Nueva Meta
                </button>
            @endif
        </div>

    </x-layouts.header_content>

    @include('partials.mensajes')
    <div class="container">
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
                        <button class="btn btn-secondary" type="submit">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card shadow-sm border-0">
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
                                        <div class="small text-muted mb-1 text-uppercase fw-bold"
                                            style="font-size: 0.7rem;">
                                            Objetivo Padre:</div>
                                        <div class="fw-bold text-primary small">
                                            {{ $item->objetivoNacional->codigo_objetivo ?? 'Sin Objetivo Asignado' }}</div>
                                    </td>
                                    <td>
                                        <a href="{{ route('catalogos.metas.show', $item) }}"
                                            class="fw-bold text-decoration-none" title="Ver historial completo">
                                            <div class="text-dark small">{{ $item->nombre_meta }}</div>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge rounded-pill {{ $item->estado == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $item->estado == 1 ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        @php
                                            // Obtenemos el dato calculado del Modelo (Ponderado)
                                            $avance = $item->avance_actual;

                                            // Definimos el color según el semáforo
                                            $claseColor = 'bg-danger';
                                            if ($avance >= 40) {
                                                $claseColor = 'bg-warning';
                                            } // Amarillo
                                            if ($avance >= 80) {
                                                $claseColor = 'bg-primary';
                                            } // Azul/Bueno
                                            if ($avance >= 100) {
                                                $claseColor = 'bg-success';
                                            } // Verde/Excelente
                                        @endphp

                                        {{-- CONTEO--}}
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-xs font-weight-bold text-primary"
                                                title="Cantidad de Indicadores asociados">
                                                <i class="fas fa-chart-pie me-1"></i>
                                                {{ $item->indicadoresNacionales->count() }} Indicadores
                                            </span>
                                            <span class="small font-weight-bold text-dark">
                                                {{ number_format($avance, 1) }}%
                                            </span>
                                        </div>

                                        {{-- PROGRESO --}}
                                        <div class="progress shadow-sm" style="height: 8px; background-color: #e9ecef;">
                                            <div class="progress-bar {{ $claseColor }}" role="progressbar"
                                                style="width: {{ $avance }}%" aria-valuenow="{{ $avance }}"
                                                aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                        @if ($item->indicadoresNacionales->isEmpty())
                                            <div class="text-xs text-muted fst-italic mt-1">
                                                Sin indicadores
                                            </div>
                                        @endif
                                        {{-- SECCIÓN ODS--}}
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
                                    {{-- ACCIONES: ELIIMINAR - EDITAR- DCUMENTO- VALOR ACTUAL --}}
                                    <td class="text-end px-4">
                                        <div class="btn-group shadow-sm">
                                            {{-- BOTON ABRIR VINCULACION METAS-ODS --}}
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="abrirVinculacionOds(this)" data-id="{{ $item->id_meta_nacional }}"
                                                data-ods="{{ $item->ods->pluck('id_ods')->implode(',') }}"
                                                data-nombre="{{ $item->nombre_meta }}">
                                                <i class="fas fa-globe"></i> ODS
                                            </button>
                                            <a href="{{ route('catalogos.metas.show', $item) }}"
                                                class="btn btn-sm btn-outline-primary" title="Ver ficha">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if (Auth::user()->tienePermiso('metas_pnd.gestionar'))
                                                {{-- BOTON MOSTRAR DOCUMENTO DE RESPALDO --}}
                                                @if ($item->url_documento)
                                                    <a href="{{ $item->url_documento }}" target="_blank"
                                                        class="btn btn-sm btn-white text-danger border" title="Ver PDF">
                                                        <i class="fas fa-file-pdf" data-feather="file-text"></i>
                                                    </a>
                                                @endif
                                                {{-- BOTON ABRIR MODAL DE EDICION --}}
                                                <button type="button"
                                                    class="btn btn-sm btn-white text-warning border edit-meta-btn"
                                                    onclick="abrirEditarMeta(this)" data-bs-toggle="modal"
                                                    data-bs-target="#modalEditMeta" data-id="{{ $item->id_meta_nacional }}"
                                                    data-objetivo="{{ $item->id_objetivo_nacional }}"
                                                    data-codigo="{{ $item->codigo_meta }}"
                                                    data-nombre="{{ $item->nombre_meta }}"
                                                    data-descripcion="{{ $item->descripcion_meta }}"
                                                    data-indicador="{{ $item->nombre_indicador }}"
                                                    data-unidad="{{ $item->unidad_medida }}"
                                                    data-linea-base="{{ $item->linea_base }}"
                                                    data-meta-valor="{{ $item->meta_valor }}"
                                                    data-valor-actual="{{ $item->valor_actual }}"
                                                    data-url="{{ $item->url_documento }}"
                                                    data-estado="{{ $item->estado }}">
                                                    <i class="fas fa-edit" data-feather="edit-2"></i>
                                                </button>
                                                {{-- BOTON ELIMINAR --}}
                                                <form
                                                    action="{{ route('catalogos.metas.destroy', $item->id_meta_nacional) }}"
                                                    method="POST" class="d-inline form-eliminar">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-sm btn-white text-danger border btn-eliminar-meta">
                                                        <i class="fas fa-trash-alt" data-feather="trash-2"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">No hay metas registradas para
                                        este
                                        nivel.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center mt-4 px-2">
                        {{-- Información de resultados --}}
                        <div class="text-muted small">
                            Mostrando {{ $metas->firstItem() }} al {{ $metas->lastItem() }} de {{ $metas->total() }}
                            metas
                        </div>

                        {{-- Enlaces de páginas --}}
                        <div class="pagination-clean pagination-custom mt-4">
                            {{ $metas->links('partials.paginacion') }}
                        </div>
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
    @include('dashboard.configuracion.metas.partials.ods')
@endsection
@push('scripts')
    <script>
        // Puente entre Laravel y el JS externo
        const CONFIG_METAS = {
            urlIndex: "{{ route('catalogos.metas.index') }}",
            msgSuccess: "{{ session('success') }}" // Pasamos el mensaje de sesión
        };
    </script>

    <script src="{{ asset('js/metas/metas-logic.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
