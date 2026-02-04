{{-- Modal Editar Plan --}}
<div class="modal fade" id="modalEditarPlan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditarPlan" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Editar Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_id">
                    <div class="mb-3">
                        <label class="fw-bold">Año Fiscal</label>
                        <input type="text" id="edit_anio" name="anio" class="form-control" required>
                        <small class="text-muted">El año no se puede editar.</small>
                    </div>
                    <div class="alert alert-light border mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="check_nueva_version" name="es_reforma"
                                value="1">
                            <label class="form-check-label fw-bold" for="check_nueva_version">
                                Generar Nueva Versión (Reforma)
                            </label>
                        </div>
                        <small class="text-muted">
                            Marca esto si hay cambios legales o presupuestarios importantes.
                            <br>La versión subirá de <span id="span_version_actual" class="fw-bold"></span> a la
                            siguiente.
                        </small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">N° Resolución / Acuerdo</label>
                        <input type="text" name="numero_resolucion" id="edit_resolucion" class="form-control"
                            placeholder="Ej: RES-001-2026">
                    </div>
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Monto Total ($)</label>
                        <input type="number" step="0.01" name="monto_total" id="edit_monto" class="form-control"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Estado</label>
                        <select name="estado" id="edit_estado" class="form-select">
                            <option value="FORMULACION">Formulación (Borrador)</option>
                            <option value="APROBADO">Aprobado</option>
                            <option value="EJECUCION">En Ejecución</option>
                            <option value="CERRADO">Cerrado / Finalizado</option>
                        </select>
                    </div>
                    {{-- Archivo actual --}}
                    <div id="aviso_archivo_actual" class="mt-2 p-2 border rounded bg-light" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Documento Habilitante (Resolución)</label>
                            <input type="file" name="documento_soporte" class="form-control"
                                accept="application/pdf">
                            <div class="form-text">Subir la resolución de aprobación escaneada (Solo PDF, máx 5MB).
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-success me-2">
                                <i class="fas fa-check"></i> Existe Archivo
                            </span>
                            <a id="link_archivo_descarga" href="#" target="_blank"
                                class="text-decoration-none text-dark text-truncate" style="max-width: 250px;">
                                <i class="fas fa-file-pdf text-danger me-1"></i>
                                <span id="texto_nombre_archivo">Cargando...</span>
                            </a>
                        </div>
                        <small class="text-muted d-block mt-1 ms-1">
                            Si subes otro archivo, este será reemplazado.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
