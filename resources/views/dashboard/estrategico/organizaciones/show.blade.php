@extends('layouts.app')

@section('content')
    {{-- ========================================================== --}}
    {{-- SECCIÓN 1: PERFIL COMPLETO DE LA ORGANIZACIÓN (HERO HEADER) --}}
    {{-- ========================================================== --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-dark text-white overflow-hidden">
                <div class="position-absolute end-0 top-0 p-3 opacity-10">
                    <i class="fas fa-building fa-10x"></i>
                </div>
                <div class="card-body p-4 position-relative">
                    {{-- Título y Botones --}}
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle me-4 shadow d-flex align-items-center justify-content-center overflow-hidden position-relative"
                                style="width: 80px; height: 80px;">

                                @if ($organizacion->logo)
                                    <img src="{{ asset('storage/' . $organizacion->logo) }}" alt="Logo Institucional"
                                        class="w-100 h-100" style="object-fit: cover;">
                                @else
                                    <span class="fw-bold fs-3 text-white">
                                        {{ substr($organizacion->nom_organizacion, 0, 1) }}
                                    </span>
                                @endif
                            </div>

                            <div>
                                <h2 class="mb-1 fw-bold">{{ $organizacion->nom_organizacion }}</h2>

                                <div class="mb-1">
                                    <span class="badge bg-info text-dark">
                                        <i class="fas fa-id-badge me-1"></i> ID: {{ $organizacion->id }}
                                    </span>

                                    @if (isset($organizacion->siglas))
                                        <span class="badge border border-light ms-2">{{ $organizacion->siglas }}</span>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center">
                                    <strong class="me-2 badge bg-info text-dark">Estado:</strong>
                                    @if ($organizacion->estado == 'A' || $organizacion->estado == 1)
                                        <span class="badge bg-success ">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column align-items-start gap-3">

                            <a href="{{ route('institucional.organizaciones.index') }}"
                                class="btn btn-outline-light rounded-pill px-4 hover-scale shadow-sm">
                                <i class="fas fa-arrow-left me-2"></i>Regresar
                            </a>

                            <button type="button" class="btn btn-outline-light rounded-pill px-4 hover-scale shadow-sm"
                                data-bs-toggle="modal" data-bs-target="#modalEditarPerfil">
                                <i class="fas fa-cog me-2"></i>Editar Perfil
                            </button>

                            <form
                                action="{{ route('institucional.organizaciones.destroy', $organizacion->id_organizacion) }}"
                                method="POST"
                                onsubmit="return confirm('⚠️ ¿Estás seguro?\n\nSe eliminará la organización y todas sus alineaciones estratégicas.\nEsta acción no se puede deshacer.');">
                                @csrf
                                @method('DELETE')

                                {{-- Usamos las mismas clases: btn-outline-light, rounded-pill, etc. --}}
                                <button type="submit"
                                    class="btn btn-outline-light rounded-pill px-4 hover-scale shadow-sm">
                                    <i class="fas fa-trash-alt me-2"></i>Eliminar
                                </button>
                            </form>

                        </div>

                    </div>

                    {{-- Separador estético --}}
                    <hr class="border-secondary opacity-50 my-4">

                    {{-- Fila Inferior: Detalles del Perfil (Sector, Misión, Etc) --}}
                    <div class="row g-4">
                        {{-- SECCIÓN COLAPSABLE DE INFORMACIÓN INSTITUCIONAL --}}

                        <div class="accordion mb-4 shadow-sm" id="accordionInfo">
                            <div class="accordion-item border-0">

                                <h2 class="accordion-header" id="headingInfo">
                                    <button class="accordion-button collapsed bg-white text-dark fw-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseInfo" aria-expanded="false"
                                        aria-controls="collapseInfo">
                                        <i class="fas fa-info-circle me-2 text-primary"></i>
                                        Información Institucional y Contacto
                                    </button>
                                </h2>

                                {{-- Agregamos 'bg-light' y forzamos el texto oscuro --}}
                                <div id="collapseInfo" class="accordion-collapse collapse" aria-labelledby="headingInfo"
                                    data-bs-parent="#accordionInfo">

                                    <div class="accordion-body bg-light text-dark">
                                        <div class="row">
                                            {{-- Misión y Visión --}}
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <h6 class="fw-bold text-uppercase small text-muted">Misión</h6>
                                                    <p class="mb-0 small fst-italic text-dark">
                                                        {{ $organizacion->mision ?? 'No se ha definido la misión.' }}
                                                    </p>
                                                </div>
                                                <hr class="my-2">
                                                <div>
                                                    <h6 class="fw-bold text-uppercase small text-muted">Visión</h6>
                                                    <p class="mb-0 small fst-italic text-dark">
                                                        {{ $organizacion->vision ?? 'No se ha definido la visión.' }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Datos de Contacto --}}
                                            <div class="col-md-4 border-start">
                                                <h6 class="fw-bold text-uppercase small text-muted mb-3">Datos de Contacto
                                                </h6>
                                                <ul class="list-unstyled small text-dark">
                                                    <li class="mb-2">
                                                        <i class="fas fa-envelope me-2 text-secondary"></i>
                                                        {{ $organizacion->email ?? 'Sin correo' }}
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-phone me-2 text-secondary"></i>
                                                        {{ $organizacion->telefono ?? 'Sin teléfono' }}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 1. Sector --}}
                        <div class="col-md-3 border-end border-secondary border-opacity-25">
                            <small class="text-secondary text-uppercase fw-bold ls-1">Sector / Industria</small>
                            <div class="d-flex align-items-center mt-2">
                                <i class="fas fa-industry text-primary fa-2x me-3"></i>
                                <div>
                                    {{-- Ajusta 'sector' al nombre real de tu columna --}}
                                    <h5 class="mb-0">{{ $organizacion->subsector->sector->nombre ?? 'No definido' }}</h5>
                                    <small class="text-light opacity-75">Categoría Principal</small>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Tipo / Naturaleza (Ejemplo) --}}
                        <div class="col-md-3 border-end border-secondary border-opacity-25">
                            <small class="text-secondary text-uppercase fw-bold ls-1">Tipo Entidad</small>
                            <div class="d-flex align-items-center mt-2">
                                <i class="fas fa-landmark text-warning fa-2x me-3"></i>
                                <div>
                                    <h5 class="mb-0">{{ $organizacion->tipo->nombre ?? 'Pública/Privada' }}</h5>
                                    <small class="text-light opacity-75">Naturaleza Jurídica</small>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Descripción o Misión Corta --}}
                        <div class="col-md-6">
                            <small class="text-secondary text-uppercase fw-bold ls-1">Resumen</small>
                            <p class="mt-2 text-light opacity-75 lh-sm mb-0">
                            <ul class="list-unstyled mb-0">
                                <li><strong>RUC:</strong> {{ $organizacion->ruc }}</li>
                                <li><strong>Siglas:</strong> {{ $organizacion->siglas }}</li>
                                <li><strong>Nivel de Gobierno:</strong> {{ $organizacion->nivel_gobierno }}</li>

                            </ul>
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- SECCIÓN 2: ALINEACIÓN ESTRATÉGICA (TABLA Y GRÁFICO)      --}}
    {{-- ========================================================== --}}
    <div class="row">
        {{-- COLUMNA IZQUIERDA: TABLA DE OBJETIVOS --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 text-primary fw-bold"><i class="fas fa-list-check me-2"></i>Alineación de Objetivos</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-uppercase small text-muted">
                                    <th class="ps-4" style="width: 30%;">Obj. Estratégico</th>
                                    <th style="width: 50%;">Alineación PND</th>
                                    <th class="text-center" style="width: 20%;">ODS Impactado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($organizacion->objetivos as $obj)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex flex-column">
                                                <span
                                                    class="badge bg-primary bg-opacity-10 text-primary mb-1 align-self-start">{{ $obj->codigo }}</span>
                                                <span
                                                    class="fw-bold text-dark small">{{ Str::limit($obj->nombre, 50) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($obj->alineacion)
                                                <div class="card bg-light border-0 p-2">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="badge bg-dark me-2">Meta
                                                            {{ $obj->alineacion->metaPnd->codigo_meta ?? '?' }}</span>
                                                        <small class="fw-bold text-dark">Plan Nacional de
                                                            Desarrollo</small>
                                                    </div>
                                                    <small class="text-muted fst-italic">
                                                        "{{ Str::limit($obj->alineacion->metaPnd->descripcion ?? 'Sin descripción', 80) }}"
                                                    </small>
                                                </div>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                    <i class="fas fa-unlink me-1"></i> Sin alineación
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($obj->alineacion && $obj->alineacion->ods)
                                                <div class="d-inline-block text-center shadow-sm rounded p-1"
                                                    style="background-color: {{ $obj->alineacion->ods->color_hex }}; min-width: 45px;"
                                                    data-bs-toggle="tooltip" title="{{ $obj->alineacion->ods->nombre }}">
                                                    <span
                                                        class="text-white fw-bold h5 mb-0">{{ $obj->alineacion->ods->codigo }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
                                            <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i><br>
                                            No se han registrado objetivos estratégicos.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: GRÁFICOS Y KPI --}}
        <div class="col-lg-4">
            {{-- KPI: TOTAL ALINEACIONES --}}
            <div class="card border-0 shadow-sm bg-primary text-white mb-4 overflow-hidden position-relative">
                <div class="position-absolute end-0 bottom-0 p-2 opacity-25">
                    <i class="fas fa-chart-line fa-5x transform-rotate-12"></i>
                </div>
                <div class="card-body text-center py-4">
                    <h1 class="display-3 fw-bold mb-0">{{ $organizacion->alineaciones->count() }}</h1>
                    <p class="mb-0 text-uppercase ls-1 small opacity-75">Objetivos Alineados</p>
                </div>
            </div>

            {{-- GRÁFICO ODS --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 text-primary fw-bold"><i class="fas fa-chart-pie me-2"></i>Impacto en ODS</h5>
                </div>
                <div class="card-body position-relative">
                    <div style="height: 300px;">
                        <canvas id="chartOds"></canvas>
                    </div>
                    @if(count ($labels??[])==0)
                        <div class="position-absolute top-50 start-50 translate-middle text-center text-muted w-100">
                            <small>No hay datos suficientes para el gráfico</small>
                        </div>
                   @endif
                </div>
            </div>
        </div>
    </div>
    {{-- ========================================================== --}}
    {{-- MODAL PARA EDICION DE PERFIL ORGANIZACION ORGANIZACIONAL   --}}
    {{-- ========================================================== --}}
    <div class="modal fade" id="modalEditarPerfil" tabindex="-1" aria-hidden="true">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="fas fa-building me-2"></i>Gestión Institucional</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- El formulario envuelve TODAS las pestañas --}}
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-body p-0">

                        {{-- 1. NAVEGACIÓN DE PESTAÑAS --}}
                        <ul class="nav nav-tabs nav-justified bg-light" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-0" id="general-tab" data-bs-toggle="tab"
                                    data-bs-target="#general" type="button" role="tab">
                                    <i class="fas fa-info-circle me-1"></i> Datos Generales
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-0" id="institucional-tab" data-bs-toggle="tab"
                                    data-bs-target="#institucional" type="button" role="tab">
                                    <i class="fas fa-book me-1"></i> Misión y Visión
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-0" id="config-tab" data-bs-toggle="tab"
                                    data-bs-target="#config" type="button" role="tab">
                                    <i class="fas fa-cogs me-1"></i> Configuración
                                </button>
                            </li>
                        </ul>

                        {{-- 2. CONTENIDO DE LAS PESTAÑAS --}}
                        <div class="tab-content p-4" id="myTabContent">

                            {{-- TAB 1: DATOS GENERALES --}}
                            <div class="tab-pane fade show active" id="general" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label fw-bold">Nombre de la Organización <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="nom_organizacion" class="form-control"
                                            value="{{ $organizacion->nom_organizacion }}" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Siglas / Acrónimo</label>
                                        <input type="text" name="siglas" class="form-control"
                                            value="{{ $organizacion->siglas }}" placeholder="Ej: UTN">
                                    </div>
                                    {{-- Ejemplo de campos extra si los tuvieras en BD --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Teléfono / Contacto</label>
                                        <input type="text" name="telefono" class="form-control"
                                            value="{{ $organizacion->telefono ?? '' }}" placeholder="(Opcional)">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Correo Institucional</label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ $organizacion->email ?? '' }}" placeholder="(Opcional)">
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 2: INSTITUCIONAL (MISIÓN / VISIÓN) --}}
                            <div class="tab-pane fade" id="institucional" role="tabpanel">
                                <div class="alert alert-info py-2 small">
                                    <i class="fas fa-lightbulb me-1"></i> Estos datos aparecerán en los reportes oficiales.
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Misión</label>
                                    <textarea name="mision" class="form-control" rows="3"
                                        placeholder="¿Cuál es la razón de ser de la institución?">{{ $organizacion->mision ?? '' }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Visión</label>
                                    <textarea name="vision" class="form-control" rows="3" placeholder="¿A dónde quiere llegar?">{{ $organizacion->vision ?? '' }}</textarea>
                                </div>
                            </div>

                            {{-- TAB 3: CONFIGURACIÓN Y ESTADO --}}
                            <div class="tab-pane fade" id="config" role="tabpanel">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Estado del Sistema</label>
                                    <select name="estado"
                                        class="form-select border-3 {{ $organizacion->estado == 'A' || $organizacion->estado == 1 ? 'border-success' : 'border-danger' }}">
                                        <option value="A"
                                            {{ $organizacion->estado == 'A' || $organizacion->estado == 1 ? 'selected' : '' }}>
                                            🟢 Activo (Visible para todos)</option>
                                        <option value="I"
                                            {{ $organizacion->estado == 'I' || $organizacion->estado == 0 ? 'selected' : '' }}>
                                            🔴 Inactivo (Oculto)</option>
                                    </select>
                                    <div class="form-text">Desactivar la organización impedirá crear nuevas alineaciones.
                                    </div>
                                </div>

                                <hr>

                                {{-- Sección visual para LOGO (Si tienes subida de archivos) --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Actualizar Logo</label>
                                    <input type="file" name="logo" class="form-control">
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <style>
        .accordion-collapse.show {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
    </style>


    {{-- SCRIPTS (Mantenemos tu lógica original de Chart.js) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('chartOds');

            // Datos PHP
            const misLabels = {!! json_encode($labels) !!};
            const misValores = {!! json_encode($valores) !!};
            const misColores = {!! json_encode($colores) !!};

            if (ctx && misValores.length > 0) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: misLabels,
                        datasets: [{
                            data: misValores,
                            backgroundColor: misColores,
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: 10
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.8)',
                                padding: 10,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        let value = context.raw;
                                        let total = context.chart._metasets[context.datasetIndex].total;
                                        let percentage = Math.round(value / total * 100) + '%';
                                        return label + value + ' (' + percentage + ')';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
