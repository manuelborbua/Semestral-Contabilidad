<?php
require __DIR__ . '/../includes/header.php';
?>

<div class="encabezado-pagina">
    <div>
        <h1>Compartir reporte</h1>
        <p>Genera el reporte en PDF y compártelo desde tu dispositivo.</p>
    </div>
</div>

<?php if ($contenido_reporte === ''): ?>
    <div class="mensaje mensaje-info">
        Genere un reporte y use su botón “Compartir reporte”.
    </div>
<?php endif; ?>

<div class="formulario formulario-correo">
    <div class="campo">
        <label for="correo_destino">
            Correo del destinatario
        </label>

        <input
            type="email"
            id="correo_destino"
            name="correo_destino"
            placeholder="profesora@utp.ac.pa"
        >
    </div>

    <div class="campo">
        <label for="asunto_reporte">Asunto</label>

        <input
            type="text"
            id="asunto_reporte"
            value="<?= escapar($asunto_reporte) ?>"
        >
    </div>

    <button
        type="button"
        id="btnCompartirReporte"
        class="boton boton-dorado"
        <?= $contenido_reporte === '' ? 'disabled' : '' ?>
    >
        Compartir PDF
    </button>

    <a
        href="generar_pdf.php"
        id="btnDescargarReporte"
        class="boton"
        download
        <?= $contenido_reporte === '' ? 'aria-disabled="true"' : '' ?>
    >
        Descargar PDF
    </a>

    <p id="estadoCompartir" class="texto-ayuda"></p>
</div>

<script>
(() => {
    const botonCompartir = document.getElementById(
        'btnCompartirReporte'
    );

    const estado = document.getElementById('estadoCompartir');

    botonCompartir?.addEventListener('click', async () => {
        const asunto = document
            .getElementById('asunto_reporte')
            .value
            .trim();

        const destinatario = document
            .getElementById('correo_destino')
            .value
            .trim();

        botonCompartir.disabled = true;
        estado.textContent = 'Generando el PDF...';

        try {
            const respuesta = await fetch('generar_pdf.php', {
                method: 'GET',
                credentials: 'same-origin',
                cache: 'no-store'
            });

            if (!respuesta.ok) {
                const mensaje = await respuesta.text();

                throw new Error(
                    mensaje || 'No fue posible generar el PDF.'
                );
            }

            const blob = await respuesta.blob();

            const archivo = new File(
                [blob],
                'reporte-planilla.pdf',
                {
                    type: 'application/pdf',
                    lastModified: Date.now()
                }
            );

            const datosCompartir = {
                title: asunto || 'Reporte de Planilla Prospera',
                text: destinatario
                    ? `Reporte preparado para ${destinatario}`
                    : 'Reporte generado por Planilla Prospera',
                files: [archivo]
            };

            const puedeCompartirArchivo =
                typeof navigator.share === 'function' &&
                typeof navigator.canShare === 'function' &&
                navigator.canShare({
                    files: [archivo]
                });

            if (puedeCompartirArchivo) {
                await navigator.share(datosCompartir);

                estado.textContent =
                    'El reporte fue entregado al menú de compartir.';
            } else {
                descargarArchivo(blob, archivo.name);

                estado.textContent =
                    'Tu navegador no permite compartir archivos. ' +
                    'El PDF fue descargado.';
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                estado.textContent = 'Se canceló el proceso.';
            } else {
                console.error(error);

                estado.textContent =
                    error.message ||
                    'No fue posible compartir el reporte.';
            }
        } finally {
            botonCompartir.disabled = false;
        }
    });

    function descargarArchivo(blob, nombre) {
        const url = URL.createObjectURL(blob);
        const enlace = document.createElement('a');

        enlace.href = url;
        enlace.download = nombre;

        document.body.appendChild(enlace);
        enlace.click();
        enlace.remove();

        setTimeout(() => {
            URL.revokeObjectURL(url);
        }, 1000);
    }
})();
</script>

<?php
require __DIR__ . '/../includes/footer.php';
?>
