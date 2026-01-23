/**
 * LÓGICA DEL MÓDULO DE INDICADORES
 *
 */

//  ABRIR MODAL DE EDICIÓN
function abrirEditarIndicador(boton) {
    const btn = $(boton);

    // Asignar ruta dinámica al formulario
    $('#formEditIndicador').attr('action', CONFIG_IND.urlIndex + '/' + btn.data('id'));

    // Llenar los campos
    $('#edit_id_meta').val(btn.data('meta'));
    $('#edit_nombre_indicador').val(btn.data('nombre'));
    $('#edit_linea_base').val(btn.data('linea'));
    $('#edit_anio_linea_base').val(btn.data('anio'));
    $('#edit_meta_final').val(btn.data('final'));
    $('#edit_unidad_medida').val(btn.data('unidad'));
    $('#edit_frecuencia').val(btn.data('frecuencia'));
    $('#edit_metodo_calculo').val(btn.data('metodo'));
    $('#edit_descripcion_indicador').val(btn.data('descripcion'));
    $('#edit_fuente_informacion').val(btn.data('fuente'));
    $('#edit_estado_indicador').val(btn.data('estado'));

    // Usamos getOrCreateInstance para evitar el problema de la pantalla gris
    const modalElement = document.getElementById('modalEditIndicador');
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    modal.show();
}

// FUNCIÓN MAESTRA DEL BUSCADOR (AJAX CON DOMPARSER)
function buscarIndicadores(url) {
    const tbody = document.getElementById('tablaIndicadores');
    const paginacion = document.getElementById('contenedorPaginacion');

    // Efecto visual de carga
    if (tbody) tbody.style.opacity = '0.4';

    fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Reemplazamos SOLO el cuerpo de la tabla
            const nuevoTbody = doc.getElementById('tablaIndicadores');
            if (nuevoTbody && tbody) {
                tbody.innerHTML = nuevoTbody.innerHTML;
            }

            // Reemplazamos la paginación
            const nuevaPag = doc.getElementById('contenedorPaginacion');
            if (nuevaPag && paginacion) {
                paginacion.innerHTML = nuevaPag.innerHTML;
            }

            // Quitamos efecto de carga y actualizamos URL
            if (tbody) tbody.style.opacity = '1';
            window.history.pushState({}, '', url);
        })
        .catch(err => {
            console.error("Error en búsqueda:", err);
            if (tbody) tbody.style.opacity = '1';
        });
}

// EVENTOS AL CARGAR EL DOM
document.addEventListener('DOMContentLoaded', function () {
    //  INICIALIZAR TOOLTIPS DE BOOTSTRAP
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // --- Lógica del Input Buscador
    let timeout = null;
    const inputB = document.getElementById('inputBusqueda');
    const btnLimpiar = document.getElementById('btnLimpiarBusqueda');

    if (inputB) {
        inputB.addEventListener('input', function () {
            // Mostrar u ocultar la X
            if (btnLimpiar) btnLimpiar.style.display = this.value.length > 0 ? 'block' : 'none';

            // Debounce: Esperar 500ms antes de buscar
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                const url = CONFIG_IND.urlIndex + "?busqueda=" + encodeURIComponent(this.value) + "&page=1";
                buscarIndicadores(url);
            }, 500);
        });
    }

    // --- Botón Limpiar X
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function () {
            inputB.value = '';
            this.style.display = 'none';
            buscarIndicadores(CONFIG_IND.urlIndex + "?busqueda=&page=1");
        });
    }

    // --- Paginación AJAX Delegación de eventos
    document.addEventListener('click', function (e) {
        const link = e.target.closest('#contenedorPaginacion a');
        if (link) {
            e.preventDefault();
            buscarIndicadores(link.href);
        }
    });

    // ---  Eliminar con SweetAlert2  ---
    $(document).on('click', '.btn-eliminar-indicador', function (e) {
        e.preventDefault();

        const formulario = $(this).closest('form');
        const nombre = $(this).data('nombre') || 'este indicador';

        Swal.fire({
            title: '¿Eliminar Indicador?',
            text: `Vas a eliminar: "${nombre}". Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                formulario.submit();
            }
        });
    });

    // ---  Parche de seguridad para Modales (Pantalla Gris) ---
    document.addEventListener('hidden.bs.modal', function () {
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.paddingRight = '';
        document.body.style.overflow = '';
    });

});
// FUNCIÓN PARA ABRIR MODAL DE AVANCE
function abrirModalAvance(boton) {
    const btn = $(boton);

    // Pasamos datos al modal
    $('#avance_id_indicador').val(btn.data('id'));
    $('#avance_nombre_indicador').text(btn.data('nombre'));
    $('#avance_unidad').text(btn.data('unidad'));

    // Abrimos modal
    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalRegistrarAvance'));
    modal.show();
}
