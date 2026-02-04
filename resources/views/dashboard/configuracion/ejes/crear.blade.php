<div class="modal fade" id="modalCreateEje" tabindex="-1" aria-labelledby="modalCreateEjeLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalCreateEjeLabel">
                    <i class="fas fa-plus-circle me-2"></i>Nuevo Eje Estratégico
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form action="{{ route('catalogos.ejes.store') }}" method="POST" id="formCreateEje">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id_plan" value="{{ $planActivo->id_plan ?? '' }}">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Plan Nacional Vigente</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-university"></i></span>
                            <input type="text" class="form-control bg-light"
                                value="{{ $planActivo->nombre ?? 'ATENCIÓN: No hay un plan activo configurado' }}"
                                readonly tabindex="-1">
                        </div>
                        @if (!$planActivo)
                            <small class="text-danger">Debe activar un Plan Nacional antes de crear ejes.</small>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Eje</label>
                        <input type="text" name="nombre_eje" class="form-control" placeholder="Ej: Eje Social"
                            required value="{{ old('nombre_eje') }}">
                    </div>
                    {{-- Descripción --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion_eje" class="form-control" rows="3" placeholder="Breve descripción...">{{ old('descripcion_eje') }}</textarea>
                    </div>

                    {{-- URL Documento --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">URL del Documento (PND)</label>
                        <input type="url" name="url_documento" class="form-control"
                            placeholder="https://ejemplo.com/archivo.pdf" value="{{ old('url_documento') }}">
                    </div>

                    {{-- Estado --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="1" selected>Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    @if (!$planActivo)
                        <div class="alert alert-warning w-100 mb-0 d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>
                                <strong>Acción bloqueada:</strong> No existe un Plan Nacional activo.
                                Por favor, active uno en el catálogo de Planes.
                            </div>
                        </div>
                    @endif

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    {{-- El botón se deshabilita si no hay plan activo --}}
                    <button type="submit" class="btn btn-primary" {{ !$planActivo ? 'disabled' : '' }}>
                        <i class="fas fa-save me-1"></i> Guardar Eje
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
