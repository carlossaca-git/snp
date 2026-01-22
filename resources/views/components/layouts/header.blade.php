<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">

    {{--  NOMBRE DEL SISTEMA --}}
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">Sistema de Planificacion</a>

    <button class="navbar-toggler p-1 fs-6 position-absolute d-md-none collapsed border-0" type="button" @click="open = !open"
        style="top: .5rem; right: 1rem; z-index: 1100;">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-nav ms-auto d-none d-md-flex">
        <div class="nav-item text-nowrap">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a class="nav-link px-3 text-white" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); this.closest('form').submit();">
                    Cerrar Sesión
                </a>
            </form>
        </div>
    </div>
</header>
