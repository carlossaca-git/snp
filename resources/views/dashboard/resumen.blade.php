@extends('layouts.app')

@section('titulo', 'Panel de Control SIPEIP')

@section('content')
    <div
        class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-3 mb-4 bg-white border-bottom shadow-sm mx-n4 mt-n4 px-4">
        <div class="d-flex align-items-center">
            <div class="bg-primary rounded-pill me-3" style="width: 5px; height: 35px; background-color: #1a4a72 !important;">
            </div>
            <div>
                <h1 class="h4 mb-0 fw-bold text-dark">Panel de Control</h1>
                <small class="text-muted">Gestión Administrativa SIPEIP</small>
            </div>
        </div>

        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-light border disabled text-dark" style="opacity: 1;">
                    <i class="far fa-calendar-alt me-1"></i> {{ date('d/m/Y') }}
                </button>
            </div>
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="imprimirReporteActual()">
                    <i class="fas fa-file-pdf me-1"></i> Exportar PDF
                </button>
            </div>

            {{-- DROPDOWN DE FILTRO TEMPORAL --}}
            <div class="dropdown">
                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="btnFiltroFecha"
                    data-bs-toggle="dropdown" aria-expanded="false"
                    style="background-color: #1a4a72; border-color: #1a4a72;">
                    <i class="fas fa-filter me-1"></i> <span id="textoFiltro">Este Año</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="btnFiltroFecha">
                    <li><a class="dropdown-item" href="javascript:void(0)"
                            onclick="filtrarDatos('semana', 'Esta Semana')">Esta Semana</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="filtrarDatos('mes', 'Este Mes')">Este
                            Mes</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="filtrarDatos('anio', 'Este Año')">Este
                            Año</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container-fluid px-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h5 text-dark fw-bold mb-0">Bienvenido, {{ auth()->user()->usuario ?? 'Usuario' }} 👋</h2>
                <p class="text-muted small mb-0">Resumen de la planificación e inversión pública.</p>
            </div>
        </div>
        {{-- Total proyectos --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white shadow-sm border-0 h-100 py-2"
                    style="background-color: #1a4a72 !important;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-uppercase mb-1" style="opacity: 0.8;">Total Proyectos</div>
                                <div class="h3 mb-0 fw-bold" id="kpi-total">{{ $totalProyectos }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-folder fa-2x" style="opacity: 0.4;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Inversión Total --}}
            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white shadow-sm border-0 h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-uppercase mb-1" style="opacity: 0.8;">Inversión Total</div>
                                <div class="h4 mb-0 fw-bold">
                                    $ <span id="kpi-solicitado">{{ number_format($montoTotal / 1000000, 2) }}</span> M
                                </div>
                                <small style="opacity: 0.8;">Millones USD</small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x" style="opacity: 0.4;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dictámenes Favorables --}}
            <div class="col-xl-3 col-md-6">
                <div class="card bg-info text-white shadow-sm border-0 h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-uppercase mb-1" style="opacity: 0.8;">Dictamen Favorable
                                </div>
                                <div class="h3 mb-0 fw-bold" id="kpi-favorables">{{ $proyectosFavorables }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x" style="opacity: 0.4;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pendientes --}}
            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-dark shadow-sm border-0 h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-uppercase mb-1" style="opacity: 0.7;">Pendientes / Revisión
                                </div>
                                <div class="h3 mb-0 fw-bold" id="kpi-pendientes">{{ $proyectosPendientes }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-dark" style="opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECCIÓN DE METAS NACIONALES --}}
        <div class="row g-3 mb-4">
            <div class="col-12">
                <h3 class="h5 text-dark fw-bold mb-2">📊 Seguimiento de Metas Nacionales</h3>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card shadow-sm border-0 h-100 py-2 border-start border-4 border-info">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-info text-uppercase mb-1">Total Indicadores</div>
                                <div class="h3 mb-0 fw-bold text-gray-800">{{ $totalIndicadores }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-bullseye fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card shadow-sm border-0 h-100 py-2 border-start border-4 border-danger">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-danger text-uppercase mb-1">Metas en Riesgo</div>
                                <div class="h3 mb-0 fw-bold text-gray-800">{{ $indicadoresCriticos }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-md-12">
                <div class="card bg-light border-0 h-100 d-flex flex-row align-items-center px-4">
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold">Gestión de Indicadores</h6>
                        <small class="text-muted">Revise el cumplimiento de metas del Plan Nacional.</small>
                    </div>
                    <a href="{{ route('catalogos.indicadores.index') }}"
                        class="btn btn-primary btn-sm rounded-pill shadow-sm">
                        Ir al Listado <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- GRAFICO SEMÁFORO Y TABLA CRÍTICA --}}
        <div class="row mb-4">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <h6 class="fw-bold text-muted text-uppercase small">Cumplimiento Global de Metas</h6>
                        <div style="position: relative; height: 200px;">
                            <canvas id="chartCumplimiento"></canvas>
                            <div style="position: absolute; top: 65%; left: 50%; transform: translate(-50%, -50%);">
                                <h2
                                    class="fw-bolder mb-0 {{ $promedioGlobal < 40 ? 'text-danger' : ($promedioGlobal < 75 ? 'text-warning' : 'text-success') }}">
                                    {{ $promedioGlobal }}%
                                </h2>
                                <span class="text-muted small">Promedio Nacional</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <span
                                class="badge {{ $promedioGlobal < 40 ? 'bg-danger' : 'bg-light text-muted' }} small">Crítico</span>
                            <span
                                class="badge {{ $promedioGlobal >= 40 && $promedioGlobal < 75 ? 'bg-warning text-dark' : 'bg-light text-muted' }} small">En
                                Alerta</span>
                            <span
                                class="badge {{ $promedioGlobal >= 75 ? 'bg-success' : 'bg-light text-muted' }} small">Satisfactorio</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla de Indicadores Críticos --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="m-0 fw-bold text-danger"><i class="fas fa-exclamation-circle me-2"></i>Indicadores de
                            Atención Prioritaria</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light small">
                                    <tr>
                                        <th class="ps-4">Indicador</th>
                                        <th>Meta</th>
                                        <th>Avance</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Aquí podrías iterar indicadores reales si los pasas desde el controlador --}}
                                    <tr>
                                        <td class="ps-4 small fw-bold">Tasa de Desempleo Juvenil</td>
                                        <td class="small">8.5%</td>
                                        <td class="small">12.1%</td>
                                        <td><span class="badge bg-danger">Crítico</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 small fw-bold">Acceso a Agua Potable Rural</td>
                                        <td class="small">95.0%</td>
                                        <td class="small">82.3%</td>
                                        <td><span class="badge bg-warning text-dark">Alerta</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- GRÁFICOS DE INVERSIÓN Y ESTADOS --}}
        <div class="row mb-4">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header py-3 bg-white border-0">
                        <h6 class="m-0 fw-bold text-primary">💰 Inversión por Eje Estratégico</h6>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px;">
                            <canvas id="chartEjes"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header py-3 bg-white border-0">
                        <h6 class="m-0 fw-bold text-primary">📊 Estado de Dictámenes</h6>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px;">
                            <canvas id="chartEstados"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header py-3 bg-white border-0">
                <h6 class="m-0 fw-bold text-primary">📝 Últimos Proyectos Registrados</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 border-0">CUP / Nombre</th>
                                <th class="border-0">Monto</th>
                                <th class="border-0">Estado</th>
                                <th class="border-0">Ficha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ultimosProyectos as $p)
                                <tr>
                                    <td class="ps-4" style="max-width: 300px;">
                                        <div class="fw-bold text-dark small">{{ $p->cup }}</div>
                                        <div class="text-muted text-truncate small">
                                            {{ $p->nombre_proyecto }}
                                        </div>
                                    </td>
                                    <td class="fw-bold text-success small">
                                        ${{ number_format($p->monto_total_inversion, 0) }}
                                    </td>
                                    <td>
                                        @if ($p->estado_dictamen == 'FAVORABLE')
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1">Favorable</span>
                                        @elseif($p->estado_dictamen == 'OBSERVADO')
                                            <span
                                                class="badge bg-warning bg-opacity-10 text-dark border border-warning px-2 py-1">Observado</span>
                                        @else
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-dark border border-secondary px-2 py-1">{{ $p->estado_dictamen ?? 'Pendiente' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger"
                                            onclick="abrirVisorPdf('{{ route('reportes.proyecto.individual', $p->id) }}')">
                                            <i class="fas fa-file-pdf"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No hay proyectos recientes para
                                        mostrar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-center bg-white border-0 py-3">
                <a href="{{ route('inversion.proyectos.index') }}"
                    class="btn btn-sm btn-outline-primary rounded-pill px-4">Ver todos los proyectos</a>
            </div>
        </div>

        {{-- TARJETAS DE METAS PND (DETALLE) --}}
        <div class="row">
            <div class="card-header py-3 bg-white border-0">
                <h6 class="m-0 fw-bold text-primary">📝 Desglose de metas nacionales y proyectos</h6>
            </div>
            @forelse ($metasNacionales as $meta)
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100 border-start border-primary">
                        <div class="card-body">
                            <small class="text-muted text-uppercase">Meta PND {{ $meta->codigo_meta }}</small>
                            <h5 class="fw-bold mt-1">{{ Str::limit($meta->meta_nacional, 100) }}</h5>
                            <hr>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-bold text-dark">Cumplimiento de Meta</span>
                                    <span
                                        class="fw-bold {{ $meta->avance_promedio > 80 ? 'text-success' : 'text-primary' }}">
                                        {{ number_format($meta->avance_promedio, 1) }}%
                                    </span>
                                </div>
                                <div class="progress" style="height: 15px;">
                                    <div class="progress-bar bg-primary progress-bar-striped"
                                        style="width: {{ $meta->avance_promedio }}%"></div>
                                </div>
                            </div>
                            {{-- Desglose de proyectos --}}
                            @if ($meta->proyectos->count() > 0)
                                <div class="bg-light p-2 rounded">
                                    <small class="fw-bold d-block mb-2 text-secondary">Proyectos contribuyentes:</small>
                                    @foreach ($meta->proyectos->take(3) as $proy)
                                        <div
                                            class="d-flex justify-content-between align-items-center mb-1 border-bottom pb-1">
                                            <span class="small text-truncate" style="max-width: 70%;">
                                                <i class="fas fa-project-diagram me-1 text-muted"></i>
                                                {{ $proy->nombre_proyecto }}
                                            </span>
                                            <span
                                                class="badge bg-white text-dark border">{{ number_format($proy->avance_fisico_real, 0) }}%</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-3">No hay metas nacionales vinculadas actualmente.</div>
            @endforelse
        </div>

    </div>


