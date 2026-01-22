@extends('layouts.app')
{{-- COLOCAR ESTO AL PRINCIPIO DE TU ARCHIVO INDEX BLADE --}}
<script>
    const aniosProyecto = @json($anios);
    const PROJECT_START = aniosProyecto.length > 0 ? aniosProyecto[0] : new Date().getFullYear();
    const PROJECT_END = aniosProyecto.length > 0 ? aniosProyecto[aniosProyecto.length - 1] : new Date().getFullYear();
</script>
@section('content')
    <x-layouts.header_content titulo="Matriz de Marco Lógico" subtitulo="{{ Auth::user()->organizacion->nom_organizacion }}">

        <a href="{{ route('inversion.proyectos.index') }}" class="btn btn-outline-secondary align-items-center" title="Proyectos">
            <i class="fas fa-home me-1"></i> Proyectos
        </a>
        <button type="button" class="btn btn-secondary" onclick="history.back()">
            <i class="fas fa-arrow-left me-1"></i> Atras
        </button>

    </x-layouts.header_content>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="text-muted">
                Proyecto: <span class="fw-bold text-primary">{{ $proyecto->nombre_proyecto }}</span>
                CUP: <span class="badge bg-secondary ms-2">{{ $proyecto->cup }}</span>
            </div>
        </div>

    </div>
    @include('partials.mensajes')
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="row align-items-center">

                <div class="col-md-3 border-end">
                    <small class="text-muted text-uppercase fw-bold">Techo del Proyecto</small>
                    <h4 class="text-primary fw-bold mb-0">${{ number_format($techoPresupuestario, 2) }}</h4>
                </div>

                <div class="col-md-9">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-bold">
                            Asignado en Actividades:
                            <span class="{{ $totalPlanificado > $techoPresupuestario ? 'text-danger' : 'text-success' }}">
                                ${{ number_format($totalPlanificado, 2) }}
                            </span>
                        </span>
                        <span class="small text-muted">
                            Disponible:
                            <strong>${{ number_format($techoPresupuestario - $totalPlanificado, 2) }}</strong>
                        </span>
                    </div>

                    {{-- BARRA DE PROGRESO  --}}
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar {{ $totalPlanificado > $techoPresupuestario ? 'bg-danger' : 'bg-success' }} progress-bar-striped progress-bar-animated"
                            role="progressbar"
                            style="width: {{ $porcentajeFinanciero > 100 ? 100 : $porcentajeFinanciero }}%">
                            {{ number_format($porcentajeFinanciero, 1) }}%
                        </div>
                    </div>

                    @if ($totalPlanificado > $techoPresupuestario)
                        <div class="text-danger small mt-1 fw-bold">
                            <i class="fas fa-exclamation-circle"></i>
                            ¡ALERTA! Ha sobrepasado el presupuesto del proyecto por
                            ${{ number_format($totalPlanificado - $techoPresupuestario, 2) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="card border-success border-2 shadow-sm mb-5">
        <div class="card-header bg-success bg-gradient text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-bullseye me-2"></i>Propósito (Objetivo Central)</h5>
            <button class="btn btn-sm btn-light text-primary"
                onclick="abrirModal(
            'PROPOSITO',
            {{ $proposito ?? 'null' }},
            null,
            {{ $proposito?->indicador?->metasAnuales->pluck('valor_meta', 'anio') ?? '{}' }}
            )">
                <i class="fas fa-edit"></i> Editar
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- EL OBJETIVO (Resumen) --}}
                <div class="col-md-4 border-end">
                    <h6 class="text-muted fw-bold text-uppercase small">Resumen Narrativo</h6>
                    @if ($proyecto->proposito)
                        <p class="lead text-dark">{{ $proyecto->proposito->resumen_narrativo }}</p>
                    @else
                        <p class="text-muted fst-italic">Sin propósito registrado.</p>
                    @endif
                    <hr>

                    <h6 class="text-muted fw-bold text-uppercase small">Supuestos</h6>
                    <p class="small text-secondary fst-italic">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $proposito->supuestos ?? 'Sin supuestos definidos' }}
                    </p>
                </div>

                {{--  INDICADOR Y FUENTE --}}
                <div class="col-md-4 border-end">
                    <h6 class="text-muted fw-bold text-uppercase small">Indicador</h6>
                    @if ($proposito?->indicador)
                        <p class="fw-bold mb-1">{{ $proposito->indicador->descripcion }}</p>
                        <span class="badge bg-info text-dark">{{ $proposito->indicador->unidad_medida }}</span>

                        <div class="mt-3">
                            <strong class="small text-muted">Medio de Verificación:</strong><br>
                            <span class="small">{{ $proposito->indicador->medio_verificacion }}</span>
                        </div>
                    @else
                        <div class="alert alert-warning py-1 small">Falta definir indicador</div>
                    @endif
                </div>
                {{-- METAS --}}
                <div class="col-md-4">
                    <h6 class="text-muted fw-bold text-uppercase small">Metas Anuales</h6>
                    @if ($proposito?->indicador?->metasAnuales?->count() > 0)
                        <table class="table table-sm table-striped mt-2">
                            <thead>
                                <tr>
                                    <th>Año</th>
                                    <th class="text-end">Meta</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($proposito->indicador->metasAnuales as $meta)
                                    <tr>
                                        <td>{{ $meta->anio }}</td>
                                        <td class="text-end fw-bold">{{ $meta->valor_meta }}</td>
                                    </tr>
                                @endforeach
                                <tr class="table-primary">
                                    <td><strong>Total</strong></td>
                                    <td class="text-end"><strong>{{ $proposito->indicador->meta_total }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted small">No se han cargado metas.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- COMPONENTES  --}}
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h5 class="fw-bold text-dark m-0 border-bottom border-3 border-warning pb-1">
            <i class="fas fa-cubes me-2"></i>Componentes y Actividades
        </h5>
        <button class="btn btn-dark" onclick="abrirModal('COMPONENTE')">
            <i class="fas fa-plus me-1"></i> Nuevo Componente
        </button>
    </div>

    {{-- LISTA DE COMPONENTES --}}
    @forelse($proyecto->componentes as $comp)
        <div class="card mb-4 border-2 border-warning shadow-sm bg-light">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex w-100">
                        <div class="bg-warning text-dark fw-bold rounded px-2 py-1 me-3" style="height: fit-content">
                            C:{{ $loop->iteration }}
                        </div>

                        <div class="w-100">
                            <h5 class="fw-bold text-dark mb-2">{{ $comp->resumen_narrativo }}</h5>
                            <div class="card border mb-3 bg-white">
                                <div class="card-body py-2 px-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-5 border-end">
                                            <small class="text-muted fw-bold text-uppercase"
                                                style="font-size: 0.7rem">Indicador de Componente</small>
                                            @if ($comp->indicador)
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <span class="d-block fw-bold text-primary"
                                                            style="font-size: 0.9rem">
                                                            {{ $comp->indicador->descripcion }}
                                                        </span>
                                                        <span class="badge bg-light text-dark border mt-1">
                                                            {{ $comp->indicador->unidad_medida }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-danger small mt-1">
                                                    <i class="fas fa-exclamation-triangle"></i> Sin indicador definido
                                                </div>
                                                <button class="btn btn-sm btn-link text-danger p-0 small"
                                                    onclick="abrirModal('COMPONENTE', null, null, {{ $comp->id_marco_logico }})">
                                                    Definir ahora
                                                </button>
                                            @endif
                                        </div>

                                        {{--  Metas Físicas  --}}
                                        <div class="col-md-7 ps-4">
                                            <div class="d-flex align-items-center">
                                                <small class="text-muted fw-bold text-uppercase me-3"
                                                    style="font-size: 0.7rem">Metas:</small>

                                                @if ($comp->indicador && $comp->indicador->metasAnuales->count() > 0)
                                                    <div class="d-flex flex-wrap">
                                                        @foreach ($anios as $anio)
                                                            @php
                                                                $meta = $comp->indicador->metasAnuales->firstWhere(
                                                                    'anio',
                                                                    $anio,
                                                                );
                                                            @endphp
                                                            <div class="text-center me-2 mb-1 border rounded px-2 bg-light">
                                                                <div style="font-size: 0.65rem" class="text-muted">
                                                                    {{ $anio }}</div>
                                                                <div class="fw-bold text-dark small">
                                                                    {{ $meta ? $meta->valor_meta : '-' }}</div>
                                                            </div>
                                                        @endforeach

                                                        {{-- Total --}}
                                                        <div
                                                            class="text-center me-2 mb-1 border rounded px-2 bg-primary text-white">
                                                            <div style="font-size: 0.65rem" class="text-white-50">Total
                                                            </div>
                                                            <div class="fw-bold small">{{ $comp->indicador->meta_total }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted small fst-italic">No hay metas cargadas</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- (Editar Componente) --}}
                    <div class="dropdown ms-2">
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"
                                    onclick="abrirModal(
                            'COMPONENTE',
                            {{ $comp }}, {{-- Objeto Componente --}}
                            {{ $proyecto->id }},
                            {{ $comp->indicador ? $comp->indicador->metasAnuales->pluck('valor_meta', 'anio') : '{}' }}
                            )">
                                    <i class="fas fa-pencil-alt me-1"></i> Editar Datos
                                </a></li>
                            <li>
                                <a class="dropdown-item text-danger" href="#"
                                    onclick="event.preventDefault(); confirmarEliminar({{ $comp->id_marco_logico }})">
                                    <i class="fas fa-trash-alt me-1"></i> Eliminar
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- TABLA DE ACTIVIDADES  --}}
                <div class="card border-0">
                    <div class="card-header bg-white border-bottom-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-secondary small text-uppercase">Actividades vinculadas</span>
                            <button class="btn btn-sm btn-outline-primary"
                                onclick="abrirModal('ACTIVIDAD', null, {{ $comp->id_marco_logico }})">
                                <i class="fas fa-plus me-1"></i> Agregar Actividad
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary small">
                                <tr>
                                    <th>Actividad</th>
                                    <th>Fechas</th>
                                    <th>Presupuesto</th>
                                    <th class="text-center">Ponderación</th>
                                    <th class="text-end"> Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comp->actividades as $act)
                                    <tr>
                                        <td>{{ $act->resumen_narrativo }}</td>
                                        <td class="small">
                                            {{-- Validamos si existen las fechas para que no de error --}}
                                            @if ($act->fecha_inicio && $act->fecha_fin)
                                                {{ \Carbon\Carbon::parse($act->fecha_inicio)->format('d/m/y') }} -
                                                {{ \Carbon\Carbon::parse($act->fecha_fin)->format('d/m/y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        {{-- Validamos monto --}}
                                        <td class="fw-bold text-dark">
                                            @if ($act->monto > 0)
                                                <span class="fw-bold text-dark">
                                                    ${{ number_format($act->monto ?? 0, 2) }}
                                                </span>
                                            @else
                                                <span class="text-muted small">$ 0.00</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($act->ponderacion > 0)
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ $act->ponderacion ?? 0 }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ $act->ponderacion ?? 0 }}%</small>
                                        </td>
                                        <td class="text-end">
                                            {{-- REPORTAR AVANCE --}}
                                            <button class="btn btn-sm btn-outline-success border-0"
                                                title="Reportar Avance Físico"
                                                onclick="abrirModalAvance(
                                                {{ $act->id_marco_logico }},
                                                '{{ $act->resumen_narrativo }}',
                                                {{ $act->avance_actual ?? 0 }}
                                                )">
                                                <i class="fas fa-play-circle fa-lg"></i>
                                            </button>
                                            {{-- EDITAR --}}
                                            <button class="btn btn-link text-muted p-0"
                                                onclick="abrirModal(
                                           'ACTIVIDAD',
                                            {{ $act }},  {{-- Pasamos TODO el objeto actividad --}}
                                            {{ $comp->id_marco_logico }},
                                            {{ $act->indicador ? $act->indicador->metasAnuales->pluck('valor_meta', 'anio') : '{}' }}
                                            )">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            {{-- Busca el botón rojo con fa-trash --}}
                                            <button class="btn btn-xs" title="Eliminar"
                                                onclick="confirmarEliminar({{ $act->id_marco_logico }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <span class="text-muted small">-</span>
                                @endif
                                </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted small py-3">
                                        No hay actividades registradas en este componente.
                                    </td>
                                </tr>
                            </tbody>
    @endforelse
    </tbody>
    </table>
    </div>
    </div>
    </div>
    </div>
