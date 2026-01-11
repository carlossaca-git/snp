<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Proyecto - {{ $proyecto->cup }}</title>
    <style>
        @page {
            /* Margen superior ajustado para recuperar espacio sin chocar */
            margin: 100px 50px 80px 50px;
        }

        body {
            font-family: 'sans-serif';
            margin: 0;
            padding: 0;
        }

        /* Encabezado compacto y más arriba */
        header {
            position: fixed;
            top: -85px;
            left: 0px;
            right: 0px;
            height: 75px;
            border-bottom: 2px solid #1a4a72;
            padding-bottom: 5px;
        }

        /* Tablas de Datos (Tus estilos originales) */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th {
            background-color: #f2f5f8;
            color: #1a4a72;
            text-align: left;
            padding: 8px;
            border: 1px solid #dee2e6;
            font-size: 11px;
            text-transform: uppercase;
        }

        .table td {
            padding: 8px;
            border: 1px solid #dee2e6;
            font-size: 12px;
        }

        /* Secciones (Tus estilos originales) */
        .section-title {
            background-color: #1a4a72;
            color: white;
            padding: 5px 10px;
            font-size: 13px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }

        .bg-success { background-color: #d1e7dd; color: #0f5132; }
        .bg-warning { background-color: #fff3cd; color: #664d03; }

        .signature-table {
            width: 100%;
            margin-top: 50px;
            border-collapse: collapse;
        }

        .signature-table td {
            text-align: center;
            border: none;
            padding: 20px;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 0 auto;
            padding-top: 5px;
        }

        .signature-title {
            font-size: 10px;
            text-transform: uppercase;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <header>
        <table style="width: 100%; border-collapse: collapse; border: none;">
            <tr>
                <td style="width: 25%; border: none; vertical-align: middle;">
                    @if ($logoBase64)
                        <img src="{{ $logoBase64 }}" style="height: 55px;">
                    @else
                        <div style="font-weight: bold; color: #1a4a72;">LOGO</div>
                    @endif
                </td>
                <td style="width: 75%; border: none; text-align: right; vertical-align: middle;">
                    <div style="font-size: 14px; font-weight: bold; color: #1a4a72; text-transform: uppercase;">
                        Sistema de Planificación e Inversión (SIPEIP)
                    </div>
                    <div style="font-size: 11px; color: #555;">
                        Ficha Técnica de Proyecto de Inversión
                    </div>
                    <div style="font-size: 10px; color: #888; font-weight: normal;">
                        Código CUP: {{ $proyecto->cup }}
                    </div>
                </td>
            </tr>
        </table>
    </header>

    <div class="section-title">1. INFORMACIÓN GENERAL</div>
    <table class="table">
        <tr>
            <th width="30%">Nombre del Proyecto</th>
            <td colspan="3">{{ $proyecto->nombre_proyecto }}</td>
        </tr>
        <tr>
            <th width="30%">Entidad Responsable</th>
            <td colspan="3">{{ $proyecto->organizacion->nom_organizacion }}</td>
        </tr>
        <tr>
            <th>Unidad Ejecutora</th>
            <td width="30%">{{ $proyecto->unidadEjecutora->nombre_unidad ?? 'N/A' }}</td>
            <th width="20%">Monto Inversión</th>
            <td>${{ number_format($proyecto->monto_total_inversion, 2) }}</td>
        </tr>
        <tr>
            <th>Estado Dictamen</th>
            <td>
                <span class="badge {{ $proyecto->estado_dictamen == 'FAVORABLE' ? 'bg-success' : 'bg-warning' }}">
                    {{ $proyecto->estado_dictamen ?? 'PENDIENTE' }}
                </span>
            </td>
            <th>Fecha Registro</th>
            <td>{{ $proyecto->created_at->format('d/m/Y') }}</td>
        </tr>
    </table>

    <div class="section-title">2. PROGRAMACIÓN FINANCIERA</div>
    <table class="table" style="margin-top: 5px;">
        <thead>
            <tr style="background-color: #f2f5f8;">
                <th>Periodo / Año</th>
                <th style="text-align: right;">Monto Programado</th>
                <th style="text-align: center;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($proyecto->financiamientos as $item)
                <tr>
                    <td>{{ $item->anio }}</td>
                    <td style="text-align: right;">$ {{ number_format($item->monto, 2) }}</td>
                    <td style="text-align: center;">{{ $item->estado ?? 'Pendiente' }}</td>
                </tr>
            @empty
                <tr><td colspan="3" style="text-align: center;">No se ha registrado programación financiera.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td style="text-align: right;">TOTAL:</td>
                <td style="text-align: right;">$ {{ number_format($proyecto->monto_total_inversion, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">3. ALINEACIÓN ESTRATÉGICA (PND)</div>
    <table class="table">
        <tr>
            <th width="30%">Eje Estratégico</th>
            <td>{{ $proyecto->objetivo->eje->nombre_eje ?? 'No definido' }}</td>
        </tr>
        <tr>
            <th>Objetivo Nacional</th>
            <td>{{ $proyecto->objetivo->descripcion_objetivo ?? 'No definido' }}</td>
        </tr>
    </table>

    <div class="section-title">4. ARCHIVOS Y DOCUMENTOS ADJUNTOS</div>
    <table class="table">
        <thead>
            <tr>
                <th>Tipo de Documento</th>
                <th>Nombre del Archivo</th>
                <th>Fecha de Carga</th>
            </tr>
        </thead>
        <tbody>
            @forelse($proyecto->documentos as $doc)
                <tr>
                    <td>{{ $doc->tipo_documento }}</td>
                    <td>{{ $doc->nombre_archivo }}</td>
                    <td>{{ $doc->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="3" style="text-align:center;">No existen documentos registrados.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">5. DIAGNÓSTICO Y JUSTIFICACIÓN</div>
    <div style="padding: 10px; border: 1px solid #dee2e6; font-size: 12px; text-align: justify; margin-bottom: 20px;">
        {{ $proyecto->descripcion_diagnostico ?? 'No se registra diagnóstico.' }}
    </div>

    <div class="section-title">6. UBICACIÓN GEOGRÁFICA</div>
    <table class="table">
        <tr>
            <th style="background: #f8f9fa;">Provincia</th>
            <th style="background: #f8f9fa;">Cantón</th>
            <th style="background: #f8f9fa;">Parroquia</th>
        </tr>
        <tr>
            <td>{{ $proyecto->localizacion->provincia ?? 'N/A' }}</td>
            <td>{{ $proyecto->localizacion->canton ?? 'N/A' }}</td>
            <td>{{ $proyecto->localizacion->parroquia ?? 'N/A' }}</td>
        </tr>
    </table>

    <div style="page-break-inside: avoid;">
        <table class="signature-table">
            <tr>
                <td width="40%">
                    <div class="signature-line"></div>
                    <div style="font-weight: bold; font-size: 12px;">{{ auth()->user()->name ?? 'Responsable' }}</div>
                    <div class="signature-title">Responsable de Unidad Ejecutora</div>
                </td>
                <td width="20%">
                    @if ($qrCodeBase64)
                        <img src="data:image/png;base64,{{ $qrCodeBase64 }}" style="width: 80px; height: 80px;">
                        <div style="font-size: 7px; color: #999; margin-top: 5px;">VALIDACIÓN DIGITAL</div>
                    @endif
                </td>
                <td width="40%">
                    <div class="signature-line"></div>
                    <div style="font-weight: bold; font-size: 12px;">Director de Planificación</div>
                    <div class="signature-title">Firma y Sello</div>
                </td>
            </tr>
        </table>
    </div>

    <footer style="position: fixed; bottom: -30px; left: 0px; right: 0px; height: 50px; text-align: center; font-size: 10px; color: #777;">
        Generado por SIPEIP - {{ now()->format('d/m/Y H:i') }}
    </footer>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("sans-serif", "normal");
                $pdf->text(520, 820, "Pagina $PAGE_NUM de $PAGE_COUNT", $font, 9);
            ');
        }
    </script>
</body>
</html>
