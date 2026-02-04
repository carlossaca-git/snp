@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark">
            <i class="fas fa-sitemap me-2 text-primary"></i> Matriz de Alineación: ODS vs Metas Nacionales
        </h4>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-print"></i> Imprimir Reporte
        </button>
    </div>
    {{-- TABLA DE ALINEACIÓN --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 align-middle">
                    <thead class="bg-light text-uppercase small fw-bold">
                        <tr>
                            <th style="width: 35%;" class="ps-4">Objetivo de Desarrollo Sostenible (ODS)</th>
                            <th style="width: 65%;">Metas Nacionales Vinculadas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($odsConMetas as $od)
                            <tr>
                                {{-- COLUMNA IZQUIERDA: ODS --}}
                                <td class="bg-light ps-4">
                                    <div class="d-flex align-items-center">
                                        <span class="badge me-2" style="background-color: {{ $od->color_hex }}; font-size: 1rem; width: 45px;">
                                            {{ $od->codigo }}
                                        </span>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $od->nombre }}</div>
                                            <small class="text-muted">{{ Str::limit($od->pilar, 30) }}</small>
                                        </div>
                                    </div>
                                </td>

                                {{-- COLUMNA DERECHA: LISTA DE METAS --}}
                                <td class="p-0">
                                    <table class="table table-hover mb-0 w-100">
                                        @foreach($od->metasNacionales as $meta)
                                            <tr>
                                                <td class="border-0 border-bottom px-3 py-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span class="text-xs fw-bold text-muted">{{ $meta->codigo_meta }}</span>
                                                            <a href="{{ route('catalogos.metas.show', $meta->id_meta_nacional) }}" class="text-dark text-decoration-none fw-bold d-block">
                                                                {{ $meta->nombre_meta }}
                                                            </a>
                                                        </div>
                                                        <span class="badge bg-light text-dark border">
                                                            Avance: {{ number_format($meta->avance_actual, 1) }}%
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ALERTA DE METAS HUÉRFANAS (SIN ODS) --}}
    @if($metasHuerfanas->count() > 0)
        <div class="alert alert-danger shadow-sm">
            <h6 class="fw-bold"><i class="fas fa-exclamation-triangle me-2"></i> Metas Nacionales sin vinculación a ODS</h6>
            <p class="small mb-2">Las siguientes metas existen en el sistema pero no contribuyen a ningún objetivo internacional:</p>
            <ul class="mb-0 small">
                @foreach($metasHuerfanas as $huerfana)
                    <li>
                        <strong>{{ $huerfana->codigo_meta }}</strong>: {{ $huerfana->nombre_meta }}
                        <a href="{{ route('catalogos.metas.show', $huerfana->id_meta_nacional) }}" class="alert-link ms-2">[Corregir]</a>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="alert alert-success shadow-sm">
            <i class="fas fa-check-circle me-2"></i> Excelente: Todas las Metas Nacionales están alineadas a al menos un ODS.
        </div>
    @endif

</div>
@endsection
