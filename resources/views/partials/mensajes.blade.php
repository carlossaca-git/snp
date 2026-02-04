
@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '¡Operación Exitosa!',
                text: "{{ session('success') }}",
                icon: 'success',
                toast: false,
                position: 'center',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        });
    </script>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow mb-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="fas fa-bug fa-2x"></i> </div>
            <div>
                <h5 class="alert-heading fw-bold mb-1">¡Algo salió mal!</h5>
                <p class="mb-0">{{ session('error') }}</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow mb-4 border-start border-danger" role="alert">
        <div class="d-flex align-items-center mb-2">
            <i class="fas fa-exclamation-triangle me-2 fs-4"></i>
            <h5 class="alert-heading fw-bold mb-0">No se pudo guardar</h5>
        </div>

        <p class="small text-muted mb-2">Por favor corrige los siguientes errores antes de continuar:</p>

        <ul class="mb-0 bg-white p-3 rounded shadow-sm">
            @foreach ($errors->all() as $error)
                <li class="text-danger small fw-bold">{{ $error }}</li>
            @endforeach
        </ul>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
