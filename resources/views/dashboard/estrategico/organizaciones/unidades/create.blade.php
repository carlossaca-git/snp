{{-- MODAL CREAR --}}
    <div class="modal fade" id="modalCrear" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('institucional.unidades.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Nueva Unidad Ejecutora</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Área / Dirección <span class="text-danger">*</span></label>
                            <input type="text" name="nombre_unidad" class="form-control" required
                                placeholder="Ej: Dirección de Obras Públicas">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Responsable (Director/Jefe)</label>
                            <input type="text" name="nombre_responsable" class="form-control"
                                placeholder="Nombre del encargado">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Código Interno</label>
                            <input type="text" name="codigo_unidad" class="form-control"
                                placeholder="Ej: UE-001">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
