<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Planificación - @yield('title')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://kit.fontawesome.com/e4370263c0.js" crossorigin="anonymous"></script>
    @yield('styles')
    @stack('styles')
    <style>
        .layout-fixed-scroll {
            height: auto;
            overflow-y: visible;
        }

        /* Solo en PC (min-width: 768px): Altura fija y scroll interno */
        @media (min-width: 768px) {
            .layout-fixed-scroll {
                height: calc(100vh - 60px);
                overflow-y: auto;
                overflow-x: hidden;
            }
        }

        /* Regla para pantallas medianas (PC/Tablet) en adelante */
        @media (min-width: 768px) {
            #sidebarMenu {
                display: block !important;
                transform: none !important;
                visibility: visible !important;
            }
        }

        body,
        html {
            overflow: hidden;
            height: 100%;
        }

        .hover-scroll::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }


        .hover-scroll::-webkit-scrollbar-track {
            background: transparent;
        }


        .hover-scroll::-webkit-scrollbar-thumb {
            background: transparent;
            border-radius: 10px;
            border: 2px solid transparent;
            background-clip: content-box;
        }


        .hover-scroll:hover::-webkit-scrollbar-thumb {
            background-color: rgba(100, 100, 100, 0.5);
        }

        .hover-scroll {
            scrollbar-width: thin;
            scrollbar-color: transparent transparent;
            transition: scrollbar-color 0.3s;
        }

        .hover-scroll:hover {
            scrollbar-color: rgba(100, 100, 100, 0.5) transparent;
        }
    </style>
</head>

<body x-data="{ open: false }" class="font-sans antialiased hover-scroll">

    @include('components.layouts.header')

    <div class="container-fluid">
        <div class="row">

            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-white border-end sidebar"
                :class="open ? 'd-block position-fixed top-0 start-0 bottom-0 bg-white w-75 shadow' : 'collapse'"
                style="z-index: 1000; transition: all 0.3s;">

                @include('components.layouts.sidebar')

            </nav>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 hover-scroll bg-light layout-fixed-scroll""
                style="height: calc(100vh - 60px); overflow-y: auto; overflow-x: hidden;">
                <div x-show="open" @click="open = false"
                    class="d-md-none position-fixed top-0 start-0 w-100 h-100 bg-dark opacity-50" style="z-index: 999;">
                </div>

                <div x-show="open" @click="open = false" class="sidebar-backdrop d-md-none" style="display: none;"
                    x-transition.opacity>
                </div>
                <div class="pt-3 pb-5 container-fluid">
                    <div class="bg-white p-3 p-md-4 rounded shadow-sm border">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div class="modal fade" id="modalPdf" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-dark text-white py-2">
                    <h6 class="modal-title"><i class="fas fa-file-pdf me-2 text-danger"></i> Visor de Documentos SIPEIP
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="visorPdf" src="" width="100%" height="750px" style="border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" defer></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script>
        (function() {
            'use strict'
            feather.replace({
                'aria-hidden': 'true'
            })
        })()
    </script>

    @yield('scripts')
    @stack('scripts')
    <script>
        // Función global accesible desde cualquier botón del sistema
        function abrirVisorPdf(url) {
            const visor = document.getElementById('visorPdf');
            if (visor) {
                visor.src = url;
                new bootstrap.Modal(document.getElementById('modalPdf')).show();
            }
        }
    </script>
</body>

</html>
