@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Banco de Proyectos de Inversión"
        subtitulo="Listado oficial de intervenciones y alineación estratégica">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('inversion.proyectos.create') }}"
                class="btn btn-secondary border-2 fw-bold d-inline-flex align-items-center">
                <span data-feather="plus" data-feather="plus"></span> Nuevo Proyecto
            </a>
        </div>

    </x-layouts.header_content>
    <div class="container-fluid py-4">
        @include('partials.mensajes')
        <form action="{{ route('inversion.proyectos.index') }}" method="GET">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body bg-light rounded">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-7">
                            <div class="input-group shadow-sm bg-white rounded">
                                <span class="input-group-text bg-white border-end-0 text-muted">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" name="buscar" id="inputBusqueda"
                                    class="form-control border-start-0 border-end-0 shadow-none"
                                    placeholder="Buscar por CUP o Nombre..." value="{{ request('buscar') }}">
                                <button type="button" id="btnLimpiarBusqueda"
                                    class="btn bg-white border-top border-bottom border-start-0 text-danger"
                                    style="display: none; z-index: 5;">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button class="btn btn-secondary" type="submit">Buscar</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select name="entidad" class="form-select shadow-sm" onchange="this.form.submit()">
                                <option value="">Todas las Entidades</option>
                                @foreach ($unidades as $u)
                                    <option value="{{ $u->id_unidad_ejecutora }}"
                                        {{ request('entidad') == $u->id_unidad_ejecutora ? 'selected' : '' }}>
                                        {{ $u->nombre_unidad }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            @if (request()->filled('buscar') || request()->filled('entidad'))
                                <a href="{{ route('inversion.proyectos.index') }}"
                                    class="btn btn-outline-danger w-100 shadow-sm" title="Quitar todos los filtros">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </form>
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th class="ps-3">CUP</th>
                            <th>Nombre del Proyecto</th>
                            <th>E.Responsable</th>
                            <th>OEI</th>
                            <th class="text-center">Avance</th>
                            <th>Estado</th>
                            <th class="text-end pe-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proyectos as $p)
                            <tr>
                                {{-- NOMBRE --}}
                                <td class="ps-3">
                                    <span class="badge bg-light text-dark border fw-bold">{{ $p->cup }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('inversion.proyectos.show', $p->id) }}">
                                        <div class="fw-bold text-dark">{{ Str::limit($p->nombre_proyecto, 50) }}</div>
                                    </a>
                                    <small class="text-muted">Prog:
                                        {{ $p->programa->nombre_programa ?? 'Sin Programa' }}</small>
                                </td>
                                <td>
                                    <div class="small">{{ $p->organizacion->siglas ?? 'No asignada' }}</div>
                                </td>
                                {{-- ALINEACION --}}
                                <td>
                                    @if ($p->objetivoEstrategico)
                                        <span class="badge  text-dark" title="{{ $p->objetivoEstrategico->codigo }}">
                                            <span data-feather="target" style="width: 12px"></span> Obj.
                                            {{ $p->objetivoEstrategico->codigo }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">No alineado</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        // Obtenemos el valor calculado (automáticamente llama a getAvanceRealAttribute)
                                        $avance = $p->avance_real;

                                        // Definimos el color según el semáforo
                                        $color = 'bg-danger';
                                        if ($avance >= 30) {
                                            $color = 'bg-warning';
                                        } // Amarillo
                                        if ($avance >= 70) {
                                            $color = 'bg-info';
                                        } // Azul
                                        if ($avance >= 100) {
                                            $color = 'bg-success';
                                        } // Verde
                                    @endphp

                                    <div class="d-flex flex-column justify-content-center">
                                        {{-- Texto del porcentaje --}}
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="fw-bold text-dark" style="font-size: 12px;">Físico</span>
                                            <span class="fw-bold {{ str_replace('bg-', 'text-', $color) }}"
                                                style="font-size: 12px;">
                                                {{ number_format($avance, 1) }}%
                                            </span>
                                        </div>

                                        {{-- Barra Gráfica --}}
                                        <div class="progress"
                                            style="height: 6px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);">
                                            <div class="progress-bar {{ $color }}" role="progressbar"
                                                style="width: {{ $avance }}%" aria-valuenow="{{ $avance }}"
                                                aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                {{-- ESTADO --}}
                                <td class="text-center">
                                    @if ($p->estado == 1)
                                        <span class="badge rounded-pill bg-success">Activo</span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary">Inactivo</span>
                                    @endif
                                </td>
                                {{-- ACCIONES --}}
                                <td class="text-end pe-3">
                                    <div class="btn-group shadow-sm">
                                        <a href="{{ route('inversion.proyectos.show', $p->id) }}"
                                            class="btn btn-sm btn-info text-white" title="Ver detalles">
                                            <i class="fas fa-eye" ></i>
                                        </a>
                                        <a href="{{ route('inversion.proyectos.edit', $p->id) }}"
                                            class="btn btn-sm btn-white border" title="Editar">
                                            <i class="fas fa-edit" ></i>
                                        </a>
                                        <form action="{{ route('inversion.proyectos.destroy', $p->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('¿Estás SEGURO de eliminar este proyecto?\n\nSe borrarán también sus metas y financiamientos.');">

                                            @csrf
                                            @method('DELETE') <button type="submit"
                                                class="btn btn-sm btn-white border text-danger" title="Eliminar Proyecto">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted"><div
                                            class="d-flex flex-column align-items-center justify-content-center text-muted">
                                            <i class="fas fa-folder-open fa-3x mb-3 opacity-50"></i>
                                            <h6 class="fw-bold">No se encontraron Proyectos</h6>
                                            <p class="small mb-0">Comience creando un nuevo proyecto.</p>
                                        </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $proyectos->appends(['buscar' => request('buscar')])->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputBusqueda = document.getElementById('inputBusqueda');
            const btnLimpiar = document.getElementById('btnLimpiarBusqueda');

            // Función para mostrar/ocultar el botón
            function toggleLimpiarButton() {
                if (inputBusqueda.value.trim() !== '') {
                    btnLimpiar.style.display = 'block';
                } else {
                    btnLimpiar.style.display = 'none';
                }
            }
            toggleLimpiarButton();
            inputBusqueda.addEventListener('input', toggleLimpiarButton);
            btnLimpiar.addEventListener('click', function() {
                inputBusqueda.value = '';
                toggleLimpiarButton();

                inputBusqueda.closest('form').submit();
            });
        });
    </script>
@endpush
