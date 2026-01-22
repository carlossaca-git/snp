<!DOCTYPE html>
<html>
<head>
    <title>Ficha Técnica - {{ $indicador->nombre_indicador }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }

        /* Encabezado */
        .header { width: 100%; border-bottom: 2px solid #444; margin-bottom: 20px; padding-bottom: 10px; }
        .logo { width: 150px; } /* Ajusta si tienes logo */
        .titulo-doc { text-align: right; float: right; }
        .titulo-doc h2 { margin: 0; color: #444; }
        .titulo-doc p { margin: 0; color: #777; font-size: 10px; }

        /* Secciones */
        h3 { background-color: #f0f0f0; padding: 5px; border-left: 4px solid #0d6efd; margin-top: 20px; }

        /* Tablas de datos */
        .tabla-info { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .tabla-info td { padding: 5px; vertical-align: top; }
        .label { font-weight: bold; color: #555; width: 120px; }

        /* Tabla de historial con bordes */
        .tabla-bordes { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .tabla-bordes th, .tabla-bordes td { border: 1px solid #ddd; padding: 6px; text-align: center; }
        .tabla-bordes th { background-color: #f8f9fa; font-weight: bold; }
        .text-left { text-align: left !important; }

    </style>
</head>
<body>

    {{-- 1. ENCABEZADO --}}
   <table style="width: 100%; border-bottom: 2px solid #444; margin-bottom: 20px; padding-bottom: 10px;">
    <tr>
        <td style="width: 50%;">
            {{-- <img src="{{ public_path('img/logo.png') }}" style="width: 150px;"> --}}
            <span style="font-size: 10px; color: #777;">SISTEMA DE PLANIFICACIÓN</span>
        </td>
        <td style="width: 50%; text-align: right;">
            <h2 style="margin: 0; color: #444;">FICHA TÉCNICA</h2>
            <p style="margin: 0; color: #777; font-size: 10px;">Generado el: {{ date('d/m/Y H:i') }}</p>
        </td>
    </tr>
</table>

    {{-- 2. INFORMACIÓN GENERAL --}}
    <h3>1. Identificación del Indicador</h3>
    <table class="tabla-info">
        <tr>
            <td class="label">Nombre:</td>
            <td><strong>{{ $indicador->nombre_indicador }}</strong></td>
        </tr>
        <tr>
            <td class="label">Meta Vinculada:</td>
            <td>{{ $indicador->meta->codigo_meta ?? '' }} - {{ $indicador->meta->nombre_meta ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Unidad de Medida:</td>
            <td>{{ $indicador->unidad_medida }}</td>
        </tr>
        <tr>
            <td class="label">Frecuencia:</td>
            <td>{{ $indicador->frecuencia }}</td>
        </tr>
    </table>

    {{-- 3. DATOS CUANTITATIVOS --}}
    <h3>2. Parámetros de Medición</h3>
    <table class="tabla-info">
        <tr>
            <td style="width: 50%;">
                <span class="label">Línea Base ({{ $indicador->anio_linea_base }}):</span><br>
                <span style="font-size: 16px;">{{ number_format($indicador->linea_base, 2) }}</span>
            </td>
            <td style="width: 50%;">
                <span class="label">Meta Planificada:</span><br>
                <span style="font-size: 16px; color: #0d6efd;">{{ number_format($indicador->meta_final, 2) }}</span>
            </td>
        </tr>
    </table>

    <br>
    <div style="background: #fafafa; padding: 10px; border: 1px solid #eee;">
        <span class="label">Método de Cálculo (Fórmula):</span><br>
        <em>{{ $indicador->metodo_calculo }}</em>
    </div>

    {{-- 4. HISTORIAL DE AVANCES --}}
    <h3>3. Historial de Seguimiento</h3>
    <table class="tabla-bordes">
        <thead>
            <tr>
                <th>Fecha Reporte</th>
                <th>Valor Logrado</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($indicador->avances as $avance)
                <tr>
                    <td>{{ date('d/m/Y', strtotime($avance->fecha_reporte)) }}</td>
                    <td>{{ number_format($avance->valor_logrado, 2) }}</td>
                    <td class="text-left">{{ Str::limit($avance->observaciones, 80) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="color: #999; padding: 20px;">
                        No se han registrado reportes de avance.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- PIE DE PÁGINA --}}
    <div style="position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 5px;">
        Documento generado automáticamente por el Sistema de Planificación
    </div>

</body>
</html>
