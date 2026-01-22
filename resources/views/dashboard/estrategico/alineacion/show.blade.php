@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Ficha de Alineación Estratégica" subtitulo="Vinculación PND - Institucional">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('estrategico.alineacion.gestionar', $alineacion->organizacion_id) }}"
                class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver al Listado
            </a>
        </div>
    </x-layouts.header_content>
    <div class="container-fluid">
        {{-- RESUMEN DE IMPACTO  --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Impacto de esta Alineación (Proyectos del Objetivo)
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $alineacion->objetivoEstrategico->proyectos->count() }} Proyectos
                                    <span class="mx-2 text-gray-300">|</span>
                                    ${{ number_format($alineacion->objetivoEstrategico->proyectos->sum('monto_total_inversion'), 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row align-items-center">

            {{--  NIVEL INSTITUCIONAL (EL APORTE) --}}
            <div class="col-lg-5 mb-4">
                <div class="card shadow border-top-primary h-100">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-building me-2"></i>Nivel Institucional (Aporte)
                        </h6>
                    </div>
                    <div class="card-body text-center p-4">

                        <div class="mb-4">
                            <img src="{{ asset('img/logo_institucion.png') }}" alt="Logo"
                                style="height: 50px; opacity: 0.5;" onerror="this.style.display='none'">
                            <h5 class="font-weight-bold text-dark mt-2">{{ $alineacion->organizacion->nombre }}</h5>
                        </div>

                        <div class="card bg-gray-100 border-0 mb-3">
                            <div class="card-body text-start">
                                <small class="text-uppercase text-muted fw-bold d-block mb-1">Objetivo Estratégico</small>
                                <div class="d-flex align-items-start">
                                    <span
                                        class="badge bg-primary mt-1 me-2">{{ $alineacion->objetivoEstrategico->codigo }}</span>
                                    <span class="text-dark fw-bold lead" style="font-size: 1.1rem;">
                                        {{ $alineacion->objetivoEstrategico->nombre }}
                                    </span>
                                </div>
                                <p class="small text-muted mt-2 mb-0 text-justify">
                                    {{ $alineacion->objetivoEstrategico->descripcion ?? 'Sin descripción.' }}
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{--  CONECTOR VISUAL --}}
            <div class="col-lg-2 text-center mb-4 d-none d-lg-block">
                <div class="d-flex flex-column align-items-center justify-content-center">
                    <span class="text-xs text-uppercase font-weight-bold text-muted mb-2">Contribuye a</span>
                    <i class="fas fa-arrow-circle-right fa-3x text-gray-300"></i>
                </div>
            </div>

            {{-- NIVEL NACIONAL --}}
            <div class="col-lg-5 mb-4">
                <div class="card shadow border-top-success h-100">
                    <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-flag me-2"></i>Plan Nacional de Desarrollo
                        </h6>
                        <span class="badge bg-success">PND Vigente</span>
                    </div>
                    <div class="card-body">
                        <div class="timeline-steps">
                            {{--  EJE --}}
                            @if ($alineacion->metaNacional->objetivoNacional && $alineacion->metaNacional->objetivoNacional->eje)
                                <div class="mb-3">
                                    <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Eje
                                        Estratégico</small>
                                    <div class="d-flex align-items-center text-dark">
                                        <i class="fas fa-layer-group text-gray-400 me-2"></i>
                                        {{ $alineacion->metaNacional->objetivoNacional->eje->nombre_eje }}
                                    </div>
                                </div>
                            @endif
                            {{-- OBJETIVO NACIONAL --}}
                            @if ($alineacion->metaNacional->objetivoNacional)
                                <div class="mb-3 ps-3 border-start border-3 border-secondary">
                                    <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Objetivo
                                        Nacional</small>
                                    <div class="fw-bold text-secondary">
                                        {{ $alineacion->metaNacional->objetivoNacional->codigo_objetivo }} -
                                        {{ Str::limit($alineacion->metaNacional->objetivoNacional->descripcion_objetivo, 60) }}
                                    </div>
                                </div>
                            @endif

                            {{-- META NACIONAL  --}}
                            <div class="mb-4 ps-3 border-start border-3 border-success bg-light py-2 pe-2 rounded-end">
                                <small class="text-uppercase text-success fw-bold" style="font-size: 0.7rem;">Meta Nacional
                                    Vinculada</small>
                                <div class="fw-bold text-dark">
                                    <i class="fas fa-bullseye text-danger me-1"></i>
                                    {{ $alineacion->metaNacional->codigo_meta }}
                                </div>
                                <div class="text-dark small mt-1">
                                    {{ $alineacion->metaNacional->nombre_meta }}
                                </div>
                            </div>

                        </div>

                        <hr class="my-3">

                        {{-- ODS --}}
                        <div class="text-center">
                            <small class="text-uppercase text-muted fw-bold mb-2 d-block">Contribución a ODS</small>
                            <div class="d-flex justify-content-center flex-wrap gap-2">
                                @forelse($alineacion->metaNacional->ods as $ods)
                                    <div class="text-center" title="{{ $ods->nombre }}" data-bs-toggle="tooltip">
                                        <div class="shadow-sm d-flex align-items-center justify-content-center rounded"
                                            style="background-color: {{ $ods->color_hex }}; width: 60px; height: 60px;">
                                            <span class="text-white fw-bold h4 mb-0" style="font-size: 0.7rem;">{{ $ods->codigo }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <span class="badge bg-light text-muted border">Sin ODS Directo</span>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        {{-- AUDITORÍA --}}
        <div class="text-center text-muted small mt-2">
            Registro de alineación creado el {{ $alineacion->created_at->format('d/m/Y') }}
            por {{ $alineacion->usuario->name ?? 'Sistema' }}.
        </div>

    </div>
@endsection
