<div class="modal fade" id="modalEditPnd" tabindex="-1" aria-labelledby="modalEditPndLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="modalEditPndLabel">Editar Objetivo Nacional
                    <span id="label_codigo_pnd" class="badge bg-primary ms-2"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditPnd" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Código del Objetivo</label>
                            <input type="text" name="codigo_objetivo" id="edit_pnd_codigo" class="form-control"
                                required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Eje Estratégico</label>
                            <select name="id_eje" id="edit_pnd_eje" class="form-select" required>
                                <option value="">Seleccione...</option>
                                @foreach ($relEje as $eje)
                                    <option value="{{ $eje->id_eje }}">{{ $eje->nombre_eje }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Año Inicio</label>
                            <input type="number" name="periodo_inicio" id="edit_pnd_inicio" class="form-control"
                                placeholder="Ej: 2021">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Año Fin</label>
                            <input type="number" name="periodo_fin" id="edit_pnd_fin" class="form-control"
                                placeholder="Ej: 2025">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción del Objetivo</label>
                        <textarea name="descripcion_objetivo" id="edit_pnd_descripcion" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado</label>
                        <select name="estado" id="edit_pnd_estado" class="form-select">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
