<div class="alert alert-warning shadow-sm border-2">
    <h3 class="fw-bold"><i data-feather="check-circle"></i> ¡CONEXIÓN EXITOSA!</h3>
    <p>Si estás viendo este mensaje, el sistema de inyección dinámica (Dashboard Maestro) está funcionando perfectamente.</p>
    <hr">
    <ul>
        <li><strong>Módulo:</strong> {{ $modulo }}</li>
        <li><strong>Sección:</strong> {{ $seccion }}</li>
        <li><strong>Organización ID:</strong> {{ $organizacion->id_organizacion }}</li>
        <li><strong>Nombre:</strong> {{ $organizacion->nom_organizacion }}</li>
    </ul>
    <p class="mb-0 small text-muted">Prueba técnica realizada el 29 de diciembre de 2025.</p>
    <div class="mt-3 p-3 bg-white border">
    <strong>Conteo de Relaciones ODS:</strong> {{ DB::table('rel_objetivo_nacional_ods')->count() }} registros encontrados.
</div>
</div>

<script>
    feather.replace();
</script>
