@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Planificación Estratégica (PEI)" subtitulo="Gestión de Objetivos Institucionales">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('estrategico.objetivos.create') }}"
                class="btn btn-sm btn-secondary shadow-sm d-inline-flex align-items-center">
                <i class="fas fa-plus me-2"></i> Nuevo Objetivo
            </a>
        </div>
    </x-layouts.header_content>

    @include('partials.mensajes')

    <div class="container-fluid py-3">
        {{-- Buscador --}}
        <div class="row justify-content-end mb-3">
            <div class="col-md-5">
                <form action="{{ route('estrategico.objetivos.index') }}" method="GET">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="busqueda" id="inputBusqueda"
                            class="form-control border-start-0 border-end-0 shadow-none"
                            placeholder="Buscar por código, nombre..." value="{{ request('busqueda') }}">
                        <button type="button" id="btnLimpiarBusqueda"
                            class="btn bg-white border-top border-bottom border-start-0 text-danger" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>

                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla Principal --}}
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-4 text-uppercase text-secondary small fw-bold" width="10%">Código</th>
                                <th class="text-uppercase text-secondary small fw-bold" width="35%">Objetivo
                                    Institucional</th>
                                <th class="text-uppercase text-secondary small fw-bold" width="35%">Alineación Nacional
                                    (PND)</th>
                                <th class="text-center text-uppercase text-secondary small fw-bold" width="10%">Vigencia
                                </th>
                                <th class="text-end pe-4 text-uppercase text-secondary small fw-bold" width="10%">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($objetivos as $obj)
                                <tr>
                                    {{--  Código --}}
                                    <td class="ps-4">
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-primary border border-primary fw-bold px-3 py-2">
                                            {{ $obj->codigo }}
                                        </span>
                                    </td>

                                    {{--  Nombre e Indicador --}}
                                    <td>
                                        <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">
                                            {{ Str::limit($obj->nombre, 120) }}
                                        </div>
                                        <div class="text-muted lh-sm" style="font-size: 0.8rem;">
                                            {{ Str::limit($obj->descripcion, 120) }}
                                        </div>
                                    </td>

                                    {{-- Alineación (Mejorada visualmente) --}}
                                    <td>
                                        @forelse ($obj->metasNacionales ?? [] as $meta)
                                            <div class="d-flex align-items-start mb-2 p-2 rounded">
                                                <div class="me-2 mt-1">
                                                    <i class="fas fa-flag text-success"></i>
                                                </div>
                                                <div>
                                                    <span class="badge bg-success bg-opacity-75 mb-1"
                                                        style="font-size: 0.7rem;">
                                                        META {{ $meta->codigo_meta ?? 'S/C' }}
                                                    </span>
                                                    <div class="text-muted lh-sm" style="font-size: 0.8rem;">
                                                        {{ Str::limit($meta->descripcion_meta, 90) }}
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <span class="badge bg-secondary fw-normal">
                                                <i class="fas fa-unlink me-1"></i> Sin Alineación
                                            </span>
                                        @endforelse
                                    </td>

                                    {{-- Vigencia --}}
                                    <td class="text-center">
                                        <div class="d-flex flex-column align-items-center">
                                            <span
                                                class="fw-bold text-dark">{{ \Carbon\Carbon::parse($obj->fecha_inicio)->format('Y') }}</span>
                                            <span class="text-muted small">a</span>
                                            <span
                                                class="fw-bold text-dark">{{ \Carbon\Carbon::parse($obj->fecha_fin)->format('Y') }}</span>
                                        </div>
                                    </td>

                                    {{-- Acciones (Fusionadas y Limpias) --}}
                                    <td class="text-end pe-4">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('estrategico.objetivos.show', $obj->id_objetivo_estrategico) }}"
                                                class="btn btn-sm btn-light border text-info" title="Ver Detalle y ODS">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            {{-- Editar --}}
                                            <a href="{{ route('estrategico.objetivos.edit', $obj) }}"
                                                class="btn btn-sm btn-light border text-warning" title="Editar">
                                                <i class="fas fa-pencil-alt"></i> {{-- Corregí data-feather por fontawesome para consistencia --}}
                                            </a>

                                            {{-- Eliminar --}}
                                            <form
                                                action="{{ route('estrategico.objetivos.destroy', $obj->id_objetivo_estrategico) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('¿Está seguro de eliminar este objetivo? Se perderán las alineaciones asociadas.');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light border text-danger"
                                                    title="Eliminar">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div
                                            class="d-flex flex-column align-items-center justify-content-center text-muted">
                                            <i class="fas fa-folder-open fa-3x mb-3 opacity-50"></i>
                                            <h6 class="fw-bold">No se encontraron objetivos</h6>
                                            <p class="small mb-0">Comience creando un nuevo objetivo estratégico
                                                institucional.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="px-4 py-3 border-top bg-light d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        @if ($objetivos->total() > 0)
                            Mostrando {{ $objetivos->firstItem() }} - {{ $objetivos->lastItem() }} de
                            {{ $objetivos->total() }}
                        @endif
                    </div>
                    <div>
                        {{ $objetivos->appends(request()->query())->links() }} {{-- Paginación simple de Bootstrap --}}
                    </div>
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
            // Ejecutar al cargar (por si ya vienes de una búsqueda)
            toggleLimpiarButton();

            // Ejecutar cada vez que el usuario escribe
            inputBusqueda.addEventListener('input', toggleLimpiarButton);

            // Acción al hacer clic en la "X"
            btnLimpiar.addEventListener('click', function() {
                inputBusqueda.value = '';
                toggleLimpiarButton();

                inputBusqueda.closest('form').submit();
            });
        });
    </script>
@endpush
