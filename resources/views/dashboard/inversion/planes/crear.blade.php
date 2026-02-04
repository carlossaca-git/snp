{{-- Modal Crear Plan --}}
<div class="modal fade" id="modalCrearPlan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('inversion.planes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Nuevo Plan de Inversión</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Año Fiscal</label>
                        <select name="anio" class="form-select" required>
                            <option value="{{ date('Y') }}" selected>{{ date('Y') }} (Año en curso)</option>
                            <option value="{{ date('Y') + 1 }}">{{ date('Y') + 1 }} (Planificación futura)</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Version</label>
                        <input type="text" name="version" class="form-control" placeholder="Ej: RES-001-2026">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">N° Resolución / Acuerdo</label>
                        <input type="text" name="numero_resolucion" class="form-control"
                            placeholder="Ej: RES-001-2026">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre del Plan</label>
                        <input type="text" name="nombre" class="form-control"
                            placeholder="Ej: PAI 2025 Ministerio de X" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Monto Techo Presupuestario ($)</label>
                        <input type="number" step="0.01" min="0" name="monto_total" class="form-control"
                            placeholder="0.00"
                            onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 46"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Documento Habilitante (Resolución)</label>
                        <input type="file" name="documento_soporte" class="form-control" accept="application/pdf">
                        <div class="form-text">Subir la resolución de aprobación escaneada (Solo PDF, máx 5MB).
                        </div>
                    </div>
                </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Guardar Plan</button>
        </div>
        </form>
    </div>
</div>
</div>
