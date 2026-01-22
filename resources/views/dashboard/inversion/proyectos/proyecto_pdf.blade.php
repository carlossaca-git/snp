<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Proyecto - {{ $proyecto->cup }}</title>
    <style>
        @page {
            margin: 100px 50px 80px 50px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
        }

        /* Encabezado fijo */
        header {
            position: fixed;
            top: -85px;
            left: 0px;
            right: 0px;
            height: 75px;
            border-bottom: 2px solid #1a4a72;
        }

        /* Pie de página fijo */
        footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 30px;
            text-align: center;
            font-size: 9px;
            color: #777;
            border-top: 1px solid #dee2e6;
            padding-top: 5px;
        }

        /* Estilos de Tablas mejorados */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .table th {
            background-color: #f2f5f8;
            color: #1a4a72;
            text-align: left;
            padding: 6px 8px;
            border: 1px solid #dee2e6;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .table td {
            padding: 6px 8px;
            border: 1px solid #dee2e6;
            font-size: 11px;
            vertical-align: top;
        }

        /* repetir cabeceras en saltos de página */
        thead { display: table-header-group; }
        tfoot { display: table-row-group; }
        tr { page-break-inside: avoid; }

        /* Títulos de sección */
        .section-title {
            background-color: #1a4a72;
            color: white;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .badge {
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
        }
        .bg-success { background-color: #d1e7dd; color: #0f5132; }
        .bg-warning { background-color: #fff3cd; color: #664d03; }
        .bg-danger { background-color: #f8d7da; color: #721c24; }

        /* Firmas */
        .signature-table {
            width: 100%;
            margin-top: 40px;
            border: none;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 85%;
            margin: 0 auto 5px auto;
        }
    </style>
</head>

<body>
    <header>
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 20%; border: none; vertical-align: middle;">
                    @if (!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" style="max-height: 55px; max-width: 150px;">
                    @else
                        <strong style="color: #1a4a72; font-size: 18px;">LOGO</strong>
                    @endif
                </td>
                <td style="width: 80%; border: none; text-align: right; vertical-align: middle;">
                    <div style="font-size: 14px; font-weight: bold; color: #1a4a72;">
                        SISTEMA DE PLANIFICACIÓN E INVERSIÓN (SIPEIP)
                    </div>
                    <div style="font-size: 11px; font-weight: bold; color: #444;">
                        FICHA TÉCNICA DE PROYECTO
                    </div>
                    <div style="font-size: 10px; color: #666;">
                        CUP: <strong>{{ $proyecto->cup }}</strong> | Fecha: {{ now()->format('d/m/Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        Usuario: {{ auth()->user()->name ?? 'Sistema' }} | Generado el: {{ now()->format('d/m/Y H:i:s') }}
    </footer>

    <div class="section-title">1. Información General</div>
    <table class="table">
        <tr>
            <th width="25%">Nombre del Proyecto</th>
            <td colspan="3"><strong>{{ $proyecto->nombre_proyecto }}</strong></td>
        </tr>
        <tr>
            <th>Entidad Responsable</th>
            <td colspan="3">{{ $proyecto->organizacion->nom_organizacion ?? 'No definida' }}</td>
        </tr>
        <tr>
            <th>Unidad Ejecutora</th>
            <td>{{ $proyecto->unidadEjecutora->nombre_unidad ?? 'N/A' }}</td>
            <th width="20%">Plazo Ejecución</th>
            <td width="20%">{{ $proyecto->plazo_ejecucion ?? 'N/A' }} Meses</td> </tr>
        <tr>
            <th>Monto Inversión</th>
            <td><strong>$ {{ number_format($proyecto->monto_total_inversion, 2) }}</strong></td>
            <th>Beneficiarios</th>
            <td>{{ $proyecto->numero_beneficiarios ?? '0' }} Hab.</td> </tr>
        <tr>
            <th>Estado Dictamen</th>
            <td>
                <span class="badge {{ $proyecto->estado_dictamen == 'FAVORABLE' ? 'bg-success' : ($proyecto->estado_dictamen == 'RECHAZADO' ? 'bg-danger' : 'bg-warning') }}">
                    {{ $proyecto->estado_dictamen ?? 'PENDIENTE' }}
                </span>
            </td>
            <th>Fecha Registro</th>
            <td>{{ $proyecto->created_at->format('d/m/Y') }}</td>
        </tr>
    </table>

    <div class="section-title">2. Diagnóstico y Problema Central</div>
    <div style="border: 1px solid #dee2e6; padding: 10px; font-size: 11px; text-align: justify; margin-bottom: 15px; background-color: #fff;">
        <strong>Problema / Diagnóstico:</strong><br>
        {{ $proyecto->descripcion_diagnostico ?? 'No se ha registrado información de diagnóstico.' }}

        @if(!empty($proyecto->objetivo_especifico))
            <br><br><strong>Objetivo Específico:</strong><br>
            {{ $proyecto->objetivo_especifico }}
        @endif
    </div>

    <div class="section-title">3. Alineación Estratégica</div>
    <table class="table">
        <tr>
            <th width="25%">Eje Estratégico</th>
            <td>{{ $proyecto->objetivo->eje->nombre_eje ?? 'No definido' }}</td>
        </tr>
        <tr>
            <th>Objetivo Nacional</th>
            <td>{{ $proyecto->objetivo->descripcion_objetivo ?? 'No definido' }}</td>
        </tr>
    </table>

    <div class="section-title">4. Programación Financiera Plurianual</div>
    <table class="table">
        <thead>
            <tr>
                <th width="15%">Año</th>
                <th>Fuente de Financiamiento</th> <th width="20%" style="text-align: right;">Monto</th>
                <th width="15%" style="text-align: center;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($proyecto->financiamientos as $item)
                <tr>
                    <td>{{ $item->anio }}</td>
                    <td>{{ $item->fuente_financiamiento ?? 'Recursos Fiscales' }}</td> <td style="text-align: right;">$ {{ number_format($item->monto, 2) }}</td>
                    <td style="text-align: center;">
                         <span class="badge bg-warning">{{ $item->estado ?? 'Proyectado' }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align: center; color: #777;">Sin programación registrada.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #e9ecef;">
                <td colspan="2" style="text-align: right; font-weight: bold;">TOTAL INVERSIÓN:</td>
                <td style="text-align: right; font-weight: bold;">$ {{ number_format($proyecto->monto_total_inversion, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">5. Ubicación Geográfica</div>
    <table class="table">
        <thead>
            <tr>
                <th>Provincia</th>
                <th>Cantón</th>
                <th>Parroquia</th>
                <th>Coordenadas / Dirección</th> </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $proyecto->localizacion->provincia ?? 'N/A' }}</td>
                <td>{{ $proyecto->localizacion->canton ?? 'N/A' }}</td>
                <td>{{ $proyecto->localizacion->parroquia ?? 'N/A' }}</td>
                <td>{{ $proyecto->localizacion->direccion ?? 'S/N' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">6. Documentación Habilitante</div>
    <table class="table">
        <thead>
            <tr>
                <th>Documento</th>
                <th>Nombre Archivo</th>
                <th width="15%">Fecha</th>
            </tr>
        </thead>
        <tbody>
            @forelse($proyecto->documentos as $doc)
                <tr>
                    <td>{{ $doc->tipo_documento }}</td>
                    <td style="font-size: 10px;">{{ Str::limit($doc->nombre_archivo, 40) }}</td> <td>{{ $doc->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="3" style="text-align:center; color: #999;">No hay documentos adjuntos.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="page-break-inside: avoid;">
        <table class="signature-table">
            <tr>
                <td width="40%">
                    <div class="signature-line"></div>
                    <div style="font-weight: bold;">{{ auth()->user()->name ?? 'Funcionario Responsable' }}</div>
                    <div class="signature-title">Elaborado Por</div>
                </td>
                <td width="20%">
                    @if (!empty($qrCodeBase64))
                        <img src="data:image/png;base64,{{ $qrCodeBase64 }}" style="width: 70px; height: 70px;">
                    @endif
                </td>
                <td width="40%">
                    <div class="signature-line"></div>
                    <div style="font-weight: bold;">DIRECTOR DE PLANIFICACIÓN</div>
                    <div class="signature-title">Aprobado Por</div>
                </td>
            </tr>
        </table>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $font = $fontMetrics->get_font("Helvetica", "normal");
            $size = 9;
            $color = array(0.5, 0.5, 0.5);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->page_text(520, 820, $text, $font, $size, $color, $word_space, $char_space, $angle);
        }
    </script>
</body>
</html>
