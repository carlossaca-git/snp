<div class="modal fade" id="modalCrearOds" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Registrar Nuevo ODS</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form action="{{ route('catalogos.ods.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Código</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">ODS-</span>
                                <input type="number" name="codigo" class="form-control" placeholder="Ej: 1"
                                    required min="1">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small">Nombre del Objetivo</label>
                            <input type="text" class="form-control" name="nombre"
                                placeholder="Ej: Fin de la Pobreza" required>
                        </div>
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <label class="form-label fw-bold small">Pilar / Eje</label>
                            <select class="form-select @error('pilar') is-invalid @enderror" name="pilar">
                                <option value="" selected disabled>Seleccione una opción...</option>

                                {{-- Los 5 pilares de la Agenda 2030 --}}
                                <option value="Personas" {{ old('pilar') == 'Personas' ? 'selected' : '' }}>Personas
                                    (Social)</option>
                                <option value="Planeta" {{ old('pilar') == 'Planeta' ? 'selected' : '' }}>Planeta
                                    (Ambiental)</option>
                                <option value="Prosperidad" {{ old('pilar') == 'Prosperidad' ? 'selected' : '' }}>
                                    Prosperidad (Económico)</option>
                                <option value="Paz" {{ old('pilar') == 'Paz' ? 'selected' : '' }}>Paz (Institucional)
                                </option>
                                <option value="Alianzas" {{ old('pilar') == 'Alianzas' ? 'selected' : '' }}>Alianzas
                                    (Transversal)</option>
                            </select>
                            @error('pilar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Color Identificativo</label>
                            <input type="color" class="form-control form-control-color w-100" name="color_hex"
                                value="#563d7c" title="Elige el color del ODS">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Estado</label>
                            <select class="form-select" name="estado">
                                <option value="1" selected>Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>

                    {{--  Descripción --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Descripción Detallada</label>
                        <textarea class="form-control" name="descripcion" rows="3"></textarea>
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Guardar ODS</button>
                </div>
            </form>
        </div>
    </div>
</div>
