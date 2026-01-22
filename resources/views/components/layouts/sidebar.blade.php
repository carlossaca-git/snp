{{-- ESTILOS --}}
<style>
    .nav-link {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        font-size: 0.85rem;
        color: #333;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }

    .nav-link svg.feather {
        margin-right: 12px;
        width: 18px;
        height: 18px;
    }

    .menu-arrow-svg {
        margin-left: auto;
        width: 16px;
        height: 16px;
        transition: transform 0.25s ease;
        opacity: 0.5;
    }

    .rotate-90 {
        transform: rotate(90deg);
        opacity: 1;
    }

    .sidebar-heading {
        font-size: 0.7rem !important;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 1.5rem;
        margin-bottom: 0.5rem;
        padding-left: 15px;
        color: #adb5bd;
    }

    .nav-link:hover {
        background-color: #f8f9fa;
        color: #4e73df;
    }

    .nav-link.active {
        background-color: #eaecf4;
        color: #4e73df;
        font-weight: bold;
    }

    .submenu-link {
        padding-left: 45px !important;
        font-size: 0.8rem;
    }
</style>
{{-- LOGICA DE APERTURA AUTOMÁTICA --}}
@php
    $menuAbierto = '';
    // Esta lógica mantiene el menú desplegado según la ruta actual
    if (request()->routeIs('administracion.auditoria.*')) {
        $menuAbierto = 'auditoria';
    } elseif (request()->routeIs('administracion.*')) {
        $menuAbierto = 'admin';
    } elseif (request()->routeIs('catalogos.*')) {
        $menuAbierto = 'normativa';
    } elseif (request()->routeIs('estrategico.*') || request()->routeIs('institucional.*')) {
        $menuAbierto = 'estrategico';
    } elseif (request()->routeIs('inversion.*')) {
        $menuAbierto = 'inversion';
    }
    //Seguridad para menu
    $puedeVerNormativa = auth()
        ->user()
        ->hasAnyPermission(['pnd.ver', 'ejes.ver', 'objetivos.ver', 'ods.ver', 'metas_pnd.ver', 'indicadores.ver']);
@endphp



<div class="position-sticky pt-3 sidebar-sticky hover-scroll"
    style="height: calc(100vh - 56px); overflow-y: auto; top: 56px;" x-data="{ activeMenu: '{{ $menuAbierto }}' }">

    {{-- ESTILOS CSS --}}


    {{-- INFO DE USUARIO --}}
    <div class="user-info border-bottom mb-2 pb-2 px-3">
        <div class="d-flex align-items-center">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                style="width: 36px; height: 36px;">
                <span data-feather="user"></span>
            </div>
            <div class="lh-1 overflow-hidden">
                <span class="text-muted text-uppercase d-block fw-bold text-truncate" style="font-size: 0.65rem;">
                    {{ auth()->user()->organizacion->nom_organizacion ?? 'SIPeIP' }}
                </span>
                <span class="fw-bold text-dark text-truncate d-block" style="font-size: 0.85rem;">
                    {{ auth()->user()->usuario ?? 'Usuario' }}
                </span>
                <span class="badge bg-secondary mt-1" style="font-size: 0.6rem;">
                    {{ auth()->user()->roles->first()->nombre_corto ?? 'Sin Rol' }}
                </span>
            </div>
        </div>
    </div>

    <ul class="nav flex-column">

        {{--    INICIO (Visible para todos)                            --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <span data-feather="home"></span> <span>Inicio</span>
            </a>
        </li>


        {{--  ADMINISTRACIÓN                                         --}}
        {{-- Se muestra si puedes gestionar usuarios O ver auditoría --}}
        @if (auth()->user()->tienePermiso('usuarios.gestionar') || auth()->user()->tienePermiso('auditoria.ver'))
            <div class="sidebar-heading">Administración</div>

            <li class="nav-item">
                <a class="nav-link" @click.prevent="activeMenu = (activeMenu === 'admin' ? '' : 'admin')"
                    :class="{ 'active': activeMenu === 'admin' }">
                    <span data-feather="settings"></span>
                    <span>Seguridad y Acceso</span>
                    <svg class="menu-arrow-svg" :class="{ 'rotate-90': activeMenu === 'admin' }" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>

                <ul x-show="activeMenu === 'admin'" x-collapse class="nav flex-column bg-light">
                    @if (auth()->user()->tienePermiso('usuarios.gestionar'))
                        <li class="nav-item"><a
                                class="nav-link submenu-link {{ request()->routeIs('administracion.usuarios.*') ? 'active text-primary' : 'text-secondary' }}"
                                href="{{ route('administracion.usuarios.index') }}">Usuarios</a></li>
                        <li class="nav-item"><a
                                class="nav-link submenu-link {{ request()->routeIs('administracion.roles.*') ? 'active text-primary' : 'text-secondary' }}"
                                href="{{ route('administracion.roles.index') }}">Roles y Permisos</a></li>
                    @endif
                </ul>
            </li>
        @endif


        {{--    NORMATIVA                                              --}}

        {{-- Usamos hasAnyPermissions de Spatie para preguntar --}}

        @if ($puedeVerNormativa)
            <div class="sidebar-heading">Normativa Nacional</div>

            <li class="nav-item">
                <a class="nav-link" @click.prevent="activeMenu = (activeMenu === 'normativa' ? '' : 'normativa')"
                    :class="{ 'active': activeMenu === 'normativa' }">
                    <span data-feather="book"></span>
                    <span>Catálogos PND/ODS</span>
                    <svg class="menu-arrow-svg" :class="{ 'rotate-90': activeMenu === 'normativa' }" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>

                <ul x-show="activeMenu === 'normativa'" x-collapse class="nav flex-column bg-light">
                    <li class="nav-item">
                        @can('pnd.ver')
                            <a class="nav-link submenu-link {{ request()->routeIs('catalogos.planes-nacionales*') ? 'active text-primary' : 'text-secondary' }}"
                                href="{{ route('catalogos.planes-nacionales.index') }}">Plan Nacional</a>
                        @endcan
                    </li>
                    <li class="nav-item">
                        @can('ejes.ver')
                            <a class="nav-link submenu-link {{ request()->routeIs('catalogos.ejes.*') ? 'active text-primary' : 'text-secondary' }}"
                                href="{{ route('catalogos.ejes.index') }}">Ejes Estratégicos</a>
                        @endcan
                    </li>
                    <li class="nav-item">
                        @can('objetivos.ver')
                            <a class="nav-link submenu-link {{ request()->routeIs('catalogos.pnd.*') ? 'active text-primary' : 'text-secondary' }}"
                                href="{{ route('catalogos.objetivos.index') }}">Objetivos Nacionales</a>
                        @endcan
                    </li>
                    <li class="nav-item">
                        @can('ods.ver')
                            <a class="nav-link submenu-link {{ request()->routeIs('catalogos.ods.*') ? 'active text-primary' : 'text-secondary' }}"
                                href="{{ route('catalogos.ods.index') }}">Agenda ODS 2030</a>
                        @endcan
                    </li>
                    <li class="nav-item">
                        @can('metas_pnd.ver')
                            <a class="nav-link submenu-link {{ request()->routeIs('catalogos.metas.*') ? 'active text-primary' : 'text-secondary' }}"
                                href="{{ route('catalogos.metas.index') }}">Banco de Metas</a>
                        @endcan
                    </li>
                    <li class="nav-item">
                        @can('indicadores.ver')
                            <a class="nav-link submenu-link {{ request()->routeIs('catalogos.indicadores.*') ? 'active text-primary' : 'text-secondary' }}"
                                href="{{ route('catalogos.indicadores.index') }}">Indicadores</a>
                        @endcan
                    </li>
                </ul>
            </li>
        @endif


        {{--    ESTRATEGIA (PEI)                                       --}}

        {{-- Lógica OR. Si puede editar organización O gestionar planificación --}}
        @if (auth()->user()->tienePermiso('organizacion.editar') || auth()->user()->tienePermiso('planificacion.gestionar'))
            <div class="sidebar-heading">Estrategia (PEI)</div>

            {{-- Ficha Institucional Solo si tiene permiso (organizacion.editar) --}}
            @if (auth()->user()->tienePermiso('organizacion.editar'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('institucional.*') ? 'active' : '' }}"
                        href="{{ route('institucional.organizaciones.index') }}">
                        <span data-feather="layout"></span> <span>Institución</span>
                    </a>
                </li>
            @endif

            {{-- Planificación Solo si tiene permiso (planificacion.gestionar) --}}
            @if (auth()->user()->tienePermiso('planificacion.gestionar'))
                <li class="nav-item">
                    <a class="nav-link"
                        @click.prevent="activeMenu = (activeMenu === 'estrategico' ? '' : 'estrategico')"
                        :class="{ 'active': activeMenu === 'estrategico' }">
                        <span data-feather="crosshair"></span>
                        <span>Planificación</span>
                        <svg class="menu-arrow-svg" :class="{ 'rotate-90': activeMenu === 'estrategico' }"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>

                    <ul x-show="activeMenu === 'estrategico'" x-collapse class="nav flex-column bg-light">
                        {{-- Planes Anuales --}}
                        <li class="nav-item"><a
                                class="nav-link submenu-link {{ request()->routeIs('inversion.planes.*') ? 'active text-primary' : 'text-secondary' }}"
                                href="{{ route('estrategico.planificacion.planes.index') }}">Planes Institucionales</a></li>
                        <li class="nav-item"><a
                                class="nav-link submenu-link {{ request()->routeIs('estrategico.objetivos.*') ? 'active text-primary' : 'text-secondary' }}"
                                href="{{ route('estrategico.objetivos.index') }}">Objetivos (OEI)</a></li>
                        <li class="nav-item"><a
                                class="nav-link submenu-link {{ request()->routeIs('estrategico.alineacion.*') ? 'active text-primary' : 'text-secondary' }}"
                                href="{{ route('estrategico.alineacion.gestionar', ['organizacion_id' => auth()->user()->id_organizacion ?? 0]) }}">Matriz
                                de Alineación</a></li>
                    </ul>
                </li>
            @endif
        @endif



        {{--    INVERSIÓN (PAI)                                        --}}

        @if (auth()->user()->tienePermiso('proyectos.ver'))
            <div class="sidebar-heading">Inversión (PAI)</div>

            <li class="nav-item">
                <a class="nav-link" @click.prevent="activeMenu = (activeMenu === 'inversion' ? '' : 'inversion')"
                    :class="{ 'active': activeMenu === 'inversion' }">
                    <span data-feather="briefcase"></span>
                    <span>Cartera Inversión</span>
                    <svg class="menu-arrow-svg" :class="{ 'rotate-90': activeMenu === 'inversion' }"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>

                <ul x-show="activeMenu === 'inversion'" x-collapse class="nav flex-column bg-light">

                    {{-- Programas --}}
                    <li class="nav-item"><a
                            class="nav-link submenu-link {{ request()->routeIs('inversion.programas.*') ? 'active text-primary' : 'text-secondary' }}"
                            href="{{ route('inversion.programas.index') }}">Programas (CUP)</a></li>

                    {{-- Proyectos --}}
                    <li class="nav-item"><a
                            class="nav-link submenu-link {{ request()->routeIs('inversion.proyectos.*') ? 'active text-primary' : 'text-secondary' }}"
                            href="{{ route('inversion.proyectos.index') }}">Proyectos</a></li>
                </ul>
            </li>
        @endif

        {{--    CONTROL                                                --}}

        @if (auth()->user()->tienePermiso('auditoria.ver'))
            <div class="sidebar-heading text-danger">Control</div>
            <li class="nav-item">
                <a class="nav-link text-danger"
                    @click.prevent="activeMenu = (activeMenu === 'auditoria' ? '' : 'auditoria')"
                    :class="{ 'active': activeMenu === 'auditoria' }">
                    <span data-feather="shield"></span>
                    <span>Auditoría</span>
                    <svg class="menu-arrow-svg" :class="{ 'rotate-90': activeMenu === 'auditoria' }"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
                <ul x-show="activeMenu === 'auditoria'" x-collapse class="nav flex-column bg-light">
                    <li class="nav-item"><a class="nav-link submenu-link text-danger"
                            href="{{ route('administracion.auditoria.index') }}">Dashboard Control</a></li>
                    <li class="nav-item"><a class="nav-link submenu-link text-danger" href="#">Logs de
                            Actividad</a></li>
                </ul>
            </li>
        @endif


        {{--   SALIDAS (REPORTES)              --}}

        @if (auth()->user()->tienePermiso('reportes.ver'))
            <div class="sidebar-heading">Salidas</div>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}"
                    href="{{ route('reportes.proyectos.general') }}">
                    <span data-feather="printer"></span> <span>Reportes</span>
                </a>
            </li>
        @endif

        {{-- LOGOUT (MOVIL) --}}
        <li class="nav-item d-md-none mt-3 border-top pt-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="nav-link text-danger w-100 d-flex align-items-center border-0 bg-transparent">
                    <span data-feather="log-out"></span>
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </li>

    </ul>
</div>
