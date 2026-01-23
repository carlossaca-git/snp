document.addEventListener('DOMContentLoaded', function () {

    // Verificamos que exista el canvas para evitar errores
    const canvas = document.getElementById('graficoAvance');

    if (canvas && typeof DATOS_KARDEX !== 'undefined') {
        const ctx = canvas.getContext('2d');

        // Leemos los datos desde la variable global definida en la vista
        const labels = DATOS_KARDEX.labels;
        const data = DATOS_KARDEX.values;
        const metaFinal = DATOS_KARDEX.meta;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Avance Real',
                        data: data,
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderWidth: 3,
                        pointRadius: 5,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#4e73df',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Meta Objetivo',
                        data: Array(labels.length).fill(metaFinal),
                        borderColor: '#1cc88a',
                        borderDash: [5, 5],
                        borderWidth: 2,
                        pointRadius: 0,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false
                    }
                },
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.dataset.label + ': ' + context.parsed.y;
                            }
                        }
                    }
                }
            }
        });
    }
});

/**
 * Función global para manejar el visor de PDF en Modal
 * @param {string} url - La ruta generada por Laravel
 */
function abrirVisorPdf(url) {
    const visor = document.getElementById('visorPdf');
    const btnDescargar = document.getElementById('btnDescargarPdf');
    const modalElement = document.getElementById('modalPdf');

    if (visor && modalElement) {
        //Limpiar el src antes para que no se vea el PDF anterior si se abre otro
        visor.src = '';

        //  Asignar la nueva URL
        visor.src = url;

        //  Configurar el botón de descarga
        if (btnDescargar) btnDescargar.href = url;

        //  Mostrar el modal
        const miModal = new bootstrap.Modal(modalElement);
        miModal.show();
    }
}

