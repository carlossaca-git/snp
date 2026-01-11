<div class="modal fade" id="modalCrearMeta" tabindex="-1" aria-labelledby="modalCrearMetaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalCrearMetaLabel text-white">
                    <i class="fas fa-bullseye me-2"></i> Registrar Nueva Meta Nacional
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form action="{{ route('catalogos.metas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">1. Objetivo Nacional Vinculado</label>
                            <select name="id_objetivo_nacional" id="id_objetivo_crear"
                                class="form-select @error('id_objetivo') is-invalid @enderror" required>
                                <option value="" selected disabled>-- Seleccione el Objetivo al que pertenece esta
                                    meta --</option>
                                @foreach ($objetivos as $obj)
                                    <option value="{{ $obj->id_objetivo_nacional }}"
                                        {{ old('id_objetivo') == $obj->id_objetivo_nacional ? 'selected' : '' }}>
                                        {{ $obj->codigo_objetivo }}-{{ Str::limit($obj->descripcion_objetivo, 80) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_objetivo_nacional')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- COdigo identificativo de la menta --}}
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">2. Código / Identificador de la Meta</label>
                            <input type="text" name="codigo_meta" id="codigo_meta_crear" class="form-control"
                                placeholder="Ej: Meta 1.1 o M.1" value="{{ old('codigo_meta') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">3. Estado</label>
                            <select name="estado" id="estado_crear" class="form-select">
                                <option value="1" selected>Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                        {{-- Unidad de Medida e Indicador --}}

                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">4. Nombre del Indicador</label>
                            <input type="text" name="nombre_indicador" class="form-control"
                                placeholder="Ej: Tasa de desempleo juvenil" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">5. Unidad de Medida</label>
                            <select name="unidad_medida" class="form-select" required>
                                <option value="" disabled selected>-- Seleccione --</option>
                                <option value="Porcentaje">Porcentaje (%)</option>
                                <option value="Tasa">Tasa</option>
                                <option value="Indice">Índice</option>
                                <option value="Número Absoluto">Número Absoluto</option>
                                <option value="USD">Dólares (USD)</option>
                            </select>
                        </div>
                        {{-- Cuantificación --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-primary">6. Línea Base (Valor Inicial)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-primary"><i
                                        class="fas fa-history"></i></span>
                                <input type="text" name="linea_base" class="form-control" placeholder="Ej: 15.20"
                                    required>
                            </div>
                            <small class="text-muted">Valor medido al inicio del periodo.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-success">7. Meta Valor (Objetivo Final)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success"><i
                                        class="fas fa-flag-checkered"></i></span>
                                <input type="text" name="meta_valor" class="form-control" placeholder="Ej: 10.00"
                                    required>
                            </div>
                            <small class="text-muted">Valor que se desea alcanzar.</small>
                        </div>
                        {{-- Definicion de la meta --}}
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">8. Definición de la Meta</label>
                            <textarea name="nombre_meta" id="nombre_meta_crear" class="form-control @error('nombre_meta') is-invalid @enderror"
                                rows="3" placeholder="Redacte aquí el compromiso cuantificable..." required>{{ old('nombre_meta') }}</textarea>
                            @error('nombre_meta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">9. Descripción Técnica (Opcional)</label>
                            <textarea name="descripcion_meta" id="descripcion_meta_crear" class="form-control" rows="2"
                                placeholder="Detalles adicionales sobre la meta...">{{ old('descripcion_meta') }}</textarea>
                        </div>
                        {{-- Documentos de respaldo --}}
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Enlace al Documento de Soporte (URL)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-link"></i></span>
                                <input type="url" name="url_documento" id="url_documento_crear"
                                    class="form-control" placeholder="https://ejemplo.com/archivo.pdf"
                                    value="{{ old('url_documento') }}">
                            </div>
                            <small class="text-muted">Pega el link del PDF guardado en la carpeta del
                                repositorio.</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success d-inline-flex align-items-center">
                        <i class="fas fa-save me-1" data-feather="save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
