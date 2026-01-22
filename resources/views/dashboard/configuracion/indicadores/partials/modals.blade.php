<div class="modal fade" id="modalRegistrarAvance" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-chart-line me-2"></i>Registrar Avance</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('catalogos.indicadores.avances.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id_indicador" id="avance_id_indicador">
                    <div class="alert alert-light border mb-3">
                        <small class="text-muted d-block">Registrando avance para:</small>
                        <strong id="avance_nombre_indicador" class="text-dark">...</strong>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Valor Logrado (<span id="avance_unidad"></span>)</label>
                            <input type="number" step="0.01" name="valor_logrado" class="form-control" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Evidencia (PDF/Imagen)</label>
                            <input type="file" name="evidencia" class="form-control" accept=".pdf,.jpg,.png">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar Avance</button>
                </div>
            </form>
        </div>
    </div>
</div>
