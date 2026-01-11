<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Proyectos SIPEIP</title>
    <style>
        /* CONFIGURACIÓN GENERAL */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        /* ENCABEZADO */
        .header {
            width: 100%;
            border-bottom: 2px solid #1a4a72;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-table {
            width: 100%;
        }
        .logo {
            width: 150px; /* Ajusta según tu logo */
        }
        .company-info {
            text-align: right;
            color: #555;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #1a4a72;
            margin: 0;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 12px;
            margin-top: 5px;
        }

        /* RESUMEN EJECUTIVO */
        .summary {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .summary-table {
            width: 100%;
        }
        .kpi-box {
            text-align: center;
        }
        .kpi-value {
            font-size: 16px;
            font-weight: bold;
            color: #1a4a72;
            display: block;
        }
        .kpi-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #666;
        }

        /* TABLA PRINCIPAL */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .main-table th {
            background-color: #1a4a72;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        .main-table td {
            border-bottom: 1px solid #eee;
            padding: 8px;
            vertical-align: top;
        }
        /* Zebra Striping (Filas alternas) */
        .main-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* BADGES DE ESTADO (Simulados con CSS simple) */
        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        .badge-success { background-color: #d1e7dd; color: #0f5132; }
        .badge-warning { background-color: #fff3cd; color: #664d03; }
        .badge-danger  { background-color: #f8d7da; color: #842029; }
        .badge-secondary { background-color: #e2e3e5; color: #41464b; }

        /* PIE DE PÁGINA */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            border-top: 1px solid #ddd;
            padding-top: 5px;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
        .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>

    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 30%;">
                    <h2 style="margin:0; color:#1a4a72;">SIPEIP</h2>
                </td>
                <td class="company-info">
                    <h1 class="title">Reporte Ejecutivo de Proyectos</h1>
                    <div class="subtitle">Filtro Aplicado: {{ $rango }}</div>
                    <div class="subtitle">Generado: {{ $fecha }}</div>
                    <div class="subtitle">Usuario: {{ auth()->user()->name ?? 'Sistema' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="summary">
        <table class="summary-table">
            <tr>
                <td class="kpi-box" style="border-right: 1px solid #ddd;">
                    <span class="kpi-value">{{ count($proyectos) }}</span>
                    <span class="kpi-label">Proyectos Listados</span>
                </td>
                <td class="kpi-box" style="border-right: 1px solid #ddd;">
                    <span class="kpi-value">${{ number_format($proyectos->sum('monto_total_inversion'), 2) }}</span>
                    <span class="kpi-label">Inversión Total</span>
                </td>
                <td class="kpi-box">
                    <span class="kpi-value">{{ $proyectos->where('estado_dictamen', 'FAVORABLE')->count() }}</span>
                    <span class="kpi-label">Favorables</span>
                </td>
            </tr>
        </table>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 15%;">CUP</th>
                <th style="width: 40%;">Nombre del Proyecto</th>
                <th style="width: 20%;">Entidad / Eje</th>
                <th style="width: 15%; text-align: right;">Monto ($)</th>
                <th style="width: 10%; text-align: center;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proyectos as $p)
            <tr>
                <td><strong>{{ $p->cup }}</strong></td>
                <td>
                    {{ $p->nombre_proyecto }}
                    <br>
                    <small style="color: #666;">
                        Iniciado: {{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y') }}
                    </small>
                </td>
                <td>
                    <div style="font-weight: bold; font-size: 11px;">
                        {{ $p->organizacion->siglas ?? 'N/A' }}
                    </div>
                    <div style="font-size: 10px; color: #555; margin-top: 2px;">
                        {{ Str::limit($p->objetivoNacional->ejeEstrategico->nombre_eje ?? 'Sin Eje', 20) }}
                    </div>
                </td>
                <td style="text-align: right;">
                    {{ number_format($p->monto_total_inversion, 2) }}
                </td>
                <td style="text-align: center;">
                    @php
                        $estado = $p->estado_dictamen ?? 'PENDIENTE';
                        $clase = 'badge-secondary';
                        if($estado == 'FAVORABLE') $clase = 'badge-success';
                        elseif($estado == 'OBSERVADO') $clase = 'badge-warning';
                        elseif($estado == 'RECHAZADO') $clase = 'badge-danger';
                    @endphp
                    <span class="badge {{ $clase }}">{{ $estado }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Sistema SIPEIP - Reporte Generado Automáticamente | Página <span class="page-number"></span>
    </div>

</body>
</html>
