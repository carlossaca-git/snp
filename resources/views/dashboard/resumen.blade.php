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
                <button type="button" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-share-alt me-1"></i> Compartir
                </button>

                    <a href="{{ route('reportes.proyectos.general', ['rango' => 'anio']) }}" target="_blank"
                        class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-file-pdf me-1 text-danger"></i> Exportar PDF
                    </a>


            </div>
            <div class="dropdown">
                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="btnFiltroFecha"
                    data-bs-toggle="dropdown" aria-expanded="false"
                    style="background-color: #1a4a72; border-color: #1a4a72;">
                    <i class="fas fa-filter me-1"></i> <span id="textoFiltro">Este Año</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="btnFiltroFecha">
                    <li><a class="dropdown-item" href="#" onclick="filtrarDatos('semana', 'Esta Semana')">Esta
                            Semana</a></li>
                    <li><a class="dropdown-item" href="#" onclick="filtrarDatos('mes', 'Este Mes')">Este Mes</a></li>
                    <li><a class="dropdown-item" href="#" onclick="filtrarDatos('anio', 'Este Año')">Este Año</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="container-fluid px-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h5 text-dark fw-bold mb-0">Bienvenido, {{ auth()->user()->usuario }} 👋</h2>
                <p class="text-muted small mb-0">Resumen de la planificación e inversión pública.</p>
            </div>
        </div>

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

            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white shadow-sm border-0 h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-uppercase mb-1" style="opacity: 0.8;">Inversión Total</div>
                                <div class="h4 mb-0 fw-bold" id="kpi-solicitado">$
                                    {{ number_format($montoTotal / 1000000, 2) }} M</div>
                                <small style="opacity: 0.8;">Millones USD</small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x" style="opacity: 0.4;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ultimosProyectos as $p)
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
                                        @else
                                            <span
                                                class="badge bg-warning bg-opacity-10 text-dark border border-warning px-2 py-1">{{ $p->estado_dictamen ?? 'Pendiente' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-center bg-white border-0 py-3">
                <a href="{{ route('inversion.proyectos.index') }}"
                    class="btn btn-sm btn-outline-primary rounded-pill px-4">Ver todos los proyectos</a>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @push('scripts')
        <script>
            // 1. DEFINIR VARIABLES GLOBALES (Para que la función de filtrado pueda verlas)
            let chartEjesInstance = null;
            let chartEstadosInstance = null;

            // 2. INICIALIZAR GRÁFICOS AL CARGAR LA PÁGINA
            document.addEventListener("DOMContentLoaded", function() {

                // --- A. Configuración Gráfico de Barras (Ejes) ---
                const ctxEjes = document.getElementById('chartEjes').getContext('2d');
                const dataEjesPHP = @json($inversionPorEje); // Datos iniciales desde Laravel

                chartEjesInstance = new Chart(ctxEjes, {
                    type: 'bar',
                    data: {
                        labels: dataEjesPHP.map(item => item.nombre_eje),
                        datasets: [{
                            label: 'Inversión ($)',
                            data: dataEjesPHP.map(item => item.total),
                            backgroundColor: '#1a4a72', // Azul Institucional
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
                                grid: {
                                    borderDash: [2, 4]
                                },
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value / 1000000 + 'M';
                                    }
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

                // --- B. Configuración Gráfico Circular (Estados) ---
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
                                    usePointStyle: true,
                                    padding: 20
                                }
                            }
                        }
                    }
                });
            });

            // 3. FUNCIÓN AJAX PARA FILTRAR (Esta debe estar AFUERA del addEventListener)
            // Nota: Le puse el nombre 'filtrarDatos' para que coincida con tu error anterior
            function filtrarDatos(rango, textoBoton) {
                // Feedback visual
                if (document.getElementById('textoFiltro')) {
                    document.getElementById('textoFiltro').innerText = textoBoton;
                }
                document.body.style.cursor = 'wait';

                fetch("{{ route('reportes.dashboard.filtrar') }}?rango=" + rango)
                    .then(response => response.json()) // Ahora siempre será JSON, incluso si hay error
                    .then(data => {

                        // 1. SI HAY ERROR, LO MOSTRAMOS EN UNA ALERTA
                        if (data.error) {
                            alert("ERROR DETECTADO:\n\n" + data.mensaje + "\n\nEn línea: " + data.linea);
                            console.error("Detalle del error:", data);
                            return; // Detenemos todo
                        }

                        // 2. Si no hay error, actualizamos todo
                        animateValue("kpi-total", data.kpis.total);

                        // Usamos 'monto' porque así lo enviamos desde el controlador
                        if (document.getElementById('kpi-solicitado'))
                            document.getElementById('kpi-solicitado').innerText = data.kpis.solicitado;

                        animateValue("kpi-favorables", data.kpis.favorables);
                        animateValue("kpi-pendientes", data.kpis.pendientes);
                        console.log("Datos para barras:", data.graficos.ejesData);
                        if (chartEjesInstance) {
                            chartEjesInstance.data.labels = data.graficos.ejesLabels;
                            chartEjesInstance.data.datasets[0].data = data.graficos.ejesData;
                            chartEjesInstance.update();
                        }

                        if (chartEstadosInstance) {
                            chartEstadosInstance.data.labels = data.graficos.estadosLabels;
                            chartEstadosInstance.data.datasets[0].data = data.graficos.estadosData;
                            chartEstadosInstance.update();
                        }
                    })
                    .catch(error => {
                        console.error('Error de red:', error);
                        alert("Error de conexión. Revisa la consola.");
                    })
                    .finally(() => {
                        document.body.style.cursor = 'default';
                    });
            }

            // Función auxiliar para animar números
            function animateValue(id, end) {
                const obj = document.getElementById(id);
                if (!obj) return; // Protección por si el ID no existe

                // Si el valor viene como texto (ej: "$ 1.5M") no animamos, solo reemplazamos
                if (typeof end === 'string') {
                    obj.innerText = end;
                    return;
                }

                obj.innerText = end;
            }
        </script>
    @endpush
@endsection
