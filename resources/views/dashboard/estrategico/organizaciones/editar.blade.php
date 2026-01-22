@extends('layouts.app') {{-- O el layout que uses --}}

@section('content')
    <x-layouts.header_content titulo="Editar Institución: {{ $organizacion->siglas }}"
        subtitulo="Gestión y monitoreo de instituciones del sector público">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('institucional.organizaciones.index') }}"
                class="btn btn-sm btn-outline-secondary me-2 d-inline-flex align-items-center">
                <i class="fas fa-arrow-left me-2"></i> Regresar
            </a>
        </div>
    </x-layouts.header_content>
    @include('partials.mensajes')
    <div class="container-fluid py-4">
        <form action="{{ route('institucional.organizaciones.update', $organizacion->id_organizacion) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-sitemap me-2"></i>Paso 1: Clasificación</h6>
                        </div>
                        <div class="card-body bg-light">
                            {{-- MACROSECTOR  --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-secondary">1. Macrosector</label>
                                <select id="selectMacrosector" class="form-select" name="id_macrosector">
                                    @foreach ($macrosectores as $macro)
                                        <option value="{{ $macro->id_macrosector }}"
                                            {{ optional($sector_actual)->id_macrosector == $macro->id_macrosector ? 'selected' : '' }}>
                                            {{ $macro->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- SECTOR --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-secondary">2. Sector</label>
                                <select id="selectSector" class="form-select" name="id_sector">
                                    @foreach ($sectores as $sec)
                                        <option value="{{ $sec->id_sector }}"
                                            {{ $sec->id_sector == $sec->id_sector ? 'selected' : '' }}>
                                            {{ $sec->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- SUBSECTOR --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-secondary">3. Subsector *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i
                                            class="fas fa-check-circle text-success"></i></span>
                                    <select name="id_subsector" id="selectSubsector"
                                        class="form-select @error('id_subsector') is-invalid @enderror" required>
                                        @foreach ($subsectores as $sub)
                                            <option value="{{ $sub->id_subsector }}"
                                                {{ $sub->id_subsector == $organizacion->id_subsector ? 'selected' : '' }}>
                                                {{ $sub->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>

                            {{-- JERARQUÍA ID PADRE --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-secondary">4. Dependencia
                                    Institucional</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i
                                            class="fas fa-layer-group text-primary"></i></span>
                                    <select name="id_padre" class="form-select">
                                        <option value="">● Institución Autónoma / Matriz</option>
                                        @foreach ($organizaciones as $org)
                                            <option value="{{ $org->id_organizacion }}"
                                                {{ $organizacion->id_padre == $org->id_organizacion ? 'selected' : '' }}>
                                                {{ $org->nom_organizacion }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: Datos de la Entidad --}}
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-edit me-2"></i>Paso 2: Datos y Contacto
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-3">
                                {{-- Nombre --}}
                                <div class="row g-3 mb-3">
                                    <div class="col-md-8">
                                        <label class="form-label fw-bold">Nombre Completo de la Institución *</label>
                                        <input type="text" name="nom_organizacion" class="form-control form-control-lg"
                                            value="{{ old('nom_organizacion', $organizacion->nom_organizacion) }}"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold">Estado de la Organización</label>

                                            <div class="form-check form-switch">
                                                <input type="hidden" name="estado" value="0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="switchEstado" name="estado" value="1"
                                                    style="cursor: pointer; width: 3em; height: 1.5em;"
                                                    {{ old('estado', $organizacion->estado) == '1' || $organizacion->estado == '1' ? 'checked' : '' }}>

                                                <label class="form-check-label ms-2 mt-1" for="switchEstado">
                                                    Activo / Inactivo
                                                </label>
                                            </div>

                                            @error('estado')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                {{-- SEGUNDA COLUMNA --}}
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">RUC *</label>
                                        <input type="text" name="ruc" class="form-control"
                                            value="{{ old('ruc', $organizacion->ruc) }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Siglas</label>
                                        <input type="text" name="siglas" class="form-control"
                                            value="{{ old('siglas', $organizacion->siglas) }}">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold">Nivel de Gobierno *</label>
                                        <select name="nivel_gobierno" class="form-select" required>
                                            <option value="Nacional"
                                                {{ $organizacion->nivel_gobierno == 'Nacional' ? 'selected' : '' }}>
                                                Nacional
                                            </option>
                                            <option value="Provincial"
                                                {{ $organizacion->nivel_gobierno == 'Provincial' ? 'selected' : '' }}>
                                                Provincial</option>
                                            <option value="Cantonal"
                                                {{ $organizacion->nivel_gobierno == 'Cantonal' ? 'selected' : '' }}>
                                                Cantonal
                                            </option>
                                            <option value="Parroquial"
                                                {{ $organizacion->nivel_gobierno == 'Parroquial' ? 'selected' : '' }}>
                                                Parroquial</option>
                                        </select>
                                    </div>
                                </div>

                                <hr class="my-4">
                                {{-- Tipo de Entidad --}}
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-dark">Tipo de Entidad / Naturaleza Jurídica
                                            *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white"><i
                                                    class="fas fa-landmark text-primary"></i></span>
                                            <select name="id_tipo_org"
                                                class="form-select @error('id_tipo_org') is-invalid @enderror" required>
                                                <option value="">Seleccione Tipo de Organización...</option>
                                                @foreach ($tipos as $tipo)
                                                    <option value="{{ $tipo->id_tipo_org }}"
                                                        {{ old('id_tipo_org', $organizacion->id_tipo_org) == $tipo->id_tipo_org ? 'selected' : '' }}>
                                                        {{ $tipo->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('id_tipo_org')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    {{-- TELÉFONO --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Teléfono de Contacto</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            <input type="text" name="telefono" class="form-control"
                                                value="{{ old('telefono', $organizacion->telefono) }}"
                                                placeholder="Ej: 022-555-555">
                                        </div>
                                    </div>

                                    {{-- SITIO WEB --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Sitio Web (URL)</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                            <input type="url" name="web" class="form-control"
                                                value="{{ old('web', $organizacion->web) }}"
                                                placeholder="https://www.institucion.gob.ec">
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    {{-- CORREO --}}
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Correo Electrónico Institucional</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            <input type="email" name="email" class="form-control"
                                                value="{{ old('email', $organizacion->email) }}"
                                                placeholder="contacto@institucion.gob.ec">
                                        </div>
                                    </div>
                                </div>

                                {{-- Logo Actual --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-md-3 text-center">
                                        <label class="form-label d-block fw-bold">Logo Actual</label>
                                        @if ($organizacion->logo)
                                            <img src="{{ asset('storage/' . $organizacion->logo) }}"
                                                class="img-thumbnail" style="height: 100px;">
                                        @else
                                            <div class="bg-light border rounded p-3 text-muted small">Sin Logo</div>
                                        @endif
                                    </div>
                                    <div class="col-md-9">
                                        <label class="form-label fw-bold">Cambiar Logo</label>
                                        <input type="file" name="logo" class="form-control" accept="image/*">
                                    </div>
                                </div>

                                {{-- Misión y Visión --}}
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Misión</label>
                                        <textarea name="mision" class="form-control" rows="3">{{ old('mision', $organizacion->mision) }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Visión</label>
                                        <textarea name="vision" class="form-control" rows="3">{{ old('vision', $organizacion->vision) }}</textarea>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer bg-light py-3 d-flex justify-content-end">
                                <button type="submit" class="btn btn-success btn-lg px-2 shadow">
                                    <i class="fas fa-save me-2"></i> ACTUALIZAR
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>
@endsection
{{-- SCRIPT JAVASCRIPT  --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Definir constantes
        const macroSelect = document.getElementById('selectMacrosector');
        const sectorSelect = document.getElementById('selectSector');
        const subsectorSelect = document.getElementById('selectSubsector');


        // ---------------------------------------------------
        //  CAMBIO DE MACROSECTOR Carga Sectores
        // ---------------------------------------------------
        macroSelect.addEventListener('change', function() {
            const id = this.value;

            // Resetear hijos
            sectorSelect.innerHTML = '<option value="">Cargando...</option>';
            sectorSelect.disabled = true;
            subsectorSelect.innerHTML = '<option value="">Esperando Sector...</option>';
            subsectorSelect.disabled = true;

            if (id) {
                // URL para Sectores
                let url = "{{ route('institucional.api.sectores', ':id') }}";
                url = url.replace(':id', id);

                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        sectorSelect.innerHTML = '<option value="">▼ Seleccione Sector</option>';
                        data.forEach(item => {
                            sectorSelect.innerHTML +=
                                `<option value="${item.id_sector}">${item.nombre}</option>`;
                        });
                        sectorSelect.disabled = false;
                    })
                    .catch(err => console.error('Error Sectores:', err));
            } else {
                sectorSelect.innerHTML = '<option value="">Esperando Macrosector...</option>';
            }
        });

        // ---------------------------------------------------
        // CAMBIO DE SECTOR Carga Subsectores
        // ---------------------------------------------------
        sectorSelect.addEventListener('change', function() {
            const id = this.value;

            subsectorSelect.innerHTML = '<option value="">Cargando...</option>';
            subsectorSelect.disabled = true;

            if (id) {
                // URL para Subsectores
                let url = "{{ route('institucional.api.subsectores', ':id') }}";
                url = url.replace(':id', id);

                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        subsectorSelect.innerHTML =
                            '<option value="">▼ Seleccione Subsector</option>';
                        data.forEach(item => {
                            subsectorSelect.innerHTML +=
                                `<option value="${item.id_subsector}">${item.nombre}</option>`;
                        });
                        subsectorSelect.disabled = false;
                    })
                    .catch(err => console.error('Error Subsectores:', err));
            }
        });
    });
</script>
