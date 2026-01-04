<div class="position-sticky pt-3 sidebar-sticky hover-scroll"
    style="height: calc(100vh - 56px); overflow-y: auto; top: 56px;">

    {{-- 1. SECCIÓN DE USUARIO (Info de Sesión) --}}
    <div class="user-info nav-link border-bottom mb-3 pb-3">
        <div class="d-flex align-items-center mb-2">
            {{-- CAMBIO: bg-primary -> bg-secondary (Gris en lugar de azul) --}}
            <div class="bg-secondary text-white rounded-circle p-2 me-2">
                <span data-feather="user"></span>
            </div>
            <div>
                <span class="text-muted small text-uppercase d-block" style="font-size: 0.7rem;">Sesión activa</span>
                <span class="fw-bold text-dark">{{ auth()->user()->usuario ?? 'Usuario' }}</span>
            </div>
        </div>
        <div class="small text-muted">
            <span class="fw-bold">Rol:</span>
            @foreach (auth()->user()->roles ?? [] as $rol)
                <span class="badge bg-secondary">{{ $rol->nombre_rol }}</span>
            @endforeach
        </div>
    </div>

    <li class="nav-item d-md-none">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a class="nav-link text-danger fw-bold" href="{{ route('logout') }}"
                onclick="event.preventDefault(); this.closest('form').submit();">
                <span data-feather="log-out" class="align-text-bottom"></span>
                Cerrar Sesión
            </a>
        </form>

    </li>
    <hr class="my-3 d-md-none">
    <li class="nav-item">
        <ul class="nav flex-column">

            {{-- DASHBOARD --}}
            <li class="nav-item">
                {{-- CAMBIO: Quitamos 'text-primary' y ponemos 'text-dark' para el activo --}}
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-bold text-dark' : 'text-dark' }}"
                    href="{{ route('dashboard') }}">
                    <span data-feather="home" class="align-text-bottom"></span>
                    Panel Principal
                </a>
            </li>

            {{-- ========================================================= --}}
            {{-- MÓDULO 2: GESTIÓN DE INVERSIÓN (CORE DEL NEGOCIO)         --}}
            {{-- ========================================================= --}}
            {{-- CAMBIO: Quitamos 'text-primary' del título, usamos 'text-secondary' --}}
            <h6
                class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-secondary text-uppercase fw-bold">
                <span>Gestión de Inversión</span>
                <span data-feather="dollar-sign" class="text-muted"></span>
            </h6>

            {{-- 1. BANCO DE PROYECTOS --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('inversion.proyectos.*') ? 'active fw-bold text-dark' : 'text-dark' }}"
                    href="{{ route('inversion.proyectos.index') }}">
                    <span data-feather="briefcase"></span>
                    Banco de Proyectos
                    <span class="badge bg-danger rounded-pill ms-2">Borrador</span>
                </a>
            </li>

            {{-- 2. MENÚ DESPLEGABLE: PLANIFICACIÓN DE INVERSIÓN --}}
            <li class="nav-item" x-data="{ abierto: {{ request()->routeIs('inversion.planes.*') || request()->routeIs('inversion.programas.*') ? 'true' : 'false' }} }">
                <a class="nav-link d-flex justify-content-between align-items-center cursor-pointer text-muted"
                    @click.prevent="abierto = !abierto">
                    <div class="text-dark"> {{-- Aseguramos texto oscuro --}}
                        <span data-feather="layers" class="align-text-bottom"></span>
                        Cartera de Inversión
                    </div>
                    <span :class="abierto ? 'rotate-180' : ''" class="transition-transform duration-200 text-dark">
                        <svg xmlns="www.w3.org/2000/svg" width="12" height="12" fill="currentColor"
                            class="bi bi-chevron-down" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z" />
                        </svg>
                    </span>
                </a>

                <ul x-show="abierto" x-transition class="nav flex-column ms-3 border-start" style="display: none;">
                    {{-- Planes (PAI) --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('inversion.planes.*') ? 'active fw-bold text-dark' : 'text-secondary' }}"
                            href="{{-- route('inversion.planes.index') --}} #">
                            <span data-feather="calendar" class="small"></span> Planes Anuales (PAI)
                        </a>
                    </li>
                    {{-- Programas --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('inversion.programas.*') ? 'active fw-bold text-dark' : 'text-secondary' }}"
                            href="{{ route('inversion.programas.index') }}">
                            <span data-feather="grid" class="small"></span> Programas
                        </a>
                    </li>
                </ul>
            </li>

            {{-- ========================================================= --}}
            {{-- MÓDULO 1: PLANIFICACIÓN ESTRATÉGICA                       --}}
            {{-- ========================================================= --}}
            <h6
                class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-secondary text-uppercase fw-bold">
                <span>Estratégico</span>
            </h6>

            {{-- Estructura Institucional --}}
            <li class="nav-item" x-data="{ abierto: {{ request()->routeIs('estrategico.organizaciones.*') ? 'true' : 'false' }} }">
                <a class="nav-link d-flex justify-content-between align-items-center cursor-pointer {{ request()->routeIs('estrategico.organizaciones.*') ? 'active fw-bold text-dark' : 'text-dark' }}"
                    @click.prevent="abierto = !abierto">
                    <div>
                        <span data-feather="book" class="align-text-bottom"></span>
                        Instituciones
                    </div>
                    <span :class="abierto ? 'rotate-180' : ''" class="transition-transform duration-200">
                        <svg xmlns="www.w3.org/2000/svg" width="12" height="12" fill="currentColor"
                            class="bi bi-chevron-down" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z" />
                        </svg>
                    </span>
                </a>

                <ul x-show="abierto" x-transition class="nav flex-column ms-3 border-start" style="display: none;">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('estrategico.organizaciones.index') ? 'active fw-bold text-dark' : 'text-secondary' }}"
                            href="{{ route('estrategico.organizaciones.index') }}">
                            <span data-feather="list" class="small"></span> Directorio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('estrategico.organizaciones.create') ? 'active fw-bold text-dark' : 'text-secondary' }}"
                            href="{{ route('estrategico.organizaciones.create') }}">
                            <span data-feather="plus" class="small"></span> Nueva Entidad
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Alineación PND/ODS --}}
            <li class="nav-item">
                <a class="nav-link text-dark" href="#">
                    <span data-feather="target" class="align-text-bottom"></span>
                    Obj. Nacionales (PND)
                </a>
            </li>

            {{-- ========================================================= --}}
            {{-- MÓDULO TRANSVERSAL: SEGURIDAD                             --}}
            {{-- ========================================================= --}}
            @if (auth()->user()->roles->contains('nombre_rol', 'SUPERADMIN') || true)
                <h6
                    class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-danger text-uppercase fw-bold">
                    <span>Admin y Seguridad</span>
                </h6>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('administracion.usuarios.*') ? 'active fw-bold text-dark' : 'text-dark' }}"
                        href="{{ route('administracion.usuarios.index') }}">
                        <span data-feather="users" class="align-text-bottom"></span>
                        Usuarios y Roles
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-dark" href="#">
                        <span data-feather="activity" class="align-text-bottom"></span>
                        Auditoría (Logs)
                    </a>
                </li>
            @endif

            {{-- REPORTES --}}
            <h6
                class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase fw-bold">
                <span>Salidas</span>
            </h6>
            <li class="nav-item">
                <a class="nav-link text-dark" href="#">
                    <span data-feather="file-text" class="align-text-bottom"></span>
                    Reportes PDF/Excel
                </a>
            </li>
        </ul>

</div>
