<div class="modal fade" id="modalMarcoLogico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('inversion.proyectos.marco-logico.store') }}" method="POST" id="formMarcoLogico">
            @csrf

            <input type="hidden" name="_method" id="metodo_form" value="POST">
            <input type="hidden" name="proyecto_id" value="{{ $proyecto->id }}">
            <input type="hidden" name="nivel" id="nivel_input">
            <input type="hidden" name="padre_id" id="padre_id_input">
            <input type="hidden" name="id" id="id_input">

            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalTitulo">Nuevo Elemento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Descripción / Nombre --}}
                        <div class="col-12">
                            <label class="form-label fw-bold">Descripción / Resumen Narrativo</label>
                            <textarea name="resumen_narrativo" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Nombre indicador</label>
                            <input name="descripcion_indicador" class="form-control" rows="2" required>
                        </div>
                        {{-- Vigencia --}}
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Vigencia (Inicio/Fin)</label>
                                <input type="date" name="fecha_inicio" class="form-control">
                                <br>
                                <input type="date" name="fecha_fin" class="form-control">
                            </div>
                            {{-- Presupuesto --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-success">Presupuesto ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="monto" class="form-control"
                                        placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-primary">Peso (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" max="100" name="ponderacion"
                                        class="form-control" placeholder="Ej: 20">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        {{-- Indicador y Unidad --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Unidad de Medida</label>
                            <input type="text" name="unidad_medida" class="form-control"
                                placeholder="Ej: Porcentaje, Km, Unidades">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Medio de verificacion</label>
                            <input type="text" name="medio_verificacion" class="form-control"
                                placeholder="Fuente de Verificación">
                        </div>

                        {{-- Metas Anuales (Se inyecta con JS) --}}
                        <div class="col-12 mt-3">
                            <label class="form-label fw-bold border-bottom d-block mb-2">
                                Programación Anual de Metas
                                <small class="text-muted fw-normal">(Ingrese las cantidades por año)</small>
                            </label>

                            {{-- AQUÍ JAVASCRIPT DIBUJARÁ LOS INPUTS --}}
                            <div id="contenedor_metas_anuales" class="row g-2">
                            </div>
                        </div>

                        {{-- Supuestos (Opcional) --}}
                        <div class="col-12">
                            <label class="form-label fw-bold">Supuestos (Riesgos/Condiciones)</label>
                            <input type="text" name="supuestos" class="form-control"
                                placeholder="Condiciones externas necesarias...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Datos</button>
                </div>
            </div>
        </form>
    </div>
</div>
