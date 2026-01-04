{{-- NO usamos @extends porque este archivo se carga con @include en principal.blade --}}

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2">Gestión de Planes y Programas</h1>
        <p class="text-muted small">Módulo de Inversión Pública - Listado Maestro de Programas (tra_programa)</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        {{-- Botón para cambiar la sección a "programas_crear" --}}
        <a href="{{ route('principal', ['seccion' => 'programas_crear']) }}" class="btn btn-sm btn-primary shadow-sm">
            <span data-feather="plus-circle"></span> Nuevo Programa
        </a>
    </div>
</div>

<!-- Filtros Rápidos (Opcional pero recomendado para el 2026) -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body bg-light">
        <form class="row g-3 align-items-center">
            <div class="col-auto">
                <input type="text" class="form-control form-control-sm" placeholder="Buscar por Código CUP...">
            </div>
            <div class="col-auto">
                <select class="form-select form-select-sm">
                    <option selected>Año Fiscal...</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                    <option value="2026">2026</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-outline-secondary">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Resultados -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4">Código CUP</th>
                        <th>Nombre del Programa</th>
                        <th>Vigencia</th>
                        <th class="text-end">Presupuesto</th>
                        <th class="text-center">Proyectos</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Iniciamos el bucle con la variable que envíes desde el controlador --}}
                    @forelse($programas as $programa)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-light text-dark border">{{ $programa->codigo_cup }}</span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $programa->nombre_programa }}</div>
                                <div class="text-muted x-small" style="font-size: 0.75rem;">
                                    Registrado por: {{ $programa->usuario_registro ?? 'Sistema' }}
                                </div>
                            </td>
                            <td>{{ $programa->anio_inicio }} - {{ $programa->anio_fin }}</td>
                            <td class="text-end fw-bold">
                                ${{ number_format($programa->monto_planificado, 2) }}
                            </td>
                            <td class="text-center">
                                {{-- Relación con tra_proyecto_inversion --}}
                                <span class="badge rounded-pill bg-info text-white">
                                    {{ $programa->proyectos_count ?? 0 }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group" role="group">
                                    {{-- Botón para ver detalle --}}
                                    <a href="#" class="btn btn-sm btn-outline-primary" title="Ver Proyectos">
                                        <span data-feather="eye"></span>
                                    </a>
                                    {{-- Botón para editar --}}
                                    <a href="#" class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <span data-feather="edit-2"></span>
                                    </a>
                                    {{-- Botón de eliminación (Soft Delete sugerido) --}}
                                    <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                                        <span data-feather="trash-2"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <span data-feather="info" class="mb-2"></span>
                                    <p>No se encontraron programas de inversión registrados.</p>
                                    <a href="{{ route('principal', ['seccion' => 'programas_crear']) }}"
                                        class="btn btn-sm btn-primary">
                                        Crear el primer programa
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        {{-- Aquí irá la paginación de Laravel --}}
        <small class="text-muted">Mostrando registros de la tabla <code>tra_programa</code></small>
    </div>
</div>
