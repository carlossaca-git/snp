@extends('layouts.app')

@section('content')
    <x-layouts.header_content titulo="Nueva Institución" subtitulo="Registro de entidad y asignación de jerarquía.">
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('institucional.organizaciones.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Regresar
            </a>
        </div>

    </x-layouts.header_content>
    @include('partials.mensajes')
    <form action="{{ route('institucional.organizaciones.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-sitemap me-2"></i>Paso 1: Clasificación</h6>
                    </div>
                    <div class="card-body bg-light">
                        <div class="alert alert-info small mb-3">
                            <i class="fas fa-info-circle me-1"></i> Defina la ubicación estratégica.
                        </div>
                        {{-- MACROSECTOR --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">1. Macrosector</label>
                            <select id="selectMacrosector" class="form-select" required>
                                <option value="">▼ Seleccione Macrosector</option>
                                @foreach ($macrosectores as $macro)
                                    <option value="{{ $macro->id_macrosector ?? $macro->id }}">
                                        {{ $macro->nombre ?? $macro->nom_macrosector }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- SECTOR --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">2. Sector</label>
                            <select id="selectSector" class="form-select" disabled>
                                <option value="">Esperando Macrosector...</option>
                            </select>
                        </div>
                        {{-- SUBSECTOR --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">3. Subsector *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i
                                        class="fas fa-check-circle text-success"></i></span>
                                <select name="id_subsector" id="selectSubsector"
                                    class="form-select @error('id_subsector') is-invalid @enderror" required disabled>
                                    <option value="">Esperando Sector...</option>
                                </select>
                            </div>
                            @error('id_subsector')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- JERARQUIA ID PADRE --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">
                                4. Dependencia Institucional (Adscripción)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i
                                        class="fas fa-layer-group text-primary"></i></span>
                                <select name="id_padre" id="selectPadre"
                                    class="form-select @error('id_padre') is-invalid @enderror">
                                    <option value="">● Institución Autónoma / Matriz</option>
                                    @foreach ($organizaciones as $org)
                                        <option value="{{ $org->id_organizacion }}"
                                            {{ old('id_padre') == $org->id_organizacion ? 'selected' : '' }}>
                                            {{ $org->nom_organizacion }} ({{ $org->siglas }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <small class="text-muted" style="font-size: 0.7rem">
                                Seleccione solo si esta entidad depende de un Ministerio o Secretaría.
                            </small>
                            @error('id_padre')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            {{-- DATOS DE LA ENTIDAD  --}}

            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-building me-2"></i>Paso 2: Datos de la
                            Entidad</h6>
                    </div>
                    <div class="card-body p-4">

                        {{--  Nombre --}}
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Nombre Completo de la Institución *</label>
                                <input type="text" name="nom_organizacion"
                                    class="form-control form-control-lg @error('nom_organizacion') is-invalid @enderror"
                                    value="{{ old('nom_organizacion') }}" placeholder="Ej: Empresa Eléctrica Quito"
                                    required>
                                @error('nom_organizacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{--  RUC, Siglas y NIVEL DE GOBIERNO --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">RUC *</label>
                                <input type="text" name="ruc" class="form-control @error('ruc') is-invalid @enderror"
                                    value="{{ old('ruc') }}" placeholder="1790000000001" maxlength="13" required>
                                @error('ruc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Siglas</label>
                                <input type="text" name="siglas" class="form-control" value="{{ old('siglas') }}"
                                    placeholder="Ej: EEQ">
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-bold">Nivel de Gobierno *</label>
                                <select name="nivel_gobierno"
                                    class="form-select @error('nivel_gobierno') is-invalid @enderror" required>
                                    <option value="">Seleccione Nivel...</option>
                                    <option value="Nacional" {{ old('nivel_gobierno') == 'Nacional' ? 'selected' : '' }}>
                                        Nacional</option>
                                    <option value="Provincial"
                                        {{ old('nivel_gobierno') == 'Provincial' ? 'selected' : '' }}>Provincial</option>
                                    <option value="Cantonal" {{ old('nivel_gobierno') == 'Cantonal' ? 'selected' : '' }}>
                                        Cantonal</option>
                                    <option value="Parroquial"
                                        {{ old('nivel_gobierno') == 'Parroquial' ? 'selected' : '' }}>Parroquial</option>
                                </select>
                                @error('nivel_gobierno')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <hr class="text-muted opacity-25">
                        {{--  Tipo de Entidad --}}
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
                                                {{ old('id_tipo_org') == $tipo->id_tipo_org ? 'selected' : '' }}>
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
                        {{-- LOGO E IMAGEN --}}
                        <h6 class="fw-bold text-uppercase small text-muted mb-3">Identidad y Contacto</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Logo Institucional</label>
                                <input type="file" name="logo" class="form-control" accept="image/*">
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem">Máx: 10MB
                                    (PNG/JPG)</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Teléfono</label>
                                <input type="text" name="telefono" class="form-control"
                                    value="{{ old('telefono') }}" placeholder="Ej: 022-555-555">
                            </div>
                        </div>

                        {{--  Contacto Digital --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Sitio Web</label>
                                <input type="url" name="web" class="form-control form-control-sm"
                                    value="{{ old('web') }}" placeholder="https://...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Correo Electrónico</label>
                                <input type="email" name="email" class="form-control form-control-sm"
                                    value="{{ old('email') }}" placeholder="contacto@...">
                            </div>
                        </div>

                        <hr class="text-muted opacity-25">

                        {{-- - MISIÓN Y VISIÓN --}}
                        <h6 class="fw-bold text-uppercase small text-muted mb-3">Direccionamiento Estratégico</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Misión</label>
                                <textarea name="mision" class="form-control" rows="3"
                                    placeholder="¿Cuál es la razón de ser de la institución?">{{ old('mision') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Visión</label>
                                <textarea name="vision" class="form-control" rows="3" placeholder="¿A dónde quiere llegar en el futuro?">{{ old('vision') }}</textarea>
                            </div>
                        </div>

                    </div>

                    {{-- FOOTER  --}}
                    <div class="card-footer bg-light py-3 border-top">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-success btn-lg shadow px-5">
                                <i class="fas fa-save me-2"></i> REGISTRAR
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
{{-- SCRIPT JAVASCRIPT  --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Definir constantes
            const macroSelect = document.getElementById('selectMacrosector');
            const sectorSelect = document.getElementById('selectSector');
            const subsectorSelect = document.getElementById('selectSubsector');

            //  CAMBIO DE MACROSECTOR Carga Sectores

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

            // CAMBIO DE SECTOR Carga Subsectores

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
@endpush
