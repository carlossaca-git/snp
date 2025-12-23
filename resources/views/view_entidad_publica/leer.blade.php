@extends('layouts.app')
@section('content')
    <h1>Lista de Entidades</h1>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Codigo Ofical</th>
                <th scope="col">Nombre Institucion</th>
                <th scope="col">Subsector</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach ( $entidadpublica as $entidad)
                    <td>{{ $entidad->codigo_oficial }}</td>
                    <td>{{ $entidad->nombre_institucion }}</td>
                    <td>{{ $entidad->id_subsector }}</td>
                <td><button class="btn btn-primary" data-target="#modal{{$entidad->id_institucion}}">Editar</button>
                @include('view_entidad_publica.actualizar', ['entidad' => $entidad])
                </td>

            </tr>
            @endforeach

        </tbody>
    </table>
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
@endsection