@empty
    <div class="alert alert-warning text-center">
        <i class="fas fa-folder-open mb-2 fa-lg"></i><br>
        Aún no ha creado componentes. ¡Empiece agregando uno!
    </div>
    @endforelse

    {{-- INCLUIMOS EL MODAL GENERICO --}}
    @include('dashboard.inversion.proyectos.marcologico.partials.modal_form')
    {{-- MODAL REPORTAR AVANCE --}}
    <div class="modal fade" id="modalAvance" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Reportar Ejecución</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formAvance" action="{{ route('inversion.proyectos.registrar-avance.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="marco_logico_id" id="avance_actividad_id">

                    <div class="modal-body">
                        {{-- Resumen --}}
                        <div class="alert alert-light border mb-3">
                            <small class="text-muted text-uppercase">Actividad:</small>
                            <div class="fw-bold text-dark" id="avance_titulo_actividad">...</div>
                            <div class="mt-2 small">
                                Avance Actual: <span class="badge bg-success" id="avance_actual_badge">0%</span>
                            </div>
                        </div>

                        {{-- Campos de Reporte --}}
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">Fecha de Corte</label>
                                <input type="date" name="fecha_reporte" class="form-control" required
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold text-success">Nuevo % Acumulado</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" max="100" min="0"
                                        name="avance_total_acumulado" class="form-control fw-bold" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted" style="font-size: 0.7rem">Ingrese el total real a la
                                    fecha.</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Observaciones / Logros</label>
                                <textarea name="observacion" class="form-control" rows="2" placeholder="Describa el trabajo realizado..."></textarea>
                            </div>

                            {{-- Evidencia (Opcional por ahora) --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Evidencia (Foto/PDF)</label>
                                <input type="file" name="evidencia" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar Reporte</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT PARA ABRIR ESTE MODAL --}}
    <script>
        function abrirModalAvance(id, titulo, avanceActual) {
            document.getElementById('avance_actividad_id').value = id;
            document.getElementById('avance_titulo_actividad').innerText = titulo;
            document.getElementById('avance_actual_badge').innerText = avanceActual + '%';

            // Sugerir el avance actual en el input
            document.querySelector('[name="avance_total_acumulado"]').value = avanceActual;

            new bootstrap.Modal(document.getElementById('modalAvance')).show();
        }
    </script>
@endsection

@push('scripts')
    <script>
        let metasActuales = {};

        function abrirModal(nivel, data = null, padreId = null, metasData = null) {
            const form = document.getElementById('formMarcoLogico');
            form.reset();
            //  Configuración Básica
            document.getElementById('nivel_input').value = nivel;
            document.getElementById('padre_id_input').value = padreId || '';

            //  Lógica Es CREAR o EDITAR?
            if (data) {
                // --- MODO EDICIÓN ---
                document.getElementById('modalTitulo').innerText = 'Editar ' + nivel;
                document.getElementById('metodo_form').value = 'PUT'; // Laravel necesita esto para updates
                document.getElementById('id_input').value = data.id_marco_logico;

                form.action = "{{ url('inversion/proyectos/marco-logico') }}/" + data.id_marco_logico;

                // LLENAR CAMPOS (Mapeo directo de la Base de Datos al HTML)

                setVal('resumen_narrativo', data.resumen_narrativo);
                setVal('supuestos', data.supuestos);
                setVal('fecha_inicio', data.fecha_inicio);
                setVal('fecha_fin', data.fecha_fin);
                setVal('monto', data.monto);
                setVal('ponderacion', data.ponderacion);

                // Si tiene indicador (Solo para Propósito y Componente)
                if (data.indicador) {
                    setVal('descripcion_indicador', data.indicador.descripcion);
                    setVal('unidad_medida', data.indicador.unidad_medida);
                    setVal('medio_verificacion', data.indicador.medio_verificacion);
                }

            } else {
                // --- MODO CREAR ---
                document.getElementById('modalTitulo').innerText = 'Nuevo ' + nivel;
                document.getElementById('metodo_form').value = 'POST';
                document.getElementById('id_input').value = '';
                form.action = "{{ route('inversion.proyectos.marco-logico.store') }}";
            }

            // GENERAR Y LLENAR METAS
            generarInputsMetas(metasData);

            //  Mostrar
            var myModal = new bootstrap.Modal(document.getElementById('modalMarcoLogico'));
            myModal.show();
        }

        // Función auxiliar para no escribir document.getElementById
        function setVal(name, val) {
            let input = document.querySelector(`[name="${name}"]`);
            if (input) input.value = val || '';
        }

        //  lógica de metas simplificada \
        function generarInputsMetas(metasData) {
            const container = document.getElementById('contenedor_metas_anuales');
            container.innerHTML = '';

            if (typeof PROJECT_START !== 'undefined') {
                for (let anio = PROJECT_START; anio <= PROJECT_END; anio++) {
                    let valor = '';
                    if (metasData && metasData[anio]) {
                        valor = metasData[anio];
                    }

                    const html = `
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">${anio}</span>
                        <input type="number" name="metas[${anio}]" value="${valor}" class="form-control" step="0.01">
                    </div>
                </div>`;
                    container.insertAdjacentHTML('beforeend', html);
                }
            }
        }

        function confirmarEliminar(id) {
            if (confirm('¿Está seguro de eliminar este elemento? Esta acción no se puede deshacer.')) {
                // Creamos un formulario invisible temporal
                let form = document.createElement('form');
                form.method = 'POST';
                // Ajusta esta URL igual que hiciste con el editar:
                form.action = "{{ url('inversion/proyectos/marco-logico') }}/" + id;

                // Token CSRF (Obligatorio en Laravel)
                let csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                let inputCsrf = document.createElement('input');
                inputCsrf.type = 'hidden';
                inputCsrf.name = '_token';
                inputCsrf.value = csrfToken;

                // Método DELETE (Spoofing)
                let inputMethod = document.createElement('input');
                inputMethod.type = 'hidden';
                inputMethod.name = '_method';
                inputMethod.value = 'DELETE';

                form.appendChild(inputCsrf);
                form.appendChild(inputMethod);
                document.body.appendChild(form);

                form.submit();
            }
        }
    </script>
@endpush
