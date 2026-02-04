@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Ficha Técnica de Meta Nacional"
        subtitulo="Compromisos cuantificables vinculados a los Objetivos Nacionales">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('catalogos.metas.index') }}"
                class="btn btn-sm btn-outline-secondary me-2 d-inline-flex align-items-center">
                <i class="fas fa-home"></i> Catalogo
            </a>
            <button type="button" class="btn btn-secondary" onclick="history.back()">
                <i class="fas fa-arrow-left me-1"></i> Atras
            </button>
        </div>
    </x-layouts.header_content>

    @include('partials.mensajes')

    <div class="container-fluid">
        @php
            $sumaPesos = $meta->indicadoresNacionales->sum('peso_oficial');
        @endphp

        @if($sumaPesos != 100)
            <div class="alert alert-danger d-flex align-items-center shadow-sm mb-4" role="alert">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div>
                    <strong>¡Atención! Error de Configuración de Pesos</strong><br>
                    La suma de los pesos de los indicadores es <strong>{{ $sumaPesos }}%</strong>.
                    Para que el cálculo del avance sea matemático exacto, debe sumar <strong>100%</strong>.
                </div>
            </div>
        @endif

        <div class="row">
            {{-- INFORMACION --}}
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm h-100 border-top-primary">
                    <div class="card-header py-3 bg-light">
                        <h6 class="m-0 font-weight-bold fw-bold text-dark">
                            <i class="fas fa-info-circle text-primary me-2"></i> Información Estratégica</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="text-xs font-weight-bold text-uppercase text-muted mb-1">Nombre de la Meta</label>
                            <div class="h5 font-weight-bold text-gray-800">{{ $meta->nombre_meta }}</div>
                            <div class="p-3 bg-light rounded border-0 text-dark small"
                                style="text-align: justify; line-height: 1.6;">
                                {{ $meta->descripcion_meta ?? 'No se ha registrado una descripción detallada para esta meta.' }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="text-xs font-weight-bold text-uppercase text-muted mb-1">Objetivo Nacional (Padre)</label>
                                <div class="p-2 bg-light rounded border-left-primary d-flex align-items-center">
                                    <span class="badge bg-primary me-2">{{ $meta->objetivoNacional->codigo_objetivo ?? 'N/A' }}</span>
                                    <span class="text-dark">{{ $meta->objetivoNacional->descripcion_objetivo ?? 'Sin Asignar' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2">
                            <label class="text-xs font-weight-bold text-uppercase text-muted mb-1">Alineación ODS</label>
                            <div class="d-flex flex-wrap align-items-start gap-3">
                                @forelse($meta->ods ?? [] as $od)
                                    <div class="d-flex flex-column align-items-center" style="width: 100px;">
                                        <span class="badge rounded-pill mb-1 p-2 w-100"
                                            style="background-color: {{ $od->color_hex ?? '#777777' }};">
                                            <i class="fas fa-globe-americas me-1"></i>
                                            {{ $od->codigo ?? 'ODS' }}
                                        </span>
                                        <div class="text-center lh-sm">
                                            <small class="text-muted fw-bold" style="font-size: 0.7rem;">
                                                {{ Str::limit($od->nombre_ods ?? $od->nombre, 45) }}
                                            </small>
                                        </div>
                                    </div>
                                @empty
                                    <span class="text-muted small">
                                        <i class="fas fa-info-circle me-1"></i> No vinculado a ODS
                                    </span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100 border-top-info">
                    <div class="card-header py-3 bg-light">
                        <h6 class="m-0 font-weight-bold text-dark fw-bold">
                            <i class="fas fa-chart-line text-info me-2"></i> Desempeño Global</h6>
                    </div>
                    <div class="card-body">
                        {{-- TARJETAS DATOS --}}
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <span class="text-xs font-weight-bold text-uppercase text-muted">Línea Base (Inicio)</span>
                                    <div class="h6 mb-0">{{ number_format($meta->linea_base, 2) }}</div>
                                </div>
                                <span class="badge bg-secondary rounded-circle p-2"><i class="fas fa-history"></i></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <span class="text-xs font-weight-bold text-uppercase text-muted">Meta PND (Fin)</span>
                                    <div class="h6 mb-0">{{ number_format($meta->meta_final ?? $meta->meta_valor, 2) }}</div>
                                </div>
                                <span class="badge bg-primary rounded-circle p-2"><i class="fas fa-flag-checkered"></i></span>
                            </li>
                        </ul>

                        {{-- RESULTADO CALCULADO --}}
                        @php
                            $avanceGlobal = $meta->avance_actual;
                        @endphp

                        <div class="text-center mt-2 p-3 bg-light rounded">
                            <span class="text-xs font-weight-bold text-uppercase text-muted">Cumplimiento Ponderado</span>
                            <div class="display-4 font-weight-bold {{ $avanceGlobal < 50 ? 'text-danger' : ($avanceGlobal >= 100 ? 'text-success' : 'text-primary') }} mb-1">
                                {{ number_format($avanceGlobal, 2) }}%
                            </div>

                            <div class="progress shadow-sm" style="height: 15px; border-radius: 10px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated {{ $avanceGlobal < 50 ? 'bg-danger' : ($avanceGlobal >= 100 ? 'bg-success' : 'bg-primary') }}"
                                    role="progressbar" style="width: {{ $avanceGlobal }}%"></div>
                            </div>

                            <p class="small text-muted mt-2 mb-0">
                                <i class="fas fa-calculator me-1"></i>
                                Suma ponderada de {{ $meta->indicadoresNacionales->count() }} indicadores.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DESGLOSE POR INDICADORES --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light border-bottom-warning d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold fw-bold text-dark">
                    <i class="fas fa-chart-pie text-warning me-2"></i> Composición por Indicadores Nacionales
                </h6>
                <span class="badge bg-light text-dark border">
                    Suma de Pesos: <span class="{{ $sumaPesos != 100 ? 'text-danger fw-bold' : 'text-success' }}">{{ $sumaPesos }}%</span>
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary small text-uppercase">
                            <tr>
                                <th class="ps-4">Indicador Nacional</th>
                                <th class="text-center">Peso</th>
                                <th class="text-center">Avance Real</th>
                                <th class="text-center bg-light-soft text-dark">Aporte a la Meta</th>
                                <th class="text-end pe-4">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($meta->indicadoresNacionales as $indicador)
                                @php
                                    $peso = $indicador->peso_oficial;
                                    $avance = $indicador->porcentaje_cumplimiento;
                                    $aporte = ($avance * $peso) / 100;
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $indicador->nombre }}</div>
                                        <div class="small text-muted">
                                            {{ $indicador->proyectos_count ?? $indicador->proyectos->count() }} Proyectos vinculados
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-white text-dark border">{{ number_format($peso, 2) }}%</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center flex-column">
                                            <span class="fw-bold small">{{ number_format($avance, 1) }}%</span>
                                            <div class="progress w-75" style="height: 4px;">
                                                <div class="progress-bar {{ $avance >= 100 ? 'bg-success' : 'bg-info' }}" style="width: {{ $avance }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold text-primary bg-light-soft">
                                        +{{ number_format($aporte, 2) }} pts
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('catalogos.indicadores.show', $indicador->id_indicador) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No hay indicadores configurados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TABLA DE PROYECTOS  --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-gray-100 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold fw-bold text-dark">
                    <i class="fas fa-project-diagram me-2 text-primary"></i> Listado de Proyectos de Inversión
                </h6>
                <form action="{{ route('catalogos.metas.show', $meta->id_meta_nacional) }}" method="GET">
                    <div class="input-group shadow-sm">

                        <input type="text" name="busqueda" id="inputBusqueda"
                            class="form-control border-start-0 border-end-0 shadow-none"
                            placeholder="Buscar proyecto..." value="{{ request('busqueda') }}">
                        <button type="button" id="btnLimpiarBusqueda"
                            class="btn bg-white border-top border-bottom border-end-0 text-danger"
                            style="display: none; z-index: 1000 !important;">
                            <i class="fas fa-times"></i>
                        </button>
                        <button class="btn btn-secondary" type="submit">
                            <i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40%">Proyecto</th>
                                <th>Indicador Padre</th>
                                <th class="text-center">Monto</th>
                                <th class="text-center" style="width: 20%">Avance Físico</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proyectos as $proy)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ Str::limit($proy->nombre_proyecto, 60) }}</div>
                                        <div class="small text-muted">CUP: {{ $proy->cup ?? 'S/N' }}</div>
                                    </td>
                                    <td>
                                        {{-- Indicadores --}}
                                        <span class="badge bg-light text-secondary border">
                                            <i class="fas fa-chart-line me-1"></i>
                                            {{ Str::limit($proy->indicador_padre->codigo_indicador ?? 'Ver Indicador', 20) }}
                                        </span>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        ${{ number_format($proy->monto_total_inversion, 2) }}
                                    </td>
                                    <td style="vertical-align: middle;">
                                        @php
                                            $avanceFisico = $proy->avance_fisico_real ?? 0;
                                        @endphp
                                        <div class="d-flex justify-content-between">
                                            <span class="small fw-bold">{{ number_format($avanceFisico, 1) }}%</span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar {{ $avanceFisico >= 100 ? 'bg-success' : 'bg-info' }}"
                                                style="width: {{ $avanceFisico }}%"></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('inversion.proyectos.show', $proy->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="bg-light rounded-circle p-4 mb-3">
                                                <i class="fas fa-folder-open fa-3x text-gray-400"></i>
                                            </div>
                                            <h5 class="text-gray-600">Sin proyectos vinculados</h5>
                                            <p class="text-gray-500 mb-0">Esta meta aún no tiene proyectos reportando avances a sus indicadores.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if(method_exists($proyectos, 'links'))
                <div class="card-footer bg-white">
                    {{ $proyectos->links() }}
                </div>
            @endif
        </div>

    </div>
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
            // Ejecutar al cargar (por si ya viene de una búsqueda)
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
@endsection
