<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Formulación de Nuevo Programa de Inversión</h1>
</div>

<form action="{{ route('usuario.programas.store') }}" method="POST">
    @csrf

    <div class="row g-3">
        <div class="col-md-6">
            <label for="nombre_programa" class="form-label">Nombre del Programa</label>
            <input type="text" class="form-control" id="nombre_programa" name="nombre_programa" required>
        </div>
        <div class="col-md-3">
            <label for="codigo_cup" class="form-label">Código CUP</label>
            <input type="text" class="form-control" id="codigo_cup" name="codigo_cup" required>
        </div>
        <div class="col-md-3">
            <label for="monto_planificado" class="form-label">Monto Planificado ($)</label>
            <input type="number" step="0.01" class="form-control" id="monto_planificado" name="monto_planificado" required>
        </div>
        <div class="col-12">
            <label for="descripcion" class="form-label">Descripción Detallada</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-success">
            <span data-feather="plus-circle"></span> Registrar Programa
        </button>
        <a href="{{ route('usuario.programas.index') }}" class="btn btn-secondary">Cancelar</a>
    </div>
</form>
