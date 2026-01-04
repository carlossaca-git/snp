@extends('layouts.app')

@section('content')
    {{-- ENCABEZADO: Título y Botón Crear --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Banco de Proyectos</h1>
            <p class="text-muted mb-0 small">Gestión y seguimiento de inversiones</p>
        </div>
        <a href="{{ route('inversion.proyectos.create') }}" class="btn btn-primary">
            <span data-feather="plus"></span> Nuevo Proyecto
        </a>
    </div>
    {{-- Mensaje de Éxito --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <span data-feather="check-circle" class="me-2"></span>
                <div>
                    <strong>¡Excelente!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Mensaje de Error (por si algo falla en el futuro) --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <span data-feather="alert-triangle" class="me-2"></span>
                {{ session('error') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    {{-- TARJETA BLANCA DE CONTENIDO --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 small">Total Proyectos</h6>
                        <h2 class="mb-0">{{ $stats['total_proyectos'] }}</h2>
                    </div>
                    <span data-feather="briefcase" style="width: 40px; height: 40px; opacity: 0.5;"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 small">Inversión Total</h6>
                        <h2 class="mb-0">${{ number_format($stats['inversion_total'], 2) }}</h2>
                    </div>
                    <span data-feather="dollar-sign" style="width: 40px; height: 40px; opacity: 0.5;"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 small">Promedio por Proyecto</h6>
                        <h2 class="mb-0">${{ number_format($stats['promedio_monto'], 2) }}</h2>
                    </div>
                    <span data-feather="trending-up" style="width: 40px; height: 40px; opacity: 0.5;"></span>
                </div>
            </div>
        </div>
    </div>
</div>
            {{-- BARRA DE HERRAMIENTAS (Buscador) --}}
            <div class="row mb-4 justify-content-between align-items-center">
                <div class="col-md-4">
                    <form action="{{ route('inversion.proyectos.index') }}" method="GET" class="row g-2">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control"
                                placeholder="Buscar por nombre o CUP..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <select name="tipo" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Todos los tipos --</option>
                                <option value="Obra" {{ request('tipo') == 'Obra' ? 'selected' : '' }}>Obra</option>
                                <option value="Bien" {{ request('tipo') == 'Bien' ? 'selected' : '' }}>Bien</option>
                                <option value="Servicio" {{ request('tipo') == 'Servicio' ? 'selected' : '' }}>Servicio
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-light border w-100">Filtrar</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-auto text-end">
                    <span class="text-muted small">Mostrando {{ $proyectos->count() }} registros</span>
                </div>
            </div>

            {{-- TABLA DE DATOS --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-secondary small text-uppercase">CUP / Código</th>
                            <th scope="col" class="text-secondary small text-uppercase">Nombre del Proyecto</th>
                            <th scope="col" class="text-secondary small text-uppercase">Programa</th>
                            <th scope="col" class="text-secondary small text-uppercase">Inversión ($)</th>
                            <th scope="col" class="text-secondary small text-uppercase">Estado</th>
                            <th scope="col" class="text-end text-secondary small text-uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proyectos as $proyecto)
                            <tr>
                                {{-- Columna 1: Código --}}
                                <td class="fw-bold text-dark">
                                    {{ $proyecto->cup ?? '---' }}
                                </td>

                                {{-- Columna 2: Nombre --}}
                                <td>
                                    <div class="text-wrap" style="max-width: 300px;">
                                        {{ $proyecto->nombre_proyecto }}
                                    </div>
                                    <small class="text-muted d-block">
                                        {{ $proyecto->tipo_inversion }}
                                    </small>
                                </td>

                                {{-- Columna 3: Programa Relacionado --}}
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $proyecto->programa->codigo_programa ?? 'N/A' }}
                                    </span>
                                </td>

                                {{-- Columna 4: Monto --}}
                                <td class="fw-bold text-success">
                                    ${{ number_format($proyecto->monto_total_inversion, 2) }}
                                </td>

                                {{-- Columna 5: Estado con Colores --}}
                                <td>
                                    @php
                                        $estadoClasses = [
                                            'Solicitado' => 'bg-warning text-dark',
                                            'Aprobado' => 'bg-success',
                                            'Rechazado' => 'bg-danger',
                                            'Observado' => 'bg-info text-dark',
                                        ];
                                        $clase = $estadoClasses[$proyecto->estado_dictamen] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge rounded-pill {{ $clase }}">
                                        {{ $proyecto->estado_dictamen }}
                                    </span>
                                </td>

                                {{-- Columna 6: Botones de Acción --}}
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('inversion.proyectos.show', $proyecto->id) }}"
                                            class="btn btn-outline-secondary" title="Ver Detalles">
                                            <span data-feather="eye"></span>
                                        </a>
                                        <a href="{{ route('inversion.proyectos.edit', $proyecto->id) }}"
                                            class="btn btn-outline-primary" title="Editar">
                                            <span data-feather="edit-2"></span>
                                        </a>

                                        {{-- Botón Eliminar con confirmación --}}
                                        <form action="{{ route('inversion.proyectos.destroy', $proyecto->id) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('¿Estás seguro de eliminar este proyecto? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                                <span data-feather="trash-2"></span>
                                            </button>
                                        </form>
                                    </div>

                                    <form id="delete-form-{{ $proyecto->id }}"
                                        action="{{ route('inversion.proyectos.destroy', $proyecto->id) }}" method="POST"
                                        class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <span data-feather="folder" style="width: 40px; height: 40px; opacity: 0.5;"></span>
                                        <p class="mt-2">No se encontraron proyectos registrados.</p>
                                        <a href="{{ route('inversion.proyectos.create') }}"
                                            class="btn btn-sm btn-primary mt-2">
                                            Crear el primero
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINACIÓN --}}
            <div class="mt-4 d-flex justify-content-end">
                {{ $proyectos->links() }}
            </div>

        </div>
    </div>
@endsection
