<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte Integral de Gestión</title>
    <style>
        @page {
            margin: 1cm 1.5cm;
            font-family: 'Helvetica', 'Arial', sans-serif;
        }

        body {
            font-size: 11px;
            color: #444;
            line-height: 1.3;
        }

        /* --- CLASES UTILITARIAS --- */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .fw-bold {
            font-weight: bold;
        }

        .w-100 {
            width: 100%;
        }

        .mb-2 {
            margin-bottom: 10px;
        }

        .mt-2 {
            margin-top: 10px;
        }

        /* --- ENCABEZADO --- */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #1a4a72;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .titulo-doc h2 {
            margin: 0;
            color: #1a4a72;
            font-size: 16px;
        }

        .titulo-doc p {
            margin: 2px 0 0 0;
            color: #666;
            font-size: 10px;
        }

        .info-periodo {
            font-size: 10px;
            color: #333;
        }

        /* --- KPIS (Diseño en Tabla para estabilidad en PDF) --- */
        .tabla-kpis {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px 0;
            /* Espacio entre celdas */
            margin-bottom: 20px;
        }

        .kpi-cell {
            background-color: #fff;
            border: 1px solid #e3e6f0;
            border-top: 3px solid #ccc;
            padding: 10px;
            text-align: center;
            width: 25%;
            border-radius: 3px;
        }

        .kpi-label {
            display: block;
            font-size: 9px;
            color: #858796;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .kpi-value {
            display: block;
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        /* Colores KPI */
        .border-primary {
            border-top-color: #4e73df !important;
        }

        .border-success {
            border-top-color: #1cc88a !important;
        }

        .border-info {
            border-top-color: #36b9cc !important;
        }

        .border-warning {
            border-top-color: #f6c23e !important;
        }

        /* --- TABLAS DE DATOS --- */
        h3 {
            background-color: #eaecf4;
            color: #333;
            padding: 8px;
            border-left: 5px solid #1a4a72;
            font-size: 12px;
            margin-top: 25px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .tabla-datos {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .tabla-datos thead th {
            background-color: #f8f9fa;
            color: #5a5c69;
            border: 1px solid #e3e6f0;
            padding: 8px;
            text-transform: uppercase;
            font-size: 9px;
        }

        .tabla-datos tbody td {
            border: 1px solid #e3e6f0;
            padding: 6px 8px;
            vertical-align: middle;
        }

        /* Filas alternas (Zebra) */
        .tabla-datos tbody tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        /* --- SECCIÓN DETALLADA (Ejes -> Obj) --- */
        .eje-titulo {
            background-color: #1a4a72;
            color: white;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 11px;
            margin-top: 15px;
        }

        .obj-titulo {
            background-color: #f1f3f9;
            color: #444;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 10px;
            border-bottom: 1px solid #ddd;
        }

        /* --- BADGES Y ESTADOS --- */
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
        }

        /* --- PIE DE PÁGINA --- */
        footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 30px;
            border-top: 1px solid #ddd;
            color: #999;
            font-size: 9px;
            line-height: 30px;
        }

        /* Evitar cortes feos en impresión */
        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

    <footer>
        <table width="100%">
            <tr>
                <td width="70%">Reporte Oficial SIPEIP | Generado: {{ date('d/m/Y H:i') }}</td>
                <td width="30%" align="right">
                    <script type="text/php">
                        if (isset($pdf)) {
                            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
                            $size = 9;
                            $font = $fontMetrics->getFont("Helvetica");
                            $width = $fontMetrics->get_text_width($text, $font, $size);
                            $pdf->page_text($pdf->get_width() - $width - 30, $pdf->get_height() - 30, $text, $font, $size, array(0.6, 0.6, 0.6));
                        }
                    </script>
                </td>
            </tr>
        </table>
    </footer>

    <table class="header-table">
        <tr>
            <td valign="middle">
                <div class="titulo-doc">
                    <h2>REPORTE INTEGRAL DE GESTIÓN</h2>
                    <p>PLANIFICACIÓN E INVERSIÓN PÚBLICA</p>
                </div>
            </td>
            <td valign="middle" class="text-right">
                <div class="info-periodo">
                    <span class="fw-bold" style="color: #1a4a72; font-size: 11px;">
                        PERIODO: {{ strtoupper($rango) }}
                    </span><br>
                    Del {{ $inicio->format('d/m/Y') }} al {{ $fin->format('d/m/Y') }}
                </div>
            </td>
        </tr>
    </table>

    <table class="tabla-kpis">
        <tr>
            <td class="kpi-cell border-primary">
                <span class="kpi-label">Proyectos Totales</span>
                <span class="kpi-value">{{ $kpis['total'] }}</span>
            </td>
            <td class="kpi-cell border-success">
                <span class="kpi-label">Monto Inversión</span>
                <span class="kpi-value">${{ number_format($kpis['monto'] / 1000000, 2) }}M</span>
            </td>
            <td class="kpi-cell border-info">
                <span class="kpi-label">Cumplimiento</span>
                <span class="kpi-value">{{ $promedioGlobal }}%</span>
            </td>
            <td class="kpi-cell border-warning">
                <span class="kpi-label">Dictamen Favorable</span>
                <span class="kpi-value">{{ $kpis['favorables'] }}</span>
            </td>
        </tr>
    </table>

    <h3>1. Resumen de Inversión por Eje</h3>
    <table class="tabla-datos">
        <thead>
            <tr>
                <th align="left">Eje Estratégico</th>
                <th width="120" class="text-right">Monto Total</th>
                <th width="80" class="text-center">% Part.</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datosAgrupados as $nombreEje => $grupoObjetivos)
                @php
                    // usamos flatten() para aplanar la lista y sumar el total de todo el eje.
                    $montoEje = $grupoObjetivos->flatten()->sum('total');

                    // Calculamos el porcentaje
                    $porcentaje = $kpis['monto'] > 0 ? ($montoEje / $kpis['monto']) * 100 : 0;
                @endphp
                <tr>
                    <td>{{ $nombreEje }}</td>
                    <td class="text-right fw-bold">$ {{ number_format($montoEje, 2) }}</td>
                    <td class="text-center">
                        {{ number_format($porcentaje, 1) }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="page-break-inside: avoid; margin-top: 25px;">
        <h3 style="margin-top: 0;">2. Alineación PND y ODS (Detallado)</h3>
    </div>

    @foreach ($datosAgrupados as $nombreEje => $objetivos)
        <div class="no-break">
            <div class="eje-titulo">{{ strtoupper($nombreEje) }}</div>

            @foreach ($objetivos as $nombreObjNacional => $items)
                <div class="obj-titulo">OBJETIVO: {{ $nombreObjNacional }}</div>

                <table class="tabla-datos mb-2">
                    <thead>
                        <tr>
                            <th width="10%">Cód</th>
                            <th width="45%">Meta Nacional</th>
                            <th width="30%">ODS Vinculado</th>
                            <th width="15%" class="text-right">Inversión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td class="text-center">{{ $item->codigo_meta }}</td>
                                <td>{{ $item->nombre_meta }}</td>
                                <td>
                                    @if ($item->nombre_ods)
                                        <span style="color: #4e73df; font-weight: bold;">ODS
                                            {{ $item->codigo_ods }}:</span> {{ $item->nombre_ods }}
                                    @else
                                        <span style="color: #aaa; font-style: italic;">-- Sin vincular --</span>
                                    @endif
                                </td>
                                <td class="text-right">$ {{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        </div>
    @endforeach

    <h3 style="page-break-before:">3. Estado de Dictámenes</h3>
    <table class="tabla-datos" style="width: 60%;">
        <thead>
            <tr>
                <th align="left">Estado Actual</th>
                <th width="80" class="text-center">Proyectos</th>
            </tr>
        </thead>
        <tbody>
            @forelse($estadosDictamen as $estado)
                <tr>
                    <td>{{ $estado->estado_dictamen ?? 'SIN DICTAMEN' }}</td>
                    <td class="text-center fw-bold">{{ $estado->total }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center">No hay registros</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>4. Listado Detallado de Proyectos</h3>
    <table class="tabla-datos">
        <thead>
            <tr>
                <th align="left">Proyecto</th>
                <th width="20%">Organización</th>
                <th width="15%" class="text-right">Monto</th>
                <th width="15%" class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($proyectos as $proy)
                @php
                    $badgeColor = '#858796'; // Default gris
                    if ($proy->estado_dictamen == 'FAVORABLE') {
                        $badgeColor = '#1cc88a';
                    } elseif ($proy->estado_dictamen == 'NO FAVORABLE') {
                        $badgeColor = '#e74a3b';
                    } elseif ($proy->estado_dictamen == 'PENDIENTE') {
                        $badgeColor = '#f6c23e';
                    } elseif ($proy->estado_dictamen == 'OBSERVADO') {
                        $badgeColor = '#f6c23e';
                    }
                @endphp
                <tr>
                    <td>
                        <span style="display:block; font-weight:bold; color:#333;">{{ $proy->cup }}</span>
                        <span style="color:#555;">{{ $proy->nombre_proyecto }}</span>
                    </td>
                    <td>{{ $proy->organizacion->nom_organizacion ?? 'N/A' }}</td>
                    <td class="text-right">$ {{ number_format($proy->monto_total_inversion, 2) }}</td>
                    <td class="text-center">
                        <span class="badge" style="background-color: {{ $badgeColor }};">
                            {{ $proy->estado_dictamen ?? 'PENDIENTE' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding: 20px;">
                        No se encontraron proyectos en el periodo seleccionado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