@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // --- FUNCIÓN GLOBAL PARA ABRIR VISOR PDF
        // (Se usa en el botón de la cabecera)
        function imprimirReporteActual() {
            let rango = 'anio';
            const textoFiltro = document.getElementById('textoFiltro').innerText.toLowerCase();

            if (textoFiltro.includes('mes')) rango = 'mes';
            else if (textoFiltro.includes('semana')) rango = 'semana';

            let url = "{{ route('reportes.proyectos.general') }}?rango=" + rango;
            abrirVisorPdf(url);
        }

        // --- VARIABLES GLOBALES CHART.JS ---
        let chartEjesInstance = null;
        let chartEstadosInstance = null;
        let chartCumplimientoInstance = null;

        // --- INICIALIZACIÓN ---
        document.addEventListener("DOMContentLoaded", function() {

            // Gráfico Barras (Ejes)
            const ctxEjes = document.getElementById('chartEjes').getContext('2d');
            const dataEjesPHP = @json($inversionPorEje);

            chartEjesInstance = new Chart(ctxEjes, {
                type: 'bar',
                data: {
                    labels: dataEjesPHP.map(item => item.nombre_eje),
                    datasets: [{
                        label: 'Inversión ($)',
                        data: dataEjesPHP.map(item => item.total),
                        backgroundColor: '#1a4a72',
                        borderRadius: 4,
                        maxBarThickness: 50
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => '$' + value / 1000000 + 'M'
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

            //  Grafico Dona (Estados)
            const ctxEstados = document.getElementById('chartEstados').getContext('2d');
            const dataEstadosPHP = @json($estadosDictamen);

            chartEstadosInstance = new Chart(ctxEstados, {
                type: 'doughnut',
                data: {
                    labels: dataEstadosPHP.map(item => item.estado_dictamen || 'SIN DEFINIR'),
                    datasets: [{
                        data: dataEstadosPHP.map(item => item.total),
                        backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b', '#858796'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true
                            }
                        }
                    }
                }
            });

            // Gráfico Semáforo (Cumplimiento)
            const ctxCump = document.getElementById('chartCumplimiento').getContext('2d');
            let promedio = {{ $promedioGlobal }};
            let colorCumplimiento = promedio < 40 ? '#e74a3b' : (promedio < 75 ? '#f6c23e' : '#1cc88a');

            chartCumplimientoInstance = new Chart(ctxCump, {
                type: 'doughnut',
                data: {
                    labels: ['Cumplimiento', 'Restante'],
                    datasets: [{
                        data: [promedio, 100 - promedio],
                        backgroundColor: [colorCumplimiento, '#eaecf4'],
                        borderWidth: 0,
                        circumference: 180,
                        rotation: 270,
                        cutout: '80%',
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });

        //FUNCIÓN AJAX PARA FILTRAR DATOS
        function filtrarDatos(rango, textoBoton) {
            // Feedback visual
            const elTexto = document.getElementById('textoFiltro');
            if (elTexto) elTexto.innerText = textoBoton;
            document.body.style.cursor = 'wait';

            fetch("{{ route('reportes.dashboard.filtrar') }}?rango=" + rango)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert("Error: " + data.mensaje);
                        return;
                    }

                    // Actualizar KPIs con animación
                    animateValue("kpi-total", parseInt(document.getElementById('kpi-total').innerText), data.kpis
                    .total);
                    animateValue("kpi-favorables", parseInt(document.getElementById('kpi-favorables').innerText), data
                        .kpis.favorables);
                    animateValue("kpi-pendientes", parseInt(document.getElementById('kpi-pendientes').innerText), data
                        .kpis.pendientes);

                    // Formatear Dinero
                    const formatter = new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    if (data.kpis.solicitado !== undefined && data.kpis.solicitado !== null) {
                        // Actualizamos SOLO el número dentro del span
                        document.getElementById('kpi-solicitado').innerText = data.kpis.solicitado;
                    } else {
                        // Si por alguna razón llega nulo, ponemos 0.00 para evitar "NN" o vacíos
                        document.getElementById('kpi-solicitado').innerText = "0.00";
                    }
                    // Actualizar Gráfico Cumplimiento
                    const txtPromedio = document.querySelector('.fw-bolder.mb-0');
                    if (txtPromedio) {
                        txtPromedio.innerText = data.kpis.promedioGlobal + '%';
                        txtPromedio.className = 'fw-bolder mb-0 ' + (data.kpis.promedioGlobal < 40 ? 'text-danger' :
                            (data.kpis.promedioGlobal < 75 ? 'text-warning' : 'text-success'));
                    }
                    if (chartCumplimientoInstance) {
                        chartCumplimientoInstance.data.datasets[0].data = [data.kpis.promedioGlobal, 100 - data.kpis
                            .promedioGlobal
                        ];
                        chartCumplimientoInstance.data.datasets[0].backgroundColor[0] = data.kpis.promedioGlobal < 40 ?
                            '#e74a3b' :
                            (data.kpis.promedioGlobal < 75 ? '#f6c23e' : '#1cc88a');
                        chartCumplimientoInstance.update();
                    }

                    // Actualizar Gráfico Ejes
                    if (chartEjesInstance) {
                        chartEjesInstance.data.labels = data.graficos.ejesLabels;
                        chartEjesInstance.data.datasets[0].data = data.graficos.ejesData;
                        chartEjesInstance.update();
                    }

                    // Actualizar Gráfico Estados
                    if (chartEstadosInstance) {
                        chartEstadosInstance.data.labels = data.graficos.estadosLabels;
                        chartEstadosInstance.data.datasets[0].data = data.graficos.estadosData;
                        chartEstadosInstance.update();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Error de conexión.");
                })
                .finally(() => {
                    document.body.style.cursor = 'default';
                });
        }

        // --- FUNCIÓN PARA ANIMAR NÚMEROS  ---
        function animateValue(id, start, end) {
            if (start === end) return;
            const obj = document.getElementById(id);
            if (!obj) return;

            const range = end - start;
            let current = start;
            const increment = end > start ? 1 : -1;
            const stepTime = Math.abs(Math.floor(1000 / range));

            const timer = setInterval(function() {
                current += increment;
                obj.innerText = current;
                if (current == end) {
                    clearInterval(timer);
                }
            }, Math.max(stepTime, 20));
        }
    </script>
@endpush
