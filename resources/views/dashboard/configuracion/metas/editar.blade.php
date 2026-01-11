<div class="modal fade" id="modalEditMeta" tabindex="-1" aria-labelledby="modalEditMetaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalEditMetaLabel">
                    <i class="fas fa-edit me-2"></i> Editar Meta Nacional
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEditMeta" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <input type="hidden" name="id_meta_nacional" id="id">
                            <label class="form-label fw-bold">1. Objetivo Nacional Vinculado</label>
                            <select name="id_objetivo_nacional" id="id_objetivo_editar" class="form-select" required>
                                <option value="" disabled>-- Seleccione el Objetivo --</option>
                                @foreach ($objetivos as $obj)
                                    <option value="{{ $obj->id_objetivo_nacional }}">
                                        {{$obj->codigo_objetivo}}-{{ Str::limit($obj->descripcion_objetivo, 80) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">2. Código / Identificador</label>
                            <input type="text" name="codigo_meta" id="edit_codigo" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">3. Estado</label>
                            <select name="estado" id="edit_estado" class="form-select">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                        {{--  Unidad de Medida e Indicador --}}

                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold small">4. Nombre del Indicador</label>
                                <input type="text" name="nombre_indicador" id="edit_indicador" class="form-control"
                                    value="" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold small">5. Unidad de Medida</label>
                                <select name="unidad_medida" id="edit_unidad" class="form-select" required>
                                    <option value="Porcentaje (%)">Porcentaje (%)</option>
                                    <option value="Tasa por 100k hab." >Tasa por 100k hab.</option>
                                    <option value="Índice (0-100)" >Índice (0-100)</option>
                                    <option value="Unidades">Unidades</option>
                                    <option value="Millones USD" >Millones USD</option>
                                    <option value="Número personas">Numero Personas</option>
                                    <option value="Megavatios (MW)" >Megavatios (MW)</option>
                                    <option value="Relación 1:1000" >Realacion 1:1000</option>
                                    <option value="Puesto Mundial">Puesto Mundial</option>

                                </select>
                            </div>

                        {{--  Cuantificación --}}

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-primary small">6. Línea Base</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-primary"><i
                                            class="fas fa-history"></i></span>
                                    <input type="text" name="linea_base" id="edit_linea_base" class="form-control"
                                        value="" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-success small">7. Meta Valor</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-success"><i
                                            class="fas fa-flag-checkered"></i></span>
                                    <input type="text" name="meta_valor" id="edit_meta_valor" class="form-control"
                                        value="" required>
                                </div>
                            </div>


                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">8. Definición de la Meta</label>
                            <textarea name="nombre_meta" id="edit_nombre" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">9. Descripción Técnica (Opcional)</label>
                            <textarea name="descripcion_meta" id="edit_descripcion" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">10. Enlace al Documento (URL)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-link"></i></span>
                                <input type="url" name="edit_url" id="url_documento_editar"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold">
                        <i class="fas fa-sync-alt me-1"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
