
/**
 * Llena y muestra el modal de edición con los datos del botón presionado
 * @param {HTMLElement} boton - El botón que activa la función
 */
function abrirEditarMeta(boton) {
    try {
        // Usamos jQuery para facilitar la manipulación de atributos y valores
        const btn = $(boton);

        // Asignar valores a los inputs del modal usando los IDs definidos en editar.blade.php
        $('#edit_id').val(btn.data('id'));
        $('#edit_nombre').val(btn.data('nombre'));
        $('#edit_indicador').val(btn.data('indicador'));
        $('#edit_linea_base').val(btn.data('linea-base'));
        $('#edit_meta_valor').val(btn.data('meta-valor'));
        $('#edit_objetivo').val(btn.data('objetivo'));
        $('#edit_estado').val(btn.data('estado'));
        $('#edit_descripcion').val(btn.data('descripcion'));
        $('#edit_unidad').val(btn.data('unidad'));
        $('#edit_url').val(btn.data('url'));
        $('#edit_codigo').val(btn.data('codigo'));

        // Construcción dinámica de la URL de actualización (update)
        // Usamos la constante global CONFIG_METAS definida en metas.blade.php
        $('#formEditMeta').attr('action', CONFIG_METAS.urlIndex + '/' + btn.data('id'));

        // Mostrar el modal usando la API de Bootstrap 5
        const modalEdit = document.getElementById('modalEditMeta');
        bootstrap.Modal.getOrCreateInstance(modalEdit).show();
    } catch (error) {
        console.error("Error en abrirEditarMeta:", error);
    }
}

/**
 * Prepara el modal de seguimiento de valor actual
 */
function abrirSeguimiento(boton) {
    try {
        const id = boton.getAttribute('data-id');
        const unidad = boton.getAttribute('data-unidad');
        const nombre = boton.getAttribute('data-nombre');
        const valor = boton.getAttribute('data-valor');

        document.getElementById('id_meta_seguimiento').value = id;
        document.getElementById('avance_nombre_meta').innerText = nombre;
        document.getElementById('unidad_medida_label').innerText = unidad;
        document.getElementById('valor_actual_input').value = valor;

        const modalSeg = document.getElementById('modalSeguimiento');
        bootstrap.Modal.getOrCreateInstance(modalSeg).show();
    } catch (error) {
        console.error("Error en abrirSeguimiento:", error);
    }
}

/**
 * Gestiona la vinculación de ODS mediante checkboxes
 */
function abrirVinculacionOds(boton) {
    try {
        const idMeta = boton.getAttribute('data-id');
        const odsVinculados = boton.getAttribute('data-ods');
        const nombreMeta = boton.getAttribute('data-nombre');

        // Mostrar el nombre de la meta en el título del modal
        const displayNombre = document.getElementById('nombre_meta_ods_display');
        if (displayNombre) displayNombre.innerText = nombreMeta;

        document.getElementById('id_meta_ods_input').value = idMeta;

        // Desmarcar todos los checkboxes antes de marcar los correctos
        document.querySelectorAll('#modalVincularOds .btn-check').forEach(check => {
            check.checked = false;
        });

        // Si hay ODS vinculados, los separamos por coma y marcamos cada checkbox
        if (odsVinculados) {
            odsVinculados.split(',').forEach(id => {
                const checkbox = document.getElementById('ods_' + id.trim());
                if (checkbox) checkbox.checked = true;
            });
        }

        const modalOds = document.getElementById('modalVincularOds');
        bootstrap.Modal.getOrCreateInstance(modalOds).show();
    } catch (error) {
        console.error("Error en abrirVinculacionOds:", error);
    }
}

//  LÓGICA DE BÚSQUEDA AJAX Y NAVEGACIÓN

//  con paginación sin recargar la página
let timeout = null;

/**
 * Función principal para actualizar la tabla y paginación sin recargar la página
 * @param {string} url - La URL con los parámetros de búsqueda o página
 */
