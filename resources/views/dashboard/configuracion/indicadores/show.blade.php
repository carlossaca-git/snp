@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="{{ $indicador->nombre_indicador }}"
        subtitulo="Kardex de seguimiento y evolución histórica">
        {{-- Botón PDF --}}

             @if(Auth::user()->tienePermiso('indicadores.gestionar'))
        <button type="button" class="btn btn-danger shadow-sm"
            onclick="abrirVisorPdf('{{ route('reportes.catalogos.indicadores.pdf', $indicador->id_indicador) }}')">
            <i class="fas fa-file-pdf me-2"></i>Exportar Ficha
        </button>
        @endif

        <a href="{{ route('catalogos.indicadores.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-2"></i>Volver al Listado
        </a>
    </x-layouts.header_content>

    <div class="row">
        {{-- COLUMNA IZQUIERDA: Ficha Técnica --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 fw-bold"><i class="fas fa-file-alt me-2"></i>Ficha Técnica</h6>
                </div>
                <div class="card-body">
                    <h5 class="fw-bold text-dark mb-3">{{ $indicador->nombre_indicador }}</h5>

                    <div class="mb-3">
                        <small class="text-muted text-uppercase fw-bold">Meta Vinculada</small>
                        <p class="mb-0 small">{{ $indicador->meta->codigo_meta ?? '' }} -
                            {{ $indicador->meta->nombre_meta ?? 'N/A' }}</p>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted text-uppercase fw-bold">Línea Base</small>
                            <div class="h5 text-secondary">{{ number_format($indicador->linea_base, 2) }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-primary text-uppercase fw-bold">Meta Final</small>
                            <div class="h5 text-primary">{{ number_format($indicador->meta_final, 2) }}</div>
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
        </div>

        {{-- COLUMNA DERECHA Gráfica y Tabla --}}
        <div class="col-lg-8">

            {{-- GRÁFICA --}}
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-chart-line me-2"></i>Evolución Histórica</h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="graficoAvance"></canvas>
                    </div>
                </div>
            </div>

            {{-- TABLA DE AVANCES --}}
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark"><i class="fas fa-history me-2"></i>Historial de Reportes</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Fecha Reporte</th>
                                <th class="text-center">Valor Logrado</th>
                                <th>Observaciones</th>
                                <th class="text-end">Evidencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($indicador->avances as $avance)
                                <tr>
                                    <td>{{ date('d/m/Y', strtotime($avance->fecha_reporte)) }}</td>
                                    <td class="text-center fw-bold">{{ number_format($avance->valor_logrado, 2) }}</td>
                                    <td class="small text-muted">{{ Str::limit($avance->observaciones, 50) }}</td>
                                    <td class="text-end">
                                        @if ($avance->evidencia_path)
                                            <a href="{{ asset('storage/' . $avance->evidencia_path) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-paperclip"></i> Ver
                                            </a>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">Solo se ha registrado la línea
                                        base.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- SCRIPT PARA LA GRÁFICA --}}
    @push('scripts')
        {{-- Librería Chart --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        {{-- El Puente de Datos PHP - JS --}}
        <script>
            // Creamos un objeto global con los datos que necesita el gráfico
            const DATOS_KARDEX = {
                labels: @json($fechasGrafico),
                values: @json($valoresGrafico),
                meta: {{ $indicador->meta_final }}
            };
        </script>
        <script src="{{ asset('js/indicadores/show-logic.js') }}?v={{ time() }}"></script>
    @endpush
@endsection
