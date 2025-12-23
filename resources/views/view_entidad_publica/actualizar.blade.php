<div class="modal fade" id="modal{{ $entidadpublica->id_institucion }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Actualizar</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('entidades.update', $entidadpublica) }}">
                @csrf
                @method('PUT')
                <div form-group mb-3>
                    <label for="nombre">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $entidadpublica->nombre }}">

                </div>
                <div form-group mb-3>
                    <label for="descripcion">Descripcion</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion" value="{{ $entidadpublica->descripcion }}">
                </div>
                <div form-group mb-3>
                    <label for="sector">Sector</label>
                    <input type="text" class="form-control" id="sector" name="sector" value="{{ $entidadpublica->sector }}">
                </div>
                <button type="submit" class="form-control">Actualizar</button>
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
