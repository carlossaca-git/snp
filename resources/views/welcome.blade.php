<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Planificacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body>
    <!-- Responsive navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container px-5">
            <a class="navbar-brand">Sistema de Planificacion</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" aria-current="page"
                            href="{{ route('login') }}">Iniciar
                            sesion</a></li>
                </ul>
            </div>
        </div>
        <!--<form class="d-flex" role="search">
            <input class="form-control me-2" type="search" placeholder="Buscar" aria-label="Buscar" />
            <button class="btn btn-outline-success" type="submit">Buscar</button>
        </form>-->
    </nav>

    </div>
    </div>
    </nav>
    <div class="container">

        <div class="container">
            <div class="container px-4 px-lg-5">
                <!-- Heading Row-->
                <div class="row gx-4 gx-lg-5 align-items-center my-5">
                    <div class="col-lg-7">
                        <img class="img-fluid rounded mb-4 mb-lg-0" src="{{ asset('images/planificacion.png') }}"
                            alt="Planificacion" />
                    </div>

                    <div class="col-lg-5">
                        <h1 class="font-weight-light">Secretaria Nacional de planificacion</h1>
                        <p>La Secretaría Nacional de Planificación (anteriormente conocida como SENPLADES) es el ente
                            rector de la planificación pública en Ecuador.
                            Su función principal es coordinar, diseñar y evaluar las políticas del Estado para asegurar
                            un desarrollo sostenible y articulado entre los diferentes niveles de gobierno</p>
                    </div>
                </div>
                <!-- Call to Action-->
                <div class="card text-white bg-secondary my-5 py-4 text-center">
                    <div class="card-body">
                        <p class="text-white m-0">La secretaria nacional de planificacion trabaja por el bienestar de
                            todos los ecuatorianos en el desarrollo sostenible del país.
                        </p>
                    </div>
                </div>
                <!-- Content Row-->
                <div class="row gx-4 gx-lg-5">
                    <div class="col-md-4 mb-5">
                        <div class="card h-100">
                            <div class="card-body">
                                <h2 class="card-title">Objetivos Estrategicos</h2>
                                <p class="card-text">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"></li>
                                    <li class="list-group-item">Incrementar la efectividad de la gestión del ciclo de la
                                        planificación</li>
                                    <li class="list-group-item">Incrementar la eficiencia institucional</li>
                                    <li class="list-group-item">Incrementar el desarrollo del talento humano</li>
                                    <li class="list-group-item">Incrementar el uso eficiente del presupuesto</li>
                                </ul>
                                </p>
                            </div>
                            <div class="card-footer"><a class="btn btn-outline-primary btn-sm" href="#!">Leer
                                    mas</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-5">
                        <div class="card h-100">
                            <div class="card-body">
                                <h2 class="card-title">Ejes de Plan Nacional de desarrollo</h2>
                                <p class="card-text">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"></li>
                                    <li class="list-group-item">Social</li>
                                    <li class="list-group-item">Desarrollo Economico</li>
                                    <li class="list-group-item">Infraestructura, Energía y Medio Ambiente</li>
                                    <li class="list-group-item">Gestión de Riesgos</li>
                                </ul>
                                </p>
                            </div>
                            <div class="card-footer">
                                <a target="_blank" class="btn btn-outline-primary btn-sm"
                                    href="https://bit.ly/4scKbXW">Leer mas</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-5">
                        <div class="card h-100">
                            <div class="card-body">
                                <h2 class="card-title">Objetivos de desarrolo sostenible</h2>
                                <p class="card-text">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"></li>
                                    <li class="list-group-item">Fin de la Pobreza</li>
                                    <li class="list-group-item">Hambre Cero</li>
                                    <li class="list-group-item">Salud y Bienestar</li>
                                    <li class="list-group-item">Educación de Calidad</li>
                                    <li class="list-group-item">Igualdad de Género</li>
                                    <li class="list-group-item">Agua Limpia y Saneamiento</li>
                                </ul>
                                </p>
                            </div>
                            <div class="card-footer"><a target="_blank" class="btn btn-outline-primary btn-sm"
                                    href="https://bit.ly/4p591pV">Leer mas</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer-->
            <footer class="py-5 bg-dark">
                <div class="container px-4 px-lg-5">
                    <p class="m-0 text-center text-white">Copyright &copy; Sistema creado para examen complexivo</p>
                </div>
            </footer>
            <!-- Bootstrap core JS-->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
            <!-- Core theme JS-->

        </div>
    </div>
    <script src="js/scripts.js"></script>
</body>

</div>
</div>
<script src="cdn.jsdelivr.net"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
    crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
</script>
<!--<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    -- >
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
        integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous">
    </script>-->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
</script>
</body>

</html>
