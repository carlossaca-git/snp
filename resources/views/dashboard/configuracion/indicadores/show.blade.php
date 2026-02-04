@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Ficha tecnica de indicadores" subtitulo="Kardex de seguimiento y evolución histórica">
         @if (Auth::user()->tienePermiso('indicadores.gestionar'))
            <button type="button" class="btn btn-danger shadow-sm"
                onclick="abrirVisorPdf('{{ route('reportes.catalogos.indicadores.pdf', $indicador->id_indicador) }}')">
                <i class="fas fa-file-pdf me-2"></i>Exportar Ficha
            </button>
        @endif

        <a href="{{ route('catalogos.indicadores.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-2"></i>Volver al Listado
        </a>
    </x-layouts.header_content>
    @include('partials.mensajes')
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-1 mb-3">
                <div class="card-header bg-ligth py-3 fw-bold">
                    <i class="fas fa-file-alt me-2 text-primary"></i>Ficha Técnica
                </div>
                <div class="card-body">
                    <h5 class="fw-bold text-dark mb-3">{{ $indicador->nombre_indicador }}</h5>

                    <div class="mb-3">
                        <small class="text-muted text-uppercase fw-bold">Meta Vinculada</small>
                        <p class="mb-0 small">{{ $indicador->metaNacional->codigo_meta ?? '' }} -
                            {{ $indicador->metaNacional->nombre_meta ?? 'N/A' }}</p>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted text-uppercase fw-bold">Línea Base</small>
                            <div class="h5 text-secondary">{{ number_format($indicador->linea_base, $indicador->precision ) }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-primary text-uppercase fw-bold">Meta Final</small>
                            <div class="h5 text-primary">{{ number_format($indicador->meta_final, $indicador->precision ) }}</div>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-2">
                        <small class="text-muted fw-bold">Frecuencia:</small> {{ $indicador->frecuencia }}
                    </div>
                    <div class="mb-2">
                        <small class="text-muted fw-bold">Unidad:</small> {{ $indicador->unidad_medida }}
                    </div>
                    <div class="mb-2">
                        <small class="text-muted fw-bold">Fórmula:</small>
                        <div class="bg-light p-2 rounded small mt-1 border">
                            <em>{{ $indicador->metodo_calculo }}</em>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-1 shadow-sm ">
                <div class="card-header bg-ligth fw-bold py-3">
                    <i class="fas fa-chart-pie me-2 text-primary"></i> Estado Actual
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                    <h1
                        class="display-1 fw-bold {{ $indicador->porcentaje_cumplimiento >= 100 ? 'text-success' : 'text-primary' }}">
                        {{ number_format($indicador->porcentaje_cumplimiento, $indicador->precision) }}%
                    </h1>
                    <p class="text-muted mb-3">Avance de Gestión</p>
                    {{-- Progreso Geneeral--}}
                    <div class="progress w-100 mb-2" style="height: 15px;">
                        <div class="progress-bar {{ $indicador->porcentaje_cumplimiento >= 100 ? 'bg-success' : 'bg-primary' }}"
                            role="progressbar" style="width: {{ $indicador->porcentaje_cumplimiento }}%">
                        </div>
                    </div>
                    <div class="row text-center py-3 w-100">
                        {{-- linea base --}}
                        <div class="col-4 border-end">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Línea Base</small>
                            <div class="fs-5 fw-bold text-secondary">{{ number_format($indicador->linea_base, $indicador->precision ) }}</div>
                        </div>
                        {{-- Avance Actual--}}
                        <div class="col-4 border-end bg-light py-1 rounded">
                            <small class="text-primary text-uppercase fw-bold" style="font-size: 10px;">Valor Actual</small>
                            <div class="fs-4 fw-bold text-primary">
                                {{ number_format($indicador->valor_actual_absoluto, $indicador->precision ) }}
                            </div>
                            <small class="text-muted">{{ $indicador->unidad_medida }}</small>
                        </div>
                        <div class="col-4">
                            <small class="text-success text-uppercase fw-bold" style="font-size: 10px;">Meta Final</small>
                            <div class="fs-5 fw-bold text-success">{{ number_format($indicador->meta_final, $indicador->precision ) }}</div>
                        </div>
                    </div>
                    <div class="alert alert-light border mt-3 small text-start w-100">
                        <i class="fas fa-info-circle me-1 text-info"></i>
                        Este valor es la suma de los aportes reales de
                        <strong>{{ $indicador->proyectos->count() }}</strong> proyectos vinculados.
                    </div>
                </div>
            </div>
        </div>

        {{--  Grafica de Avance y Tabla --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-1 mb-3">
                <div class="card-header bg-ligth fw-bold py-3">
                    <h6 class="m-0 fw-bold text-dark"><i class="fas fa-chart-line text-primary me-2"></i>Evolución Histórica</h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="graficoAvance"></canvas>
                    </div>
                </div>
            </div>
            {{-- TABLA DE AVANCES --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-project-diagram me-2 text-info"></i> Desglose de Aportes por Proyecto</span>
                    <span class="badge bg-light text-dark border">Peso PND: {{ $indicador->peso_oficial }}%</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="40%" class="ps-4">Proyecto</th>
                                    <th width="20%" class="text-center">Avance Físico<br><small>(Realidad)</small></th>
                                    <th width="20%" class="text-center">Contribución<br><small>(Peso)</small></th>
                                    <th width="20%" class="text-center bg-light-soft text-success">Aporte
                                        Final<br><small>(Suma)</small></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($proyectos as $proyecto)
                                    @php
                                        // Cálculos para mostrar en la tabla
                                        $avance = $proyecto->avance_fisico_real ?? 0;
                                        $peso = $proyecto->pivot->contribucion_proyecto ?? 0;
                                        $aporte = ($avance * $peso) / 100;
                                    @endphp
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">
                                                {{ Str::limit($proyecto->nombre_proyecto, 50) }}
                                            </div>
                                            <small class="text-muted">ID: {{ $proyecto->cup }}</small>
                                        </td>

                                        {{-- Avance del Proyecto --}}
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ number_format($avance, $indicador->precision) }}%</span>
                                        </td>

                                        {{-- Peso asignado en este indicador --}}
                                        <td class="text-center">
                                            <span class="badge bg-white text-dark border">{{ $peso }}%</span>
                                        </td>

                                        {{-- Resultado Matemático --}}
                                        <td class="text-center fw-bold text-success bg-light-soft">
                                            +{{ number_format($aporte, 2) }}pts
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="fas fa-folder-open fa-2x mb-3 opacity-25"></i>
                                            <p>No hay proyectos vinculados que alimenten este indicador.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            {{-- TOTAL --}}
                            @if ($indicador->proyectos->isNotEmpty())
                                <tfoot class="bg-light border-top">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold  pe-3">TOTAL CUMPLIMIENTO:</td>
                                        <td class="text-center fw-bold text-dark bg-success-subtle">
                                            {{ $indicador->porcentaje_cumplimiento }}%
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                        <div class="pagination-clean pagination-custom mt-4">
                            {{ $proyectos->links('partials.paginacion') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT PARA LA GRÁFICA --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('graficoAvance').getContext('2d');

                // Datos que vienen del controlador
                const labels = {!! $chartLabels !!};
                const dataValues = {!! $chartData !!};

                // Crear un degradado bonito (Verde a Blanco)
                let gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(25, 135, 84, 0.5)');
                gradient.addColorStop(1, 'rgba(25, 135, 84, 0.0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Cumplimiento Acumulado (%)',
                            data: dataValues,
                            borderColor: '#198754',
                            backgroundColor: gradient,
                            borderWidth: 3,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#198754',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return ' Avance: ' + context.parsed.y + '%';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                grid: {
                                    borderDash: [5, 5],
                                    color: '#e9ecef'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
