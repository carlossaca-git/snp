<div class="modal fade" id="modalEditIndicador" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="formEditIndicador" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Indicador Técnico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Meta Nacional Vinculada</label>
                            <select name="id_meta" id="edit_id_meta" class="form-select" required>
                                @foreach($metas as $m)
                                    <option value="{{ $m->id_meta }}">{{ $m->nombre_meta }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Nombre del Indicador</label>
                            <input type="text" name="nombre_indicador" id="edit_nombre_indicador" class="form-control" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Estado</label>
                            <select name="estado" id="edit_estado_indicador" class="form-select">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Línea Base</label>
                            <input type="number" step="0.01" name="linea_base" id="edit_linea_base" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Año Línea Base</label>
                            <input type="number" name="anio_linea_base" id="edit_anio_linea_base" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Meta Final (Valor)</label>
                            <input type="number" step="0.01" name="meta_final" id="edit_meta_final" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Unidad de Medida</label>
                            <select name="unidad_medida" id="edit_unidad_medida" class="form-select" required>
                                <option value="Porcentaje">Porcentaje (%)</option>
                                <option value="Tasa">Tasa</option>
                                <option value="USD">Dólares (USD)</option>
                                <option value="Número">Número Absoluto</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Frecuencia de Medición</label>
                            <select name="frecuencia" id="edit_frecuencia" class="form-select" required>
                                <option value="Anual">Anual</option>
                                <option value="Semestral">Semestral</option>
                                <option value="Trimestral">Trimestral</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Método de Cálculo (Fórmula)</label>
                            <textarea name="metodo_calculo" id="edit_metodo_calculo" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Fuente de Información</label>
                            <input type="text" name="fuente_informacion" id="edit_fuente_informacion" class="form-control">
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Descripción Técnica</label>
                            <textarea name="descripcion_indicador" id="edit_descripcion_indicador" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold">Actualizar Indicador</button>
                </div>
            </div>
        </form>
    </div>
</div>
