{{-- LOGICA DE APERTURA AUTOMÁTICA --}}
@php
    $menuAbierto = '';
    if (request()->routeIs('administracion.*')) {
        $menuAbierto = 'admin';
    } elseif (request()->routeIs('catalogos.*')) {
        $menuAbierto = 'normativa';
    } elseif (request()->routeIs('estrategico.*') || request()->routeIs('institucional.*')) {
        $menuAbierto = 'estrategico';
    } elseif (request()->routeIs('inversion.*')) {
        $menuAbierto = 'inversion';
    } elseif (request()->routeIs('auditoria.*')) {
        $menuAbierto = 'auditoria';
    }
@endphp

<div class="position-sticky pt-3 sidebar-sticky hover-scroll"
    style="height: calc(100vh - 56px); overflow-y: auto; top: 56px;" x-data="{ activeMenu: '{{ $menuAbierto }}' }">

    <style>
        .nav-link {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            font-size: 0.85rem;
            color: #333;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            /* Mano al pasar el mouse */
        }

        .nav-link svg.feather {
            margin-right: 12px;
            width: 18px;
            height: 18px;
        }

        /* FLECHA PERSONALIZADA (SVG DIRECTO) */
        .menu-arrow-svg {
            margin-left: auto;
            width: 16px;
            height: 16px;
            transition: transform 0.25s ease;
            opacity: 0.5;
            /* Color gris suave */
        }

        /* Rotación al abrir */
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

    {{-- INFO DE USUARIO --}}
    <div class="user-info border-bottom mb-2 pb-2 px-3">
        <div class="d-flex align-items-center">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                style="width: 36px; height: 36px;">
                <span data-feather="user"></span>
            </div>
            <div class="lh-1 overflow-hidden">
                <span class="text-muted text-uppercase d-block fw-bold text-truncate" style="font-size: 0.65rem;">
                    {{ auth()->user()->organizacion->nombre_organizacion ?? 'SIPeIP' }}
                </span>
                <span class="fw-bold text-dark text-truncate d-block" style="font-size: 0.85rem;">
                    {{ auth()->user()->usuario ?? 'Usuario' }}
                </span>
                <span class="badge bg-secondary mt-1" style="font-size: 0.6rem;">
                    {{ auth()->user()->roles->first()->nombre ?? 'Sin Rol' }}
                </span>
            </div>
        </div>
    </div>

    <ul class="nav flex-column">

        {{-- INICIO --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <span data-feather="home"></span> <span>Inicio</span>
            </a>
        </li>

        {{-- ================================================================= --}}
        {{-- 1. ADMINISTRACIÓN --}}
        {{-- ================================================================= --}}
        @if (auth()->user()->tieneRol(['ADMIN_TI', 'SuperAdmin']))
            <div class="sidebar-heading">Administración</div>

            <li class="nav-item">
                <a class="nav-link" @click.prevent="activeMenu = (activeMenu === 'admin' ? '' : 'admin')"
                    :class="{ 'active': activeMenu === 'admin' }">
                    <span data-feather="settings"></span>
                    <span>Seguridad y Acceso</span>

                    {{-- FLECHA SVG INCRUSTADA (NO FALLA NUNCA) --}}
                    <svg class="menu-arrow-svg" :class="{ 'rotate-90': activeMenu === 'admin' }" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>

                <ul x-show="activeMenu === 'admin'" x-collapse class="nav flex-column bg-light">
                    <li class="nav-item"><a
                            class="nav-link submenu-link {{ request()->routeIs('administracion.usuarios.*') ? 'active text-primary' : 'text-secondary' }}"
                            href="{{ route('administracion.usuarios.index') }}">Usuarios</a></li>
                    <li class="nav-item"><a
                            class="nav-link submenu-link {{ request()->routeIs('administracion.roles.*') ? 'active text-primary' : 'text-secondary' }}"
                            href="#">Roles y Permisos</a></li>
                </ul>
            </li>
        @endif

        {{-- ================================================================= --}}
        {{-- 2. NORMATIVA --}}
        {{-- ================================================================= --}}
        @if (auth()->user()->tieneRol(['ADMIN_TI', 'TECNICO_PLAN', 'SuperAdmin']))
            <div class="sidebar-heading">Normativa Nacional</div>

            <li class="nav-item">
                <a class="nav-link" @click.prevent="activeMenu = (activeMenu === 'normativa' ? '' : 'normativa')"
                    :class="{ 'active': activeMenu === 'normativa' }">
                    <span data-feather="book"></span>
                    <span>Catálogos PND/ODS</span>

                    {{-- FLECHA SVG --}}
                    <svg class="menu-arrow-svg" :class="{ 'rotate-90': activeMenu === 'normativa' }" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>

                <ul x-show="activeMenu === 'normativa'" x-collapse class="nav flex-column bg-light">
                    <li class="nav-item"><a class="nav-link submenu-link text-secondary"
                            href="{{ route('catalogos.planes-nacionales.index') }}">Plan Nacional</a></li>
                    <li class="nav-item"><a class="nav-link submenu-link text-secondary"
                            href="{{ route('catalogos.ejes.index') }}">Ejes Estratégicos</a></li>
                    <li class="nav-item"><a class="nav-link submenu-link text-secondary"
                            href="{{ route('catalogos.pnd.index') }}">Objetivos Nacionales</a></li>
                    <li class="nav-item"><a class="nav-link submenu-link text-secondary"
                            href="{{ route('catalogos.ods.index') }}">Agenda ODS 2030</a></li>
                    <li class="nav-item"><a class="nav-link submenu-link text-secondary"
                            href="{{ route('catalogos.metas.index') }}">Banco de Metas</a></li>
                    <li class="nav-item"><a class="nav-link submenu-link text-secondary"
                            href="{{ route('catalogos.indicadores.index') }}">Indicadores</a></li>
                </ul>
            </li>
        @endif

        {{-- ================================================================= --}}
        {{-- 3. ESTRATEGIA --}}
        {{-- ================================================================= --}}
        @if (auth()->user()->tieneRol(['TECNICO_PLAN', 'SuperAdmin']))
            <div class="sidebar-heading">Estrategia (PEI)</div>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('institucional.*') ? 'active' : '' }}"
                    href="{{ route('institucional.organizaciones.index') }}">
                    <span data-feather="layout"></span> <span>Institución</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" @click.prevent="activeMenu = (activeMenu === 'estrategico' ? '' : 'estrategico')"
                    :class="{ 'active': activeMenu === 'estrategico' }">
                    <span data-feather="crosshair"></span>
                    <span>Planificación</span>

                    {{-- FLECHA SVG --}}
                    <svg class="menu-arrow-svg" :class="{ 'rotate-90': activeMenu === 'estrategico' }"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>

                <ul x-show="activeMenu === 'estrategico'" x-collapse class="nav flex-column bg-light">
                    <li class="nav-item"><a
                            class="nav-link submenu-link {{ request()->routeIs('estrategico.objetivos.*') ? 'active text-primary' : 'text-secondary' }}"
                            href="{{ route('estrategico.objetivos.index') }}">Objetivos (OEI)</a></li>
                    <li class="nav-item"><a
                            class="nav-link submenu-link {{ request()->routeIs('planificacion.alineacion.*') ? 'active text-primary' : 'text-secondary' }}"
                            href="{{ route('estrategico.alineacion.gestionar', ['organizacion_id' => auth()->user()->id_organizacion ?? 1]) }}">Matriz
                            de Alineación</a></li>
                </ul>
            </li>
        @endif

        {{-- ================================================================= --}}
        {{-- 4. INVERSIÓN --}}
        {{-- ================================================================= --}}
        @if (auth()->user()->tieneRol(['TECNICO_PLAN', 'SuperAdmin']))
            <div class="sidebar-heading">Inversión (PAI)</div>

            <li class="nav-item">
                <a class="nav-link" @click.prevent="activeMenu = (activeMenu === 'inversion' ? '' : 'inversion')"
                    :class="{ 'active': activeMenu === 'inversion' }">
                    <span data-feather="briefcase"></span>
                    <span>Cartera Inversión</span>

                    {{-- FLECHA SVG --}}
                    <svg class="menu-arrow-svg" :class="{ 'rotate-90': activeMenu === 'inversion' }"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>

                <ul x-show="activeMenu === 'inversion'" x-collapse class="nav flex-column bg-light">
                    <li class="nav-item"><a
                            class="nav-link submenu-link {{ request()->routeIs('inversion.planes.*') ? 'active text-primary' : 'text-secondary' }}"
                            href="#">Planes Anuales</a></li>
                    <li class="nav-item"><a
                            class="nav-link submenu-link {{ request()->routeIs('inversion.programas.*') ? 'active text-primary' : 'text-secondary' }}"
                            href="{{ route('inversion.programas.index') }}">Programas (CUP)</a></li>
                    <li class="nav-item"><a
                            class="nav-link submenu-link {{ request()->routeIs('inversion.proyectos.*') ? 'active text-primary' : 'text-secondary' }}"
                            href="{{ route('inversion.proyectos.index') }}">Proyectos</a></li>
                    <li class="nav-item"><a
                            class="nav-link submenu-link {{ request()->routeIs('inversion.financiamiento.*') ? 'active text-primary' : 'text-secondary' }}"
                            href="#">Fuentes Financiamiento</a></li>
                </ul>
            </li>
        @endif

        {{-- ================================================================= --}}
        {{-- 5. CONTROL --}}
        {{-- ================================================================= --}}
        @if (auth()->user()->tieneRol(['AUDITOR', 'SuperAdmin']))
            <div class="sidebar-heading text-danger">Control</div>
            <li class="nav-item">
                <a class="nav-link text-danger"
                    @click.prevent="activeMenu = (activeMenu === 'auditoria' ? '' : 'auditoria')"
                    :class="{ 'active': activeMenu === 'auditoria' }">
                    <span data-feather="shield"></span>
                    <span>Auditoría</span>

                    {{-- FLECHA SVG --}}
                    <svg class="menu-arrow-svg" :class="{ 'rotate-90': activeMenu === 'auditoria' }"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
                <ul x-show="activeMenu === 'auditoria'" x-collapse class="nav flex-column bg-light">
                    <li class="nav-item"><a class="nav-link submenu-link text-danger"
                            href="{{ route('auditoria.index') }}">Dashboard Control</a></li>
                    <li class="nav-item"><a class="nav-link submenu-link text-danger" href="#">Logs de
                            Actividad</a></li>
                </ul>
            </li>
        @endif

        <div class="sidebar-heading">Salidas</div>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}"
                href="{{ route('reportes.proyectos.general') }}">
                <span data-feather="printer"></span> <span>Reportes</span>
            </a>
        </li>
        {{-- ================== LOGOUT (SOLO MOVIL) ================== --}}
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
