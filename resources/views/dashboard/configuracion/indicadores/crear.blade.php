<div class="modal fade" id="modalCrearIndicador" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('catalogos.indicadores.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="fas fa-chart-line me-2"></i>Nuevo Indicador Técnico</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Meta Nacional Vinculada</label>
                            <select name="id_meta" class="form-select" required>
                                <option value="" selected disabled>-- Seleccione la Meta --</option>
                                @foreach ($metas as $m)
                                    <option value="{{ $m->id_meta }}">{{ $m->nombre_meta }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Nombre del Indicador</label>
                            <input type="text" name="nombre_indicador" class="form-control"
                                placeholder="Tasa de variación de..." required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Línea Base</label>
                            <input type="number" step="0.01" name="linea_base" class="form-control"
                                placeholder="0.00">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Año Línea Base</label>
                            <input type="number" name="anio_linea_base" class="form-control" placeholder="Anio 2023...">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Meta Final (Valor)</label>
                            <input type="number" step="0.01" name="meta_final" class="form-control"
                                placeholder="0.00">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Unidad de Medida</label>
                            <select name="unidad_medida" class="form-select" required>
                                <option value="Porcentaje">Porcentaje (%)</option>
                                <option value="Tasa">Tasa</option>
                                <option value="USD">Dólares (USD)</option>
                                <option value="Número">Número Absoluto</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Frecuencia de Medición</label>
                            <select name="frecuencia" class="form-select" required>
                                <option value="Anual">Anual</option>
                                <option value="Semestral">Semestral</option>
                                <option value="Trimestral">Trimestral</option>
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Método de Cálculo (Fórmula)</label>
                            <textarea name="metodo_calculo" class="form-control" rows="2" placeholder="(Variable A / Variable B) * 100"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Fuente de Información</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-university"></i></span>
                                    <input type="text" name="fuente_informacion" id="fuente_informacion_crear"
                                        class="form-control" placeholder="Ministerio de Salud Pública / INEC...">
                                </div>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Descripción Técnica del Indicador</label>
                                <textarea name="descripcion_indicador" id="descripcion_indicador_crear" class="form-control" rows="3"
                                    placeholder="Explique detalladamente qué mide este indicador y su relevancia..."></textarea>
                            </div>
                            <input type="hidden" name="estado" value="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-dark">Guardar Indicador</button>
                </div>
            </div>
        </form>
    </div>
</div>
