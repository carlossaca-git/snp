@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/indicadores/indicadores-style.css') }}?v={{ time() }}">
@endpush
@section('content')
    <x-layouts.header_content titulo="Indicadores de Desempeño"
        subtitulo="Gestión y parametrización de indicadores nacionales">
         @if(Auth::user()->tienePermiso('indicadores.gestionar'))
        <button type="button" class="btn btn-secondary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCrearIndicador">
            <i class="fas fa-plus fa-sm text-white-50 me-2"></i>Nuevo Indicador
        </button>
        @endif
    </x-layouts.header_content>

    {{-- Alertas de Exito y Errores  --}}
    @include('partials.mensajes')
    <div class="card shadow-sm border-0">
        <div class="row mb-3 align-items-center">

            {{-- Texto Informativo --}}
            <div class="col-md-6 text-start">
                <small class="text-primary fst-italic">
                    <i class="fas fa-mouse-pointer fa-sm me-1"></i>
                    Clic en el nombre del indicador para ver detalle (Reporte)
                </small>
            </div>

            {{--  Buscador --}}
            <div class="col-md-6">
                <form action="{{ route('catalogos.indicadores.index') }}" method="GET">
                    <div class="input-group shadow-sm">
                        {{-- Lupa --}}
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fas fa-search"></i>
                        </span>

                        {{-- Input --}}
                        <input type="text" name="busqueda" id="inputBusqueda"
                            class="form-control border-start-0 border-end-0 shadow-none"
                            placeholder="Buscar por nombre, código..." value="{{ request('busqueda') }}">

                        {{-- Botón X Limpiar --}}
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
                            <th class="text-center" style="width: 15%">Progreso Estimado</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaIndicadores">
                        @forelse($indicadores as $item)
                            <tr>
                                {{-- Nombre Indicador --}}
                                <td class="px-4">
                                    <div class="fw-bold">
                                        <a href="{{ route('catalogos.indicadores.show', $item->id_indicador) }}"
                                            class="text-decoration-none text-dark hover-primary">
                                            {{ $item->nombre_indicador }}
                                        </a>
                                    </div>

                                    <small class="text-muted text-uppercase">{{ $item->unidad_medida }}</small>
                                </td>
                                {{-- Meta vinculada --}}
                                <td>
                                    <div class="small text-muted" title="{{ $item->meta->nombre_meta ?? 'Sin meta' }}">
                                        {{ Str::limit($item->meta->nombre_meta ?? 'No asignada', 50) }}
                                    </div>
                                </td>
                                {{-- Anio Base --}}
                                <td class="text-center">
                                    <div class="fw-bold text-secondary">{{ number_format($item->linea_base, 2) }}</div>
                                    <small class="text-muted">Año: {{ $item->anio_linea_base }}</small>
                                </td>
                                {{-- Meta final --}}
                                <td class="text-center">
                                    <div class="fw-bold text-primary">{{ number_format($item->meta_final, 2) }}</div>
                                    <small class="text-primary small fw-bold">Objetivo</small>
                                </td>
                                {{-- Frecuenci de actualziacion --}}
                                <td class="text-center">
                                    <span class="badge bg-info text-dark">{{ $item->frecuencia }}</span>
                                </td>
                                {{-- Barras de progreso --}}
                                <td class="align-middle">
                                    @php
                                        // 1. Obtener dato real
                                        $valorActual = $item->ultimoAvance
                                            ? $item->ultimoAvance->valor_logrado
                                            : $item->linea_base;

                                        // 2. Calcular matemáticas
                                        $brecha = $item->meta_final - $item->linea_base;
                                        $porcentaje = 0;

                                        if ($brecha != 0) {
                                            $avanceLogrado = $valorActual - $item->linea_base;
                                            $porcentaje = ($avanceLogrado / $brecha) * 100;
                                        }

                                        // 3. Ajustar visuales (Límites 0-100% y Color)
                                        $porcentajeVisual = max(0, min(100, $porcentaje));

                                        $colorBarra = 'bg-primary';
                                        if ($item->meta_final < $item->linea_base) {
                                            $colorBarra = 'bg-success'; // Verde para reducción
                                        }
                                    @endphp

                                    <div class="d-flex flex-column">
                                        {{-- Rango --}}
                                        <div class="d-flex justify-content-between text-muted" style="font-size: 0.7rem;">
                                            <span>{{ number_format($item->linea_base, 2) }}</span>
                                            <span
                                                class="fw-bold text-dark">{{ number_format($item->meta_final, 2) }}</span>
                                        </div>

                                        {{-- Barra --}}
                                        <div class="progress shadow-sm" style="height: 6px; background-color: #e9ecef;">
                                            <div class="progress-bar {{ $colorBarra }}" role="progressbar"
                                                style="width: {{ $porcentajeVisual }}%; transition: width 1s ease;"
                                                aria-valuenow="{{ $porcentajeVisual }}" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>

                                        {{-- Porcentaje Numérico --}}
                                        <div class="text-end mt-1" style="font-size: 0.7rem;">
                                            <span class="text-{{ $porcentaje >= 100 ? 'success fw-bold' : 'muted' }}">
                                                {{ number_format($porcentaje, 1) }}%
                                                @if ($item->ultimoAvance)
                                                    <i class="fas fa-history text-info ms-1"
                                                        title="Último reporte: {{ $item->ultimoAvance->fecha_reporte }}"></i>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                {{-- Estado --}}
                                <td class="text-center">
                                    <span class="badge rounded-pill {{ $item->estado == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $item->estado == 1 ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                {{-- Acciones --}}
                                <td class="text-end px-4">
                                    <div class="btn-group shadow-sm">
                                        {{-- Botón de Fórmula (Tooltip con método de cálculo) --}}
                                        <button type="button" class="btn btn-sm btn-white border text-info"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ $item->metodo_calculo ?? 'Sin fórmula definida' }}">
                                            <i class="fas fa-calculator"></i>
                                        </button>
                                        {{-- BOTON DE ACTUALIZAR AVANCES --}}
                                         @if(Auth::user()->tienePermiso('indicadores.gestionar'))
                                        <button type="button" class="btn btn-sm btn-white border text-success"
                                            title="Regitrar Avance" onclick="abrirModalAvance(this)"
                                            data-id="{{ $item->id_indicador }}"
                                            data-nombre="{{ $item->nombre_indicador }}"
                                            data-unidad="{{ $item->unidad_medida }}">
                                            <i class="fas fa-chart-line"></i>
                                        </button>
                                        {{-- BOTON EDITAR --}}
                                        <button type="button"
                                            class="btn btn-sm btn-white border text-warning edit-indicador-btn"
                                            title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditIndicador"
                                            data-id="{{ $item->id_indicador }}" data-meta="{{ $item->id_meta }}"
                                            data-nombre="{{ $item->nombre_indicador }}"
                                            data-linea="{{ $item->linea_base }}"
                                            data-anio="{{ $item->anio_linea_base }}"
                                            data-final="{{ $item->meta_final }}"
                                            data-unidad="{{ $item->unidad_medida }}"
                                            data-frecuencia="{{ $item->frecuencia }}"
                                            data-metodo="{{ $item->metodo_calculo }}"
                                            data-descripcion="{{ $item->descripcion_indicador }}"
                                            data-fuente="{{ $item->fuente_informacion }}"
                                            data-estado="{{ $item->estado }}" onclick="abrirEditarIndicador(this)">
                                            <i class="fas fa-edit" data-feather="edit-2"></i>
                                        </button>
                                        {{-- BOTON ELIMINAR --}}
                                        <form action="{{ route('catalogos.indicadores.destroy', $item->id_indicador) }}"
                                            method="POST" class="d-inline form-eliminar">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn btn-sm btn-white border text-danger btn-eliminar-indicador"
                                                title="Elimnar Indicador">
                                                <i class="fas fa-trash-alt" data-feather="trash-2"></i>
                                            </button>
                                        </form>
                                        @endif
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
                {{-- Paginación --}}
                <div class="pagination-custom mt-3" id="contenedorPaginacion">
                    {{ $indicadores->links() }}
                </div>
            </div>
        </div>
    </div>
    {{-- MODAL DE CREAR INDICADORES --}}
    @include('dashboard.configuracion.indicadores.crear')
    {{-- MODAL DE EDITAR INDICADORES --}}
    @include('dashboard.configuracion.indicadores.editar')
    @include('dashboard.configuracion.indicadores.partials.modals')
@endsection
{{-- Invocamos los scripts --}}
@push('scripts')
    <script>
        const CONFIG_IND = {
            urlIndex: "{{ route('catalogos.indicadores.index') }}"
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/indicadores/indicadores-logic.js') }}"></script>
@endpush