function buscarMetas(url) {
    const contenedorTabla = document.getElementById('tablaMetas');
    const contenedorPaginacion = document.querySelector('.pagination-custom');

    if(!contenedorTabla) return;

    // Efecto visual de carga
    contenedorTabla.style.opacity = '0.4';

    fetch(url, {
        headers: { "X-Requested-With": "XMLHttpRequest" } // Indica que es una petición AJAX
    })
    .then(response => response.text())
    .then(html => {
        // Convertimos el texto HTML recibido en un objeto DOM manipulable
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        // Extraemos solo el contenido nuevo de la tabla
        const nuevaTabla = doc.getElementById('tablaMetas');
        if (nuevaTabla) contenedorTabla.innerHTML = nuevaTabla.innerHTML;

        // Extraemos y reemplazamos la paginación para actualizar los números
        const nuevaPaginacion = doc.querySelector('.pagination-custom');
        if (nuevaPaginacion && contenedorPaginacion) {
            contenedorPaginacion.innerHTML = nuevaPaginacion.innerHTML;
        }

        // Restauramos visibilidad y actualizamos la URL en el navegador
        contenedorTabla.style.opacity = '1';
        window.history.pushState({}, '', url);
    })
    .catch(error => {
        console.error('Error en búsqueda AJAX:', error);
        contenedorTabla.style.opacity = '1';
    });
}

/**
 * Controla si el botón X de limpiar búsqueda debe ser visible
 */
function actualizarVisibilidadX() {
    const inputB = document.getElementById('inputBusqueda');
    const btnLimpiar = document.getElementById('btnLimpiarBusqueda');

    if (inputB && btnLimpiar) {
        // Si hay texto, mostramos la X; si no, la ocultamos
        if (inputB.value.length > 0) {
            btnLimpiar.style.setProperty('display', 'block', 'important');
        } else {
            btnLimpiar.style.setProperty('display', 'none', 'important');
        }
    }
}

// --- EVENTOS CUANDO EL DOM ESTa LISTO

document.addEventListener('DOMContentLoaded', function () {
    const inputB = document.getElementById('inputBusqueda');
    const btnLimpiar = document.getElementById('btnLimpiarBusqueda');

    // Escuchar cuando el usuario escribe en el buscador
    if (inputB) {
        inputB.addEventListener('input', function() {
            actualizarVisibilidadX();
            // Usamos un timeout para esperar a que el usuario termine de escribir
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                const url = CONFIG_METAS.urlIndex + "?busqueda=" + encodeURIComponent(this.value) + "&page=1";
                buscarMetas(url);
            }, 500);
        });
    }

    // Lógica para el botón X para Limpiar
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function () {
            inputB.value = '';
            actualizarVisibilidadX();
            // Realizamos una búsqueda sin filtro
            buscarMetas(CONFIG_METAS.urlIndex + "?busqueda=&page=1");
        });
    }

    // Paginación AJAX Escucha clics en los números de página
    document.addEventListener('click', function (e) {
        const link = e.target.closest('.pagination-custom a');
        if (link) {
            e.preventDefault();    // Evitamos la recarga de página
            buscarMetas(link.href); // Llamamos a la función AJAX con la URL del enlace
        }
    });

    // ---  Eliminar con SweetAlert2
    $(document).on('click', '.btn-eliminar-meta', function (e) {
        e.preventDefault();
        const formulario = $(this).closest('form');
        const nombreMeta = $(this).data('nombre') || 'esta meta';

        Swal.fire({
            title: '¿Eliminar Meta?',
            text: `¿Estás seguro de eliminar "${nombreMeta}"? Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'rgb(160, 73, 73)',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                formulario.submit(); // Enviamos el formulario para eliminar
            }
        });
    });

    // Actualizamos la visibilidad del botón X al cargar la página
    actualizarVisibilidadX();
});
