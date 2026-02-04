@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Planes nacionales" subtitulo="Plan nacional de Desarrollo">
        @if (Auth::user()->tienePermiso('pnd.gestionar'))
            <a href="{{ route('catalogos.planes-nacionales.create') }}" class="btn btn-secondary">
                <i class="fas fa-plus"></i> Nuevo Plan
            </a>
        @endif
    </x-layouts.header_content>
    <div class="row justify-content-end align-items-end mb-2">
        <div class="col-md-4">
            <form action="{{ route('catalogos.planes-nacionales.index') }}" method="GET">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fas fa-search"></i>
                    </span>
                    {{-- Buqueda --}}
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
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Nombre del Plan</th>
                            <th>Periodo</th>
                            <th>Registro Oficial</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($planes as $plan)
                            <tr class="{{ $plan->estado === 'ACTIVO' ? 'bg-success bg-opacity-10' : '' }}">
                                <td class="ps-4 fw-bold text-dark">
                                    {{ $plan->nombre }}
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $plan->periodo_inicio }} - {{ $plan->periodo_fin }}
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    {{ $plan->registro_oficial ?? 'N/A' }}
                                </td>
                                <td>
                                    @if ($plan->estado === 'ACTIVO')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle"></i> VIGENTE
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">HISTÓRICO</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                     @if(Auth::user()->tienePermiso('pnd.gestionar'))
                                    @if ($plan->estado !== 'ACTIVO')
                                        <form action="{{ route('catalogos.planes-nacionales.activar', $plan->id_plan) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary"
                                                onclick="return confirm('ATENCIÓN: ¿Desea activar este Plan?\n\nEsto desactivará el plan actual y cambiará los Ejes disponibles para todas las instituciones.')">
                                                <i class="fas fa-power-off"></i> Activar
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('catalogos.planes-nacionales.edit', $plan->id_plan) }}"
                                        class="btn btn-sm btn-light text-warning border ms-1">
                                        <i class="fas fa-edit" data-feather="edit-2"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No hay planes registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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

            // Función para mostrar/ocultar el botón "X"
            function toggleLimpiarButton() {
                if (inputBusqueda.value.trim() !== '') {
                    btnLimpiar.style.display = 'block';
                } else {
                    btnLimpiar.style.display = 'none';
                }
            }

            // 1. Ejecutar al cargar (por si ya vienes de una búsqueda)
            toggleLimpiarButton();

            // 2. Ejecutar cada vez que el usuario escribe
            inputBusqueda.addEventListener('input', toggleLimpiarButton);

            // 3. Acción al hacer clic en la "X"
            btnLimpiar.addEventListener('click', function() {
                inputBusqueda.value = ''; // Borra el texto
                toggleLimpiarButton(); // Oculta el botón
                // Envía el formulario vacío para limpiar la búsqueda en el backend
                inputBusqueda.closest('form').submit();
            });
        });
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
