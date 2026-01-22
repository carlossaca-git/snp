@extends('layouts.app')

@section('content')
<x-layouts.header_content titulo="Perfil de Usuario"
        subtitulo="Perfilc completo de usuario">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('estrategico.objetivos.index') }}"
                class="btn btn-sm btn-outline-secondary me-2 d-inline-flex align-items-center">
                <i class="fas fa-home"></i> Inicio
            </a>
            <button type="button" class="btn btn-secondary" onclick="history.back()">
                <i class="fas fa-arrow-left me-1"></i> Atras
            </button>
        </div>
    </x-layouts.header_content>
    <div class="container-fluid">
        <div class="card shadow mb-4 border-left-primary">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-lg-2 text-center">
                        <img class="rounded-circle img-thumbnail shadow-sm"
                            src="https://ui-avatars.com/api/?name={{ urlencode($user->nombres) }}&background=4e73df&color=ffffff&size=128&font-size=0.4"
                            alt="Avatar" style="width: 120px; height: 120px;">
                    </div>
                    {{--  DATOS  --}}
                    <div class="col-lg-7">
                        <h3 class="font-weight-bold text-dark mb-0">{{ $user->nombres }} {{ $user->apellidos }}</h3>
                        <small class="text-muted d-block mb-3">Nombre Completo</small>
                        <div class="mb-3">
                            <span class="badge bg-primary px-3 py-2">
                                <i class="fas fa-user-shield me-1"></i>
                                {{ $user->roles->first()->nombre_corto ?? 'Sin Rol Asignado' }}
                            </span>
                        </div>
                        {{-- Identificacion-Usuario-Correo --}}
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.7rem;">
                                        Identificación
                                    </small>
                                    <div class="text-dark font-weight-bold">
                                        <i class="fas fa-address-card me-2 text-secondary"></i>
                                        {{ $user->cedula ?? ($user->identificacion ?? 'No registrada') }}
                                    </div>
                                </div>
                            </div>
                            {{-- USUARIO--}}
                            <div class="col-md-6">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.7rem;">
                                        Usuario de Sistema
                                    </small>
                                    <div class="text-dark font-weight-bold">
                                        <i class="fas fa-user me-2 text-secondary"></i>
                                        {{ $user->usuario ?? $user->email }}
                                    </div>
                                </div>
                            </div>

                            {{-- CORREO ELECTRÓNICO --}}
                            <div class="col-12">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.7rem;">
                                        Correo Electrónico
                                    </small>
                                    <div class="text-dark">
                                        <i class="fas fa-envelope me-2 text-secondary"></i>
                                        {{ $user->correo_electronico }}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    {{-- COLUMNA ACCIONES (Derecha) --}}
                    <div class="col-lg-3 text-end border-start">
                        <p class="small text-muted mb-2">Acciones de Cuenta</p>
                        <a href="{{ route('administracion.usuarios.edit' , $user) }}" class="btn btn-primary btn-sm w-100 mb-2">
                            <i class="fas fa-user-edit me-1"></i> Editar Mis Datos
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            {{--  CONTEXTO ORGANIZACION --}}
            <div class="col-lg-4 mb-4">
                <div class="card shadow h-100 border-top-info">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-info">
                            <i class="fas fa-building me-2"></i>Organización
                        </h6>
                    </div>
                    <div class="card-body">
                        @if ($user->organizacion)
                            <div class="text-center mb-4">
                                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center border mb-2"
                                    style="width: 80px; height: 80px;">
                                    <i class="fas fa-university fa-3x text-gray-300"></i>
                                </div>
                                <h5 class="font-weight-bold text-dark">{{ $user->organizacion->nom_organizacion }}</h5>
                                <span class="badge bg-light text-dark border">Cód: {{ $user->organizacion->siglas }}</span>
                            </div>

                            <hr>

                            <div class="small">
                                <p class="mb-1 fw-bold text-muted">RESUMEN DE ACTIVIDAD</p>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Proyectos Registrados</span>
                                    <span class="badge bg-info">{{ $proyectos->count() }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Proyectos Registrados</span>
                                    <span class="badge bg-info">{{ $proyectos->count() }}</span>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning text-center small">
                                Sin organización asignada.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- LISTADO DE PROYECTOS  --}}
            <div class="col-lg-8 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-dark">
                            <i class="fas fa-folder-open me-2 text-warning"></i>Proyectos Recientes
                        </h6>
                        @if ($proyectos->count() > 5)
                            <a href="{{ route('inversion.proyectos.index', ['usuario' => $user->id]) }}"
                                class="text-xs font-weight-bold text-primary text-decoration-none">
                                Ver todos &rarr;
                            </a>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0 align-middle">
                                <thead class="bg-light text-xs text-uppercase text-muted">
                                    <tr>
                                        <th class="ps-4">Nombre del Proyecto</th>
                                        <th>CUP / Código</th>
                                        <th>Fecha Creación</th>
                                        <th class="text-end pe-4">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($proyectos->take(5) as $proy)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark text-truncate" style="max-width: 250px;">
                                                    {{ $proy->nombre_proyecto }}
                                                </div>
                                                <div class="small text-muted">
                                                    {{ $proy->objetivo->nombre_objetivo ?? 'Sin objetivo estratégico' }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="font-monospace small bg-light border px-1 rounded">
                                                    {{ $proy->cup ?? '---' }}
                                                </span>
                                            </td>
                                            <td class="small">
                                                {{ $proy->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('inversion.proyectos.show', $proy->id) }}"
                                                    class="btn btn-sm btn-outline-secondary" title="Ver Ficha">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="fas fa-folder-open fa-2x mb-3 text-gray-300"></i><br>
                                                No has registrado proyectos todavía.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
