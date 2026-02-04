<div class="modal fade" id="modalCreatePnd" tabindex="-1" aria-labelledby="modalCreatePndLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalCreatePndLabel">
                    <i class="fas fa-plus-circle me-2"></i>Registrar Nuevo Objetivo Nacional
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="{{ route('catalogos.objetivos.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Código del Objetivo</label>
                            <input type="text" name="codigo_objetivo" class="form-control" placeholder="Ej: OBJ-01"
                                required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Eje Estratégico</label>
                            <select name="id_eje" class="form-select" required>
                                <option value="" selected disabled>Seleccione un eje...</option>
                                @foreach ($relEje as $eje)
                                    <option value="{{ $eje->id_eje }}">{{ $eje->nombre_eje }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción del Objetivo</label>
                        <textarea name="descripcion_objetivo" class="form-control" rows="4"
                            placeholder="Escriba la descripción detallada del objetivo..." required></textarea>
                    </div>

                    {{-- El estado por defecto será Activo --}}
                    <input type="hidden" name="estado" value="1">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar Objetivo</button>
                </div>
            </form>
        </div>
    </div>
</div>
