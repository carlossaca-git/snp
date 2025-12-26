{{-- resources/views/partials/sidebar.blade.php --}}
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar shadow-sm">
    <div class="position-sticky pt-3 sidebar-sticky">

        {{-- Grupo de Navegación Principal --}}
        <ul class="nav flex-column">

            {{-- Inicio: Administración --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') && !request('seccion') ? 'active fw-bold text-primary' : '' }}"
                    aria-current="page" href="{{ route('dashboard') }}">
                    <span data-feather="home" class="align-text-bottom"></span>
                    Administración
                </a>
            </li>


            {{-- 1. GESTIÓN DE USUARIOS (DESPLEGABLE) --}}
            {{-- x-data inicia el estado. 'abierto' será true si ya estamos en una sección de usuarios --}}
            <li class="nav-item" x-data="{ abierto: {{ request('seccion') ? 'true' : 'false' }} }">
                {{-- Botón Principal que dispara el despliegue --}}
                <a class="nav-link d-flex justify-content-between align-items-center cursor-pointer"
                    @click.prevent="abierto = !abierto" style="cursor: pointer;">
                    <div>
                        <span data-feather="users" class="align-text-bottom"></span>
                        Gestión de Usuarios
                    </div>
                    {{-- Icono de flecha que rota --}}
                    <span :class="abierto ? 'rotate-180' : ''" class="transition-transform duration-200">
                        <svg xmlns="www.w3.org" width="16" height="16" fill="currentColor"
                            class="bi bi-chevron-down" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z" />
                        </svg>
                    </span>
                </a>

                {{-- Submenú desplegable --}}
                <ul x-show="abierto" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    class="nav flex-column ms-3 border-start" style="display: none;">
                    @auth
                        @if (auth()->user()->roles->contains('nombre_rol', 'ADMIN_TI'))

                                <div class="">
                                    <!--Este mensaje solo es visible para el Administrador.-->
                                </div>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.users.create') ? 'active fw-bold text-primary' : '' }}"
                                        href="{{ route('admin.users.create') }}">
                                        <span data-feather="user-plus" class="align-text-bottom"></span>
                                        {{ __('Registrar Usuario') }}
                                    </a>
                                </li>

                        @endif
                    @endauth
                    {{-- Enlace para Listado --}}
                    <li class="nav-item">
                        <a class="nav-link  {{ request()->routeIs('admin.users.index') ? 'active fw-bold' : '' }}"
                            href="{{ route('admin.users.index') }}">
                            <span data-feather="list" class="align-text-bottom"></span>
                            Ver Listado
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Resto de items --}}
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <span data-feather="briefcase" class="align-text-bottom"></span>
                    Instituciones
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#">
                    <span data-feather="layers" class="align-text-bottom"></span>
                    Planes y Programas
                </a>
            </li>
        </ul>

        {{-- Sección de Reportes --}}
        <h6
            class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
            <span>Reportes Guardados</span>
        </h6>

        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <span data-feather="file-text" class="align-text-bottom"></span>
                    Resumen Mensual
                </a>
            </li>
        </ul>
    </div>
</nav>

{{-- Estilo extra opcional para la rotación de la flecha --}}
<style>
    .rotate-180 {
        transform: rotate(180deg);
    }

    .transition-transform {
        transition: transform 0.2s ease-in-out;
    }
</style>
