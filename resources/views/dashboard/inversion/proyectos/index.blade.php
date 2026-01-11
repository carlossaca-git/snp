@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-dark fw-bold">Banco de Proyectos de Inversión</h1>
                <p class="text-muted">Listado oficial de intervenciones y alineación estratégica.</p>
            </div>
            <a href="{{ route('inversion.proyectos.create') }}"
                class="btn btn-dark border-2 fw-bold d-inline-flex align-items-center">
                <span data-feather="plus" data-feather="plus"></span> Nuevo Proyecto
            </a>
        </div>
        @include('partials.mensajes')
        <form action="{{ route('inversion.proyectos.index') }}" method="GET">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body bg-light rounded">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="buscar" class="form-control"
                                placeholder="Buscar por CUP o Nombre..." value="{{ request('buscar') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="entidad" class="form-select">
                                <option value="">Todas las Entidades</option>
                                @foreach ($unidades as $u)
                                    <option value="{{ $u->id_unidad_ejecutora }}"
                                        {{ request('entidad') == $u->id_unidad_ejecutora ? 'selected' : '' }}>
                                        {{ $u->nombre_unidad }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-secondary w-100">Filtrar</button>
                        </div>

                        @if (request()->filled('buscar') || request()->filled('entidad'))
                            <div class="col-md-2">
                                <a href="{{ route('inversion.proyectos.index') }}" class="btn btn-link text-muted w-100">
                                    Limpiar
                                </a>
                            </div>
                        @endif
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
                            <th>Entidad Responsable</th>
                            <th>Alineación PND</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proyectos as $p)
                            <tr>
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
                                <td>
                                    @if ($p->objetivo)
                                        <span class="badge bg-info text-dark"
                                            title="{{ $p->objetivo->descripion_objetivo }}">
                                            <span data-feather="target" style="width: 12px"></span> Obj.
                                            {{ $p->objetivo->id_objetivo_nacional }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">No alineado</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($p->estado == 1)
                                        <span class="badge rounded-pill bg-success">Activo</span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group shadow-sm">
                                        <a href="{{ route('inversion.proyectos.show', $p->id) }}"
                                            class="btn btn-sm btn-info text-white" title="Ver detalles">
                                            <i class="" data-feather="eye"></i>
                                        </a>
                                        <a href="{{ route('inversion.proyectos.edit', $p->id) }}"
                                            class="btn btn-sm btn-white border" title="Editar">
                                            <span data-feather="edit" class="text-primary"></span>
                                        </a>
                                        <form action="{{ route('inversion.proyectos.destroy', $p->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('¿Estás SEGURO de eliminar este proyecto?\n\nSe borrarán también sus metas y financiamientos.');">

                                            @csrf
                                            @method('DELETE') <button type="submit"
                                                class="btn btn-sm btn-white border text-danger" title="Eliminar Proyecto">
                                                <i class="fas fa-trash fs-5" data-feather="trash-2"></i>
                                            </button>

                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <span data-feather="info" class="mb-2" style="width: 48px; height: 48px;"></span>
                                    <p>No hay proyectos registrados en el banco de inversión.</p>
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
