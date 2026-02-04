{{-- MODAL PARA VINCULAR A LOS ODS --}}
<div class="modal fade" id="modalVincularOds" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <div>
                    <h5 class="modal-title"><i class="fas fa-link me-2"></i>Vincular ODS</h5>
                    Meta nacional: <span id="nombre_meta_ods_display" class="fw-light"></span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('catalogos.metas.vincular') }}" method="POST">
                @csrf
                <input type="hidden" name="id_meta_nacional" id="id_meta_ods_input">
                <div class="modal-body">
                    <p class="text-muted mb-4">Seleccione los Objetivos de Desarrollo Sostenible que se alinean con
                        esta meta nacional:</p>
                    <div class="row g-3">
                        @foreach ($ods as $ods)
                            <div class="col-md-4 col-sm-6">
                                <div class="position-relative">
                                    <input type="checkbox" name="ods_ids[]" value="{{ $ods->id_ods }}"
                                        id="ods_{{ $ods->id_ods }}" class="btn-check shadow-none">

                                    <label
                                        class="btn ods-card w-100 p-2 text-start d-flex align-items-center border shadow-sm"
                                        for="ods_{{ $ods->id_ods }}" style="height: 70px;">

                                        <span class="badge me-2 p-2 shadow-sm"
                                            style="background-color: {{ $ods->color_hex }}; min-width: 40px; font-size: 0.8rem;">
                                            {{ $ods->codigo }}
                                        </span>

                                        <small class="text-dark fw-bold" style="font-size: 0.7rem; line-height: 1.2;">
                                            {{ $ods->nombre }}
                                        </small>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    @if (Auth::user()->tienePermiso('metas_pnd.gestionar'))
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Guardar
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
