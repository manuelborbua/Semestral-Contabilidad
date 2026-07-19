<?php
// correo/enviar.php
// Responsable: Daniela Anaya — Interfaz, menú y correo
//
// Versión sencilla, según lo acordado en el documento del grupo:
// - Genera el reporte en HTML.
// - Solicita el correo del destinatario.
// - Envía el contenido del reporte en el cuerpo del mensaje.
// - Más adelante se puede adjuntar el PDF (pendiente, no bloquea esta versión).
//
// Este archivo se puede llegar de dos formas:
// 1) reportes/individual.php o reportes/grupal.php redirigen aquí
//    pasando el contenido del reporte ya armado (por sesión o por POST).
// 2) Se abre directamente y se pide el correo antes de enviar.

$titulo_pagina = 'Enviar reporte por correo';

$mensaje_resultado = '';
$tipo_mensaje = '';

// El contenido del reporte lo prepara el módulo de reportes (Arturo).
// Aquí solo se usa lo que llegue; si no llega nada, se muestra un aviso.
$contenido_reporte = $_POST['contenido_reporte'] ?? ($_SESSION['reporte_para_enviar'] ?? '');
$asunto_reporte     = $_POST['asunto_reporte'] ?? ($_SESSION['asunto_para_enviar'] ?? 'Reporte de planilla — Planilla Prospera');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_correo'])) {

    $destinatario = trim($_POST['correo_destino'] ?? '');

    if (!filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
        $mensaje_resultado = 'El correo ingresado no es válido.';
        $tipo_mensaje = 'error';
    } elseif (empty($contenido_reporte)) {
        $mensaje_resultado = 'No hay contenido de reporte para enviar. Genere el reporte antes de enviarlo.';
        $tipo_mensaje = 'error';
    } else {
        $encabezados  = "MIME-Version: 1.0" . "\r\n";
        $encabezados .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $encabezados .= "From: Planilla Prospera <no-responder@planillaprospera.local>" . "\r\n";

        $cuerpo = '<div style="font-family:Arial,sans-serif;color:#1c2333;">' . $contenido_reporte . '</div>';

        // mail() nativo de PHP para mantener la primera versión sencilla.
        // Requiere que el servidor tenga un agente de correo configurado (ej. sendmail).
        $enviado = @mail($destinatario, $asunto_reporte, $cuerpo, $encabezados);

        if ($enviado) {
            $mensaje_resultado = 'El reporte fue enviado correctamente a ' . htmlspecialchars($destinatario) . '.';
            $tipo_mensaje = 'exito';
            unset($_SESSION['reporte_para_enviar'], $_SESSION['asunto_para_enviar']);
        } else {
            $mensaje_resultado = 'No se pudo enviar el correo. Verifique la configuración del servidor de correo.';
            $tipo_mensaje = 'error';
        }
    }
}

require __DIR__ . '/../includes/header.php';
?>

<div class="encabezado-pagina">
    <div>
        <h1>Enviar reporte por correo</h1>
        <p>Ingrese el correo del destinatario para enviar el reporte generado.</p>
    </div>
</div>

<?php if ($mensaje_resultado): ?>
    <div class="mensaje mensaje-<?php echo $tipo_mensaje; ?>"><?php echo htmlspecialchars($mensaje_resultado); ?></div>
<?php endif; ?>

<?php if (empty($contenido_reporte)): ?>
    <div class="mensaje mensaje-info">
        Aún no hay un reporte cargado. Genere un reporte individual, grupal o de la CSS y use el botón "Enviar por correo" desde esa pantalla.
    </div>
<?php endif; ?>

<form method="POST" class="formulario validar formulario-correo">
    <div class="campo">
        <label for="correo_destino">Correo del destinatario</label>
        <input type="email" id="correo_destino" name="correo_destino" required placeholder="ejemplo@correo.com">
        <div class="ayuda-error"></div>
    </div>

    <div class="campo">
        <label for="asunto_reporte">Asunto</label>
        <input type="text" id="asunto_reporte" name="asunto_reporte" value="<?php echo htmlspecialchars($asunto_reporte); ?>">
    </div>

    <input type="hidden" name="contenido_reporte" value="<?php echo htmlspecialchars($contenido_reporte); ?>">

    <button type="submit" name="enviar_correo" class="boton boton-dorado" <?php echo empty($contenido_reporte) ? 'disabled' : ''; ?>>
        Enviar reporte
    </button>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>
