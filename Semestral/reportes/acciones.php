<div class="acciones-reporte no-imprimir">
    <button class="boton boton-primario" type="button" onclick="window.print()">Imprimir</button>
    <form method="POST" action="../correo/enviar.php">
        <input type="hidden" name="contenido_reporte" value="<?= escapar($contenido_reporte) ?>">
        <input type="hidden" name="asunto_reporte" value="<?= escapar($titulo_pagina . ' — Planilla Prospera') ?>">
        <button class="boton boton-dorado" type="submit">Enviar por correo</button>
    </form>
</div>
