@props(['titulo', 'subtitulo' => null, 'rutas' => null])

@php
    $rutaDashboard = route('dashboard');
    $segmentosOcultos = ['catalogos', 'admin', 'configuracion','institucional'];

    // Traducciones
    $traducciones = [
        'Indicadores' => 'Listado Indicadores',
        'Kardex'      => 'Kardex de Seguimiento',
        'Create'      => 'Nuevo Registro',
        'Edit'        => 'Modificar',
        'Show'        => 'Detalle'
    ];

    if ($rutas === null) {
        $rutas = [];
        $acumulado = '';
        $segmentos = Request::segments(); // Array con partes de la URL
        $totalSegmentos = count($segmentos);

        foreach ($segmentos as $index => $segmento) {
            //  Construir URL interna (siempre)
            $acumulado .= '/' . $segmento;

            //  Filtros de Exclusión
            // Si está en la lista negra (ej: catalogos), saltamos
            if (in_array($segmento, $segmentosOcultos)) continue;

            //  Lógica para NÚMEROS (IDs)
            if (is_numeric($segmento)) {
                // Si el número es el ÚLTIMO elemento de la URL (ej: /indicadores/15)
                // Lo mostramos como "Detalle" para que no desaparezca la miga.
                if ($index === $totalSegmentos - 1) {
                    $rutas['Detalle / Ficha'] = null; // null = Texto sin enlace
                }
                // Si el número está en el medio (ej: /indicadores/15/kardex),
                // lo ignoramos visualmente pero la URL acumulada ya lo tiene.
                continue;
            }

            //  Procesar Texto Normal
            $textoRaw = ucwords(str_replace(['-', '_'], ' ', $segmento));
            $textoFinal = $traducciones[$textoRaw] ?? $textoRaw;

            // Guardamos en el array
            $rutas[$textoFinal] = url($acumulado);
        }
    }
@endphp

{{-- El HTML de abajo se mantiene igual --}}
<div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-3">
    <div>
        <nav aria-label="breadcrumb" class="mb-1">
            <ol class="breadcrumb mb-0" style="font-size: 0.8rem; background: transparent; padding: 0;">

                <li class="breadcrumb-item">
                    <a href="{{ $rutaDashboard }}" class="text-muted text-decoration-none">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>

                @foreach($rutas as $texto => $link)
                    {{-- Si es el último, es TEXTO ACTIVO (Gris/Azul) --}}
                    @if($loop->last)
                        <li class="breadcrumb-item active text-primary" aria-current="page">{{ $texto }}</li>

                    {{-- Si el link es null, es TEXTO INTERMEDIO (Gris) --}}
                    @elseif($link === null)
                         <li class="breadcrumb-item text-muted">{{ $texto }}</li>

                    {{-- Si no, es ENLACE (Clicable) --}}
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ $link }}" class="text-muted text-decoration-none hover-primary">{{ $texto }}</a>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>

        <h1 class="h2 text-dark fw-bolder mb-0" style="letter-spacing: -0.5px;">
            {{ $titulo }}
        </h1>
       @if($subtitulo)
            <p class="text-muted mb-0" style="font-size: 0.95rem;">{{ $subtitulo }}</p>
        @endif
    </div>

    <div>
        {{ $slot }}
    </div>
</div>


