@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold text-slate-800 mb-1">Planificación Estratégica (PEI)</h1>
                <p class="text-slate-500 small mb-0">Gestión de Objetivos Institucionales</p>
            </div>
            <a href="{{ route('estrategico.objetivos.create') }}" class="btn btn-secondary shadow-sm px-3 fw-bold d-inline-flex align-items-center">
                <i class="fas fa-plus me-1" data-feather="plus"></i> Nuevo Objetivo
            </a>
        </div>
        @include('partials.mensajes')
        <div class="card card-clean rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 text-uppercase text-secondary small fw-bold">Código</th>
                                <th class="text-uppercase text-secondary small fw-bold" style="width: 40%">Objetivo
                                    Institucional</th>
                                <th class="text-uppercase text-secondary small fw-bold" style="width: 30%">Alineación
                                    Nacional (PND)</th>
                                <th class="text-center text-uppercase text-secondary small fw-bold">Vigencia</th>
                                <th class="text-end pe-4 text-uppercase text-secondary small fw-bold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($objetivos as $obj)
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-white border text-dark fw-bold">{{ $obj->codigo }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-slate-800">{{ Str::limit($obj->nombre, 100) }}</div>
                                        @if ($obj->indicador)
                                            <small class="text-muted"><i class="fas fa-chart-line me-1"></i> Ind:
                                                {{ $obj->indicador }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @forelse ($obj->objetivosNacionales ?? [] as $nacional)
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-flag text-warning me-2 mt-1"></i>
                                                <div>
                                                    <div class="small fw-bold text-dark">Obj. Nacional
                                                        {{ $nacional->codigo_objetivo }}
                                                        <div class="text-muted"
                                                            style="font-size: 0.75rem; line-height: 1.2;">
                                                            {{ Str::limit($nacional->descripcion_objetivo, 50) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <span class="badge bg-danger">Sin Alineación</span>
                                        @endforelse
                                    </td>
                                    <td class="text-center small text-muted">
                                        {{ \Carbon\Carbon::parse($obj->fecha_inicio)->format('Y') }} -
                                        {{ \Carbon\Carbon::parse($obj->fecha_fin)->format('Y') }}
                                    </td>
                                    <td class="text-end pe-4">
                                    <a href="{{ route('estrategico.objetivos.edit', $obj) }}"
                                            class="btn btn-sm btn-outline-warning"><span data-feather="edit"></span>
                                        </a>
                                    </td>
                                    <td class="text-end pe-4">
                                        <form
                                            action="{{ route('estrategico.objetivos.destroy', $obj->id_objetivo_estrategico) }}"
                                            method="POST" onsubmit="return confirm('¿Eliminar este objetivo?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt"
                                                    data-feather="trash-2"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">No has registrado objetivos
                                        estratégicos aún.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 px-2">
                        <div class="mb-2 mb-md-0 text-secondary small">
                            @if ($objetivos->total() > 0)
                                Mostrando <span class="fw-bold text-dark">{{ $objetivos->firstItem() }}</span>
                                - <span class="fw-bold text-dark">{{ $objetivos->lastItem() }}</span>
                                de <span class="fw-bold text-dark">{{ $objetivos->total() }}</span> resultados
                            @else
                                <span class="text-muted">Sin registros para mostrar</span>
                            @endif
                        </div>
                        <div>
                            {{ $objetivos->appends(request()->query())->links('partials.paginacion') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
