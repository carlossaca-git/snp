@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Ejes Estratégicos</h1>
                <p class="text-muted small mb-0">Pilares fundamentales del Plan Nacional de Desarrollo</p>
            </div>

            <a href="{{ route('catalogos.planes-nacionales.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Plan
            </a>
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
                                        @if ($plan->estado !== 'ACTIVO')
                                            <form
                                                action="{{ route('catalogos.planes-nacionales.activar', $plan->id_plan) }}"
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
