<div class="modal fade" id="modalEditarOds" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Editar ODS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEditarOds" action="" method="POST">
                @csrf
                @method('PUT')

                {{-- INPUT OCULTO PARA EL Id--}}
                <input type="hidden" name="id_temp" id="edit_id_temp" value="{{ old('id_temp') }}">

                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Código</label>
                            <input type="text" class="form-control @error('codigo') is-invalid @enderror"
                                id="edit_codigo" name="codigo" value="{{ old('codigo') }}" required>
                            @error('codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-9">
                            <label class="form-label fw-bold small">Nombre del Objetivo</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                id="edit_nombre" name="nombre" value="{{ old('nombre') }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-5">
                            <label class="form-label fw-bold small">Pilar / Eje</label>
                            <select class="form-select @error('pilar') is-invalid @enderror" id="edit_pilar"
                                name="pilar">
                                <option value="" selected disabled>Seleccione una opción...</option>

                                <option value="Social" {{ old('pilar') == 'Social' ? 'selected' : '' }}>Personas
                                    (Social)</option>
                                <option value="Ambiental" {{ old('pilar') == 'Ambiental' ? 'selected' : '' }}>Planeta
                                    (Ambiental)</option>
                                <option value="Económico" {{ old('pilar') == 'Económico' ? 'selected' : '' }}>
                                    Prosperidad (Económico)</option>
                                <option value="Paz" {{ old('pilar') == 'Paz' ? 'selected' : '' }}>Paz (Justicia)
                                </option>
                                <option value="Alianzas" {{ old('pilar') == 'Alianzas' ? 'selected' : '' }}>Alianzas
                                    (Transversal)</option>
                            </select>
                            @error('pilar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Color</label>
                            <input type="color"
                                class="form-control form-control-color w-100 @error('color_hex') is-invalid @enderror"
                                id="edit_color" name="color_hex" value="{{ old('color_hex') }}">
                            @error('color_hex')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Estado</label>
                            <select class="form-select @error('estado') is-invalid @enderror" id="edit_estado"
                                name="estado">
                                <option value="">-- Seleccione estado --</option>
                                <option value="1" {{ old('estado') == '1' ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ old('estado') == '0' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" id="edit_descripcion" name="descripcion"
                            rows="3">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-edit me-1"></i> Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
