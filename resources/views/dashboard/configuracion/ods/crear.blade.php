<form action="{{ isset($ods_item) ? route('config.ods.update', $ods_item->id) : route('config.ods.store') }}" method="POST">
    @csrf
    @if(isset($ods_item)) @method('PUT') @endif

    <div class="row">
        <div class="col-md-2 mb-3">
            <label class="form-label fw-bold">Número ODS</label>
            <input type="number" name="numero" class="form-control" value="{{ old('numero', $ods_item->numero ?? '') }}" required>
        </div>
        <div class="col-md-10 mb-3">
            <label class="form-label fw-bold">Nombre del Objetivo</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $ods_item->nombre ?? '') }}" required>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Descripción / Meta General</label>
        <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $ods_item->descripcion ?? '') }}</textarea>
    </div>

    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-success px-5">
            <span data-feather="save"></span> {{ isset($ods_item) ? 'Actualizar' : 'Guardar' }}
        </button>
    </div>
</form>
