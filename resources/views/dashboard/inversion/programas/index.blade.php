@extends('layouts.app')

@section('content')

<div class="container-fluid">
    {{-- CABECERA Y BREADCRUMBS --}}
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">Gestión de Planes y Programas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Gestión de Inversión</li>
                    <li class="breadcrumb-item active" aria-current="page">Programas</li>
                </ol>
            </nav>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('inversion.programas.create') }}" class="btn btn-sm btn-primary shadow-sm">
                <span data-feather="plus"></span> Nuevo Programa
            </a>
        </div>
    </div>

    {{-- ALERTAS (Coincide con ->with('status', ...) del Controlador) --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <span data-feather="check-circle" class="me-2"></span>
            <strong>¡Éxito!</strong> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-muted">Listado Maestro de Programas</h6>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 150px;">Código CUP</th>
                            <th>Nombre del Programa</th>
                            <th class="text-center">Vigencia</th>
                            <th class="text-end">Presupuesto Planificado</th>
                            <th class="text-center">Proyectos</th> {{-- Columna extra para futura relación --}}
                            <th class="text-center pe-4" style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($programas as $programa)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-150">

                                {{-- 1. CÓDIGO CUP --}}
                                <td class="px-5 py-4 text-sm font-bold text-gray-900">
                                    {{ $programa->codigo_cup }}
                                </td>

                                {{-- 2. NOMBRE Y DESCRIPCIÓN --}}
                                <td class="px-5 py-4 text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap font-weight-bold mb-0">
                                        {{ $programa->nombre_programa }}
                                    </p>
                                    @if($programa->descripcion)
                                        <small class="text-muted">
                                            {{ Str::limit($programa->descripcion, 60) }}
                                        </small>
                                    @endif
                                </td>

                                {{-- 3. VIGENCIA (Años) --}}
                                <td class="px-5 py-4 text-sm text-center">
                                    <span class="badge bg-light text-dark border">
                                        {{ $programa->anio_inicio }} - {{ $programa->anio_fin }}
                                    </span>
                                </td>

                                {{-- 4. PRESUPUESTO --}}
                                <td class="px-5 py-4 text-sm text-end">
                                    <span class="d-block fw-bold text-gray-900">
                                        ${{ number_format($programa->monto_planificado, 2) }}
                                    </span>
                                    @if($programa->presupuesto_asignado)
                                        <small class="text-success d-block">
                                            Asig: ${{ number_format($programa->presupuesto_asignado, 2) }}
                                        </small>
                                    @endif
                                </td>

                                {{-- 5. CONTEO DE PROYECTOS (Opcional, requiere relación en modelo) --}}
                                <td class="text-center">
                                    {{-- Si tienes la relación definida, descomenta esto: --}}
                                    {{-- <span class="badge bg-secondary">{{ $programa->proyectos_count ?? 0 }}</span> --}}
                                    <span class="text-muted">-</span>
                                </td>

                                {{-- 6. ACCIONES --}}
                                <td class="px-5 py-4 text-sm text-center">
                                    <div class="d-flex justify-content-center gap-2">

                                        {{-- Botón Editar --}}
                                        <a href="{{ route('inversion.programas.edit', $programa->id_programa) }}"
                                           class="btn btn-sm btn-outline-warning" title="Editar">
                                            <span data-feather="edit"></span>
                                        </a>

                                        {{-- Botón Eliminar --}}
                                        <form action="{{ route('inversion.programas.destroy', $programa->id_programa) }}"
                                              method="POST"
                                              class="d-inline forms-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Eliminar"
                                                    onclick="return confirm('¿Estás seguro de eliminar el programa {{ $programa->codigo_cup }}?');">
                                                <span data-feather="trash-2"></span>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="d-flex flex-column align-items-center">
                                        <span data-feather="inbox" style="width: 40px; height: 40px; color: #ccc;"></span>
                                        <span class="mt-2">No se encontraron programas registrados.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINACIÓN --}}
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-end">
            {{ $programas->links() }}
        </div>
    </div>
</div>
@endsection
