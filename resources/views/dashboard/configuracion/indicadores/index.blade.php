@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/indicadores/indicadores-style.css') }}?v={{ time() }}">
@endpush
@section('content')
    <x-layouts.header_content titulo="Indicadores de Desempeño"
        subtitulo="Gestión y parametrización de indicadores nacionales">
        @if (Auth::user()->tienePermiso('indicadores.gestionar'))
            <button type="button" class="btn btn-secondary shadow-sm" data-bs-toggle="modal"
                data-bs-target="#modalCrearIndicador">
                <i class="fas fa-plus fa-sm text-white-50 me-2"></i>Nuevo Indicador
            </button>
        @endif
    </x-layouts.header_content>
    @include('partials.mensajes')
    <div class="card shadow-sm border-0">
        <div class="row mb-3 align-items-center">
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
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="busqueda" id="inputBusqueda"
                            class="form-control border-start-0 border-end-0 shadow-none"
                            placeholder="Buscar por nombre, código..." value="{{ request('busqueda') }}">

                        <button type="button" id="btnLimpiarBusqueda"
                            class="btn bg-white border-top border-bottom border-end-0 text-danger"
                            style="display: none; z-index: 1000 !important;">
                            <i class="fas fa-times"></i>
                        </button>
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
                            <th>Meta</th>
                            <th class="text-center">Línea Base</th>
                            <th class="text-center">Meta Final</th>
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
                                <td>
                                    <div class="small text-muted"
                                        title="{{ $item->metaNacional->id_meta_nacional ?? 'Sin meta' }}">
                                        {{ Str::limit($item->metaNacional->codigo_meta ?? 'Sin metaNo asignada', 50) }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="fw-bold text-secondary">{{ number_format($item->linea_base, 2) }}</div>
                                    <small class="text-muted small fw-bold">Base</small>
                                </td>
                                <td class="text-center">
                                    <div class="fw-bold text-primary">{{ number_format($item->meta_final, 2) }}</div>
                                    <small class="text-primary small fw-bold">Objetivo</small>
                                </td>
                                <td class="align-middle">
                                    @php
                                        $porcentaje = $item->porcentaje_cumplimiento;
                                        $valorHoy = $item->valor_actual_absoluto;
                                        // SEMÁFORO
                                        $colorBarra = 'bg-danger';
                                        if ($porcentaje >= 30) {
                                            $colorBarra = 'bg-warning';
                                        }
                                        if ($porcentaje >= 70) {
                                            $colorBarra = 'bg-primary';
                                        }
                                        if ($porcentaje >= 100) {
                                            $colorBarra = 'bg-success';
                                        }
                                        $anchoVisual = max(0, min(100, $porcentaje));
                                    @endphp

                                    <div class="d-flex flex-column" style="min-width: 140px;">

                                        {{-- INICIO Y FIN --}}
                                        <div class="d-flex justify-content-between text-muted lh-1"
                                            style="font-size: 0.65rem;">
                                            <span title="Línea Base">
                                                {{ number_format($item->linea_base, $item->precision) }}
                                            </span>

                                            {{-- FLECHA DE DIRECCION --}}
                                            @if ($item->meta_final < $item->linea_base)
                                                <i class="fas fa-arrow-right text-success" title="La meta es REDUCIR"></i>
                                            @else
                                                <i class="fas fa-arrow-right text-primary" title="La meta es AUMENTAR"></i>
                                            @endif

                                            <span title="Meta PND" class="fw-bold text-dark">
                                                {{ number_format($item->meta_final, $item->precision) }}
                                            </span>
                                        </div>
                                        {{-- PROGRESO --}}
                                        <div class="progress shadow-sm my-1"
                                            style="height: 6px; background-color: #e9ecef;">
                                            <div class="progress-bar {{ $colorBarra }}" role="progressbar"
                                                style="width: {{ $anchoVisual }}%; transition: width 0.6s ease;"
                                                aria-valuenow="{{ $anchoVisual }}" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>

                                        {{-- RESULTADOS --}}
                                        <div class="d-flex justify-content-between align-items-center"
                                            style="font-size: 0.7rem;">
                                            <span class="text-primary fw-bold" title="Valor Actual Calculado">
                                                {{ number_format($valorHoy, $item->precision) }} <span class="text-muted fw-normal"
                                                    style="font-size: 9px;">{{ $item->unidad_medida }}</span>
                                            </span>
                                            <span class="{{ $porcentaje >= 100 ? 'text-success fw-bold' : 'text-muted' }}">
                                                {{ number_format($porcentaje, $item->precision) }}%
                                            </span>
                                        </div>

                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill {{ $item->estado == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $item->estado == 1 ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="text-end px-4">
                                    <div class="btn-group shadow-sm">
                                        <a href="{{ route('catalogos.indicadores.show', $item->id_indicador) }}"
                                            class="btn btn-sm btn-light border text-info shadow-sm" title="Ver Perfil">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if (Auth::user()->tienePermiso('indicadores.gestionar'))
                                            {{-- BOTON EDITAR --}}
                                            <button type="button"
                                                class="btn btn-sm btn-white border text-warning edit-indicador-btn"
                                                title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditIndicador"
                                                data-id="{{ $item->id_indicador }}"
                                                data-meta="{{ $item->meta_nacional_id }}"
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
                                            <form
                                                action="{{ route('catalogos.indicadores.destroy', $item->id_indicador) }}"
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
    @include('dashboard.configuracion.indicadores.create')
    {{-- MODAL DE EDITAR INDICADORES --}}
    @include('dashboard.configuracion.indicadores.edit')
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
