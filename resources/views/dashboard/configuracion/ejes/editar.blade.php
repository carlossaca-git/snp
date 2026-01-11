<div class="modal fade" id="modalEditEje" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Editar Eje</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            {{-- El Action se llena con JS --}}
            <form id="formEditEje" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    {{-- INFO DEL PLAN (Solo lectura) --}}
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">PERTENECE AL PLAN:</label>
                        <input type="text" id="edit_plan_nombre" class="form-control bg-light text-muted" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Eje</label>
                        <input type="text" name="nombre_eje" id="edit_nombre_eje" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion_eje" id="edit_descripcion_eje" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">URL Documento</label>
                        <input type="url" name="url_documento" id="edit_url_documento" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado</label>
                        <select name="estado" id="edit_estado_eje" class="form-select">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
