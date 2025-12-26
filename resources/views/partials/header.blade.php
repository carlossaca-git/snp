{{-- resources/views/partials/header.blade.php --}}
<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    {{-- Nombre del Sistema / Logo --}}
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6" href="{{ route('dashboard') }}">
        Sistema de Planificación
    </a>

    {{-- Botón para móviles (Hamburguesa) --}}
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button"
        data-bs-toggle="collapse" data-bs-target="#sidebarMenu"
        aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    {{-- Barra de búsqueda (opcional) --}}
    <input class="form-control form-control-dark w-100 rounded-0 border-0" type="text" placeholder="Buscar en el sistema..." aria-label="Search">

    {{-- Menú de Usuario y Salida --}}
    <div class="navbar-nav">
        <div class="nav-item text-nowrap d-flex align-items-center">
            {{-- Nombre del usuario logueado --}}
            <span class="text-white me-3 d-none d-lg-inline">
                {{ Auth::user()->name }}
            </span>

            {{-- Formulario de Cierre de Sesión (Importante por seguridad en Laravel) --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link px-3 bg-dark border-0 text-white hover:text-gray-300">
                    <span data-feather="log-out"></span> Salir
                </button>
            </form>
        </div>
    </div>
</header>
