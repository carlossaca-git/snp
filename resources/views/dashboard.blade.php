@extends('layouts.app')
@section('title', 'Administracion')


@section('content')



    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
                <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                <span data-feather="calendar"></span>
                This week
            </button>
        </div>
    </div>

     <!-- -->
            @if(request('seccion') == 'registrar')
                <div class="card shadow">
                    <div class="card-body">
                        @include('auth.register')
                    </div>
                </div>

            @elseif(request('seccion') == 'listar')


                @if(isset($users))
                    {{-- Si la variable existe, cargamos tu archivo listado.blade.php --}}
                    @include('users.index')
                @else
                    {{-- Si ves este error, el problema está en tu archivo routes/web.php --}}
                    <div class="alert alert-danger shadow-sm border-start border-danger border-5">
                        <h4 class="alert-heading">⚠️ Error de Conexión de Datos</h4>
                        <p>La variable <strong>$users</strong> no llegó a esta vista.</p>
                        <hr>
                        <p class="mb-0"><strong>Solución:</strong> Asegúrate de que la ruta <code>/dashboard</code> en tu archivo <code>routes/web.php</code> apunte al método <code>index</code> de tu <code>UserController</code>.</p>
                    </div>
                @endif

            @else
                <!-- Contenido por defecto cuando entras al Dashboard por primera vez -->
                <div class="p-5 mb-4 bg-light rounded-3">
                    <div class="container-fluid py-5">
                        <h1 class="display-5 fw-bold">Bienvenido</h1>
                        <p class="col-md-8 fs-4">Seleccione una opción del menú lateral para comenzar.</p>
                    </div>
                </div>
            @endif

@endsection


@section('scripts')

    <script>
        // Aquí iría el código de tu gráfico si decides recuperarlo
    </script>
@endsection
