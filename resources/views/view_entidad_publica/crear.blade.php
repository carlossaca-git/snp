@extends('layouts.app')
@section('content')
    <div class="card" style="width: 18rem;">

        <div class="card-body">
            <h5 class="card-title">Anadir entidad</h5>
            <form method="POST" action="{{ route('view_entidad_publica.store') }}">
                @csrf
                <label for="subsector">Subsector</label>
                <select  name="id_subsector" id="subsector" aria-label="Default select example" class="form-select" required>
                    <option value="">Seleccione Subsector</option>
                    @foreach ($datossubsector as $subsector)
                        <option value="{{ $subsector->id_subsector }}" {{ old('id_subsector') == $subsector->id ? 'selected' : '' }}> {{ $subsector->nombre_subsector }}</option>
                    @endforeach
                </select>
                <div class="form-group mb-3">
                    <label for="codigo_oficial">Codigo Oficial</label>
                    <input type="text" class="form-control" id="codigo_oficial" name="codigo_oficial">

                </div>
                <div class="form-group mb-3">
                    <label for="nom_organizacion">Nombre de la institucion</label>
                    <input type="text" class="form-control" id="nom_organizacion" name="nom_organizacion">
                </div>

                <div class="form-group mb-3">
                    <label for="nivel_gobierno">Nivel de Gobierno</label>
                    <input type="text" class="form-control" id="nivel_gobierno" name="nivel_gobierno">
                </div>
                <div class="form-group mb-3">
                    <label for="estado">Estado</label>
                    <input type="text" class="form-control" id="estado" name="estado">
                </div>
                <button type="submit" class="form-control">Guardar</button>
            </form>
            @if (session('success'))
                <div class="alert alert-success mt-3" role="alert">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
@endsection
