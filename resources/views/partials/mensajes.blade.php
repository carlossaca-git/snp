{{-- resources/views/layouts/mensajes.blade.php --}}

{{-- 1. MENSAJE DE ÉXITO --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-left-success" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <strong>¡Excelente!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- 2. MENSAJE DE ERROR (Manual del Controlador) --}}
@if (session('error'))
    {{-- Agregué la clase 'alerta-manual-error' para identificarla en JS si quieres --}}
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-left-danger alerta-manual-error" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Error:</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- 3. ERRORES DE VALIDACIÓN (Laravel automático) --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <div class="d-flex align-items-center mb-1">
            <i class="fas fa-times-circle me-2"></i>
            <strong>Por favor corrige los siguientes errores:</strong>
        </div>
        <ul class="mb-0 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- SCRIPT CORREGIDO: Fuera de los IFs --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuración de tiempo (4 segundos)
        const tiempoEspera = 4000;

        setTimeout(function() {
            // Seleccionamos la alerta de ÉXITO y la de ERROR MANUAL.
            // Excluimos la de validación ($errors) para que el usuario pueda leerla con calma.

            // Buscamos clases específicas
            const alertas = document.querySelectorAll('.alert-success, .alerta-manual-error');

            alertas.forEach(function(alerta) {
                // Usamos la API de Bootstrap
                if (typeof bootstrap !== 'undefined') {
                    var bsAlert = new bootstrap.Alert(alerta);
                    bsAlert.close();
                } else {
                    // Fallback
                    alerta.style.display = 'none';
                }
            });
        }, tiempoEspera);
    });
</script>
